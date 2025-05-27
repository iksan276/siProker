<?php

namespace App\Imports;

use App\Models\Pilar;
use App\Models\Renstra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PilarImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Pilar([
            'RenstraID' => $row['renstraid'],
            'Nama' => $row['nama'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'renstraid' => 'required|integer|exists:renstras,RenstraID',
            'nama' => 'required|string',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
