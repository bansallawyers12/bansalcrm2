<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Document;
use App\Models\DocumentChecklist;

use App\Traits\ClientQueries;
use App\Traits\ClientAuthorization;
use App\Traits\ClientHelpers;

/**
 * Client document and checklist management
 * 
 * Methods moved from ClientsController:
 * - uploaddocument
 * - downloadpdf
 * - deletedocs
 * - renamedoc
 * - uploadalldocument
 * - addalldocchecklist
 * - deletealldocs
 * - renamealldoc
 * - renamechecklistdoc
 * - verifydoc
 * - notuseddoc
 * - backtodoc
 * - download_document
 * - bulkUploadDocuments
 * - getAutoChecklistMatches
 * 
 * Private helpers:
 * - findBestChecklistMatch
 * - cleanFileName
 * - extractKeywords
 * - calculateSimilarity
 * - checkPatternMatch
 * - checkAbbreviationMatch
 * - checkPartialMatch
 */
class ClientDocumentController extends Controller
{
    use ClientQueries, ClientAuthorization, ClientHelpers;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Get auto-checklist matches for bulk upload
     */
    public function getAutoChecklistMatches(Request $request) {
        $response = ['status' => false, 'matches' => []];
        
        try {
            $files = $request->input('files', []);
            $clientid = $request->input('clientid');
            
            // Get all active checklists from DocumentChecklist table
            $checklists = DocumentChecklist::where('status', 1)
                ->pluck('name')
                ->toArray();
            
            // If no files provided, just return checklists (for frontend to get checklist list)
            if (empty($files)) {
                $response['status'] = true;
                $response['checklists'] = $checklists;
                return response()->json($response);
            }
            
            if (empty($checklists)) {
                $response['status'] = true;
                return response()->json($response);
            }
            
            $matches = [];
            
            foreach ($files as $file) {
                $fileName = $file['name'] ?? '';
                $match = $this->findBestChecklistMatch($fileName, $checklists);
                if ($match) {
                    $matches[$fileName] = $match;
                }
            }
            
            $response['status'] = true;
            $response['matches'] = $matches;
            $response['checklists'] = $checklists;
            
        } catch (\Exception $e) {
            Log::error('Error getting auto-checklist matches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return response()->json($response);
    }
    
    /**
     * Find best checklist match for a filename
     */
    private function findBestChecklistMatch($fileName, $checklists) {
        if (empty($fileName) || empty($checklists)) {
            return null;
        }
        
        // Clean filename
        $cleanFileName = $this->cleanFileName($fileName);
        $fileNameLower = strtolower($cleanFileName);
        $fileNameWords = $this->extractKeywords($cleanFileName);
        
        $bestMatch = null;
        $bestScore = 0;
        $bestConfidence = 'low';
        
        foreach ($checklists as $checklist) {
            $checklistLower = strtolower($checklist);
            $checklistWords = $this->extractKeywords($checklist);
            
            // Strategy 1: Exact match (after cleaning)
            if ($fileNameLower === $checklistLower) {
                return [
                    'checklist' => $checklist,
                    'confidence' => 'high',
                    'score' => 100,
                    'method' => 'exact'
                ];
            }
            
            // Strategy 2: Fuzzy matching
            $similarity = $this->calculateSimilarity($fileNameLower, $checklistLower);
            if ($similarity > 85) {
                return [
                    'checklist' => $checklist,
                    'confidence' => 'high',
                    'score' => $similarity,
                    'method' => 'fuzzy'
                ];
            } elseif ($similarity > 70 && $similarity > $bestScore) {
                $bestMatch = $checklist;
                $bestScore = $similarity;
                $bestConfidence = 'medium';
            }
            
            // Strategy 3: Pattern matching
            $patternMatch = $this->checkPatternMatch($fileNameWords, $checklistWords);
            if ($patternMatch['matched'] && $patternMatch['score'] > $bestScore) {
                $bestMatch = $checklist;
                $bestScore = $patternMatch['score'];
                $bestConfidence = $patternMatch['score'] > 80 ? 'high' : 'medium';
            }
            
            // Strategy 4: Abbreviation matching
            $abbrevMatch = $this->checkAbbreviationMatch($cleanFileName, $checklist);
            if ($abbrevMatch && $abbrevMatch > $bestScore) {
                $bestMatch = $checklist;
                $bestScore = $abbrevMatch;
                $bestConfidence = 'high';
            }
            
            // Strategy 5: Partial word matching
            $partialMatch = $this->checkPartialMatch($fileNameWords, $checklistWords);
            if ($partialMatch && $partialMatch > $bestScore) {
                $bestMatch = $checklist;
                $bestScore = $partialMatch;
                $bestConfidence = 'low';
            }
        }
        
        if ($bestMatch && $bestScore > 50) {
            return [
                'checklist' => $bestMatch,
                'confidence' => $bestConfidence,
                'score' => $bestScore,
                'method' => 'combined'
            ];
        }
        
        return null;
    }
    
    /**
     * Clean filename for matching
     */
    private function cleanFileName($fileName) {
        // Remove extension
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        // Remove common prefixes (client name, timestamps)
        $name = preg_replace('/^[^_]+_/', '', $name); // Remove prefix before first underscore
        $name = preg_replace('/_\d{10,}$/', '', $name); // Remove timestamps
        $name = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $name); // Replace special chars with spaces
        return trim($name);
    }
    
    /**
     * Extract keywords from text
     */
    private function extractKeywords($text) {
        $text = strtolower($text);
        $words = preg_split('/[\s_\-]+/', $text);
        $stopWords = ['the', 'of', 'and', 'a', 'an', 'in', 'on', 'at', 'to', 'for', 'is', 'are', 'was', 'were'];
        return array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
    }
    
    /**
     * Calculate similarity between two strings (Levenshtein-based)
     */
    private function calculateSimilarity($str1, $str2) {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        
        if ($len1 === 0 || $len2 === 0) {
            return 0;
        }
        
        $maxLen = max($len1, $len2);
        $distance = levenshtein($str1, $str2);
        
        return (1 - ($distance / $maxLen)) * 100;
    }
    
    /**
     * Check pattern match
     */
    private function checkPatternMatch($fileNameWords, $checklistWords) {
        $patterns = [
            'passport' => ['passport', 'pass', 'pp'],
            'visa' => ['visa', 'grant', 'vg'],
            'identity' => ['id', 'identity', 'aadhar', 'aadhaar', 'national'],
            'birth' => ['birth', 'certificate', 'bc'],
            'marriage' => ['marriage', 'certificate', 'mc'],
            'education' => ['education', 'degree', 'diploma', 'certificate'],
            'employment' => ['employment', 'experience', 'work', 'job']
        ];
        
        $matched = false;
        $score = 0;
        
        foreach ($patterns as $key => $keywords) {
            $fileHasKeyword = false;
            $checklistHasKeyword = false;
            
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $fileNameWords)) {
                    $fileHasKeyword = true;
                }
                if (in_array($keyword, $checklistWords)) {
                    $checklistHasKeyword = true;
                }
            }
            
            if ($fileHasKeyword && $checklistHasKeyword) {
                $matched = true;
                $score = 90; // High score for pattern match
                break;
            }
        }
        
        return ['matched' => $matched, 'score' => $score];
    }
    
    /**
     * Check abbreviation match
     */
    private function checkAbbreviationMatch($fileName, $checklist) {
        $abbreviations = [
            'pp' => 'passport',
            'vg' => 'visa grant',
            'nic' => 'national identity',
            'dob' => 'birth',
            'bc' => 'birth certificate',
            'mc' => 'marriage certificate'
        ];
        
        $fileNameLower = strtolower($fileName);
        $checklistLower = strtolower($checklist);
        
        foreach ($abbreviations as $abbrev => $full) {
            if (strpos($fileNameLower, $abbrev) !== false && strpos($checklistLower, $full) !== false) {
                return 85;
            }
        }
        
        return 0;
    }
    
    /**
     * Check partial word match
     */
    private function checkPartialMatch($fileNameWords, $checklistWords) {
        $matches = 0;
        $total = count($checklistWords);
        
        if ($total === 0) {
            return 0;
        }
        
        foreach ($checklistWords as $checklistWord) {
            foreach ($fileNameWords as $fileNameWord) {
                if (strpos($fileNameWord, $checklistWord) !== false || strpos($checklistWord, $fileNameWord) !== false) {
                    $matches++;
                    break;
                }
            }
        }
        
        return ($matches / $total) * 100;
    }
    
    /**
     * Bulk upload documents
     */
    public function bulkUploadDocuments(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $clientid = $request->clientid;
            $doctype = $request->doctype ?? 'documents';
            $type = $request->type ?? 'client';
            
            $adminInfo = Admin::select('client_id')->where('id', $clientid)->first();
            $client_unique_id = $adminInfo ? $adminInfo->client_id : '';
            
            if (!$request->hasFile('files')) {
                $response['message'] = 'No files uploaded';
                return response()->json($response);
            }
            
            $files = $request->file('files');
            $mappingsInput = $request->input('mappings', []);
            
            if (!is_array($files)) {
                $files = [$files];
            }
            
            // Parse mappings JSON strings
            $mappings = [];
            foreach ($mappingsInput as $mappingStr) {
                $mapping = json_decode($mappingStr, true);
                if ($mapping) {
                    $mappings[] = $mapping;
                }
            }
            
            $uploadedCount = 0;
            $errors = [];
            
            foreach ($files as $index => $file) {
                try {
                    $fileName = $file->getClientOriginalName();
                    $size = $file->getSize();
                    
                    // Validate filename
                    if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                        $errors[] = "File '{$fileName}' has invalid characters in name";
                        continue;
                    }
                    
                    // Get mapping for this file
                    $mapping = isset($mappings[$index]) ? $mappings[$index] : null;
                    if (!$mapping || !isset($mapping['name'])) {
                        $errors[] = "No mapping found for file '{$fileName}'";
                        continue;
                    }
                    
                    $checklistName = $mapping['name'] ?? null;
                    if (!$checklistName) {
                        $errors[] = "No checklist name specified for file '{$fileName}'";
                        continue;
                    }
                    
                    // Check if checklist exists in DocumentChecklist table
                    $checklistExists = DocumentChecklist::where('name', $checklistName)
                        ->where('status', 1)
                        ->exists();
                    
                    // If mapping type is 'new' and checklist doesn't exist, create it
                    if ($mapping['type'] === 'new' && !$checklistExists) {
                        // Create new checklist in DocumentChecklist table
                        $newChecklist = new DocumentChecklist();
                        $newChecklist->name = $checklistName;
                        $newChecklist->doc_type = 1; // Default doc_type, adjust if needed
                        $newChecklist->status = 1;
                        $newChecklist->save();
                    }
                    
                    // Check if document record exists (checklist without file)
                    $document = Document::where('client_id', $clientid)
                        ->where('doc_type', $doctype)
                        ->where('checklist', $checklistName)
                        ->where('type', $type)
                        ->whereNull('not_used_doc')
                        ->whereNull('file_name') // Only get checklists without files
                        ->first();
                    
                    // If checklist doesn't exist, create it
                    if (!$document) {
                        $document = new Document();
                        $document->user_id = Auth::user()->id;
                        $document->client_id = $clientid;
                        $document->type = $type;
                        $document->doc_type = $doctype;
                        $document->checklist = $checklistName;
                        
                        // Add category_id if provided
                        if ($request->has('category_id') && $request->category_id) {
                            $document->category_id = $request->category_id;
                        } else {
                            // Default to "General" category
                            $generalCategory = \App\Models\DocumentCategory::where('name', 'General')->where('is_default', true)->first();
                            if ($generalCategory) {
                                $document->category_id = $generalCategory->id;
                            }
                        }
                        
                        $document->save();
                    }
                    
                    // Upload file using S3 (same as checklist upload flow)
                    $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExtension = $file->getClientOriginalExtension();
                    $name = time() . $file->getClientOriginalName();
                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    
                    // Update document with file info
                    $document->file_name = $nameWithoutExtension;
                    $document->filetype = $fileExtension;
                    $document->user_id = Auth::user()->id;
                    $document->myfile = $fileUrl;
                    $document->myfile_key = $name;
                    $document->file_size = $size;
                    $document->save();
                    
                    $uploadedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Error uploading '{$fileName}': " . $e->getMessage();
                    Log::error('Bulk upload error for file', [
                        'file' => $fileName,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if ($uploadedCount > 0) {
                // Log activity
                $subject = "bulk uploaded {$uploadedCount} documents";
                $description = "<p>Bulk uploaded {$uploadedCount} documents</p>";
                
                $objs = new ActivitiesLog;
                $objs->client_id = $clientid;
                $objs->created_by = Auth::user()->id;
                $objs->description = $description;
                $objs->subject = $subject;
                $objs->task_status = 0;
                $objs->pin = 0;
                $objs->save();
                
                $response['status'] = true;
                $response['message'] = "Successfully uploaded {$uploadedCount} file(s)";
                $response['uploaded'] = $uploadedCount;
                $response['errors'] = $errors;
            } else {
                $response['message'] = 'No files were uploaded. ' . implode('; ', $errors);
                $response['errors'] = $errors;
            }
            
        } catch (\Exception $e) {
            Log::error('Error in bulk upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
        
        return response()->json($response);
    }

    /**
     * Download Document from S3
     */
    public function download_document(Request $request)
    {
        $fileUrl = $request->input('filelink');
        $filename = $request->input('filename', 'downloaded.pdf');

        if (!$fileUrl) {
            return abort(400, 'Missing file URL');
        }

        try {
            // Extract S3 key from the URL
            $parsed = parse_url($fileUrl);
            if (!isset($parsed['path'])) {
                return abort(400, 'Invalid S3 URL format');
            }
            
            $s3Key = ltrim(urldecode($parsed['path']), '/');
            
            // Check if file exists in S3
            if (!Storage::disk('s3')->exists($s3Key)) {
                return abort(404, 'File not found in S3');
            }
            
            // Generate temporary URL with proper headers
            $tempUrl = Storage::disk('s3')->temporaryUrl(
                $s3Key,
                now()->addMinutes(5), // 5 minutes expiration
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                    'ResponseContentType' => 'application/pdf'
                ]
            );
            
            // Redirect to S3 temporary URL
            return redirect($tempUrl);
            
        } catch (\Exception $e) {
            Log::error('S3 download error: ' . $e->getMessage());
            return abort(500, 'Error generating download link');
        }
    }
    //Back To Document List From Not Used Tab
    public function backtodoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => null));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to document tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }


                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }

                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' document moved to document tab';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";

                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
		}
		echo json_encode($response);
	}
     //Delete all document
     public function deletealldocs(Request $request){
		$note_id = $request->note_id;
        if(\App\Models\Document::where('id',$note_id)->exists()){
            $data = DB::table('documents')->where('id', @$note_id)->first();
            /*if(
                ( isset($data->myfile) && $data->myfile != '' )
                &&
                ( isset($data->myfile_key) && $data->myfile_key != '' )
            ){*/
            if( isset($data->myfile_key) && $data->myfile_key != '' ){
                // Extract the file path from the URL
                $parsedUrl = parse_url($data->myfile);
                $filePath = ltrim($parsedUrl['path'], '/'); //dd($filePath);

                // Find the position of the keyword
                $position = strpos($filePath, '/');
                if ($position !== false) {
                    $filePathArr = explode('/',$filePath);//dd($filePathArr);
                    if(!empty($filePathArr)){
                        $fileExistPath = $filePathArr[0]."/".$filePathArr[1]."/".$data->myfile_key;
                        if (Storage::disk('s3')->exists($fileExistPath)) {
                            // To delete the uploaded file, use the delete method
                            Storage::disk('s3')->delete($fileExistPath);
                        }
                    }
                }
            }

            $res = DB::table('documents')->where('id', @$note_id)->delete();
            if($res){
                $subject = 'deleted a document';
                $objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
				$response['status'] 	= 	true;
				$response['data']	=	'Document removed successfully';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
  
    //Rename all document
    public function renamealldoc(Request $request){
		$id = $request->id;
		$filename = $request->filename;
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['file_name' => $filename]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Document saved successfully';
				$response['Id']	=	$id;
				$response['filename']	=	$filename;
				$response['filetype']	=	$doc->filetype;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
  
    //Not Used Document
    public function notuseddoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }


                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }

                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' document moved to Not Used Tab';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";

                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
		}
		echo json_encode($response);
	}

    //Rename checklist in Document
    public function renamechecklistdoc(Request $request){
		$id = $request->id;
		$checklist = $request->checklist;
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['checklist' => $checklist]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Checklist saved successfully';
				$response['Id']	=	$id;
				$response['checklist']	=	$checklist;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	//Add All Doc checklist
    public function addalldocchecklist(Request $request){ //dd($request->all());
        try {
            $response = ['status' => false, 'message' => 'Please try again'];
            $clientid = $request->clientid;
            
            if(empty($clientid)) {
                $response['message'] = 'Client ID is required';
                echo json_encode($response);
                return;
            }
            
            $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }  //dd($client_unique_id);
            $doctype = isset($request->doctype)? $request->doctype : '';

            if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist'); //dd($checklistArray);
            if (is_array($checklistArray) && !empty($checklistArray))
            {
                $saved = false;
                foreach ($checklistArray as $item)
                {
                    if(empty($item)) continue; // Skip empty items
                    $obj = new \App\Models\Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->checklist = $item;
                    
                    // Add category_id if provided
                    if ($request->has('category_id') && $request->category_id) {
                        $obj->category_id = $request->category_id;
                    } else {
                        // Default to "General" category if no category specified
                        $generalCategory = \App\Models\DocumentCategory::where('name', 'General')->where('is_default', true)->first();
                        if ($generalCategory) {
                            $obj->category_id = $generalCategory->id;
                        }
                    }
                    
                    $saved = $obj->save();
                } //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $subject = 'added document checklist';
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                        $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                        $objs->save();
                    }

                    $response['status'] 	= 	true;
                    $response['message']	=	'You have successfully added your document checklist';
                    
                    // Check if this is an AJAX request
                    if($request->ajax() || $request->wantsJson()) {
                        return response()->json($response, 200, ['Content-Type' => 'application/json']);
                    }

                    $fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderBy('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        $addedByInfo = ($admin ? $admin->first_name : 'N/A') . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
                        //Checklist verified by
                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                            $checklist_verified_by = $checklist_verified_Info->first_name;
                        } else {
                            $checklist_verified_by = 'N/A';
                        }

                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                        } else {
                            $checklist_verified_at = 'N/A';
                        }*/
                        ?>
                        <tr class="drow document-row" id="id_<?php echo $fetch->id; ?>"
                            data-doc-id="<?php echo $fetch->id;?>"
                            data-checklist-name="<?php echo htmlspecialchars($fetch->checklist, ENT_QUOTES, 'UTF-8'); ?>"
                            data-file-name="<?php echo htmlspecialchars($fetch->file_name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-file-type="<?php echo htmlspecialchars($fetch->filetype ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-myfile="<?php echo htmlspecialchars($fetch->myfile ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-myfile-key="<?php echo isset($fetch->myfile_key) ? htmlspecialchars($fetch->myfile_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
                            data-doc-type="<?php echo htmlspecialchars($fetch->doc_type, ENT_QUOTES, 'UTF-8'); ?>"
                            data-user-role="<?php echo Auth::user()->role; ?>"
                            title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
                            style="cursor: context-menu;">
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                            </a>
                                        <?php } else {  //For old file upload
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            $myawsfile = $url.$client_unique_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
                                            ?>
                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-alldocumentlist')">
                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                            </a>
                                        <?php } ?>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="allupload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $clientid;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="documents">
                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                            <input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                <?php
                                //echo $checklist_verified_by. "<br>";
                                //echo $checklist_verified_at;
                                ?>
                            </td>-->
                        </tr>
			        <?php
			        } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <!--<a class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>-->

                                            <!--<a class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                          
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>
                                            <a href="<?php echo url('/download-document') . '?filelink=' . urlencode($fetch->myfile) . '&filename=' . urlencode($fetch->myfile_key); ?>" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>" target="_blank" rel="noopener">Download</a>


                                            <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                                <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                            <?php }?>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data']	= $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        echo json_encode($response);
        } catch (\Exception $e) {
            \Log::error('Error in addalldocchecklist: ' . $e->getMessage() . ' at line ' . $e->getLine());
            $response = ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
            echo json_encode($response);
        }
    }

    //Update all Document upload
	public function uploadalldocument(Request $request){ //dd($request->all());
        if ($request->hasfile('document_upload'))
        {
            $clientid = $request->clientid;
            $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }  //dd($client_unique_id);

            $doctype = isset($request->doctype)? $request->doctype : '';

            $files = $request->file('document_upload');
            $size = $files->getSize();
            $fileName = $files->getClientOriginalName();
            $explodeFileName = explode('.', $fileName);
            $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            $fileExtension = $files->getClientOriginalExtension();
            //echo $nameWithoutExtension."===".$fileExtension;
            $name = time() . $files->getClientOriginalName();
            $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
            Storage::disk('s3')->put($filePath, file_get_contents($files));
            $exploadename = explode('.', $name);

            $req_file_id = $request->fileid;
            $obj = \App\Models\Document::find($req_file_id);
            $obj->file_name = $nameWithoutExtension; //$explodeFileName[0];
            $obj->filetype = $fileExtension;//$exploadename[1];
            $obj->user_id = Auth::user()->id;
            //$obj->myfile = $name;
            // Get the full URL of the uploaded file
            $fileUrl = Storage::disk('s3')->url($filePath);
            $obj->myfile = $fileUrl;
            $obj->myfile_key = $name;

            $obj->type = $request->type;
            $obj->file_size = $size;
            $obj->doc_type = $doctype;
            
            // Assign to default "General" category if doc_type is 'documents' and no category_id is set
            if ($doctype == 'documents' && !$obj->category_id) {
                $generalCategory = \App\Models\DocumentCategory::where('name', 'General')
                    ->where('is_default', true)
                    ->first();
                if ($generalCategory) {
                    $obj->category_id = $generalCategory->id;
                }
            }
            
            $saved = $obj->save();

			if($saved){
				if($request->type == 'client'){
                    $subject = 'uploaded document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $clientid;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
                    $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
                    $objs->save();
                }
				$response['status'] 	= 	true;
				$response['message']	=	'You have successfully uploaded your document';
			$fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderByRaw('updated_at DESC NULLS LAST')->get();
			ob_start();
			foreach($fetchd as  $docKey=>$fetch){
				$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					$addedByInfo = $admin->first_name . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
					?>
					<tr class="drow document-row" id="id_<?php echo $fetch->id; ?>" 
						data-doc-id="<?php echo $fetch->id;?>"
						data-checklist-name="<?php echo htmlspecialchars($fetch->checklist, ENT_QUOTES, 'UTF-8'); ?>"
						data-file-name="<?php echo htmlspecialchars($fetch->file_name, ENT_QUOTES, 'UTF-8'); ?>"
						data-file-type="<?php echo htmlspecialchars($fetch->filetype, ENT_QUOTES, 'UTF-8'); ?>"
						data-myfile="<?php echo htmlspecialchars($fetch->myfile, ENT_QUOTES, 'UTF-8'); ?>"
						data-myfile-key="<?php echo isset($fetch->myfile_key) ? htmlspecialchars($fetch->myfile_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
						data-doc-type="<?php echo htmlspecialchars($fetch->doc_type, ENT_QUOTES, 'UTF-8'); ?>"
						data-user-role="<?php echo Auth::user()->role; ?>"
						title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
						style="cursor: context-menu;">
						<td style="white-space: initial;">
							<div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
								<span><?php echo $fetch->checklist; ?></span>
							</div>
						</td>
						<td style="white-space: initial;">
							<?php
							if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
								<div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
									<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
										<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
											<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
										</a>
									<?php } else {  //For old file upload
										$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
										$myawsfile = $url.$client_unique_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
										?>
										<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-alldocumentlist')">
											<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
										</a>
									<?php } ?>
								</div>
							<?php
							}
							else
							{?>
								<div class="allupload_document" style="display:inline-block;">
									<form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
										<input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
										<input type="hidden" name="clientid" value="<?php echo $fetch->client_id;?>">
										<input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
										<input type="hidden" name="type" value="client">
										<input type="hidden" name="doctype" value="documents">
										<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
										<input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
									</form>
								</div>
							<?php
							}?>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<?php
                                        //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<!--<a class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>-->

                                        <!--<a class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                      
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>

                                        <a href="<?php echo url('/download-document') . '?filelink=' . urlencode($fetch->myfile) . '&filename=' . urlencode($fetch->myfile_key); ?>" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>" target="_blank" rel="noopener">Download</a>



                                        <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                        <?php } ?>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                    </div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	= $data;
				$response['griddata'] = $griddata;
			}else{
				$response['status'] = false;
				$response['message'] = 'Please try again';
			}
		} else {
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
    

    //verify document
    public function verifydoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array(
                'checklist_verified_by' => Auth::user()->id,
                'checklist_verified_at' => date('Y-m-d H:i:s')
            ));
            if($upd){
                $docInfo = \App\Models\Document::select('client_id')->where('id',$doc_id)->first();
                $subject = 'verified '.$doc_type.' document';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
                //Get verified at and verified by
                $admin_info = DB::table('admins')->select('first_name')->where('id', '=',Auth::user()->id)->first();
                if($admin_info){
                    $response['verified_by'] = 	$admin_info->first_name;
                    $response['verified_at'] = 	date('d/m/Y');
                } else {
                    $response['verified_by'] = "";
                    $response['verified_at'] = "";
                }
                $response['doc_type'] = $doc_type;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' Document verified successfully';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['verified_by'] = "";
                $response['verified_at'] = "";
                $response['doc_type'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['verified_by'] = "";
            $response['verified_at'] = "";
            $response['doc_type'] = "";
		}
		echo json_encode($response);
	} 

    /**
     * Upload document for client
     */
    public function uploaddocument(Request $request){
		$id = $request->clientid;
        $doctype = isset($request->doctype)? $request->doctype : '';

		if ($request->hasfile('document_upload')) {

			if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}
			foreach ($files as $file) {

				$size = $file->getSize();
				$fileName = $file->getClientOriginalName();
				$nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
				$fileExtension = $file->getClientOriginalExtension();
				$explodeFileName = explode('.', $fileName);
				$document_upload = $this->uploadrenameFile($file, \Config::get('constants.documents'));
				$exploadename = explode('.', $document_upload);
				$obj = new Document;
				$obj->file_name = $nameWithoutExtension;
				$obj->filetype = $fileExtension;
				$obj->user_id = Auth::user()->id;
				$obj->myfile = $document_upload;
				$obj->client_id = $id;
				$obj->type = $request->type;
				$obj->file_size = $size;
				$obj->doc_type = $doctype;
				
				// Assign to default "General" category if doc_type is 'documents' and no category_id is set
				if ($doctype == 'documents' && !$request->has('category_id')) {
					$generalCategory = \App\Models\DocumentCategory::where('name', 'General')
						->where('is_default', true)
						->first();
					if ($generalCategory) {
						$obj->category_id = $generalCategory->id;
					}
				} elseif ($request->has('category_id')) {
					// Use provided category_id if available
					$obj->category_id = $request->category_id;
				}
				
				$saved = $obj->save();

			}

			if($saved){
				if($request->type == 'client'){
				$subject = 'added 1 document';
				$objs = new ActivitiesLog;
				$objs->client_id = $id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0;
				$objs->pin = 0;
				$objs->save();

				}
				$response['status'] 	= 	true;
				$response['message']	=	'You\'ve successfully uploaded your document';
				$fetchd = Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = Admin::where('id', $fetch->user_id)->first();
                  
                    if( isset($doctype) && $doctype == 'migration'){
                        $preview_container_type = 'preview-container-migrationdocumentlist';
                    } else if( isset($doctype) && $doctype == 'education'){
                        $preview_container_type = 'preview-container-documentlist';
                    }
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td style="white-space: initial;">
                            <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
								<a style="white-space: initial;" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('img/documents/'.$fetch->myfile); ?>','<?php echo $preview_container_type;?>')">
                                    <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                </a>
							</div>
                        </td>
						<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>

						<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
									<a target="_blank" class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
									<?php
														$explodeimg = explode('.',$fetch->myfile);
										if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
														?>
															<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
															<?php } ?>
									<a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>

									<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>
										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	=$data;
				$response['griddata']	=$griddata;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		 }else{
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		 }
		 echo json_encode($response);
	}

    /**
     * Rename document
     */
    public function renamedoc(Request $request){
		$id = $request->id;
		$filename = $request->filename;
		if(Document::where('id',$id)->exists()){
			$doc = Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['file_name' => $filename]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Document saved successfully';
				$response['Id']	=	$id;
				$response['filename']	=	$filename;
				$response['filetype']	=	$doc->filetype;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    /**
     * Delete document
     */
    public function deletedocs(Request $request){
		$note_id = $request->note_id;

		if(Document::where('id',$note_id)->exists()){

			$data = DB::table('documents')->where('id', @$note_id)->first();
			$res = DB::table('documents')->where('id', @$note_id)->delete();

			if($res){

				$subject = 'deleted a document';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0;
				$objs->pin = 0;
				$objs->save();
				$response['status'] 	= 	true;
				$response['data']	=	'Document removed successfully';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    public function downloadpdf(Request $request, $id = NULL){
	    	$fetchd = Document::where('id',$id)->first();
	    	$data = ['title' => 'Welcome to codeplaners.com','image' => $fetchd->myfile];
        $pdf = PDF::loadView('myPDF', $data);

        return $pdf->stream('codeplaners.pdf');
	}

    // TODO: Move remaining document methods here:
    // - uploadalldocument
    // - addalldocchecklist
    // - deletealldocs
    // - renamealldoc
    // - renamechecklistdoc
    // - notuseddoc
    // - backtodoc
}
