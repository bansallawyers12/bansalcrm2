<?php

namespace Tests\Unit;

use App\Support\ApplicationStage;
use PHPUnit\Framework\TestCase;

class ApplicationStageTest extends TestCase
{
    public function test_normalize_lowercases_and_trims_stage(): void
    {
        $this->assertSame('coe issued', ApplicationStage::normalize('  Coe Issued '));
    }

    public function test_normalize_returns_null_for_empty_values(): void
    {
        $this->assertNull(ApplicationStage::normalize(null));
        $this->assertNull(ApplicationStage::normalize(''));
        $this->assertNull(ApplicationStage::normalize('   '));
    }

    public function test_normalize_matches_sheet_filter_values(): void
    {
        $this->assertSame('awaiting document', ApplicationStage::normalize('Awaiting document'));
        $this->assertSame('coe cancelled', ApplicationStage::normalize('Coe Cancelled'));
    }
}
