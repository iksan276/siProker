<?php

namespace App\Exports;

use App\Models\ProgramRektor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProgramRektorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $programRektors;

    public function __construct($programRektors = null)
    {
        $this->programRektors = $programRektors;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->programRektors) {
            return $this->programRektors;
        }
        
        return ProgramRektor::with([
            'programPengembangan.isuStrategis.pilar.renstra',
        ])->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'No',
            'Renstra',
            'Pilar',
            'Isu Strategis',
            'Program Pengembangan',
            'Nama',
            'Tahun',
            'NA'
        ];
    }

    /**
    * @param mixed $programRektor
    * @return array
    */
    public function map($programRektor): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $programPengembangan = $programRektor->programPengembangan;
        $isuStrategis = $programPengembangan->isuStrategis;
        $pilar = $isuStrategis->pilar;
        $renstra = $pilar->renstra;

        // Format NA status
        $naStatus = ($programRektor->NA == 'Y') ? 'Non Aktif' : 'Aktif';

        return [
            $rowNumber,
            $renstra->Nama,
            $pilar->Nama,
            $isuStrategis->Nama,
            $programPengembangan->Nama,
            $programRektor->Nama,
            $programRektor->Tahun,
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
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Tahun
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // NA
        
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
