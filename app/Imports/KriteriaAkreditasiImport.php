<?php

namespace App\Imports;

use App\Models\KriteriaAkreditasi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class KriteriaAkreditasiImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
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

        // Jika KriteriaAkreditasiID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['kriteriaakreditasiid'])) {
            $data['KriteriaAkreditasiID'] = $row['kriteriaakreditasiid'];
        }

        return new KriteriaAkreditasi($data);
    }

    public function uniqueBy()
    {
        return 'KriteriaAkreditasiID';
    }

    public function rules(): array
    {
        return [
            'kriteriaakreditasiid' => 'nullable|integer',
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
