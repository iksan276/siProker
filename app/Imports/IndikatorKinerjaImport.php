<?php

namespace App\Imports;

use App\Models\IndikatorKinerja;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class IndikatorKinerjaImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'SatuanID' => $row['satuanid'],
            'Nama' => $this->convertToString($row['nama']),
            'Baseline' => $this->convertToString($row['baseline']),
            'Tahun1' => $this->convertToString($row['tahun_1']),
            'Tahun2' => $this->convertToString($row['tahun_2']),
            'Tahun3' => $this->convertToString($row['tahun_3']),
            'Tahun4' => $this->convertToString($row['tahun_4']),
            'Tahun5' => $this->convertToString($row['tahun_5']),
            'MendukungIKU' => $row['mendukung_iku'],
            'MendukungKA' => $row['mendukung_ka'],
            'IKUPTID' => $this->convertToString($row['ikuptid'] ?? null),
            'KriteriaAkreditasiID' => $this->convertToString($row['kriteriaakreditasiid'] ?? null),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika IndikatorKinerjaID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['indikatorkinerjaid'])) {
            $data['IndikatorKinerjaID'] = $row['indikatorkinerjaid'];
        }

        return new IndikatorKinerja($data);
    }

    public function uniqueBy()
    {
        return 'IndikatorKinerjaID';
    }

    public function rules(): array
    {
        return [
            'indikatorkinerjaid' => 'nullable|integer',
            'satuanid' => 'required|integer|exists:satuans,SatuanID',
            'nama' => 'required',
            'baseline' => 'required',
            'tahun_1' => 'required',
            'tahun_2' => 'required',
            'tahun_3' => 'required',
            'tahun_4' => 'required',
            'tahun_5' => 'required',
            'mendukung_iku' => 'required|in:Y,N',
            'mendukung_ka' => 'required|in:Y,N',
            'status' => 'nullable|in:Y,N',
        ];
    }

    /**
     * Convert value to string, handle numeric and other types
     */
    private function convertToString($value)
    {
        if ($value === null) {
            return null;
        }
        
        return (string) $value;
    }
}
