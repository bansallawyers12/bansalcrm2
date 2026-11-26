<?php

namespace Tests\Unit;

use App\Services\PublicOfficeCheckInService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PublicOfficeCheckInPhoneVariantsTest extends TestCase
{
    #[Test]
    public function phone_digit_match_variants_are_the_same_with_or_without_leading_zero(): void
    {
        $service = new PublicOfficeCheckInService;
        $expected = ['0406660960', '406660960', '61406660960'];

        $withZero = $service->phoneDigitMatchVariants('0406660960');
        $withoutZero = $service->phoneDigitMatchVariants('406660960');
        $withCountryCode = $service->phoneDigitMatchVariants('61406660960');

        sort($expected, SORT_STRING);
        sort($withZero, SORT_STRING);
        sort($withoutZero, SORT_STRING);
        sort($withCountryCode, SORT_STRING);

        $this->assertSame($expected, $withZero);
        $this->assertSame($expected, $withoutZero);
        $this->assertSame($expected, $withCountryCode);
    }

    #[Test]
    public function canonical_australian_phone_accepts_with_or_without_leading_zero(): void
    {
        $service = new PublicOfficeCheckInService;

        $this->assertSame('0412264458', $service->canonicalAustralianPhone('0412264458'));
        $this->assertSame('0412264458', $service->canonicalAustralianPhone('412264458'));
        $this->assertSame('0412264458', $service->canonicalAustralianPhone('+61 412 264 458'));
    }
}
