<?php

namespace App\Imports;

use App\Models\Renstra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RenstraImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Renstra([
            'Nama' => $row['nama'],
            'PeriodeMulai' => $row['periode_mulai'],
            'PeriodeSelesai' => $row['periode_selesai'],
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'periode_mulai' => 'required|integer|min:2000|max:2100',
            'periode_selesai' => 'required|integer|min:2000|max:2100',
            'status' => 'nullable|in:Y,N',
        ];
    }
}
