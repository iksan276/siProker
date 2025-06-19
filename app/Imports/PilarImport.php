<?php

namespace App\Imports;

use App\Models\Pilar;
use App\Models\Renstra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PilarImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'RenstraID' => $row['renstraid'],
            'Nama' => $this->convertToString($row['nama']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika PilarID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['pilarid'])) {
            $data['PilarID'] = $row['pilarid'];
        }

        return new Pilar($data);
    }

    public function uniqueBy()
    {
        return 'PilarID';
    }

    public function rules(): array
    {
        return [
            'pilarid' => 'nullable|integer',
            'renstraid' => 'required|integer|exists:renstras,RenstraID',
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
