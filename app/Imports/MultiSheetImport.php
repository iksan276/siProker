<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Renstra' => new RenstraImport(),
            'Pilar' => new PilarImport(),
            'Isu Strategis' => new IsuStrategisImport(),
            'Program Pengembangan' => new ProgramPengembanganImport(),
            'Indikator Kinerja' => new IndikatorKinerjaImport(),
            'Program Rektor' => new ProgramRektorImport(),
            'Kegiatan' => new KegiatanImport(),
        ];
    }
}
