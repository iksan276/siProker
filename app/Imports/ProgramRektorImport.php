<?php

namespace App\Imports;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\JenisKegiatan;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProgramRektorImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new ProgramRektor([
            'ProgramPengembanganID' => $row['programpengembanganid'],
            'IndikatorKinerjaID' => $row['indikatorkinerjaid'],
            'Nama' => $row['nama'],
            'Output' => $row['output'],
            'Outcome' => $row['outcome'],
            'JenisKegiatanID' => $row['jeniskegiatanid'],
            'MataAnggaranID' => $row['mataanggaranid'],
            'JumlahKegiatan' => $row['jumlah_kegiatan'],
            'SatuanID' => $row['satuanid'],
            'HargaSatuan' => $row['harga_satuan'],
            'Total' => $row['total'],
            'PenanggungJawabID' => $row['penanggungjawabid'],
            'PelaksanaID' => $row['pelaksanaid'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'programpengembanganid' => 'required|integer|exists:program_pengembangans,ProgramPengembanganID',
            'indikatorkinerjaid' => 'required',
            'nama' => 'required|string',
            'output' => 'required|string',
            'outcome' => 'required|string',
            'jeniskegiatanid' => 'required|integer',
            'mataanggaranid' => 'required',
            'jumlah_kegiatan' => 'required|integer|min:1',
            'satuanid' => 'required|integer',
            'penanggungjawabid' => 'required|integer',
            'pelaksanaid' => 'required',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
