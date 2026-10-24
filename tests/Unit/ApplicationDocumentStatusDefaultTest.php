<?php

namespace Tests\Unit;

use App\Models\ApplicationDocument;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplicationDocumentStatusDefaultTest extends TestCase
{
    #[Test]
    public function new_application_document_defaults_status_to_unpublished(): void
    {
        $document = new ApplicationDocument;

        $this->assertSame(0, $document->status);
    }

    #[Test]
    public function status_can_still_be_set_to_published(): void
    {
        $document = new ApplicationDocument;
        $document->status = 1;

        $this->assertSame(1, $document->status);
    }
}
