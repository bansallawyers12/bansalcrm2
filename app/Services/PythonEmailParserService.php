<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonEmailParserService
{
    protected string $pythonServiceUrl;

    public function __construct(?string $pythonServiceUrl = null)
    {
        $this->pythonServiceUrl = $pythonServiceUrl ?? env('PYTHON_SERVICE_URL', 'http://127.0.0.1:5001');
    }

    /**
     * Parse an uploaded .msg file via the Python microservice.
     *
     * @return array Parsed email data, or ['success' => false, 'error' => ...] on failure
     */
    public function parseUploadedMsgFile(UploadedFile $file): array
    {
        try {
            $sanitizedFileName = $this->sanitizeFilename($file->getClientOriginalName());
            $fileContents = file_get_contents($file->getPathname());
            if ($fileContents === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to read uploaded email file.',
                ];
            }

            $response = Http::timeout(30)
                ->attach('file', $fileContents, $sanitizedFileName)
                ->post($this->pythonServiceUrl . '/email/parse');

            if ($response->successful()) {
                try {
                    $result = $response->json();
                } catch (\Exception $jsonException) {
                    Log::error('Failed to parse Python service response as JSON', [
                        'status' => $response->status(),
                        'content_type' => $response->header('Content-Type'),
                        'body_preview' => substr($response->body(), 0, 500),
                        'error' => $jsonException->getMessage(),
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Invalid response from email processing service. The service may be experiencing issues.',
                    ];
                }

                if (isset($result['error']) || (isset($result['success']) && !$result['success'])) {
                    return [
                        'success' => false,
                        'error' => $result['error'] ?? 'Email parsing failed',
                    ];
                }

                return $result;
            }

            $body = $response->body();
            Log::error('Python service error', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            $errorMsg = 'Python service returned status: ' . $response->status();
            $decoded = json_decode($body, true);
            if (is_array($decoded) && isset($decoded['error'])) {
                $errorMsg = is_string($decoded['error']) ? $decoded['error'] : $errorMsg;
            } elseif (is_array($decoded) && isset($decoded['detail'])) {
                $detail = $decoded['detail'];
                $errorMsg = is_string($detail) ? $detail : $errorMsg;
            }

            return [
                'success' => false,
                'error' => $errorMsg,
            ];
        } catch (\Exception $e) {
            Log::error('Python service connection error', [
                'error' => $e->getMessage(),
                'url' => $this->pythonServiceUrl,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to connect to Python service: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format sent date from Python parser output for display/storage.
     */
    public function formatFetchMailSentTime(array $parsedData, string $timezone = 'Australia/Sydney'): string
    {
        if (empty($parsedData['sent_date'])) {
            return '';
        }

        try {
            $dateString = $parsedData['sent_date'];
            if (!is_string($dateString) && !is_numeric($dateString)) {
                return '';
            }
            $dateString = (string) $dateString;

            if (preg_match('/[+-]\d{2}:\d{2}$|Z$/', $dateString)) {
                $sentDate = new \DateTime($dateString);
            } else {
                $sentDate = new \DateTime($dateString, new \DateTimeZone('UTC'));
            }

            $sentDate->setTimezone(new \DateTimeZone($timezone));

            return $sentDate->format('d/m/Y h:i a');
        } catch (\Exception $e) {
            return is_string($parsedData['sent_date']) ? $parsedData['sent_date'] : '';
        }
    }

    protected function sanitizeFilename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $nameWithoutExt);
        $sanitizedName = preg_replace('/_+/', '_', $sanitizedName);
        $sanitizedName = trim($sanitizedName, '_');

        if (empty($sanitizedName)) {
            $sanitizedName = 'email_' . time();
        }

        $sanitizedFilename = !empty($extension) ? $sanitizedName . '.' . $extension : $sanitizedName;

        if (strlen($sanitizedFilename) > 255) {
            $maxNameLength = 255 - strlen($extension) - 1;
            if ($maxNameLength > 0) {
                $sanitizedName = substr($sanitizedName, 0, $maxNameLength);
                $sanitizedFilename = !empty($extension) ? $sanitizedName . '.' . $extension : $sanitizedName;
            } else {
                $sanitizedFilename = 'email_' . time() . (!empty($extension) ? '.' . $extension : '');
            }
        }

        return $sanitizedFilename;
    }
}
