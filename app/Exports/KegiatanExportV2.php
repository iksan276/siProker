<?php

namespace App\Exports;
use Carbon\Carbon;
use App\Models\Kegiatan;
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

class KegiatanExportV2 implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Kegiatan::with('programRektor')->get();
    }

    public function headings(): array
    {
        return [
            'ProgramRektorID',
            'Nama',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Tanggal Pencairan',
            'Rincian',
            'Feedback',
            'Status'
        ];
    }

    public function map($kegiatan): array
    {
        return [
            $kegiatan->ProgramRektorID,
            $kegiatan->Nama,
            Carbon::parse($kegiatan->TanggalMulai)->format('Y-m-d'),
            Carbon::parse($kegiatan->TanggalSelesai)->format('Y-m-d'),
            Carbon::parse($kegiatan->TanggalPencairan)->format('Y-m-d'),
            $kegiatan->RincianKegiatan,
            $kegiatan->Feedback,
            $kegiatan->Status
        ];
    }

    public function title(): string
    {
        return 'Kegiatan';
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
        $sheet->getStyle('C2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal columns
        $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
        
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
