<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\DocumentCategory;
use App\Models\Admin;
use App\Models\Document;

/**
 * Admin Console Document Category Management
 * 
 * Handles category CRUD operations in admin console
 */
class DocumentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of all document categories
     */
    public function index(Request $request)
    {
        try {
            $query = DocumentCategory::with(['user', 'client']);

            // Search by category name
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', '%' . $search . '%');
                });
            }

            // Search by client ID or client name
            if ($request->has('client_search') && $request->client_search) {
                $clientSearch = $request->client_search;
                $query->whereHas('client', function($q) use ($clientSearch) {
                    $q->where('client_id', 'ilike', '%' . $clientSearch . '%')
                      ->orWhere('first_name', 'ilike', '%' . $clientSearch . '%')
                      ->orWhere('last_name', 'ilike', '%' . $clientSearch . '%')
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) ilike ?", ['%' . $clientSearch . '%']);
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Filter by type (default/custom)
            if ($request->has('type')) {
                if ($request->type === 'default') {
                    $query->where('is_default', true);
                } elseif ($request->type === 'custom') {
                    $query->where('is_default', false);
                }
            }

            $categories = $query->sortable(['created_at' => 'desc'])
                ->paginate(15);

            return view('AdminConsole.documentcategory.index', compact('categories'));

        } catch (\Exception $e) {
            Log::error('Error listing document categories: ' . $e->getMessage());
            return back()->with('error', 'Error loading categories');
        }
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('AdminConsole.documentcategory.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'is_default' => 'required|boolean',
                'status' => 'required|boolean',
            ]);

            // Check if default category with same name exists
            if ($request->is_default) {
                $exists = DocumentCategory::where('name', $request->name)
                    ->where('is_default', true)
                    ->exists();

                if ($exists) {
                    return back()->with('error', 'A default category with this name already exists')->withInput();
                }
            }

            DocumentCategory::create([
                'name' => $request->name,
                'is_default' => $request->is_default,
                'user_id' => null, // Admin-created categories have no user_id
                'client_id' => null, // Admin-created categories have no client_id
                'status' => $request->status,
            ]);

            return redirect()->route('adminconsole.documentcategory.index')
                ->with('success', 'Category created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Validation error')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating document category: ' . $e->getMessage());
            return back()->with('error', 'Error creating category')->withInput();
        }
    }

    /**
     * Show the form for editing a category
     */
    public function edit($id)
    {
        try {
            $category = DocumentCategory::with(['user', 'client'])->findOrFail($id);
            return view('AdminConsole.documentcategory.edit', compact('category'));
        } catch (\Exception $e) {
            Log::error('Error loading category for edit: ' . $e->getMessage());
            return back()->with('error', 'Category not found');
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);

            // Check if updating name conflicts with existing default category
            if ($category->is_default) {
                $exists = DocumentCategory::where('name', $request->name)
                    ->where('is_default', true)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return back()->with('error', 'A default category with this name already exists')->withInput();
                }
            }

            $category->name = $request->name;
            $category->status = $request->status;
            $category->save();

            return redirect()->route('adminconsole.documentcategory.index')
                ->with('success', 'Category updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating document category: ' . $e->getMessage());
            return back()->with('error', 'Error updating category')->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);

            // Check if category has documents
            $documentCount = $category->documents()->count();
            if ($documentCount > 0) {
                return back()->with('error', "Cannot delete category. It has {$documentCount} document(s). Please move or delete documents first.");
            }

            // Prevent deletion of default "General" category
            if ($category->is_default && $category->name === 'General') {
                return back()->with('error', 'The default General category cannot be deleted');
            }

            $category->delete();

            return redirect()->route('adminconsole.documentcategory.index')
                ->with('success', 'Category deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting document category: ' . $e->getMessage());
            return back()->with('error', 'Error deleting category');
        }
    }

    /**
     * Get category details with document count
     */
    public function show($id)
    {
        try {
            $category = DocumentCategory::with(['user', 'client'])->findOrFail($id);
            
            $documentCount = $category->documents()->count();
            $documentsByClient = $category->documents()
                ->select('client_id', DB::raw('count(*) as count'))
                ->groupBy('client_id')
                ->with('client')
                ->get();

            return view('AdminConsole.documentcategory.show', compact('category', 'documentCount', 'documentsByClient'));

        } catch (\Exception $e) {
            Log::error('Error loading category details: ' . $e->getMessage());
            return back()->with('error', 'Category not found');
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);
            $category->status = !$category->status;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully',
                'new_status' => $category->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling category status: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    }
}
