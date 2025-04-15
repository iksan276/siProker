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
            'indikatorKinerja.programRektor.programPengembangan.isuStrategis.pilar.renstra',
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
            'Indikator Kinerja',
            'Nama',
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

        $indikatorKinerja = $kegiatan->indikatorKinerja;
        $programRektor = $indikatorKinerja->programRektor;
        $programPengembangan = $programRektor->programPengembangan;
        $isuStrategis = $programPengembangan->isuStrategis;
        $pilar = $isuStrategis->pilar;
        $renstra = $pilar->renstra;

        return [
            $rowNumber,
            $renstra->Nama,
            $pilar->Nama,
            $isuStrategis->Nama,
            $programPengembangan->Nama,
            $programRektor->Nama,
            $indikatorKinerja->Nama,
            $kegiatan->Nama,
            Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
            Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
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
        $sheet->getStyle('I2:J' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
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
