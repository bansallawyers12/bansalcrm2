<?php

namespace App\Services;

use App\Models\VisaType;

class VisaTypeResolveService
{
    /**
     * Resolve visa type id from import data (e.g. first entry of visa_countries).
     * Match by visa_type_matter_title to visa_types.name; if no match, create new visa_types row.
     * Bansalcrm2 does not store nick_name; it is only used as fallback for the new row's name.
     *
     * @param array $visaData Keys: visa_type (int), visa_type_matter_title, visa_type_matter_nick_name (optional)
     * @return int visa_types.id
     */
    public function resolve(array $visaData): int
    {
        $title = isset($visaData['visa_type_matter_title']) && $visaData['visa_type_matter_title'] !== ''
            ? trim((string) $visaData['visa_type_matter_title'])
            : null;
        $nickName = isset($visaData['visa_type_matter_nick_name']) && $visaData['visa_type_matter_nick_name'] !== ''
            ? trim((string) $visaData['visa_type_matter_nick_name'])
            : null;

        // Match by title (case-insensitive) on visa_types.name
        if ($title !== null && $title !== '') {
            $existing = VisaType::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($title)])
                ->first();
            if ($existing) {
                return (int) $existing->id;
            }
        }

        // No match: create new visa_types row
        $name = $title ?? $nickName ?? 'Imported';
        $visaType = new VisaType();
        $visaType->name = $name;
        $visaType->save();

        return (int) $visaType->id;
    }
}
