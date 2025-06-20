<?php

namespace App\Imports;

use App\Models\MataAnggaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class MataAnggaranImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
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

        // Jika MataAnggaranID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['mataanggaranid'])) {
            $data['MataAnggaranID'] = $row['mataanggaranid'];
        }

        return new MataAnggaran($data);
    }

    public function uniqueBy()
    {
        return 'MataAnggaranID';
    }

    public function rules(): array
    {
        return [
            'mataanggaranid' => 'nullable|integer',
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
