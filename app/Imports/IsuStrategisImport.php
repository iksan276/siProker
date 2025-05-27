<?php

namespace App\Imports;

use App\Models\IsuStrategis;
use App\Models\Pilar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class IsuStrategisImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new IsuStrategis([
            'PilarID' => $row['pilarid'],
            'Nama' => $row['nama'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'pilarid' => 'required|integer|exists:pilars,PilarID',
            'nama' => 'required|string',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
