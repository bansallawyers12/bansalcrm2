<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Signer;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Document signature from client detail Documents tab
 * Save placement, send, reminder, remove
 */
class DocumentSignatureController extends Controller
{
    protected SignatureService $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->middleware('auth:admin');
        $this->signatureService = $signatureService;
    }

    /**
     * Save signature placement and create signer
     */
    public function savePlacement(Request $request)
    {
        $request->validate([
            'doc_id' => 'required|integer|exists:documents,id',
            'client_id' => 'required|integer',
            'signer_email' => 'required|email',
            'signer_name' => 'required|string|min:1|max:100',
            'fields' => 'required|array|min:1',
            'fields.*.page' => 'required|integer|min:1',
            'fields.*.x_percent' => 'required|numeric|min:0|max:100',
            'fields.*.y_percent' => 'required|numeric|min:0|max:100',
            'fields.*.width_percent' => 'required|numeric|min:1|max:100',
            'fields.*.height_percent' => 'required|numeric|min:1|max:100',
        ]);

        $document = Document::where('id', $request->doc_id)
            ->where('client_id', $request->client_id)
            ->whereNull('not_used_doc')
            ->where('type', 'client')
            ->firstOrFail();

        if (empty($document->file_name)) {
            return response()->json(['status' => false, 'message' => 'Document has no file to sign'], 422);
        }

        // Delete existing signature fields and cancel any pending signers
        $document->signatureFields()->delete();
        $document->signers()->where('status', 'pending')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        $signer = $document->signers()->create([
            'email' => $request->signer_email,
            'name' => $request->signer_name,
            'token' => Str::random(64),
            'status' => 'pending',
            'reminder_count' => 0,
        ]);

        foreach ($request->fields as $fieldData) {
            $document->signatureFields()->create([
                'signer_id' => $signer->id,
                'page_number' => (int) $fieldData['page'],
                'x_percent' => (float) $fieldData['x_percent'],
                'y_percent' => (float) $fieldData['y_percent'],
                'width_percent' => (float) ($fieldData['width_percent'] ?? 18),
                'height_percent' => (float) ($fieldData['height_percent'] ?? 8),
            ]);
        }

        $document->update([
            'created_by' => Auth::guard('admin')->id(),
            'status' => 'signature_placed',
            'primary_signer_email' => $request->signer_email,
        ]);

        return response()->json(['status' => true, 'message' => 'Signature placement saved']);
    }

    /**
     * Send document for signature (to existing pending signers)
     */
    public function send(Request $request)
    {
        $request->validate(['doc_id' => 'required|integer|exists:documents,id']);

        $document = Document::with('signers')->findOrFail($request->doc_id);

        $pendingSigners = $document->signers()->where('status', 'pending')->get();
        if ($pendingSigners->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No pending signers. Add placement first.'], 422);
        }

        if (in_array($document->status, ['signed', 'voided', 'archived'])) {
            return response()->json(['status' => false, 'message' => 'Document cannot be sent.'], 422);
        }

        $emailService = app(\App\Services\EmailService::class);
        $emailConfig = $emailService->configureMailerForEmail(null);
        if (!$emailConfig) {
            return response()->json(['status' => false, 'message' => 'No email configuration. Add active email in Admin Console.'], 500);
        }

        $sent = 0;
        foreach ($pendingSigners as $signer) {
            try {
                $signingUrl = url("/sign/{$document->id}/{$signer->token}");
                $subject = 'Document Signature Request from Bansal Education';
                $emailMessage = "Please review and sign the document: " . ($document->checklist ?? $document->file_name ?? 'Document');
                $emailData = [
                    'signerName' => $signer->name,
                    'emailMessage' => $emailMessage,
                    'documentTitle' => $document->checklist ?? $document->file_name ?? 'Document',
                    'signingUrl' => $signingUrl,
                ];
                \Illuminate\Support\Facades\Mail::mailer('ses')->send('emails.signature-request', $emailData, function ($mail) use ($signer, $subject, $emailConfig) {
                    $mail->to($signer->email, $signer->name)
                         ->subject($subject)
                         ->from($emailConfig->email, $emailConfig->display_name ?? $emailConfig->email);
                });
                $sent++;
            } catch (\Exception $e) {
                Log::error('Signature email failed', ['doc_id' => $document->id, 'error' => $e->getMessage()]);
            }
        }

        if ($sent > 0) {
            $document->update(['status' => 'sent', 'signer_count' => $pendingSigners->count()]);
            return response()->json(['status' => true, 'message' => 'Sent for signature']);
        }

        return response()->json(['status' => false, 'message' => 'Failed to send emails.'], 500);
    }

    /**
     * Send reminder to signer
     */
    public function reminder(Request $request)
    {
        $request->validate(['doc_id' => 'required|integer|exists:documents,id']);

        $document = Document::findOrFail($request->doc_id);
        $signer = $document->signers()->where('status', 'pending')->first();

        if (!$signer) {
            return response()->json(['status' => false, 'message' => 'No pending signer'], 422);
        }

        $success = $this->signatureService->remind($signer);
        return response()->json([
            'status' => (bool) $success,
            'message' => $success ? 'Reminder sent' : 'Failed to send reminder',
        ]);
    }

    /**
     * Remove/cancel signature request
     */
    public function remove(Request $request)
    {
        $request->validate(['doc_id' => 'required|integer|exists:documents,id']);

        $document = Document::findOrFail($request->doc_id);

        foreach ($document->signers()->where('status', 'pending')->get() as $signer) {
            $signer->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        $document->signatureFields()->delete();
        $document->update([
            'status' => 'draft',
            'primary_signer_email' => null,
            'signer_count' => 0,
        ]);

        return response()->json(['status' => true, 'message' => 'Signature request removed']);
    }
}
