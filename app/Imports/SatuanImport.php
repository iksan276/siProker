<?php

namespace App\Imports;

use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SatuanImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
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

        // Jika SatuanID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['satuanid'])) {
            $data['SatuanID'] = $row['satuanid'];
        }

        return new Satuan($data);
    }

    public function uniqueBy()
    {
        return 'SatuanID';
    }

    public function rules(): array
    {
        return [
            'satuanid' => 'nullable|integer',
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
