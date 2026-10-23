<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\Client\ClientReceiptController;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClientReceiptBase64UploadTest extends TestCase
{
    private function controller(): ClientReceiptController
    {
        return new class extends ClientReceiptController
        {
            public function __construct()
            {
                // Skip auth middleware for this unit test.
            }

            public function attach(Request $request): ?bool
            {
                return $this->attachBase64ReceiptDocument($request);
            }

            /**
             * @return array{id: ?int, error: ?string}
             */
            public function applicationId(mixed $raw): array
            {
                return $this->receiptApplicationId($raw);
            }
        };
    }

    public function test_text_upload_is_visible_as_the_receipt_file_input(): void
    {
        $request = Request::create('/clients/saveaccountreport', 'POST', [
            'document_upload_base64' => base64_encode('receipt-bytes'),
            'document_upload_name' => 'folder/receipt.pdf',
            'function_type' => 'add',
        ]);

        $attached = $this->controller()->attach($request);

        $this->assertTrue($attached);
        $this->assertTrue($request->hasFile('document_upload'));
        $file = $request->file('document_upload');
        $this->assertSame('receipt.pdf', $file->getClientOriginalName());
        $this->assertSame('pdf', $file->getClientOriginalExtension());
        $this->assertSame('receipt-bytes', file_get_contents($file->getPathname()));
    }

    public function test_receipt_without_a_document_is_left_alone(): void
    {
        $request = Request::create('/clients/saveaccountreport', 'POST', [
            'function_type' => 'add',
            'client_id' => '15',
        ]);

        $this->assertNull($this->controller()->attach($request));
        $this->assertFalse($request->hasFile('document_upload'));
    }

    public function test_invalid_text_upload_is_rejected(): void
    {
        $request = Request::create('/clients/saveaccountreport', 'POST', [
            'document_upload_base64' => 'not-valid-base64!!!',
            'function_type' => 'add',
        ]);

        $this->assertFalse($this->controller()->attach($request));
        $this->assertFalse($request->hasFile('document_upload'));
    }

    public function test_course_number_is_kept_and_a_blank_course_stays_empty(): void
    {
        $this->assertSame(['id' => 29829, 'error' => null], $this->controller()->applicationId('29829'));
        $this->assertSame(['id' => 29829, 'error' => null], $this->controller()->applicationId(29829));
        $this->assertSame(['id' => null, 'error' => null], $this->controller()->applicationId(''));
        $this->assertSame(['id' => null, 'error' => null], $this->controller()->applicationId(null));
    }

    public function test_course_name_is_rejected_before_the_receipt_is_saved(): void
    {
        $request = Request::create('/clients/saveaccountreport', 'POST', [
            'function_type' => 'add',
            'application_id' => 'Certificate III in Painting and Decorating',
            'client_id' => '53191',
            'trans_date' => ['23/10/2026'],
            'entry_date' => ['23/10/2026'],
            'payment_method' => ['Bank transfer'],
            'description' => ['course test'],
            'deposit_amount' => ['10'],
        ]);

        $resolved = $this->controller()->applicationId($request->input('application_id'));
        $this->assertNull($resolved['id']);
        $this->assertSame('Select a course from the list. A course name cannot be saved in this field.', $resolved['error']);

        ob_start();
        $this->controller()->saveaccountreport($request);
        $response = json_decode((string) ob_get_clean(), true);

        $this->assertIsArray($response);
        $this->assertFalse($response['status']);
        $this->assertSame($resolved['error'], $response['message']);
        $this->assertSame('add', $response['function_type']);
    }
}
