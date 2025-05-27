<?php

namespace App\Imports;

use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProgramPengembanganImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new ProgramPengembangan([
            'IsuID' => $row['isustrategisid'],
            'Nama' => $row['nama'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'isustrategisid' => 'required|integer|exists:isu_strategis,IsuID',
            'nama' => 'required|string',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
