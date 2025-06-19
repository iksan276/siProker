<?php

namespace App\Exports;

use App\Models\ProgramRektor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProgramRektorExportV2 implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return ProgramRektor::with(['programPengembangan', 'jenisKegiatan', 'satuan'])
            ->where('NA', 'N')->get();
    }

    public function headings(): array
    {
        return [
            'ProgramRektorID',
            'ProgramPengembanganID',
            'IndikatorKinerjaID',
            'Nama',
            'Output',
            'Outcome',
            'JenisKegiatanID',
            'MataAnggaranID',
            'Jumlah Kegiatan',
            'SatuanID',
            'Harga Satuan',
            'Total',
            'PenanggungJawabID',
            'PelaksanaID',
            'Status'
        ];
    }

    public function map($program): array
    {
        return [
            $program->ProgramRektorID,
            $program->ProgramPengembanganID,
            $program->IndikatorKinerjaID,
            $program->Nama,
            $program->Output,
            $program->Outcome,
            $program->JenisKegiatanID,
            $program->MataAnggaranID,
            $program->JumlahKegiatan,
            $program->SatuanID,
            $program->HargaSatuan,
            $program->Total,
            $program->PenanggungJawabID,
            $program->PelaksanaID,
            $program->NA
        ];
    }

    public function title(): string
    {
        return 'Program Rektor';
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest row and column indexes
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        // Apply wrap text to all cells in the worksheet
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getAlignment()->setWrapText(true);
        
        // Make headers bold and centered
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2EFDA',
                ],
            ],
        ]);
        
        // Center specific columns
        $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Indikator Kinerja ID
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mata Anggaran ID
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah Kegiatan
        $sheet->getStyle('J2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Harga Satuan & Total
        $sheet->getStyle('L2:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Penanggung Jawab ID & Pelaksana ID
        $sheet->getStyle('N2:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
        
        // Add borders to all cells
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Set row height for better readability with wrapped text
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1); // Auto height
        }
    }
}
