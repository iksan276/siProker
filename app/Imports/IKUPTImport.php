<?php

namespace App\Imports;

use App\Models\IKUPT;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class IKUPTImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'Nama' => $this->convertToString($row['nama']),
            'Key' => $this->convertToString($row['key']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika IKUPTID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['ikuptid'])) {
            $data['IKUPTID'] = $row['ikuptid'];
        }

        return new IKUPT($data);
    }

    public function uniqueBy()
    {
        return 'IKUPTID';
    }

    public function rules(): array
    {
        return [
            'ikuptid' => 'nullable|integer',
            'nama' => 'required',
            'key' => 'nullable|string',
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
