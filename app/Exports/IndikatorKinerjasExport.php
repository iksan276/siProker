<?php

namespace App\Exports;

use App\Models\IndikatorKinerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndikatorKinerjasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $indikatorKinerjas;
    protected $yearLabels;

    public function __construct($indikatorKinerjas = null, $yearLabels = null)
    {
        $this->indikatorKinerjas = $indikatorKinerjas;
        $this->yearLabels = $yearLabels ?? [2025, 2026, 2027, 2028];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->indikatorKinerjas) {
            return $this->indikatorKinerjas;
        }
        
        return IndikatorKinerja::with(['satuan'])->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Use dynamic year labels for the headings
        return [
            'No',
            'Nama',
            'Satuan',
            'Baseline',
            $this->yearLabels[0] ?? 'Tahun 1',
            $this->yearLabels[1] ?? 'Tahun 2',
            $this->yearLabels[2] ?? 'Tahun 3',
            $this->yearLabels[3] ?? 'Tahun 4',
            'Mendukung IKU PT / Kriteria Akreditasi',
            'Status'
        ];
    }

    /**
    * @param mixed $indikatorKinerja
    * @return array
    */
    public function map($indikatorKinerja): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Format NA status
        $naStatus = ($indikatorKinerja->NA == 'Y') ? 'Non Aktif' : 'Aktif';
        
        // Format MendukungIKU status
        $mendukungIKUStatus = ($indikatorKinerja->MendukungIKU == 'Y') ? 'Ya' : 'Tidak';

        return [
            $rowNumber,
            $indikatorKinerja->Nama,
            $indikatorKinerja->satuan->Nama,
            $indikatorKinerja->Baseline,
            $indikatorKinerja->Tahun1,
            $indikatorKinerja->Tahun2,
            $indikatorKinerja->Tahun3,
            $indikatorKinerja->Tahun4,
            $mendukungIKUStatus,
            $naStatus
        ];
    }

    /**
     * @param Worksheet $sheet
     */
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
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2EFDA',
                ],
            ],
        ]);
        
        // Center specific columns
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('E2:H' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Tahun 1-4
        $sheet->getStyle('I2:J' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Mendukung IKU & NA
        
        // Add borders to all cells
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
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
