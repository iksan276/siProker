<?php

namespace App\Exports;

use App\Models\IndikatorKinerja;
use App\Models\MetaAnggaran;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndikatorKinerjasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $indikatorKinerjas;

    public function __construct($indikatorKinerjas = null)
    {
        $this->indikatorKinerjas = $indikatorKinerjas;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->indikatorKinerjas) {
            return $this->indikatorKinerjas;
        }
        
        return IndikatorKinerja::with([
            'programRektor.programPengembangan.isuStrategis.pilar.renstra',
            'satuan'
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
            'Program Rektor',
            'Nama',
            'Bobot',
            'Satuan',
            'Harga Satuan',
            'Jumlah',
            'Meta Anggaran',
            'Unit Terkait',
            'NA'
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

        $programRektor = $indikatorKinerja->programRektor;
        $programPengembangan = $programRektor->programPengembangan;
        $isuStrategis = $programPengembangan->isuStrategis;
        $pilar = $isuStrategis->pilar;
        $renstra = $pilar->renstra;

        // Get Meta Anggaran names
        $metaAnggaranIds = explode(',', $indikatorKinerja->MetaAnggaranID);
        $metaAnggarans = MetaAnggaran::whereIn('MetaAnggaranID', $metaAnggaranIds)->pluck('Nama')->toArray();
        $metaAnggaranText = implode(', ', $metaAnggarans);

        // Get Unit Terkait names
        $unitIds = explode(',', $indikatorKinerja->UnitTerkaitID);
        $units = Unit::whereIn('UnitID', $unitIds)->pluck('Nama')->toArray();
        $unitText = implode(', ', $units);

        // Format NA status
        $naStatus = ($indikatorKinerja->NA == 'Y') ? 'Non Aktif' : 'Aktif';

        return [
            $rowNumber,
            $renstra->Nama,
            $pilar->Nama,
            $isuStrategis->Nama,
            $programPengembangan->Nama,
            $programRektor->Nama,
            $indikatorKinerja->Nama,
            $indikatorKinerja->Bobot . '%',
            $indikatorKinerja->satuan->Nama,
            'Rp ' . number_format($indikatorKinerja->HargaSatuan, 0, ',', '.'),
            number_format($indikatorKinerja->Jumlah, 0, ',', '.'),
            $metaAnggaranText,
            $unitText,
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
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Bobot
        $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('J2:K' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT); // Harga Satuan & Jumlah
        $sheet->getStyle('N2:N' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // NA
        
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
