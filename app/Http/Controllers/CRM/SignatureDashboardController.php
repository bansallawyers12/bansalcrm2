<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Admin;
use App\Models\DocumentNote;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SignatureDashboardController extends Controller
{
    protected $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Get all documents (global access - everyone can see everything)
        $query = Document::with(['creator', 'signers', 'documentable'])
            ->forSignatureWorkflow()
            ->visible($user)
            ->notArchived()
            ->orderBy('created_at', 'desc');

        // Apply filters based on scope
        $query->when($request->has('scope'), function ($q) use ($request, $user) {
            return match($request->scope) {
                'my_documents' => $q->forUser($user->id),
                'team' => $q,
                'organization' => $q,
                default => $q
            };
        })
        // Apply status filters
        ->when($request->has('tab'), function ($q) use ($request) {
            return match($request->tab) {
                'pending' => $q->byStatus('sent'),
                'signed' => $q->byStatus('signed'),
                'sent_by_me' => $q->where('created_by', auth('admin')->id()),
                default => $q
            };
        });

        // Additional filters
        $query->when($request->filled('status'), fn($q) => $q->byStatus($request->status))
              ->when($request->filled('association'), function ($q) use ($request) {
                  return $request->association === 'associated' 
                      ? $q->associated() 
                      : $q->adhoc();
              })
              ->when($request->filled('search'), function ($q) use ($request) {
                  $search = $request->search;
                  return $q->where(function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%")
                           ->orWhere('file_name', 'like', "%{$search}%")
                           ->orWhere('primary_signer_email', 'like', "%{$search}%");
                  });
              });

        $documents = $query->paginate(20);

        // Get counts for dashboard cards
        $counts = [
            'sent_by_me' => Document::forSignatureWorkflow()->forUser($user->id)->notArchived()->count(),
            'visible_to_me' => Document::forSignatureWorkflow()->visible($user)->notArchived()->count(),
            'pending' => Document::forSignatureWorkflow()->visible($user)->byStatus('sent')->notArchived()->count(),
            'signed' => Document::forSignatureWorkflow()->visible($user)->byStatus('signed')->notArchived()->count(),
            'overdue' => Document::forSignatureWorkflow()->visible($user)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->where('status', '!=', 'signed')
                ->notArchived()
                ->count(),
            'all' => Document::forSignatureWorkflow()->notArchived()->count(),
            'all_pending' => Document::forSignatureWorkflow()->byStatus('sent')->notArchived()->count(),
        ];

        // Provide errors variable for the layout
        $errors = $request->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        // Load clients for attach modal
        $clients = Admin::where('role', 7)
            ->whereNull('is_deleted')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return view('crm.signatures.dashboard', compact('documents', 'counts', 'user', 'errors', 'clients'));
    }

    public function create(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Get clients for association dropdown
        $clients = Admin::where('role', '=', 7)->whereNull('is_deleted')->get(['id', 'first_name', 'last_name', 'email']);

        // Check if we're sending an existing document for signing
        $document = null;
        if ($request->has('document_id')) {
            $document = Document::with('signatureFields')->findOrFail($request->document_id);
        }

        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('crm.signatures.create', compact('clients', 'user', 'errors', 'document'));
    }

    public function store(Request $request)
    {
        // Check if we're using an existing document
        if ($request->has('document_id')) {
            $request->validate([
                'document_id' => 'required|integer|exists:documents,id',
                'signer_email' => 'required|email',
                'signer_name' => 'required|string|min:2|max:100',
                'email_template' => 'nullable|string',
                'email_subject' => 'nullable|string|max:255',
                'email_message' => 'nullable|string|max:1000',
                'from_email' => 'nullable|email',
                'selected_client_id' => 'nullable|integer|exists:admins,id',
            ]);
            
            $document = Document::findOrFail($request->document_id);
            
            // Check for duplicate signer
            $existingSigner = $document->signers()->where('email', $request->signer_email)->first();
            if ($existingSigner && $existingSigner->status === 'pending') {
                return redirect()->back()->withErrors(['signer_email' => 'A signing link has already been sent to this email address.']);
            }

            // Create new signer
            $signer = $document->signers()->create([
                'email' => $request->signer_email,
                'name' => $request->signer_name,
                'token' => Str::random(64),
                'status' => 'pending',
                'reminder_count' => 0,
                'email_template' => $request->email_template ?? 'emails.signature.send',
                'email_subject' => $request->email_subject,
                'email_message' => $request->email_message,
                'from_email' => $request->from_email,
            ]);
            
            // Associate document with client if specified
            if ($request->has('selected_client_id') && $request->selected_client_id) {
                $client = Admin::find($request->selected_client_id);
                if ($client) {
                    $document->update([
                        'documentable_type' => Admin::class,
                        'documentable_id' => $client->id,
                        'origin' => 'client',
                    ]);
                    
                    DocumentNote::create([
                        'document_id' => $document->id,
                        'created_by' => auth('admin')->id(),
                        'action_type' => 'associated',
                        'note' => "Document associated with client: {$client->first_name} {$client->last_name}",
                        'metadata' => [
                            'entity_id' => $client->id,
                            'entity_type' => 'client',
                        ]
                    ]);
                }
            }
            
            $successMessage = "Signer added successfully: {$signer->email}. Click 'Send for Signature' when ready to send the signing link.";
            
            return redirect()->route('signatures.show', $document->id)
                ->with('success', $successMessage);
        } else {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'title' => 'nullable|string|max:255',
                'signer_email' => 'required|email',
                'signer_name' => 'required|string|min:2|max:100',
                'document_type' => 'nullable|string|in:agreement,nda,general,contract',
                'priority' => 'nullable|string|in:low,normal,high',
                'due_at' => 'nullable|date|after:now',
                'association_id' => 'nullable|integer',
            ]);
            
            // Handle file upload for new documents
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            // Create document
            $document = Document::create([
                'file_name' => $fileName,
                'filetype' => $file->getClientMimeType(),
                'myfile' => $filePath,
                'title' => $request->title ?: pathinfo($fileName, PATHINFO_FILENAME),
                'status' => 'draft',
                'created_by' => Auth::guard('admin')->id(),
                'signer_count' => 0,
            ]);
        }

        $user = Auth::guard('admin')->user();

        // Set association if provided
        if ($request->association_id) {
            $this->signatureService->associate($document, $request->association_id);
        }

        // Store signer information in session for later use
        session([
            'pending_document_signer' => [
                'email' => $request->signer_email,
                'name' => $request->signer_name,
                'email_subject' => $request->email_subject,
                'email_message' => $request->email_message,
                'email_template' => $request->email_template,
                'from_email' => $request->from_email,
            ]
        ]);

        // Redirect to signature placement page
        return redirect()->route('documents.edit', $document->id)
            ->with('success', 'Document uploaded! Now place signature fields on the document.');
    }

    public function show($id)
    {
        $document = Document::with(['creator', 'signers', 'documentable', 'signatureFields', 'notes.creator'])
            ->findOrFail($id);

        // Load clients for attach functionality
        $clients = Admin::where('role', 7)
            ->whereNull('is_deleted')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('crm.signatures.show', compact('document', 'errors', 'clients'));
    }

    public function sendReminder(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        $signerId = $request->signer_id;
        $signer = $document->signers()->findOrFail($signerId);

        // Use service to send reminder
        $success = $this->signatureService->remind($signer);

        if ($success) {
            return back()->with('success', 'Reminder sent successfully!');
        } else {
            return back()->with('error', 'Failed to send reminder. Please check limits and try again.');
        }
    }

    public function cancelSignature(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Validate request
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id'
        ]);
        
        $signerId = $request->signer_id;
        $signer = $document->signers()->findOrFail($signerId);
        
        // Verify signer belongs to this document
        if ($signer->document_id !== $document->id) {
            return back()->with('error', 'Invalid signer for this document.');
        }
        
        // Check if already signed
        if ($signer->status === 'signed') {
            return back()->with('error', 'Cannot cancel signature. Document has already been signed.');
        }
        
        // Check if already cancelled
        if ($signer->status === 'cancelled') {
            return back()->with('info', 'Signature has already been cancelled.');
        }
        
        // Only allow cancellation of pending signatures
        if ($signer->status !== 'pending') {
            return back()->with('error', 'Can only cancel pending signatures.');
        }
        
        try {
            // Update signer status to cancelled
            $signer->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);
            
            // Create activity log entry
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id(),
                'note' => "Signature cancelled for {$signer->name} ({$signer->email})",
                'action_type' => 'signature_cancelled',
                'metadata' => [
                    'signer_id' => $signer->id,
                    'signer_name' => $signer->name,
                    'signer_email' => $signer->email,
                    'cancelled_at' => now()->toIso8601String()
                ]
            ]);
            
            return back()->with('success', 'Signature cancelled successfully. The signer will no longer be able to sign this document.');
        } catch (\Exception $e) {
            \Log::error('Error cancelling signature', [
                'document_id' => $document->id,
                'signer_id' => $signerId,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'An error occurred while cancelling the signature. Please try again.');
        }
    }

    public function sendForSignature(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Check if document has signers
        $pendingSigners = $document->signers()->where('status', 'pending')->get();
        
        if ($pendingSigners->isEmpty()) {
            return back()->with('error', 'No pending signers found for this document.');
        }
        
        // Allow sending for any status except 'signed', 'void', or 'archived'
        $blockedStatuses = ['signed', 'voided', 'archived'];
        if (in_array($document->status, $blockedStatuses)) {
            return back()->with('error', "Document cannot be sent for signature because it is {$document->status}.");
        }
        
        $emailsSent = 0;
        $errors = [];
        
        foreach ($pendingSigners as $signer) {
            try {
                // Generate signing URL
                $signingUrl = url("/sign/{$document->id}/{$signer->token}");
                
                $subject = $signer->email_subject ?? 'Document Signature Request';
                $message = $signer->email_message ?? "Please review and sign the attached document.";
                
                // Send email
                Mail::raw("Hello {$signer->name},\n\n{$message}\n\nPlease click the following link to sign the document:\n{$signingUrl}\n\nDocument: {$document->display_title}\n\nThank you.", function ($mail) use ($signer, $subject) {
                    $mail->to($signer->email, $signer->name)
                         ->subject($subject);
                });
                
                // Create activity note for successful email
                DocumentNote::create([
                    'document_id' => $document->id,
                    'created_by' => Auth::guard('admin')->id() ?? 1,
                    'action_type' => 'email_sent',
                    'note' => "Email sent successfully to {$signer->name} ({$signer->email})",
                    'metadata' => [
                        'signer_email' => $signer->email,
                        'signer_name' => $signer->name,
                        'subject' => $subject,
                    ]
                ]);
                
                $emailsSent++;
                
            } catch (\Exception $e) {
                $errors[] = "Failed to send email to {$signer->email}: " . $e->getMessage();
            }
        }
        
        // Update document status
        $document->update(['status' => 'sent']);
        
        if ($emailsSent > 0) {
            $message = "Document sent for signature! {$emailsSent} signing link(s) sent successfully.";
            if (!empty($errors)) {
                $message .= " However, some emails failed: " . implode(', ', $errors);
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'Failed to send any emails. Errors: ' . implode(', ', $errors));
        }
    }

    public function copyLink($id)
    {
        $document = Document::findOrFail($id);
        
        $signer = $document->signers()->first();
        
        if (!$signer) {
            return back()->with('error', 'No signer found for this document.');
        }

        $signingUrl = url("/sign/{$document->id}/{$signer->token}");
        
        return back()->with('success', "Signing link copied to clipboard: {$signingUrl}");
    }

    public function suggestAssociation(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $matches = [];
        
        // Find all clients with this email
        $clients = Admin::where('email', $request->email)
            ->where('role', '=', 7)
            ->whereNull('is_deleted')
            ->get();
            
        foreach ($clients as $client) {
            $matches[] = [
                'type' => 'client',
                'id' => $client->id,
                'name' => trim("{$client->first_name} {$client->last_name}"),
                'email' => $client->email,
            ];
        }

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'match' => count($matches) === 1 ? $matches[0] : null
        ]);
    }

    /**
     * Associate a document with a client
     */
    public function associate(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'entity_id' => 'required|integer',
            'note' => 'nullable|string|max:500'
        ]);

        $success = $this->signatureService->associate(
            $document,
            $request->entity_id,
            $request->note
        );

        if ($success) {
            $client = Admin::find($request->entity_id);
            $message = 'Document successfully attached to client!';
            if ($client) {
                $message = "Document successfully attached to {$client->first_name} {$client->last_name}!";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'Failed to attach document. Please try again.');
        }
    }

    /**
     * Detach a document from its current association
     */
    public function detach(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $user = Auth::guard('admin')->user();
        if ($user->role !== 1) {
            return back()->with('error', 'Only administrators can detach documents.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $success = $this->signatureService->detach(
            $document,
            $request->reason
        );

        if ($success) {
            return back()->with('success', 'Document successfully detached!');
        } else {
            return back()->with('error', 'Failed to detach document. Please try again.');
        }
    }

    /**
     * Bulk archive documents
     */
    public function bulkArchive(Request $request)
    {
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate(['ids' => 'required|array|min:1']);
        
        try {
            $count = Document::whereIn('id', $ids)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
            
            return back()->with('success', "Successfully archived {$count} document(s)");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive documents: ' . $e->getMessage());
        }
    }

    /**
     * Bulk void documents
     */
    public function bulkVoid(Request $request)
    {
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate([
            'ids' => 'required|array|min:1',
            'reason' => 'nullable|string|max:500'
        ]);
        
        try {
            $documents = Document::whereIn('id', $ids)->get();
            $count = 0;
            
            foreach ($documents as $doc) {
                if ($this->signatureService->void($doc, $request->reason)) {
                    $count++;
                }
            }
            
            return back()->with('success', "Successfully voided {$count} document(s)");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to void documents: ' . $e->getMessage());
        }
    }

    /**
     * Bulk resend reminders
     */
    public function bulkResend(Request $request)
    {
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate(['ids' => 'required|array|min:1']);
        
        try {
            $documents = Document::with('signers')->whereIn('id', $ids)->get();
            $sent = 0;
            $skipped = 0;
            
            foreach ($documents as $doc) {
                foreach ($doc->signers as $signer) {
                    if ($signer->status === 'pending') {
                        if ($this->signatureService->remind($signer)) {
                            $sent++;
                        } else {
                            $skipped++;
                        }
                    }
                }
            }
            
            $message = "Sent {$sent} reminder(s)";
            if ($skipped > 0) {
                $message .= " ({$skipped} skipped due to limits)";
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminders: ' . $e->getMessage());
        }
    }
}
