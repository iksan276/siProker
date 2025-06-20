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
            'IKUPT' => new IKUPTImport(),
            'Kriteria Akreditasi' => new KriteriaAkreditasiImport(),
            'Mata Anggaran' => new MataAnggaranImport(),
            'Satuan' => new SatuanImport(),
            'Jenis Kegiatan' => new JenisKegiatanImport(),
            'Indikator Kinerja' => new IndikatorKinerjaImport(),
            'Program Rektor' => new ProgramRektorImport(),
            'Kegiatan' => new KegiatanImport(),
        ];
    }
}
