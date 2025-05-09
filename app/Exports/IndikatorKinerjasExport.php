<?php

namespace App\Exports;

use App\Models\IndikatorKinerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IndikatorKinerjasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $indikatorKinerjas;
    protected $yearLabels;

    public function __construct($indikatorKinerjas = null, $yearLabels = null)
    {
        $this->indikatorKinerjas = $indikatorKinerjas;
        $this->yearLabels = $yearLabels ?? [2025, 2026, 2027, 2028, 2029];
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
            $this->yearLabels[4] ?? 'Tahun 5',
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
            $indikatorKinerja->Tahun5,
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
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('D2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Baseline and Tahun 1-5
        $sheet->getStyle('J2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mendukung IKU & Status
        
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
