<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\EmailLabel;
use App\Models\Admin;

/**
 * Admin Console Email Label Management
 * 
 * Handles email label CRUD operations in admin console
 */
class EmailLabelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of all email labels
     */
    public function index(Request $request)
    {
        try {
            $query = EmailLabel::with('user');

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('is_active', $request->status);
            }

            // Filter by type (system/custom)
            if ($request->has('type')) {
                if ($request->type === 'system') {
                    $query->where('type', 'system');
                } elseif ($request->type === 'custom') {
                    $query->where('type', 'custom');
                }
            }

            // Sorting
            $sortBy = $request->get('sort', 'created_at');
            $sortOrder = $request->get('order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $labels = $query->paginate(15);

            return view('AdminConsole.email_labels.index', compact('labels'));

        } catch (\Exception $e) {
            Log::error('Error listing email labels: ' . $e->getMessage());
            return back()->with('error', 'Error loading email labels');
        }
    }

    /**
     * Show the form for creating a new email label
     */
    public function create()
    {
        return view('AdminConsole.email_labels.create');
    }

    /**
     * Store a newly created email label
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:system,custom',
                'color' => ['required', 'regex:/^#[a-fA-F0-9]{6}$/'],
                'icon' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:500',
                'is_active' => 'nullable|boolean',
            ], [
                'color.regex' => 'Please enter a valid hex color code (e.g., #FF5733)',
            ]);

            // Check if label with same name exists for this type
            $exists = EmailLabel::where('name', $request->name)
                ->where('type', $request->type)
                ->exists();

            if ($exists) {
                return back()->with('error', 'An email label with this name already exists for the selected type')->withInput();
            }

            // Set user_id based on type
            $userId = $request->type === 'system' ? null : Auth::id();

            EmailLabel::create([
                'name' => $request->name,
                'type' => $request->type,
                'color' => $request->color,
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 1, // Default to active
                'user_id' => $userId,
            ]);

            return redirect()->route('adminconsole.emaillabels.index')
                ->with('success', 'Email label created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Validation error')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating email label: ' . $e->getMessage());
            return back()->with('error', 'Error creating email label')->withInput();
        }
    }

    /**
     * Show the form for editing an email label
     */
    public function edit($id)
    {
        try {
            $label = EmailLabel::with('user')->findOrFail($id);
            return view('AdminConsole.email_labels.edit', compact('label'));
        } catch (\Exception $e) {
            Log::error('Error loading email label for edit: ' . $e->getMessage());
            return back()->with('error', 'Email label not found');
        }
    }

    /**
     * Update the specified email label
     */
    public function update(Request $request, $id)
    {
        try {
            $label = EmailLabel::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'color' => ['required', 'regex:/^#[a-fA-F0-9]{6}$/'],
                'icon' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:500',
                'is_active' => 'nullable|boolean',
            ], [
                'color.regex' => 'Please enter a valid hex color code (e.g., #FF5733)',
            ]);

            // Check if updating name conflicts with existing label
            $exists = EmailLabel::where('name', $request->name)
                ->where('type', $label->type)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'An email label with this name already exists')->withInput();
            }

            $label->name = $request->name;
            $label->color = $request->color;
            $label->icon = $request->icon;
            $label->description = $request->description;
            $label->is_active = $request->has('is_active') ? 1 : 0;
            $label->save();

            return redirect()->route('adminconsole.emaillabels.index')
                ->with('success', 'Email label updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating email label: ' . $e->getMessage());
            return back()->with('error', 'Error updating email label')->withInput();
        }
    }

    /**
     * Remove the specified email label
     */
    public function destroy($id)
    {
        try {
            $label = EmailLabel::findOrFail($id);

            // Prevent deletion of system labels
            if ($label->type === 'system') {
                return back()->with('error', 'System labels cannot be deleted');
            }

            // Check if label is being used by any emails
            $emailCount = $label->mailReports()->count();
            if ($emailCount > 0) {
                return back()->with('error', "Cannot delete label. It is being used by {$emailCount} email(s). Please remove the label from emails first.");
            }

            $label->delete();

            return redirect()->route('adminconsole.emaillabels.index')
                ->with('success', 'Email label deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting email label: ' . $e->getMessage());
            return back()->with('error', 'Error deleting email label');
        }
    }

    /**
     * Toggle email label status
     */
    public function toggleStatus($id)
    {
        try {
            $label = EmailLabel::findOrFail($id);
            $label->is_active = !$label->is_active;
            $label->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully',
                'new_status' => $label->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling email label status: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    }
}
