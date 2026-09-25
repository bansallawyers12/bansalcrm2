<?php

namespace App\Support;

/**
 * Canonical stage normalization for sheet filters (matches LOWER(TRIM(stage))).
 */
final class ApplicationStage
{
    public static function normalize(?string $stage): ?string
    {
        if ($stage === null) {
            return null;
        }

        $normalized = strtolower(trim($stage));

        return $normalized === '' ? null : $normalized;
    }
}
