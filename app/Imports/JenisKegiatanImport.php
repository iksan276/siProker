<?php

namespace App\Imports;

use App\Models\JenisKegiatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class JenisKegiatanImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'Nama' => $this->convertToString($row['nama']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika JenisKegiatanID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['jeniskegiatanid'])) {
            $data['JenisKegiatanID'] = $row['jeniskegiatanid'];
        }

        return new JenisKegiatan($data);
    }

    public function uniqueBy()
    {
        return 'JenisKegiatanID';
    }

    public function rules(): array
    {
        return [
            'jeniskegiatanid' => 'nullable|integer',
            'nama' => 'required',
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
