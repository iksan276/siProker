<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Renstra' => new RenstraExport(),
            'Pilar' => new PilarExport(),
            'Isu Strategis' => new IsuStrategisExport(),
            'Program Pengembangan' => new ProgramPengembanganExport(),
            'Indikator Kinerja' => new IndikatorKinerjaExportV2(),
            'Program Rektor' => new ProgramRektorExportV2(),
            'Kegiatan' => new KegiatanExportV2(),
        ];
    }
}
