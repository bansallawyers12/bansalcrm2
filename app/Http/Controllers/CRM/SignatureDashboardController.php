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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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

        // Load clients for attach modal
        $clients = Admin::where('role', 7)
            ->whereNull('is_deleted')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return view('crm.signatures.dashboard', compact('documents', 'counts', 'user', 'clients'));
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

        return view('crm.signatures.create', compact('clients', 'user', 'document'));
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
            
            // Create activity note for signer added
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id(),
                'action_type' => 'signer_added',
                'note' => "Signer added: {$signer->name} ({$signer->email})",
                'metadata' => [
                    'signer_id' => $signer->id,
                    'signer_name' => $signer->name,
                    'signer_email' => $signer->email,
                ]
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
            // Simple upload flow - just file and optional title
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'title' => 'nullable|string|max:255',
            ]);
            
            // Handle file upload to S3
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
            $fileExtension = $file->getClientOriginalExtension();
            $fileKey = time() . '_' . $originalName;
            $filePath = 'signatures/' . $fileKey;
            
            // Upload to S3
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fileUrl = Storage::disk('s3')->url($filePath);

            // Create document
            $document = Document::create([
                'file_name' => $nameWithoutExtension,
                'filetype' => $fileExtension,
                'myfile' => $fileUrl,
                'myfile_key' => $fileKey,
                'file_size' => $file->getSize(),
                'title' => $request->title ?: $nameWithoutExtension,
                'status' => 'draft',
                'created_by' => Auth::guard('admin')->id(),
                'signer_count' => 0,
            ]);
            
            // Create activity note for document creation
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => Auth::guard('admin')->id(),
                'action_type' => 'document_created',
                'note' => 'Document created',
                'metadata' => [
                    'file_name' => $nameWithoutExtension,
                    'file_type' => $fileExtension,
                ]
            ]);

            // Redirect to the signature placement page
            return redirect()->route('signatures.edit', $document->id)
                ->with('success', 'Document uploaded successfully! Now place signature fields on the document.');
        }
    }

    /**
     * Show signature field placement editor
     */
    public function edit($id)
    {
        $document = Document::with(['signatureFields'])->findOrFail($id);
        
        return view('crm.signatures.edit', compact('document'));
    }

    /**
     * Save signature field locations
     */
    public function saveSignatureFields(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        $request->validate([
            'fields' => 'nullable|array',
            'fields.*.page' => 'required|integer|min:1',
            'fields.*.x_percent' => 'required|numeric|min:0|max:100',
            'fields.*.y_percent' => 'required|numeric|min:0|max:100',
            'fields.*.width_percent' => 'required|numeric|min:1|max:100',
            'fields.*.height_percent' => 'required|numeric|min:1|max:100',
        ]);
        
        // Delete existing signature fields
        $document->signatureFields()->delete();
        
        // Create new signature fields
        if ($request->has('fields') && is_array($request->fields)) {
            foreach ($request->fields as $fieldData) {
                $document->signatureFields()->create([
                    'page_number' => $fieldData['page'],
                    'x_percent' => $fieldData['x_percent'],
                    'y_percent' => $fieldData['y_percent'],
                    'width_percent' => $fieldData['width_percent'],
                    'height_percent' => $fieldData['height_percent'],
                ]);
            }
        }
        
        // Update document status if fields were added
        $fieldCount = $document->signatureFields()->count();
        if ($fieldCount > 0) {
            $document->update(['status' => 'signature_placed']);
            
            // Create activity note for signature fields placed
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id(),
                'action_type' => 'signature_placed',
                'note' => "Signature fields placed ({$fieldCount} field(s))",
                'metadata' => [
                    'field_count' => $fieldCount,
                ]
            ]);
        }
        
        // Redirect to add signer page
        return redirect()->route('signatures.create', ['document_id' => $document->id])
            ->with('success', 'Signature locations saved! Now add a signer.');
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

        return view('crm.signatures.show', compact('document', 'clients'));
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
        
        // Get mail configuration from config (which reads from .env)
        $mailHost = config('mail.mailers.smtp.host');
        $mailPort = config('mail.mailers.smtp.port');
        $mailUsername = config('mail.mailers.smtp.username');
        $mailPassword = config('mail.mailers.smtp.password');
        $mailEncryption = config('mail.mailers.smtp.encryption', 'tls');
        $mailFromAddress = config('mail.from.address');
        $mailFromName = config('mail.from.name');
        
        // Configure mail settings dynamically
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $mailHost,
            'port' => $mailPort,
            'encryption' => $mailEncryption,
            'username' => $mailUsername,
            'password' => $mailPassword,
        ]);
        Config::set('mail.from.address', $mailFromAddress);
        Config::set('mail.from.name', $mailFromName);
        
        // Clear cached mail manager to apply new config
        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
        
        foreach ($pendingSigners as $signer) {
            try {
                // Generate signing URL
                $signingUrl = url("/sign/{$document->id}/{$signer->token}");
                
                $subject = $signer->email_subject ?? 'Document Signature Request from Bansal Education';
                $emailMessage = $signer->email_message ?? "Please review and sign the attached document.";
                
                // Email data for template
                $emailData = [
                    'signerName' => $signer->name,
                    'emailMessage' => $emailMessage,
                    'documentTitle' => $document->display_title,
                    'signingUrl' => $signingUrl,
                ];
                
                // Send email using the signature-request template
                Mail::send('emails.signature-request', $emailData, function ($mail) use ($signer, $subject, $mailFromAddress, $mailFromName) {
                    $mail->to($signer->email, $signer->name)
                         ->subject($subject)
                         ->from($mailFromAddress, $mailFromName);
                });
                
                // Create activity note for successful email
                DocumentNote::create([
                    'document_id' => $document->id,
                    'created_by' => Auth::guard('admin')->id() ?? 1,
                    'action_type' => 'email_sent',
                    'note' => "Email sent to {$signer->name} ({$signer->email})",
                    'metadata' => [
                        'signer_email' => $signer->email,
                        'signer_name' => $signer->name,
                        'subject' => $subject,
                    ]
                ]);
                
                $emailsSent++;
                
            } catch (\Exception $e) {
                Log::error('Signature email failed', [
                    'document_id' => $document->id,
                    'signer_email' => $signer->email,
                    'error' => $e->getMessage()
                ]);
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
