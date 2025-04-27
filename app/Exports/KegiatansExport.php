<?php

namespace App\Exports;

use App\Models\Kegiatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class KegiatansExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $kegiatans;

    public function __construct($kegiatans = null)
    {
        $this->kegiatans = $kegiatans;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->kegiatans) {
            return $this->kegiatans;
        }
        
        return Kegiatan::with([
            'programRektor', 
            'programRektor.programPengembangan', 
            'programRektor.programPengembangan.isuStrategis', 
            'programRektor.programPengembangan.isuStrategis.pilar',
            'programRektor.programPengembangan.isuStrategis.pilar.renstra'
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
            'Nama Kegiatan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Rincian Kegiatan'
        ];
    }

    /**
    * @param mixed $kegiatan
    * @return array
    */
    public function map($kegiatan): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $programRektor = $kegiatan->programRektor;
        $programPengembangan = $programRektor ? $programRektor->programPengembangan : null;
        $isuStrategis = $programPengembangan ? $programPengembangan->isuStrategis : null;
        $pilar = $isuStrategis ? $isuStrategis->pilar : null;
        $renstra = $pilar ? $pilar->renstra : null;

        return [
            $rowNumber,
            $renstra ? $renstra->Nama : 'N/A',
            $pilar ? $pilar->Nama : 'N/A',
            $isuStrategis ? $isuStrategis->Nama : 'N/A',
            $programPengembangan ? $programPengembangan->Nama : 'N/A',
            $programRektor ? $programRektor->Nama : 'N/A',
            $kegiatan->Nama,
            Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y H:i'),
            Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y H:i'),
            $kegiatan->RincianKegiatan
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
        
        // Center the 'No' column
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Center the date columns
        $sheet->getStyle('H2:I' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
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
