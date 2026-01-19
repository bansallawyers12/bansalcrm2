<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signer;
use App\Models\DocumentNote;
use App\Models\ActivitiesLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Public Document Controller
 * 
 * Handles public-facing document signing operations without authentication.
 * Access is controlled through unique tokens sent via email.
 */
class PublicDocumentController extends Controller
{
    /**
     * No authentication required - using token-based validation
     */
    public function __construct()
    {
        // Public controller - no authentication middleware
    }

    /**
     * Show the public signing form for a document using a tokenized link.
     */
    public function sign($id, $token)
    {
        $documentId = (int) $id;
        if ($documentId <= 0) {
            Log::warning('Invalid document ID in public sign method', ['id' => $id]);
            abort(404, 'Invalid document link.');
        }

        // Validate token format
        if (!$token || !is_string($token) || strlen($token) < 32 || !preg_match('/^[a-zA-Z0-9]+$/', $token)) {
            Log::warning('Invalid token format in public sign method', ['token_length' => strlen($token ?? '')]);
            abort(403, 'Invalid or expired signing link.');
        }

        try {
            $document = Document::findOrFail($documentId);
            $signer = $document->signers()->where('token', $token)->first();

            if (!$signer || $signer->status === 'signed' || $signer->status === 'cancelled') {
                Log::warning('Invalid signer, already signed, or cancelled', [
                    'document_id' => $documentId,
                    'signer_exists' => !is_null($signer),
                    'signer_status' => $signer ? $signer->status : 'none'
                ]);
                
                if ($signer && $signer->status === 'signed') {
                    return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                        ->with('info', 'This document has already been signed.');
                }
                
                if ($signer && $signer->status === 'cancelled') {
                    return $this->showErrorPage('Signature Cancelled', 'This signing link has been cancelled. Please contact the document sender for assistance.', $document);
                }
                
                return $this->showErrorPage('Invalid Signing Link', 'Invalid or expired signing link. Please contact the document sender for assistance.', $document ?? null);
            }

            // Track when document was opened
            if (!$signer->opened_at) {
                $signer->update(['opened_at' => now()]);
            }

            $signatureFields = $document->signatureFields()->get();
            
            // Get PDF path - handle local files
            $url = $document->myfile;
            $pdfPath = null;
            
            if ($url && file_exists(storage_path('app/public/' . $url))) {
                $pdfPath = storage_path('app/public/' . $url);
            }

            // Count PDF pages (simplified - you may need a PDF library for this)
            $pdfPages = 1;
            if ($pdfPath && file_exists($pdfPath)) {
                $pdfPages = $this->countPdfPages($pdfPath) ?: 1;
            }

            return view('documents.sign', compact('document', 'signer', 'signatureFields', 'pdfPages'));
        } catch (\Exception $e) {
            Log::error('Error in public sign method', [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
                'token_present' => !empty($token)
            ]);
            return redirect('/')->with('error', 'An error occurred while loading the signing page.');
        }
    }

    /**
     * Submit signatures for a public document
     */
    public function submitSignatures(Request $request, $id)
    {
        Log::info('=== START submitSignatures ===', [
            'document_id' => $id,
            'signer_id' => $request->signer_id,
            'has_signatures' => $request->has('signatures'),
        ]);
        
        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id',
            'token' => 'required|string|min:32',
            'signatures' => 'required|array',
            'signatures.*' => 'nullable|string',
            'signature_positions' => 'required|array',
            'signature_positions.*' => 'nullable|string'
        ]);

        $documentId = (int) $id;
        if ($documentId <= 0) {
            Log::error('Invalid document ID in submitSignatures', ['id' => $id]);
            return redirect('/')->with('error', 'Invalid document ID.');
        }

        try {
            $document = Document::findOrFail($documentId);
            $signer = Signer::findOrFail($request->signer_id);

            // Verify signer belongs to this document
            if ($signer->document_id !== $document->id) {
                return redirect('/')->with('error', 'Invalid signing attempt.');
            }

            // Verify token matches
            if ($signer->token !== $request->token) {
                return redirect('/')->with('error', 'Invalid or expired signing link.');
            }

            // Check if signer has already signed
            if ($signer->status === 'signed') {
                return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                    ->with('info', 'This document has already been signed.');
            }
            
            // Check if signature has been cancelled
            if ($signer->status === 'cancelled') {
                return redirect('/')->with('error', 'This signing link has been cancelled.');
            }
            
            if ($signer->token !== null && $signer->status === 'pending') {
                // Get PDF file
                $url = $document->myfile;
                $pdfPath = null;
                
                if ($url && file_exists(storage_path('app/public/' . $url))) {
                    $pdfPath = storage_path('app/public/' . $url);
                }
                
                if (!$pdfPath || !file_exists($pdfPath)) {
                    Log::error('PDF file not found for document submission', [
                        'document_id' => $document->id,
                        'url' => $url,
                    ]);
                    return redirect()->back()->with('error', 'Document file not found. Please contact support.');
                }
                
                $outputPath = storage_path('app/public/signed/' . $document->id . '_signed.pdf');
                
                // Ensure signed directory exists
                if (!file_exists(storage_path('app/public/signed'))) {
                    mkdir(storage_path('app/public/signed'), 0755, true);
                }

                // Process signatures
                $signaturePositions = [];
                $signatureLinks = [];
                $signaturesSaved = false;

                foreach ($request->signatures as $page => $signaturesJson) {
                    $pageNum = (int) $page;
                    if ($pageNum < 1 || $pageNum > 999 || !$signaturesJson) {
                        continue;
                    }

                    $signatures = json_decode($signaturesJson, true);
                    $positions = json_decode($request->signature_positions[$page] ?? '{}', true);

                    if (!is_array($signatures) || !is_array($positions)) {
                        continue;
                    }

                    foreach ($signatures as $fieldId => $signatureData) {
                        $sanitizedFieldId = (int) $fieldId;
                        if ($sanitizedFieldId <= 0) continue;

                        // Validate and decode signature
                        $sanitizedSignature = $this->sanitizeSignatureData($signatureData, $sanitizedFieldId);
                        if ($sanitizedSignature === false) continue;

                        $imageData = $sanitizedSignature['imageData'];

                        // Store signature locally
                        $filename = sprintf('%d_field_%d_%s.png', $signer->id, $sanitizedFieldId, bin2hex(random_bytes(8)));
                        $localSignaturePath = 'signatures/' . $filename;
                        
                        // Ensure signatures directory exists
                        if (!file_exists(storage_path('app/public/signatures'))) {
                            mkdir(storage_path('app/public/signatures'), 0755, true);
                        }
                        
                        Storage::disk('public')->put($localSignaturePath, $imageData);
                        $signaturePath = storage_path('app/public/' . $localSignaturePath);
                        $signatureUrl = asset('storage/' . $localSignaturePath);

                        // Store position
                        $position = $positions[$fieldId] ?? [];
                        $sanitizedPosition = $this->sanitizePositionData($position);

                        $signaturePositions[$sanitizedFieldId] = [
                            'path' => $signaturePath,
                            'page' => $pageNum,
                            'x_percent' => $sanitizedPosition['x_percent'],
                            'y_percent' => $sanitizedPosition['y_percent'],
                            'w_percent' => $sanitizedPosition['w_percent'],
                            'h_percent' => $sanitizedPosition['h_percent']
                        ];
                        $signatureLinks[$sanitizedFieldId] = $signatureUrl;
                        $signaturesSaved = true;
                    }
                }

                if (!$signaturesSaved) {
                    return redirect()->back()
                        ->with('error', 'No valid signatures were detected. Please ensure all signature fields are properly signed.')
                        ->withInput();
                }

                // For now, copy original PDF as signed (you would integrate with a PDF library to overlay signatures)
                copy($pdfPath, $outputPath);

                // Generate SHA-256 hash for tamper detection
                $signedHash = hash_file('sha256', $outputPath);

                $signedPdfUrl = asset('storage/signed/' . $document->id . '_signed.pdf');

                // Update statuses and save hash
                $signer->update(['status' => 'signed', 'signed_at' => now()]);
                $document->status = 'signed';
                $document->signature_doc_link = json_encode($signatureLinks);
                $document->signed_doc_link = $signedPdfUrl;
                $document->signed_hash = $signedHash;
                $document->hash_generated_at = now();
                $document->save();

                Log::info("Public document signed successfully", [
                    'document_id' => $document->id,
                    'signer_id' => $signer->id,
                    'signed_at' => now()->toISOString()
                ]);

                // Create notifications and activity logs
                $this->createSignatureNotifications($document, $signer);

                return redirect()->route('public.documents.thankyou', ['id' => $document->id])
                    ->with('success', 'Document signed successfully! You can now download your signed document.');
            }

            return redirect()->back()
                ->with('error', 'An unexpected error occurred while processing your signature.');
        } catch (\Exception $e) {
            Log::error("Error in public submitSignatures", [
                'error' => $e->getMessage(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'An unexpected error occurred while processing your signature.');
        }
    }

    /**
     * Get a specific page of the PDF as an image
     */
    public function getPage($id, $page)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Check if cached image already exists
            $cachedImagePath = storage_path('app/public/pdf_pages/doc_' . $id . '_page_' . $page . '.png');
            if (file_exists($cachedImagePath) && filesize($cachedImagePath) > 0) {
                return response()->file($cachedImagePath, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
            
            $url = $document->myfile;
            $pdfPath = null;

            if ($url && file_exists(storage_path('app/public/' . $url))) {
                $pdfPath = storage_path('app/public/' . $url);
            }

            if (!$pdfPath || !file_exists($pdfPath)) {
                abort(404, 'Document file not found');
            }

            // Ensure cache directory exists
            $cacheDir = storage_path('app/public/pdf_pages');
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }

            // Use Imagick to convert PDF page to image (requires Imagick extension)
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($pdfPath . '[' . ($page - 1) . ']');
                    $imagick->setImageFormat('png');
                    $imagick->writeImage($cachedImagePath);
                    $imagick->clear();
                    $imagick->destroy();
                    
                    return response()->file($cachedImagePath, [
                        'Content-Type' => 'image/png',
                        'Cache-Control' => 'public, max-age=86400',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Imagick error', ['error' => $e->getMessage()]);
                }
            }

            // Fallback: return a placeholder image or error
            abort(503, 'PDF processing not available. Please install Imagick extension.');
        } catch (\Exception $e) {
            Log::error('Error in getPage', [
                'document_id' => $id,
                'page' => $page,
                'error' => $e->getMessage()
            ]);
            abort(500, 'An error occurred while retrieving the page');
        }
    }

    /**
     * Download signed document
     */
    public function downloadSigned($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            if ($document->signed_doc_link) {
                $filePath = storage_path('app/public/signed/' . $document->id . '_signed.pdf');
                
                if (file_exists($filePath)) {
                    return response()->download($filePath, $document->id . '_signed.pdf');
                }
            }
            
            return redirect('/')->with('error', 'Signed document not found.');
        } catch (\Exception $e) {
            Log::error('Error downloading signed document', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect('/')->with('error', 'An error occurred while downloading the document.');
        }
    }

    /**
     * Show thank you page after signing
     */
    public function thankyou(Request $request, $id = null)
    {
        $downloadUrl = null;
        $document = null;
        
        if ($id) {
            $document = Document::find($id);
            if ($document && $document->signed_doc_link) {
                $downloadUrl = route('public.documents.download.signed', $document->id);
            }
        }
        
        $message = 'You have successfully signed your document.';
        return view('documents.thankyou', compact('downloadUrl', 'message', 'id', 'document'));
    }

    /**
     * Send reminder to signer
     */
    public function sendReminder(Request $request, $id)
    {
        $documentId = (int) $id;
        if ($documentId <= 0) {
            return redirect()->back()->with('error', 'Invalid document ID.');
        }

        $request->validate([
            'signer_id' => 'required|integer|exists:signers,id'
        ]);

        $signerId = (int) $request->signer_id;

        try {
            $document = Document::findOrFail($documentId);
            $signer = $document->signers()->findOrFail($signerId);

            if ($signer->status === 'signed') {
                return redirect()->back()->with('error', 'Document is already signed.');
            }

            if ($signer->reminder_count >= 3) {
                return redirect()->back()->with('error', 'Maximum reminders already sent.');
            }

            // Send reminder email
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            Mail::raw("This is a reminder to sign your document: " . $signingUrl, function ($message) use ($signer) {
                $message->to($signer->email, $signer->name)
                        ->subject('Reminder: Please Sign Your Document');
            });

            $signer->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $signer->reminder_count + 1
            ]);

            return redirect()->back()->with('success', 'Reminder sent successfully!');
        } catch (\Exception $e) {
            Log::error('Error sending reminder', [
                'document_id' => $documentId,
                'signer_id' => $signerId,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'An error occurred while sending the reminder.');
        }
    }

    /**
     * Show document index
     */
    public function index($id = null)
    {
        return redirect('/')->with('info', 'Please use the link provided in your email.');
    }

    // ==================== Private Helper Methods ====================

    /**
     * Count PDF pages
     */
    protected function countPdfPages($pathToPdf)
    {
        try {
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->pingImage($pathToPdf);
                $count = $imagick->getNumberImages();
                $imagick->clear();
                $imagick->destroy();
                return $count;
            }
            return 1;
        } catch (\Exception $e) {
            Log::error('Error counting PDF pages', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    /**
     * Sanitize signature data
     */
    private function sanitizeSignatureData($signatureData, $fieldId)
    {
        if (!is_string($signatureData) || empty($signatureData)) {
            return false;
        }

        $signatureData = strip_tags($signatureData);

        $dangerousPatterns = [
            '/javascript:/i', '/vbscript:/i', '/data:text\/html/i',
            '/data:application\/javascript/i', '/onclick/i', '/onload/i',
            '/onerror/i', '/<script/i', '/<iframe/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $signatureData)) {
                return false;
            }
        }

        if (!preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $signatureData, $matches)) {
            return false;
        }

        $base64Data = $matches[1];

        if (strlen($base64Data) > 500000) {
            return false;
        }

        $imageData = base64_decode($base64Data, true);
        if ($imageData === false || strlen($imageData) < 100 || strlen($imageData) > 1000000) {
            return false;
        }

        // Validate PNG signature
        $pngSignature = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";
        if (substr($imageData, 0, 8) !== $pngSignature) {
            return false;
        }

        return [
            'imageData' => $imageData,
            'base64Data' => $base64Data,
            'size' => strlen($imageData)
        ];
    }

    /**
     * Sanitize position data
     */
    private function sanitizePositionData($position)
    {
        $sanitized = [];
        $fields = ['x_percent', 'y_percent', 'w_percent', 'h_percent'];

        foreach ($fields as $field) {
            $value = $position[$field] ?? 0;
            $value = (float) $value;
            $value = max(0, min(1, $value));
            if ($field === 'w_percent') $value = max(0.1, $value);
            if ($field === 'h_percent') $value = max(0.05, $value);
            $sanitized[$field] = $value;
        }

        return $sanitized;
    }

    /**
     * Create notifications and activity logs when document is signed
     */
    private function createSignatureNotifications($document, $signer)
    {
        try {
            $signerName = $signer->name ?? 'Unknown Signer';
            $signedAt = now()->format('d/m/Y h:i A');
            
            // Check if document is associated with a client
            if ($document->documentable_id && $document->documentable_type === 'App\\Models\\Admin') {
                $client = \App\Models\Admin::find($document->documentable_id);
                
                if ($client) {
                    $documentTitle = $document->title ?? $document->file_name ?? 'Document #' . $document->id;
                    $subject = "{$signerName} signed document '{$documentTitle}' for client {$client->first_name} {$client->last_name}";
                    $description = "Document signed by {$signerName} at {$signedAt}";
                    
                    // Create Activity Log
                    ActivitiesLog::create([
                        'client_id' => $client->id,
                        'created_by' => $document->created_by ?? 1,
                        'subject' => $subject,
                        'description' => $description,
                        'activity_type' => 'document',
                        'task_status' => 0,
                        'pin' => 0,
                    ]);
                    
                    // Create Notification
                    if ($document->created_by) {
                        Notification::create([
                            'sender_id' => $client->id,
                            'receiver_id' => $document->created_by,
                            'module_id' => $document->id,
                            'url' => url("/clients/detail/{$client->id}"),
                            'notification_type' => 'document',
                            'message' => $subject,
                            'receiver_status' => 0,
                            'seen' => 0,
                        ]);
                    }
                }
            }
            
            Log::info('Document signature notifications processed', [
                'document_id' => $document->id,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating signature notifications', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show error page for public document access
     */
    private function showErrorPage($title, $message, $document = null)
    {
        return response()->view('documents.error', [
            'title' => $title,
            'message' => $message,
            'document' => $document
        ], 200);
    }
}
