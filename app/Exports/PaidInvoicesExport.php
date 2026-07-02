<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaidInvoicesExport implements FromArray, WithHeadings
{
    /**
     * @param  array<int, array<int, string>>  $rows
     * @param  array<int, string>  $headings
     */
    public function __construct(
        private array $rows,
        private array $headings
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
