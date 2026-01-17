<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MailReport;
use App\Models\MailReportAttachment;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Partner;

/**
 * Email Query V2 Controller
 * 
 * Handles email filtering and querying for both clients and partners
 */
class EmailQueryV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Filter Inbox emails (supports both clients and partners)
     */
    public function filterEmails(Request $request)
    {
        try {
            $entityId = $request->input('client_id'); // Can be client_id or partner_id
            $entityType = $request->input('type', 'client'); // client, lead, or partner
            $client_matter_id = $request->input('client_matter_id'); // Optional for partners
            $status = $request->input('status');
            $search = $request->input('search');
            $label_id = $request->input('label_id');

            // Build base query
            $query = MailReport::where('client_id', $entityId)
                ->where('type', $entityType)
                ->where('mail_type', 1);

            // Set conversion_type based on entity type
            if ($entityType === 'partner') {
                $query->where('conversion_type', 'partner_email_fetch')
                      ->where('mail_body_type', 'inbox');
            } else {
                $query->where('conversion_type', 'conversion_email_fetch')
                      ->where('mail_body_type', 'inbox');
            }

            // Filter by matter_id if provided (mainly for clients)
            if (!empty($client_matter_id)) {
                $query->where('client_matter_id', $client_matter_id);
            }

            $query->with(['labels', 'attachments'])
                  ->orderBy('created_at', 'DESC');

            // Status filter
            if ($status !== null && $status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            // Search filter
            if ($search !== null && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Label filter
            if (!empty($label_id)) {
                $query->whereHas('labels', function ($q) use ($label_id) {
                    $q->where('email_labels.id', $label_id);
                });
            }

            $emails = $query->get();
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Get unique ID for S3 path
            $uniqueId = '';
            if ($entityType === 'partner') {
                $partnerInfo = Partner::select('id')->where('id', $entityId)->first();
                $uniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : '';
            } else {
                $adminInfo = Admin::select('client_id')->where('id', $entityId)->first();
                $uniqueId = !empty($adminInfo) ? $adminInfo->client_id : '';
            }

            $emails = $emails->map(function ($email) use ($url, $uniqueId, $entityType) {
                $DocInfo = Document::select('id','doc_type','myfile','myfile_key','mail_type')
                    ->where('id', $email->uploaded_doc_id)
                    ->first();

                $previewUrl = '';
                if ($DocInfo) {
                    if (!empty($DocInfo->myfile_key)) {
                        $previewUrl = $DocInfo->myfile;
                    } else {
                        $docType = ($entityType === 'partner') ? 'partner_email_fetch' : 'conversion_email_fetch';
                        $previewUrl = $url . $uniqueId . '/' . $docType . '/' . ($DocInfo->mail_type ?? 'inbox') . '/' . $DocInfo->myfile;
                    }
                }

                // Ensure attachments and labels relationships are loaded
                if (!$email->relationLoaded('attachments')) {
                    $email->load('attachments');
                }
                if (!$email->relationLoaded('labels')) {
                    $email->load('labels');
                }

                // Convert to array
                $emailArray = $email->toArray();
                
                // Fetch attachments
                $attachments = $email->attachments;
                if (!$attachments || (method_exists($attachments, 'count') && $attachments->count() === 0)) {
                    $attachments = MailReportAttachment::where('mail_report_id', $email->id)->get();
                }
                
                // Format attachments
                if ($attachments && method_exists($attachments, 'count') && $attachments->count() > 0) {
                    $emailArray['attachments'] = $attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'mail_report_id' => $attachment->mail_report_id,
                            'filename' => $attachment->filename,
                            'display_name' => $attachment->display_name ?? $attachment->filename,
                            'content_type' => $attachment->content_type,
                            'file_path' => $attachment->file_path,
                            's3_key' => $attachment->s3_key,
                            'file_size' => (int) $attachment->file_size,
                            'content_id' => $attachment->content_id,
                            'is_inline' => (bool) $attachment->is_inline,
                            'description' => $attachment->description,
                            'extension' => $attachment->extension,
                        ];
                    })->values()->toArray();
                } else {
                    $emailArray['attachments'] = [];
                }
                
                $emailArray['preview_url'] = $previewUrl;
                
                return $emailArray;
            });

            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Error in filterEmails V2: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Filter Sent emails (supports both clients and partners)
     */
    public function filterSentEmails(Request $request)
    {
        try {
            $entityId = $request->input('client_id'); // Can be client_id or partner_id
            $entityType = $request->input('type', 'client'); // client, lead, or partner
            $client_matter_id = $request->input('client_matter_id'); // Optional for partners
            $status = $request->input('status');
            $search = $request->input('search');
            $label_id = $request->input('label_id');

            // Build base query
            $query = MailReport::where('client_id', $entityId)
                ->where('type', $entityType)
                ->where('mail_type', 1);

            // Set conversion_type based on entity type
            if ($entityType === 'partner') {
                $query->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'partner_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                });
            } else {
                $query->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'conversion_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                });
            }

            // Filter by matter_id if provided
            if (!empty($client_matter_id)) {
                $query->where('client_matter_id', $client_matter_id);
            }

            $query->with(['labels', 'attachments'])
                  ->orderBy('created_at', 'DESC');

            // Status filter
            if ($status !== null && $status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            // Search filter
            if ($search !== null && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Label filter
            if (!empty($label_id)) {
                $query->whereHas('labels', function ($q) use ($label_id) {
                    $q->where('email_labels.id', $label_id);
                });
            }

            $emails = $query->get();
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Get unique ID for S3 path
            $uniqueId = '';
            if ($entityType === 'partner') {
                $partnerInfo = Partner::select('id')->where('id', $entityId)->first();
                $uniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : '';
            } else {
                $adminInfo = Admin::select('client_id')->where('id', $entityId)->first();
                $uniqueId = !empty($adminInfo) ? $adminInfo->client_id : '';
            }

            $emails = $emails->map(function ($email) use ($url, $uniqueId, $entityType, $entityId) {
                $previewUrl = '';

                if (!empty($email->uploaded_doc_id)) {
                    $docInfo = Document::select('id', 'doc_type', 'myfile', 'myfile_key', 'mail_type')
                        ->where('id', $email->uploaded_doc_id)
                        ->first();
                    if ($docInfo) {
                        if ($docInfo->myfile_key) {
                            $previewUrl = $docInfo->myfile;
                        } else {
                            $docType = ($entityType === 'partner') ? 'partner_email_fetch' : 'conversion_email_fetch';
                            $previewUrl = $url . $uniqueId . '/' . $docType . '/' . ($docInfo->mail_type ?? 'sent') . '/' . $docInfo->myfile;
                        }
                    }
                }

                // Ensure attachments and labels relationships are loaded
                if (!$email->relationLoaded('attachments')) {
                    $email->load('attachments');
                }
                if (!$email->relationLoaded('labels')) {
                    $email->load('labels');
                }

                // Convert to array
                $emailArray = $email->toArray();
                
                // Fetch attachments
                $attachments = $email->attachments;
                if (!$attachments || (method_exists($attachments, 'count') && $attachments->count() === 0)) {
                    $attachments = MailReportAttachment::where('mail_report_id', $email->id)->get();
                }
                
                // Format attachments
                if ($attachments && method_exists($attachments, 'count') && $attachments->count() > 0) {
                    $emailArray['attachments'] = $attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'mail_report_id' => $attachment->mail_report_id,
                            'filename' => $attachment->filename,
                            'display_name' => $attachment->display_name ?? $attachment->filename,
                            'content_type' => $attachment->content_type,
                            'file_path' => $attachment->file_path,
                            's3_key' => $attachment->s3_key,
                            'file_size' => (int) $attachment->file_size,
                            'content_id' => $attachment->content_id,
                            'is_inline' => (bool) $attachment->is_inline,
                            'description' => $attachment->description,
                            'extension' => $attachment->extension,
                        ];
                    })->values()->toArray();
                } else {
                    $emailArray['attachments'] = [];
                }
                
                $emailArray['preview_url'] = $previewUrl;
                $emailArray['from_mail'] = $emailArray['from_mail'] ?? '';
                $emailArray['to_mail'] = $emailArray['to_mail'] ?? '';
                $emailArray['subject'] = $emailArray['subject'] ?? '';
                $emailArray['message'] = $emailArray['message'] ?? '';
                
                return $emailArray;
            });

            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Error in filterSentEmails V2: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching emails'
            ], 500);
        }
    }
}
