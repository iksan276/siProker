<?php

namespace App\Imports;

use App\Models\Renstra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RenstraImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'Nama' => $this->convertToString($row['nama']),
            'PeriodeMulai' => $row['periode_mulai'],
            'PeriodeSelesai' => $row['periode_selesai'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika RenstraID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['renstraid'])) {
            $data['RenstraID'] = $row['renstraid'];
        }

        return new Renstra($data);
    }

    public function uniqueBy()
    {
        return 'RenstraID';
    }

    public function rules(): array
    {
        return [
            'renstraid' => 'nullable|integer',
            'nama' => 'required',
            'periode_mulai' => 'required|integer|min:2000|max:2100',
            'periode_selesai' => 'required|integer|min:2000|max:2100',
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
