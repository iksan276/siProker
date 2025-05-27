<?php

namespace App\Imports;

use App\Models\Kegiatan;
use App\Models\ProgramRektor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class KegiatanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Kegiatan([
            'ProgramRektorID' => $row['programrektorid'],
            'Nama' => $row['nama'],
            'TanggalMulai' => Carbon::parse($row['tanggal_mulai']),
            'TanggalSelesai' => Carbon::parse($row['tanggal_selesai']),
            'TanggalPencairan' => Carbon::parse($row['tanggal_pencairan']),
            'RincianKegiatan' => $row['rincian'],
            'Feedback' => $row['feedback'] ?? null,
            'Status' => $row['status'] ?? 'N',
            'DCreated' => now(),
            'UCreated' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'programrektorid' => 'required|integer|exists:program_rektors,ProgramRektorID',
            'nama' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_pencairan' => 'required|date',
            'rincian' => 'required|string',
            'feedback' => 'nullable|string',
            'status' => 'nullable|in:N,Y,T,R,P,PT,YT,TT,RT,TP',
        ];
    }
}
