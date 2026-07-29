<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SlipGajiTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SlipGajiTemplateSheet(),
        ];
    }
}
