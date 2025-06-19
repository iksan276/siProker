<?php

namespace App\Imports;

use App\Models\Kegiatan;
use App\Models\ProgramRektor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Carbon\Carbon;

class KegiatanImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    public function model(array $row)
    {
        $data = [
            'ProgramRektorID' => $row['programrektorid'],
            'Nama' => $row['nama'],
            'TanggalMulai' => $this->parseDate($row['tanggal_mulai']),
            'TanggalSelesai' => $this->parseDate($row['tanggal_selesai']),
            'TanggalPencairan' => $this->parseDate($row['tanggal_pencairan']),
            'RincianKegiatan' => $this->convertToString($row['rincian']),
            'Feedback' => $this->convertToString($row['feedback'] ?? null),
            'Status' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
            'DEdited' => now(),
            'UEdited' => auth()->id(),
        ];

        // Jika KegiatanID ada dan tidak kosong, gunakan ID tersebut
        if (!empty($row['kegiatanid'])) {
            $data['KegiatanID'] = $row['kegiatanid'];
        }

        return new Kegiatan($data);
    }

    public function uniqueBy()
    {
        return 'KegiatanID';
    }

    public function rules(): array
    {
        return [
            'kegiatanid' => 'nullable|integer',
            'programrektorid' => 'required|integer|exists:program_rektors,ProgramRektorID',
            'nama' => 'required|string',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'tanggal_pencairan' => 'required',
            'rincian' => 'required', // Ubah dari string ke required saja
            'feedback' => 'nullable',
            'status' => 'nullable|in:N,Y,T,R,P,PT,YT,TT,RT,TP',
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

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($date - 2);
            }
            
            // Handle string dates
            return Carbon::parse($date);
        } catch (\Exception $e) {
            // If parsing fails, try common formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
            
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $date);
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // If all fails, return current date
            return now();
        }
    }
}
