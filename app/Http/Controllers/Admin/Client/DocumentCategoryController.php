<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\DocumentCategory;
use App\Models\Document;

/**
 * Client Document Category Management
 * 
 * Handles category operations in the client detail page Documents tab
 */
class DocumentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Get categories for a specific client
     * Returns default categories and all client-specific categories (regardless of creator)
     */
    public function getCategories(Request $request)
    {
        try {
            $clientId = $request->input('client_id');

            if (!$clientId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Client ID is required'
                ], 400);
            }

            // Get all categories (default + all categories for this client, regardless of who created them)
            $categories = DocumentCategory::active()
                ->where(function($query) use ($clientId) {
                    $query->where('is_default', true) // Default categories
                          ->orWhere('client_id', $clientId); // All client-specific categories
                })
                ->orderBy('is_default', 'DESC')
                ->orderBy('name', 'ASC')
                ->get();

            // Add document count and permission flags for each category
            $categoriesWithCount = $categories->map(function($category) use ($clientId) {
                $docCount = $category->getDocumentCount($clientId);
                $isCustom = !$category->is_default;
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'is_default' => $category->is_default,
                    'document_count' => $docCount,
                    'can_delete' => $category->canBeDeleted(),
                    'can_rename' => $isCustom,
                    'can_delete_category' => $isCustom && $docCount === 0,
                ];
            });

            return response()->json([
                'status' => true,
                'categories' => $categoriesWithCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching document categories: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching categories'
            ], 500);
        }
    }

    /**
     * Create a new category for a client
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'client_id' => 'required|integer',
            ]);

            $clientId = $request->input('client_id');
            $userId = Auth::user()->id;
            $name = $request->input('name');

            // Check if category name already exists for this client (across all users)
            $exists = DocumentCategory::where('name', $name)
                ->where(function($query) use ($clientId) {
                    $query->where('is_default', true) // Check default categories
                          ->orWhere('client_id', $clientId); // Check all categories for this client
                })
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'A category with this name already exists'
                ], 422);
            }

            // Create new category
            $category = DocumentCategory::create([
                'name' => $name,
                'is_default' => false,
                'user_id' => $userId,
                'client_id' => $clientId,
                'status' => 1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category created successfully',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'is_default' => $category->is_default,
                    'document_count' => 0,
                    'can_delete' => true,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating document category: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error creating category'
            ], 500);
        }
    }

    /**
     * Update category name
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = DocumentCategory::findOrFail($id);

            // Only custom (client-specific) categories can be edited
            if ($category->is_default) {
                return response()->json([
                    'status' => false,
                    'message' => 'Default categories cannot be edited'
                ], 403);
            }

            $category->name = $request->input('name');
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating document category: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error updating category'
            ], 500);
        }
    }

    /**
     * Delete a category
     * Note: Cannot delete default categories or categories with documents
     */
    public function destroy($id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);

            // Only custom (client-specific) categories can be deleted
            if ($category->is_default) {
                return response()->json([
                    'status' => false,
                    'message' => 'Default categories cannot be deleted'
                ], 403);
            }

            // Check if category has documents (uses getDocumentCount for Education/Migration legacy docs)
            if ($category->getDocumentCount() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete category with documents. Please move or delete documents first.'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting document category: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error deleting category'
            ], 500);
        }
    }

    /**
     * Get documents for a specific category and client
     */
    public function getDocuments(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            $clientId = $request->input('client_id');

            if (!$categoryId || !$clientId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category ID and Client ID are required'
                ], 400);
            }

            $category = DocumentCategory::find($categoryId);
            $categoryName = $category ? $category->name : null;

            $query = Document::where('client_id', $clientId)
                ->whereNull('not_used_doc')
                ->where('type', 'client');

            // Education/Migration categories: include both migrated (doc_type=documents, category_id) and legacy (doc_type=education/migration)
            if ($categoryName === 'Education') {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->orWhere('doc_type', 'education');
                });
            } elseif ($categoryName === 'Migration') {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->orWhere('doc_type', 'migration');
                });
            } else {
                // Other categories: standard logic (doc_type=documents and category_id)
                $query->where('category_id', $categoryId)
                    ->where(function ($q) {
                        $q->where('doc_type', 'documents')
                            ->orWhere(function ($subQ) {
                                $subQ->whereNull('doc_type')->orWhere('doc_type', '');
                            });
                    });
            }

            $documents = $query->orderBy('updated_at', 'DESC')
                ->with(['user', 'category', 'signers'])
                ->get();

            // Sort: place docs with source_document_id right after their source
            $signedBySource = $documents->whereNotNull('source_document_id')->keyBy('source_document_id');
            $roots = $documents->whereNull('source_document_id');
            $ordered = [];
            foreach ($roots->sortByDesc('updated_at') as $doc) {
                $ordered[] = $doc;
                if ($signedBySource->has($doc->id)) {
                    $ordered[] = $signedBySource->get($doc->id);
                }
            }
            $documents = collect($ordered);

            // Add preview_url for Education/Migration docs with public path (no S3 myfile_key)
            // For signed copies (source_document_id): use signed_doc_link from source or myfile
            $documentsArray = $documents->map(function ($doc) {
                $previewUrl = null;
                if (empty($doc->myfile_key) && $doc->myfile) {
                    if ($doc->category && in_array($doc->category->name, ['Education', 'Migration'], true)) {
                        $previewUrl = asset('img/documents/' . $doc->myfile);
                    } elseif (in_array($doc->doc_type ?? '', ['education', 'migration'])) {
                        $previewUrl = asset('img/documents/' . $doc->myfile);
                    }
                }
                $arr = array_merge($doc->toArray(), ['preview_url' => $previewUrl]);
                $arr['signature_status'] = $doc->status;
                $arr['has_pending_signers'] = $doc->signers ? $doc->signers->whereIn('status', ['pending'])->count() > 0 : false;
                $arr['is_sent'] = in_array($doc->status, ['sent', 'viewed']);
                return $arr;
            });

            return response()->json([
                'status' => true,
                'documents' => $documentsArray
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching category documents: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching documents'
            ], 500);
        }
    }

    /**
     * Move a document to a different category
     */
    public function moveDocument(Request $request)
    {
        try {
            $request->validate([
                'doc_id' => 'required|integer',
                'category_id' => 'required|integer',
                'client_id' => 'required|integer',
            ]);

            $docId = $request->input('doc_id');
            $categoryId = $request->input('category_id');
            $clientId = $request->input('client_id');

            $document = Document::where('id', $docId)
                ->where('client_id', $clientId)
                ->whereNull('not_used_doc')
                ->where('type', 'client')
                ->firstOrFail();

            $targetCategory = DocumentCategory::findOrFail($categoryId);

            // Verify target category is valid for this client
            $isValidCategory = $targetCategory->status
                && ($targetCategory->is_default || (int) $targetCategory->client_id === (int) $clientId);

            if (!$isValidCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid target category'
                ], 422);
            }

            // Update document category; set doc_type for Education/Migration legacy docs
            $document->category_id = $categoryId;
            if (in_array($document->doc_type, ['education', 'migration'])) {
                $document->doc_type = 'documents';
            }
            $document->save();

            return response()->json([
                'status' => true,
                'message' => 'Document moved to ' . $targetCategory->name . ' successfully',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Document or category not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error moving document: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error moving document'
            ], 500);
        }
    }
}
