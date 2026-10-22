<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\Client\ClientDocumentController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ClientDocumentBase64UploadTest extends TestCase
{
    private function controller(): ClientDocumentController
    {
        return new class extends ClientDocumentController
        {
            public function __construct()
            {
                // Skip auth middleware for this unit test.
            }

            public function attach(Request $request): ?bool
            {
                return $this->attachBase64DocumentUpload($request);
            }

            public function attachBulk(Request $request): ?bool
            {
                return $this->attachBase64BulkFiles($request);
            }
        };
    }

    public function test_text_upload_is_visible_as_the_same_file_input(): void
    {
        $request = Request::create('/upload-alldocument', 'POST', [
            'document_upload_base64' => base64_encode('visa-bytes'),
            'document_upload_name' => 'folder/visa.pdf',
        ]);

        $attached = $this->controller()->attach($request);

        $this->assertTrue($attached);
        $this->assertTrue($request->hasFile('document_upload'));
        $file = $request->file('document_upload');
        $this->assertSame('visa.pdf', $file->getClientOriginalName());
        $this->assertSame('visa-bytes', file_get_contents($file->getPathname()));
    }

    public function test_request_without_text_upload_is_left_alone(): void
    {
        $request = Request::create('/upload-alldocument', 'POST', [
            'clientid' => '15',
        ]);

        $this->assertNull($this->controller()->attach($request));
        $this->assertFalse($request->hasFile('document_upload'));
    }

    public function test_invalid_text_upload_is_rejected(): void
    {
        $request = Request::create('/upload-alldocument', 'POST', [
            'document_upload_base64' => 'not-valid-base64!!!',
        ]);

        $this->assertFalse($this->controller()->attach($request));
        $this->assertFalse($request->hasFile('document_upload'));
    }

    public function test_text_upload_is_read_from_the_raw_form_body(): void
    {
        $body = http_build_query([
            'document_upload_base64' => base64_encode('visa-bytes'),
            'document_upload_name' => 'visa.pdf',
            'clientid' => '15',
            'fileid' => '9',
            'type' => 'client',
            'doctype' => 'documents',
        ]);
        $request = Request::create('/upload-alldocument', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded; charset=UTF-8',
        ], $body);
        $request->request->replace([]);

        $this->assertFalse($request->hasFile('document_upload'));

        $attached = $this->controller()->attach($request);

        $this->assertTrue($attached);
        $this->assertTrue($request->hasFile('document_upload'));
        $this->assertSame('visa-bytes', file_get_contents($request->file('document_upload')->getPathname()));
        $this->assertSame('15', (string) $request->input('clientid'));
        $this->assertSame('9', (string) $request->input('fileid'));
    }

    public function test_text_upload_is_read_from_a_raw_json_body(): void
    {
        $body = json_encode([
            'document_upload_base64' => base64_encode('visa-bytes'),
            'document_upload_name' => 'visa.pdf',
        ]);
        $request = Request::create('/upload-alldocument', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json; charset=UTF-8',
        ], $body);
        $request->request->replace([]);

        $this->assertTrue($this->controller()->attach($request));
        $this->assertSame('visa.pdf', $request->file('document_upload')->getClientOriginalName());
    }

    public function test_multipart_request_without_a_file_is_left_alone(): void
    {
        $request = Request::create('/upload-alldocument', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'multipart/form-data; boundary=abc',
        ], 'document_upload_base64='.base64_encode('visa-bytes'));
        $request->request->replace([]);

        $this->assertNull($this->controller()->attach($request));
        $this->assertFalse($request->hasFile('document_upload'));
    }

    public function test_bulk_text_upload_is_visible_as_the_same_files_input(): void
    {
        $request = Request::create('/documents/bulk-upload', 'POST', [
            'files_base64' => [base64_encode('one'), base64_encode('two')],
            'files_names' => ['one.pdf', 'folder/two.png'],
            'clientid' => '15',
            'mappings' => [json_encode(['name' => 'Passport', 'type' => 'existing'])],
        ]);

        $this->assertFalse($request->hasFile('files'));
        $this->assertTrue($this->controller()->attachBulk($request));

        $files = $request->file('files');
        $this->assertCount(2, $files);
        $this->assertSame('one.pdf', $files[0]->getClientOriginalName());
        $this->assertSame('two.png', $files[1]->getClientOriginalName());
        $this->assertSame('one', file_get_contents($files[0]->getPathname()));
        $this->assertSame('two', file_get_contents($files[1]->getPathname()));
        $this->assertSame('15', (string) $request->input('clientid'));
    }

    public function test_bulk_text_upload_is_read_from_the_raw_form_body(): void
    {
        $body = http_build_query([
            'files_base64' => [base64_encode('visa-bytes')],
            'files_names' => ['visa.pdf'],
            'clientid' => '15',
        ]);
        $request = Request::create('/documents/bulk-upload', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded; charset=UTF-8',
        ], $body);
        $request->request->replace([]);

        $this->assertTrue($this->controller()->attachBulk($request));
        $this->assertSame('visa-bytes', file_get_contents($request->file('files')[0]->getPathname()));
        $this->assertSame('15', (string) $request->input('clientid'));
    }

    public function test_bulk_request_without_text_upload_is_left_alone(): void
    {
        $request = Request::create('/documents/bulk-upload', 'POST', [
            'clientid' => '15',
        ]);

        $this->assertNull($this->controller()->attachBulk($request));
        $this->assertFalse($request->hasFile('files'));
    }

    public function test_invalid_bulk_text_upload_is_rejected(): void
    {
        $request = Request::create('/documents/bulk-upload', 'POST', [
            'files_base64' => ['not-valid-base64!!!'],
            'files_names' => ['visa.pdf'],
        ]);

        $this->assertFalse($this->controller()->attachBulk($request));
        $this->assertFalse($request->hasFile('files'));
    }

    public function test_bulk_text_upload_does_not_replace_a_real_file_post(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'crmdoc');
        file_put_contents($path, 'already');
        $request = Request::create('/documents/bulk-upload', 'POST', [
            'files_base64' => [base64_encode('visa-bytes')],
            'files_names' => ['visa.pdf'],
        ]);
        $request->files->set('files', [
            new UploadedFile($path, 'keep.pdf', null, UPLOAD_ERR_OK, true),
        ]);

        $this->assertNull($this->controller()->attachBulk($request));
        $this->assertSame('keep.pdf', $request->file('files')[0]->getClientOriginalName());
        $this->assertSame('already', file_get_contents($request->file('files')[0]->getPathname()));
    }
}
