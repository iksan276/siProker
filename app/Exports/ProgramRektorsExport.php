<?php

namespace App\Exports;

use App\Models\ProgramRektor;
use App\Models\MataAnggaran;
use App\Models\Unit;
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
            'indikatorKinerja', // Added indikatorKinerja relationship
            'jenisKegiatan',
            'satuan',
            'penanggungJawab',
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
            'Indikator Kinerja', // Added Indikator Kinerja column
            'Nama',
            'Output',
            'Outcome',
            'Jenis Kegiatan',
            'Mata Anggaran',
            'Jumlah Kegiatan',
            'Satuan',
            'Harga Satuan',
            'Total',
            'Penanggung Jawab',
            'Pelaksana',
            'Status'
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

        // Get mata anggaran names
        $mataAnggaranIds = explode(',', $programRektor->MataAnggaranID);
        $mataAnggaranNames = MataAnggaran::whereIn('MataAnggaranID', $mataAnggaranIds)
            ->pluck('Nama')
            ->implode(', ');
        
        // Get pelaksana names
        $pelaksanaIds = explode(',', $programRektor->PelaksanaID);
        $pelaksanaNames = Unit::whereIn('UnitID', $pelaksanaIds)
            ->pluck('Nama')
            ->implode(', ');

        // Format NA status
        $naStatus = ($programRektor->NA == 'Y') ? 'Non Aktif' : 'Aktif';

        return [
            $rowNumber,
            $renstra->Nama,
            $pilar->Nama,
            $isuStrategis->Nama,
            $programPengembangan->Nama,
            $programRektor->indikatorKinerja->Nama, // Added Indikator Kinerja
            $programRektor->Nama,
            $programRektor->Output,
            $programRektor->Outcome,
            $programRektor->jenisKegiatan->Nama,
            $mataAnggaranNames,
            $programRektor->JumlahKegiatan,
            $programRektor->satuan->Nama,
            $programRektor->HargaSatuan,
            $programRektor->Total,
            $programRektor->penanggungJawab->Nama,
            $pelaksanaNames,
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
        $sheet->getStyle('L2:L' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Jumlah Kegiatan
        $sheet->getStyle('M2:M' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Satuan
        $sheet->getStyle('R2:R' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Status
        
        // Format currency columns
        $sheet->getStyle('N2:O' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
        
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
