<?php

namespace App\Imports;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\JenisKegiatan;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProgramRektorImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'ProgramPengembanganID' => $row['programpengembanganid'],
            'IndikatorKinerjaID' => $this->convertToString($row['indikatorkinerjaid']),
            'Nama' => $this->convertToString($row['nama']),
            'Output' => $this->convertToString($row['output']),
            'Outcome' => $this->convertToString($row['outcome']),
            'JenisKegiatanID' => $row['jeniskegiatanid'],
            'MataAnggaranID' => $this->convertToString($row['mataanggaranid']),
            'JumlahKegiatan' => $row['jumlah_kegiatan'],
            'SatuanID' => $row['satuanid'],
            'HargaSatuan' => $row['harga_satuan'] ?? 0,
            'Total' => $row['total'] ?? 0,
            'PenanggungJawabID' => $row['penanggungjawabid'],
            'PelaksanaID' => $this->convertToString($row['pelaksanaid']),
            'NA' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika ProgramRektorID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['programrektorid'])) {
            $data['ProgramRektorID'] = $row['programrektorid'];
        }

        return new ProgramRektor($data);
    }

    public function uniqueBy()
    {
        return 'ProgramRektorID';
    }

    public function rules(): array
    {
        return [
            'programrektorid' => 'nullable|integer',
            'programpengembanganid' => 'required|integer|exists:program_pengembangans,ProgramPengembanganID',
            'indikatorkinerjaid' => 'required',
            'nama' => 'required',
            'output' => 'required',
            'outcome' => 'required',
            'jeniskegiatanid' => 'required|integer',
            'mataanggaranid' => 'required',
            'jumlah_kegiatan' => 'required|integer|min:1',
            'satuanid' => 'required|integer',
            'penanggungjawabid' => 'required|integer',
            'pelaksanaid' => 'required',
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
