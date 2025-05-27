<?php

namespace App\Exports;

use App\Models\IndikatorKinerja;
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

class IndikatorKinerjaExportV2 implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return IndikatorKinerja::with('satuan')->where('NA', 'N')->get();
    }

    public function headings(): array
    {
        return [
            'SatuanID',
            'Nama',
            'Baseline',
            'Tahun 1',
            'Tahun 2',
            'Tahun 3',
            'Tahun 4',
            'Tahun 5',
            'Mendukung IKU',
            'Mendukung KA',
            'IKUPTID',
            'KriteriaAkreditasiID',
            'Status'
        ];
    }

    public function map($indikator): array
    {
        return [
            $indikator->SatuanID,
            $indikator->Nama,
            $indikator->Baseline,
            $indikator->Tahun1,
            $indikator->Tahun2,
            $indikator->Tahun3,
            $indikator->Tahun4,
            $indikator->Tahun5,
            $indikator->MendukungIKU,
            $indikator->MendukungKA,
            $indikator->IKUPTID,
            $indikator->KriteriaAkreditasiID,
            $indikator->NA
        ];
    }

    public function title(): string
    {
        return 'Indikator Kinerja';
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
        $sheet->getStyle('C2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Baseline - Tahun 5
        $sheet->getStyle('I2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mendukung IKU & KA
        $sheet->getStyle('K2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // IKU PTID & Kriteria Akreditasi ID
        $sheet->getStyle('M2:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
        
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
