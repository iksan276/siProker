<?php

namespace App\Imports;

use App\Models\IsuStrategis;
use App\Models\Pilar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class IsuStrategisImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'PilarID' => $row['pilarid'],
            'Nama' => $this->convertToString($row['nama']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika IsuStrategisID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['isustrategisid'])) {
            $data['IsuID'] = $row['isustrategisid'];
        }

        return new IsuStrategis($data);
    }

    public function uniqueBy()
    {
        return 'IsuID';
    }

    public function rules(): array
    {
        return [
            'isustrategisid' => 'nullable|integer',
            'pilarid' => 'required|integer|exists:pilars,PilarID',
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
