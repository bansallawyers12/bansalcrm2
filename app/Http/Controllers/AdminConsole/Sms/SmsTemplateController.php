<?php

namespace App\Http\Controllers\AdminConsole\Sms;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * SmsTemplateController
 *
 * Handles SMS template CRUD operations for AdminConsole
 */
class SmsTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * List all SMS templates
     */
    public function index(Request $request)
    {
        $templates = SmsTemplate::orderBy('title')->paginate(20);

        return view('AdminConsole.features.sms.templates.index', compact('templates'));
    }

    /**
     * Show create template form
     */
    public function create()
    {
        return view('AdminConsole.features.sms.templates.create');
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:sms_templates,title',
            'message' => 'required|string|max:1600',
            'description' => 'nullable|string',
            'variables' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        $template = SmsTemplate::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'data' => $template
            ]);
        }
        return redirect()->route('adminconsole.features.sms.templates.index')
            ->with('success', 'Template created successfully');
    }

    /**
     * Show edit template form
     */
    public function edit($id)
    {
        $template = SmsTemplate::findOrFail($id);

        return view('AdminConsole.features.sms.templates.edit', compact('template'));
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:sms_templates,title,' . $id,
            'message' => 'required|string|max:1600',
            'description' => 'nullable|string',
            'variables' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);
        $template->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => $template
            ]);
        }
        return redirect()->route('adminconsole.features.sms.templates.index')
            ->with('success', 'Template updated successfully');
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        $template = SmsTemplate::findOrFail($id);

        if ($template->usage_count > 0) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete template that has been used. Consider deactivating it instead.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Cannot delete template that has been used.');
        }

        $template->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        }
        return redirect()->route('adminconsole.features.sms.templates.index')
            ->with('success', 'Template deleted successfully');
    }

    /**
     * Get template by ID (API endpoint)
     */
    public function show($id)
    {
        $template = SmsTemplate::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    /**
     * Get active templates (API endpoint for dropdowns)
     */
    public function active()
    {
        $templates = SmsTemplate::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'message', 'variables', 'category']);

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }
}
