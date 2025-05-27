<?php

namespace App\Imports;

use App\Models\IndikatorKinerja;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class IndikatorKinerjaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new IndikatorKinerja([
            'SatuanID' => $row['satuanid'],
            'Nama' => $row['nama'],
            'Baseline' => $row['baseline'],
            'Tahun1' => $row['tahun_1'],
            'Tahun2' => $row['tahun_2'],
            'Tahun3' => $row['tahun_3'],
            'Tahun4' => $row['tahun_4'],
            'Tahun5' => $row['tahun_5'],
            'MendukungIKU' => $row['mendukung_iku'],
            'MendukungKA' => $row['mendukung_ka'],
            'IKUPTID' => $row['ikuptid'],
            'KriteriaAkreditasiID' => $row['kriteriaakreditasiid'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'satuanid' => 'required|integer|exists:satuans,SatuanID',
            'nama' => 'required|string',
            'baseline' => 'required', // Ubah dari numeric ke required saja
            'tahun_1' => 'required',  // Ubah dari numeric ke required saja
            'tahun_2' => 'required',  // Ubah dari numeric ke required saja
            'tahun_3' => 'required',  // Ubah dari numeric ke required saja
            'tahun_4' => 'required',  // Ubah dari numeric ke required saja
            'tahun_5' => 'required',  // Ubah dari numeric ke required saja
            'mendukung_iku' => 'required|in:Y,N',
            'mendukung_ka' => 'required|in:Y,N',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
