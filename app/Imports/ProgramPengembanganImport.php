<?php

namespace App\Imports;

use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProgramPengembanganImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'IsuID' => $row['isustrategisid'],
            'Nama' => $this->convertToString($row['nama']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika ProgramPengembanganID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['programpengembanganid'])) {
            $data['ProgramPengembanganID'] = $row['programpengembanganid'];
        }

        return new ProgramPengembangan($data);
    }

    public function uniqueBy()
    {
        return 'ProgramPengembanganID';
    }

    public function rules(): array
    {
        return [
            'programpengembanganid' => 'nullable|integer',
            'isustrategisid' => 'required|integer|exists:isu_strategis,IsuID',
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
