<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Application;
use App\Models\Document;
use App\Models\DocumentCategory;
use Carbon\Carbon;

use Auth; 
use Config;

class RecentlyModifiedClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	
	/**
     * Display recently modified clients based on activities log.
     *
     * @return \Illuminate\Http\Response 
     */
	public function index(Request $request)
	{
		set_time_limit(180);

		// Get filter parameters from request (normalize to scalar to avoid "Illegal operator and value combination")
		$fromDate = $request->input('from_date');
		$fromDate = is_array($fromDate) ? '' : trim((string) $fromDate);
		$toDate = $request->input('to_date');
		$toDate = is_array($toDate) ? '' : trim((string) $toDate);
		$sortOrder = $request->input('sort_order', 'desc'); // Default to descending (newest first)
		$search = $request->input('search', '');
		$search = is_array($search) ? '' : trim((string) $search);
		$activityType = $request->input('activity_type', '');
		$activityType = is_array($activityType) ? '' : trim((string) $activityType);
		$perPage = $request->input('per_page', config('constants.limit', 20));
		$sortColumn = $request->input('sort_column', 'activity_date');
		$hasApplications = $request->input('has_applications', ''); // '' = all, '0' = no applications
		$lastActivityYears = $request->input('last_activity_years', ''); // 1, 2, 3, 4, 5 = X+ years ago
		$documentCount = $request->input('document_count', ''); // '', '0', '1', ... '9', '10+' = documents count filter
		$docStorage = $request->input('doc_storage', ''); // '', 'local', 'aws', 'both', 'none' = document storage location filter
		$noPhone = $request->input('no_phone', ''); // '' = all, '1' = only clients with no phone number
		$noEmail = $request->input('no_email', ''); // '' = all, '1' = only clients with no email address

		// Default to last 12 months when no date/search applied for faster initial load
		if ($fromDate === '' && $toDate === '' && $search === '') {
			$fromDate = Carbon::now()->subMonths(12)->format('Y-m-d');
		}
		
		// Get the most recent activity for each client
		// Filter out NULL client_id to avoid orphaned activities
		$subQuery = ActivitiesLog::select('client_id', DB::raw('MAX(created_at) as last_activity'))
			->whereNotNull('client_id')
			->groupBy('client_id');
		
		// Clients live in admins table (role = 7). Join admins twice: client info + creator info.
		$query = ActivitiesLog::select(
				'activities_logs.id as activity_id',
				'activities_logs.client_id',
				'activities_logs.created_by',
				'activities_logs.subject',
				'activities_logs.description',
				'activities_logs.created_at as activity_date',
				'client_admins.first_name as client_firstname',
				'client_admins.last_name as client_lastname',
				'client_admins.client_id as client_unique_id',
				'client_admins.email as client_email',
				'client_admins.phone as client_phone',
				'admins.first_name as admin_firstname',
				'admins.last_name as admin_lastname'
			)
			->joinSub($subQuery, 'latest_activities', function($join) {
				$join->on('activities_logs.client_id', '=', 'latest_activities.client_id')
					 ->on('activities_logs.created_at', '=', 'latest_activities.last_activity');
			})
			// Use INNER JOIN so we only show non-archived clients (archived clients would otherwise appear with NULL client info)
			->join('admins as client_admins', function($join) {
				$join->on('activities_logs.client_id', '=', 'client_admins.id')
					 ->where('client_admins.role', '=', '7')
					 ->where(function($q) {
						 $q->whereIn('client_admins.is_archived', [0, '0'])
						   ->orWhereNull('client_admins.is_archived');
					 });
			})
			->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id');

		// Only join document stats when filtering by document count or doc storage (otherwise we fetch per page later for speed)
		$useDocStatsInQuery = ($documentCount !== '' || ($docStorage !== '' && in_array($docStorage, ['local', 'aws', 'both', 'none'], true)));
		if ($useDocStatsInQuery) {
			$docStatsSubQuery = Document::select(
					'client_id',
					DB::raw('COUNT(*) as doc_count'),
					DB::raw("MAX(CASE WHEN (myfile_key IS NULL OR TRIM(COALESCE(myfile_key, '')) = '') AND myfile IS NOT NULL AND TRIM(COALESCE(myfile, '')) != '' THEN 1 ELSE 0 END) AS has_local"),
					DB::raw("MAX(CASE WHEN myfile_key IS NOT NULL AND TRIM(myfile_key) != '' THEN 1 ELSE 0 END) AS has_aws")
				)
				->whereNull('archived_at')
				->groupBy('client_id');
			$query->leftJoinSub($docStatsSubQuery, 'doc_stats', 'activities_logs.client_id', '=', 'doc_stats.client_id');
			$query->addSelect([
				DB::raw("CASE
					WHEN COALESCE(doc_stats.has_local, 0) = 1 AND COALESCE(doc_stats.has_aws, 0) = 1 THEN 'both'
					WHEN COALESCE(doc_stats.has_local, 0) = 1 THEN 'local'
					WHEN COALESCE(doc_stats.has_aws, 0) = 1 THEN 'aws'
					ELSE 'none'
				END AS doc_storage")
			]);
		}

		
		// Apply search filter (name, email, phone, client unique ID e.g. TEST105453)
		if (!empty($search)) {
			$query->where(function($q) use ($search) {
				$q->where(DB::raw("CONCAT(client_admins.first_name, ' ', client_admins.last_name)"), 'LIKE', "%{$search}%")
				  ->orWhere('client_admins.email', 'LIKE', "%{$search}%")
				  ->orWhere('client_admins.phone', 'LIKE', "%{$search}%")
				  ->orWhere('client_admins.client_id', 'LIKE', "%{$search}%");
			});
		}
		
		// Apply activity type filter
		if (!empty($activityType)) {
			$query->where(function($q) use ($activityType) {
				$q->where('activities_logs.subject', 'LIKE', "%{$activityType}%")
				  ->orWhere('activities_logs.description', 'LIKE', "%{$activityType}%");
			});
		}
		
		// Apply date filters to main query if provided
		// This filters clients whose most recent activity falls within the date range
		if ($fromDate !== '') {
			$query->where('activities_logs.created_at', '>=', $fromDate);
		}
		if ($toDate !== '') {
			$query->where('activities_logs.created_at', '<=', $toDate);
		}
		
		// Filter: clients that have no applications created
		if ($hasApplications === '0') {
			$query->whereNotIn('activities_logs.client_id', Application::select('client_id'));
		}
		
		// Filter: last activity X+ years ago (1 to 5 years on yearly basis)
		if ($lastActivityYears !== '' && in_array((int) $lastActivityYears, [1, 2, 3, 4, 5], true)) {
			$query->where('activities_logs.created_at', '<=', Carbon::now()->subYears((int) $lastActivityYears));
		}
		
		// Filter: document count (0, 1, 2, ... 9, 10+)
		if ($documentCount !== '') {
			if ($documentCount === '0') {
				$query->where(function ($q) {
					$q->whereNull('doc_stats.doc_count')->orWhere('doc_stats.doc_count', 0);
				});
			} elseif ($documentCount === '10+') {
				$query->whereNotNull('doc_stats.doc_count')->where('doc_stats.doc_count', '>=', 10);
			} elseif (in_array($documentCount, ['1', '2', '3', '4', '5', '6', '7', '8', '9'], true)) {
				$query->where('doc_stats.doc_count', '=', (int) $documentCount);
			}
		}
		
		// Filter: document storage location (local, aws, both, none)
		if ($docStorage !== '' && in_array($docStorage, ['local', 'aws', 'both', 'none'], true)) {
			$docStorageExpr = "CASE
				WHEN COALESCE(doc_stats.has_local, 0) = 1 AND COALESCE(doc_stats.has_aws, 0) = 1 THEN 'both'
				WHEN COALESCE(doc_stats.has_local, 0) = 1 THEN 'local'
				WHEN COALESCE(doc_stats.has_aws, 0) = 1 THEN 'aws'
				ELSE 'none'
			END";
			$query->whereRaw("({$docStorageExpr}) = ?", [$docStorage]);
		}
		
		// Filter: no phone number (only clients with missing/empty phone)
		if ($noPhone === '1') {
			$query->where(function ($q) {
				$q->whereNull('client_admins.phone')
				  ->orWhere(DB::raw("TRIM(COALESCE(client_admins.phone, ''))"), '=', '');
			});
		}
		
		// Filter: no email address (only clients with missing/empty email)
		if ($noEmail === '1') {
			$query->where(function ($q) {
				$q->whereNull('client_admins.email')
				  ->orWhere(DB::raw("TRIM(COALESCE(client_admins.email, ''))"), '=', '');
			});
		}
		
		// Apply column sorting
		$allowedSortColumns = [
			'client_name' => DB::raw("CONCAT(client_admins.first_name, ' ', client_admins.last_name)"),
			'client_email' => 'client_admins.email',
			'client_phone' => 'client_admins.phone',
			'activity_date' => 'activities_logs.created_at',
			'modified_by' => DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)"),
		];
		
		if (isset($allowedSortColumns[$sortColumn])) {
			$query->orderBy($allowedSortColumns[$sortColumn], $sortOrder);
		} else {
			$query->orderBy('activities_logs.created_at', $sortOrder);
		}
		
		// Use simplePaginate for fast load (no expensive COUNT over full result set)
		$lists = $query->simplePaginate($perPage)
			->appends($request->query());

		// When we skipped doc_stats in the main query, fetch storage only for clients on this page
		if (!$useDocStatsInQuery && $lists->count() > 0) {
			$clientIds = $lists->pluck('client_id')->unique()->values()->all();
			$docStats = Document::select(
					'client_id',
					DB::raw("MAX(CASE WHEN (myfile_key IS NULL OR TRIM(COALESCE(myfile_key, '')) = '') AND myfile IS NOT NULL AND TRIM(COALESCE(myfile, '')) != '' THEN 1 ELSE 0 END) AS has_local"),
					DB::raw("MAX(CASE WHEN myfile_key IS NOT NULL AND TRIM(myfile_key) != '' THEN 1 ELSE 0 END) AS has_aws")
				)
				->whereNull('archived_at')
				->whereIn('client_id', $clientIds)
				->groupBy('client_id')
				->get();
			$storageMap = [];
			foreach ($docStats as $r) {
				$storageMap[$r->client_id] = (($r->has_local ?? 0) && ($r->has_aws ?? 0)) ? 'both' : (($r->has_local ?? 0) ? 'local' : (($r->has_aws ?? 0) ? 'aws' : 'none'));
			}
			foreach ($lists->items() as $row) {
				$row->doc_storage = $storageMap[$row->client_id] ?? 'none';
			}
		}

		$totalData = null; // Not computed for fast load; use filters and Next/Previous to navigate
		
		return view('AdminConsole.recent_clients.index', compact([
			'lists', 
			'totalData', 
			'fromDate', 
			'toDate', 
			'sortOrder', 
			'search', 
			'activityType', 
			'perPage', 
			'sortColumn',
			'hasApplications',
			'lastActivityYears',
			'documentCount',
			'docStorage',
			'noPhone',
			'noEmail'
		])); 	
	}
	
	/**
     * Get client details for expandable row (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function getClientDetails(Request $request)
	{
		$clientId = (int) $request->input('client_id');
		
		if (!$clientId || $clientId < 1) {
			return response()->json([
				'success' => false,
				'message' => 'Client ID is required'
			], 400);
		}
		
		// Get client info
		$client = Admin::where('id', $clientId)
			->where('role', '7')
			->first();
		
		if (!$client) {
			return response()->json([
				'success' => false,
				'message' => 'Client not found'
			], 404);
		}
		
		// Get last activity with creator info
		$lastActivity = ActivitiesLog::where('client_id', $clientId)
			->with('createdBy')
			->orderBy('created_at', 'desc')
			->first();
		
		// Get document count
		$documentCount = Document::where('client_id', $clientId)
			->whereNull('archived_at') // Only count non-archived documents
			->count();
		
		// Get document storage location: local (myfile_key empty), AWS (myfile_key set)
		$hasLocal = Document::where('client_id', $clientId)
			->whereNull('archived_at')
			->where(function ($q) {
				$q->whereNull('myfile_key')->orWhere('myfile_key', '');
			})
			->whereNotNull('myfile')
			->where('myfile', '!=', '')
			->exists();
		$hasAws = Document::where('client_id', $clientId)
			->whereNull('archived_at')
			->whereNotNull('myfile_key')
			->where('myfile_key', '!=', '')
			->exists();
		$documentStorage = ($hasLocal && $hasAws) ? 'both' : ($hasLocal ? 'local' : ($hasAws ? 'aws' : 'none'));

		// Category doc counts (local/public folder only, not S3) - category_id resolved by category name (Application, Education, Migration)
		$applicationCategoryId = DocumentCategory::where('name', 'Application')->value('id');
		$educationCategoryId = DocumentCategory::where('name', 'Education')->value('id');
		$migrationCategoryId = DocumentCategory::where('name', 'Migration')->value('id');

		$applicationDocCountLocal = 0;
		if ($applicationCategoryId) {
			$applicationDocCountLocal = Document::where('client_id', $clientId)
				->whereNull('archived_at')
				->where('doc_type', 'documents')
				->where('category_id', $applicationCategoryId)
				->storedLocally()
				->count();
		}

		$educationDocCountLocal = 0;
		if ($educationCategoryId) {
			$educationDocCountLocal = Document::where('client_id', $clientId)
				->whereNull('archived_at')
				->where('doc_type', 'documents')
				->where('is_edu_and_mig_doc_migrate', Document::EDU_MIG_MIGRATE_SUCCESS)
				->where('category_id', $educationCategoryId)
				->storedLocally()
				->count();
		}

		$migrationDocCountLocal = 0;
		if ($migrationCategoryId) {
			$migrationDocCountLocal = Document::where('client_id', $clientId)
				->whereNull('archived_at')
				->where('doc_type', 'documents')
				->where('is_edu_and_mig_doc_migrate', Document::EDU_MIG_MIGRATE_SUCCESS)
				->where('category_id', $migrationCategoryId)
				->storedLocally()
				->count();
		}
		
		// Check if client is archived
		$isArchived = $client->is_archived == 1;
		
		return response()->json([
			'success' => true,
			'data' => [
				'client_id' => $clientId,
				'last_activity' => $lastActivity ? [
					'subject' => $lastActivity->subject,
					'description' => $lastActivity->description,
					'date' => $lastActivity->created_at->format('d/m/Y h:i A'),
					'created_by' => $lastActivity->createdBy ? 
						($lastActivity->createdBy->first_name . ' ' . $lastActivity->createdBy->last_name) : 
						'N/A'
				] : null,
				'document_count' => $documentCount,
				'document_storage' => $documentStorage,
				'application_doc_count_local' => $applicationDocCountLocal,
				'education_doc_count_local' => $educationDocCountLocal,
				'migration_doc_count_local' => $migrationDocCountLocal,
				'is_archived' => $isArchived
			]
		]);
	}

	/**
	 * Get documents for a client by category (Application, Education, Migration) - public folder only, for popup list.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getClientDocumentsByCategory(Request $request)
	{
		$clientId = (int) $request->input('client_id');
		$category = $request->input('category'); // application, education, migration
		$category = is_array($category) ? '' : trim((string) $category);

		if (!$clientId || $clientId < 1) {
			return response()->json(['success' => false, 'message' => 'Client ID is required'], 400);
		}
		if (!in_array($category, ['application', 'education', 'migration'], true)) {
			return response()->json(['success' => false, 'message' => 'Invalid category'], 400);
		}

		$categoryId = null;
		if ($category === 'application') {
			$categoryId = DocumentCategory::where('name', 'Application')->value('id');
		} elseif ($category === 'education') {
			$categoryId = DocumentCategory::where('name', 'Education')->value('id');
		} else {
			$categoryId = DocumentCategory::where('name', 'Migration')->value('id');
		}

		if (!$categoryId) {
			return response()->json(['success' => true, 'documents' => [], 'category_label' => ucfirst($category)]);
		}

		$query = Document::where('client_id', $clientId)
			->whereNull('archived_at')
			->where('doc_type', 'documents')
			->where('category_id', $categoryId)
			->whereNotNull('myfile')
			->where('myfile', '!=', '')
			->where(function ($q) {
				// Include: (a) stored locally (no S3), or (b) on S3 but still have doc_public_path (local copy exists)
				$q->where(function ($q2) {
					$q2->whereNull('myfile_key')->orWhere('myfile_key', '');
				})->orWhere(function ($q2) {
					$q2->whereNotNull('myfile_key')->where('myfile_key', '!=', '')
						->whereNotNull('doc_public_path')->where('doc_public_path', '!=', '');
				});
			});

		if ($category !== 'application') {
			$query->where('is_edu_and_mig_doc_migrate', Document::EDU_MIG_MIGRATE_SUCCESS);
		}

		$documents = $query->orderBy('created_at', 'desc')->get(['id', 'file_name', 'filetype', 'myfile', 'myfile_key', 'doc_public_path', 'created_at']);

		$list = [];
		foreach ($documents as $doc) {
			$isOnS3 = !empty(trim((string) ($doc->myfile_key ?? '')));
			$hasPublicPath = $isOnS3 && !empty(trim((string) ($doc->doc_public_path ?? '')));
			$previewUrl = null;
			if (!empty($doc->myfile)) {
				$previewUrl = $isOnS3 ? $doc->myfile : asset('img/documents/' . $doc->myfile);
			}
			$list[] = [
				'id' => $doc->id,
				'file_name' => $doc->file_name,
				'filetype' => $doc->filetype,
				'created_at' => $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : null,
				'preview_url' => $previewUrl,
				'is_on_s3' => $isOnS3,
				'has_public_path' => $hasPublicPath,
			];
		}

		return response()->json([
			'success' => true,
			'documents' => $list,
			'category_label' => ucfirst($category),
		]);
	}

	/**
	 * Upload a single public (local) document to S3. Updates only myfile and myfile_key after successful upload.
	 * Does not delete the local file to avoid data loss.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadDocumentToS3(Request $request)
	{
		$documentId = (int) $request->input('document_id');
		if (!$documentId || $documentId < 1) {
			return response()->json(['success' => false, 'message' => 'Document ID is required'], 400);
		}

		$document = Document::find($documentId);
		if (!$document) {
			return response()->json(['success' => false, 'message' => 'Document not found'], 404);
		}

		// Only allow documents that are currently stored locally (public folder), not already on S3
		if (!empty(trim((string) ($document->myfile_key ?? '')))) {
			return response()->json(['success' => false, 'message' => 'Document is already on S3'], 400);
		}
		if (empty(trim((string) $document->myfile ?? ''))) {
			return response()->json(['success' => false, 'message' => 'Document has no local file path'], 400);
		}

		// Restrict to Application, Education, Migration categories
		$allowedCategoryNames = ['Application', 'Education', 'Migration'];
		$category = $document->category_id ? DocumentCategory::find($document->category_id) : null;
		if (!$category || !in_array($category->name, $allowedCategoryNames, true)) {
			return response()->json(['success' => false, 'message' => 'Document category not allowed for this upload'], 400);
		}

		// Client unique ID for S3 path (e.g. MANV230200201)
		$client = Admin::where('id', $document->client_id)->where('role', '7')->first();
		if (!$client || empty(trim((string) ($client->client_id ?? '')))) {
			return response()->json(['success' => false, 'message' => 'Client unique ID not found'], 400);
		}
		$clientUniqueId = trim($client->client_id);

		// Local file path: public/img/documents/{myfile}
		$relativePath = ltrim(str_replace('\\', '/', $document->myfile), '/');
		$localPath = public_path('img/documents/' . $relativePath);
		if (!file_exists($localPath) || !is_readable($localPath)) {
			Log::warning('Upload to S3: local file not found or not readable', ['document_id' => $documentId, 'path' => $localPath]);
			return response()->json(['success' => false, 'message' => 'Local file not found or not readable'], 404);
		}

		$fileContents = file_get_contents($localPath);
		if ($fileContents === false) {
			return response()->json(['success' => false, 'message' => 'Failed to read local file'], 500);
		}

		// S3 path: {client_unique_id}/documents/{filename} - same structure as existing S3 documents
		$originalName = basename($relativePath);
		$sanitized = $this->sanitizeFilenameForS3($originalName);
		$s3FileName = time() . $sanitized;
		$docType = $document->doc_type ?: 'documents';
		$s3Key = $clientUniqueId . '/' . $docType . '/' . $s3FileName;

		try {
			$put = Storage::disk('s3')->put($s3Key, $fileContents);
			if (!$put) {
				Log::error('Upload to S3: put returned false', ['document_id' => $documentId, 's3_key' => $s3Key]);
				return response()->json(['success' => false, 'message' => 'S3 upload failed'], 500);
			}
			$fileUrl = Storage::disk('s3')->url($s3Key);
		} catch (\Throwable $e) {
			Log::error('Upload to S3 exception', ['document_id' => $documentId, 'error' => $e->getMessage()]);
			return response()->json(['success' => false, 'message' => 'S3 upload error: ' . $e->getMessage()], 500);
		}

		// Save public path before overwriting, so "Delete public doc" can remove the local file later
		$document->doc_public_path = $document->myfile;
		$document->myfile = $fileUrl;
		$document->myfile_key = $s3FileName;
		$document->save();

		return response()->json([
			'success' => true,
			'message' => 'Document uploaded to S3 successfully',
			's3_url' => $fileUrl,
			'document_id' => $document->id,
		]);
	}

	/**
	 * Delete the local (public) copy of a document that is already on S3.
	 * Requires doc_public_path to be set (saved at S3 upload time). Clears doc_public_path after delete.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deletePublicDoc(Request $request)
	{
		$documentId = (int) $request->input('document_id');
		if (!$documentId || $documentId < 1) {
			return response()->json(['success' => false, 'message' => 'Document ID is required'], 400);
		}

		$document = Document::find($documentId);
		if (!$document) {
			return response()->json(['success' => false, 'message' => 'Document not found'], 404);
		}

		if (empty(trim((string) ($document->myfile_key ?? '')))) {
			return response()->json(['success' => false, 'message' => 'Document is not on S3; nothing to delete for public copy'], 400);
		}
		$publicPath = trim((string) ($document->doc_public_path ?? ''));
		if ($publicPath === '') {
			return response()->json(['success' => false, 'message' => 'No public path stored; local copy may already be deleted'], 400);
		}

		$relativePath = ltrim(str_replace('\\', '/', $publicPath), '/');
		// If stored as full URL (e.g. https://bansalcrm.com/img/documents/file.pdf), extract path after /img/documents/
		if (preg_match('#^https?://#i', $relativePath)) {
			$parsed = parse_url($relativePath);
			$path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
			$prefix = 'img/documents/';
			if (stripos($path, $prefix) === 0) {
				$relativePath = substr($path, strlen($prefix));
			} else {
				$relativePath = $path;
			}
			$relativePath = ltrim($relativePath, '/');
		}
		// If stored with img/documents/ prefix (path only), strip it so we don't build img/documents/img/documents/...
		if ($relativePath !== '' && stripos($relativePath, 'img/documents/') === 0) {
			$relativePath = ltrim(substr($relativePath, strlen('img/documents/')), '/');
		}
		if ($relativePath === '' || preg_match('#\.\./#', $relativePath)) {
			return response()->json(['success' => false, 'message' => 'Invalid path'], 400);
		}

		$baseDir = realpath(public_path('img/documents'));
		if ($baseDir === false) {
			$document->doc_public_path = null;
			$document->save();
			return response()->json(['success' => true, 'message' => 'Public path cleared', 'document_id' => $document->id]);
		}

		$candidatePath = public_path('img/documents/' . $relativePath);
		$resolvedPath = realpath($candidatePath);
		if ($resolvedPath === false) {
			// File already missing or path invalid; clear stored path and return success
			$document->doc_public_path = null;
			$document->save();
			return response()->json(['success' => true, 'message' => 'Public path cleared (file was already missing)', 'document_id' => $document->id]);
		}
		if (strpos($resolvedPath, $baseDir) !== 0 || !is_file($resolvedPath)) {
			return response()->json(['success' => false, 'message' => 'Invalid path'], 400);
		}

		try {
			unlink($resolvedPath);
		} catch (\Throwable $e) {
			Log::error('Delete public doc: unlink failed', ['document_id' => $documentId, 'path' => $resolvedPath, 'error' => $e->getMessage()]);
			return response()->json(['success' => false, 'message' => 'Failed to delete file'], 500);
		}

		$document->doc_public_path = null;
		$document->save();

		return response()->json([
			'success' => true,
			'message' => 'Public document deleted successfully',
			'document_id' => $document->id,
		]);
	}

	/**
	 * Sanitize filename for S3 path to prevent 403 (same idea as EmailUploadV2Controller).
	 *
	 * @param string $filename
	 * @return string
	 */
	private function sanitizeFilenameForS3(string $filename): string
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$base = pathinfo($filename, PATHINFO_FILENAME);
		$base = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $base);
		$base = preg_replace('/_+/', '_', $base);
		$base = trim($base, '_');
		if ($base === '') {
			$base = 'doc_' . time();
		}
		return $ext ? $base . '.' . $ext : $base;
	}
	
	/**
     * Archive or unarchive a client
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function toggleArchive(Request $request)
	{
		$clientId = $request->input('client_id');
		$action = $request->input('action'); // 'archive' or 'unarchive'
		
		if (!$clientId) {
			return response()->json([
				'success' => false,
				'message' => 'Client ID is required'
			], 400);
		}
		
		// Get client info
		$client = Admin::where('id', $clientId)
			->where('role', '7')
			->first();
		
		if (!$client) {
			return response()->json([
				'success' => false,
				'message' => 'Client not found'
			], 404);
		}
		
		// Determine archive status based on action
		if ($action === 'archive') {
			$isArchived = 1;
			$updateData = [
				'is_archived' => $isArchived,
				'archived_on' => date('Y-m-d'),
				'archived_by' => Auth::user()->id
			];
			$message = 'Client has been archived successfully.';
		} else if ($action === 'unarchive') {
			$isArchived = 0;
			$updateData = [
				'is_archived' => $isArchived,
				'archived_on' => null,
				'archived_by' => null
			];
			$message = 'Client has been unarchived successfully.';
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Invalid action. Use "archive" or "unarchive".'
			], 400);
		}
		
		// Update the client
		$updated = DB::table('admins')->where('id', $clientId)->update($updateData);
		
		if ($updated) {
			// Log the activity
			$subject = $action === 'archive' ? 'Client has been archived' : 'Client has been unarchived';
			$activity = new ActivitiesLog();
			$activity->client_id = $clientId;
			$activity->created_by = Auth::user()->id;
			$activity->subject = $subject;
			$activity->description = $subject . ' by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
			$activity->task_status = 0;
			$activity->pin = 0;
			$activity->save();
			
			return response()->json([
				'success' => true,
				'message' => $message,
				'is_archived' => $isArchived
			]);
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Failed to update client. Please try again.'
			], 500);
		}
	}
	
	/**
	 * Bulk archive selected clients
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function bulkArchive(Request $request)
	{
		$clientIds = $request->input('client_ids', []);
		
		// DEBUG: Log raw request data
		\Log::info('[BulkArchive] Raw request input:', [
			'client_ids_raw' => $clientIds,
			'client_ids_type' => gettype($clientIds),
			'is_array' => is_array($clientIds),
			'all_input' => $request->all(),
		]);
		
		if (empty($clientIds) || !is_array($clientIds)) {
			return response()->json([
				'success' => false,
				'message' => 'Please select at least one client to archive.',
				'debug' => ['client_ids_raw' => $clientIds, 'reason' => 'empty_or_not_array']
			], 400);
		}
		
		$clientIds = array_map('intval', array_filter($clientIds));
		
		if (empty($clientIds)) {
			return response()->json([
				'success' => false,
				'message' => 'Please select at least one client to archive.',
				'debug' => ['client_ids_after_filter' => $clientIds, 'reason' => 'empty_after_filter']
			], 400);
		}
		
		// Match clients that are not archived (is_archived = 0 or NULL)
		$clients = Admin::whereIn('id', $clientIds)
			->where('role', '7')
			->where(function ($q) {
				$q->whereIn('is_archived', [0, '0'])
				  ->orWhereNull('is_archived');
			})
			->get();
		
		// DEBUG: Check why clients might be empty - query admins for these IDs without archive filter
		$allAdminsWithIds = DB::table('admins')
			->whereIn('id', $clientIds)
			->get(['id', 'role', 'is_archived', 'first_name', 'last_name']);
		
		\Log::info('[BulkArchive] Query results:', [
			'client_ids_requested' => $clientIds,
			'clients_found' => $clients->pluck('id')->toArray(),
			'all_admins_with_ids' => $allAdminsWithIds->toArray(),
		]);
		
		$archived = 0;
		$updateData = [
			'is_archived' => 1,
			'archived_on' => date('Y-m-d'),
			'archived_by' => Auth::user()->id
		];
		
		foreach ($clients as $client) {
			$updated = DB::table('admins')->where('id', $client->id)->update($updateData);
			if ($updated) {
				$archived++;
				$subject = 'Client has been archived';
				$activity = new ActivitiesLog();
				$activity->client_id = $client->id;
				$activity->created_by = Auth::user()->id;
				$activity->subject = $subject;
				$activity->description = $subject . ' by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
				$activity->task_status = 0;
				$activity->pin = 0;
				$activity->save();
			}
		}
		
		$message = $archived === 1
			? '1 client has been archived successfully.'
			: $archived . ' clients have been archived successfully.';
		
		return response()->json([
			'success' => true,
			'message' => $message,
			'archived_count' => $archived,
			'debug' => [
				'client_ids_sent' => $clientIds,
				'clients_found_count' => $clients->count(),
				'archived_count' => $archived,
				'all_admins_with_ids' => $allAdminsWithIds->toArray(),
			]
		]);
	}
}
