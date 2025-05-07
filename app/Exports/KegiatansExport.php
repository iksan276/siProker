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
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;

class KegiatansExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $kegiatans;
    protected $data = [];

    public function __construct($kegiatans = null)
    {
        $this->kegiatans = $kegiatans;
        $this->processData();
    }

    /**
     * Process the data to create rows for the export
     */
    private function processData()
    {
        $rowNumber = 1;
        $kegiatans = $this->kegiatans ?: Kegiatan::with([
            'programRektor', 
            'programRektor.programPengembangan', 
            'programRektor.programPengembangan.isuStrategis', 
            'programRektor.programPengembangan.isuStrategis.pilar',
            'programRektor.programPengembangan.isuStrategis.pilar.renstra',
            'subKegiatans.rabs.satuanRelation',
            'rabs.satuanRelation'
        ])->get();

        foreach ($kegiatans as $kegiatan) {
            $programRektor = $kegiatan->programRektor;
            $programPengembangan = $programRektor ? $programRektor->programPengembangan : null;
            $isuStrategis = $programPengembangan ? $programPengembangan->isuStrategis : null;
            $pilar = $isuStrategis ? $isuStrategis->pilar : null;
            $renstra = $pilar ? $pilar->renstra : null;

            // Base kegiatan information
            $kegiatanInfo = [
                $rowNumber,
                $renstra ? $renstra->Nama : 'N/A',
                $pilar ? $pilar->Nama : 'N/A',
                $isuStrategis ? $isuStrategis->Nama : 'N/A',
                $programPengembangan ? $programPengembangan->Nama : 'N/A',
                $programRektor ? $programRektor->Nama : 'N/A',
                $kegiatan->Nama,
                Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
                Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
                $kegiatan->RincianKegiatan,
            ];

            $hasSubItems = false;

            // Process Sub Kegiatans
            if ($kegiatan->subKegiatans->count() > 0) {
                foreach ($kegiatan->subKegiatans as $subKegiatan) {
                    $statusSubKegiatan = $this->getStatusLabel($subKegiatan->Status);

                    // Process RABs for this Sub Kegiatan
                    if ($subKegiatan->rabs->count() > 0) {
                        foreach ($subKegiatan->rabs as $rab) {
                            $statusRAB = $this->getStatusLabel($rab->Status);

                            $this->data[] = array_merge($kegiatanInfo, [
                                $subKegiatan->Nama,
                                Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y'),
                                Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y'),
                                $statusSubKegiatan,
                                $rab->Komponen,
                                number_format($rab->Volume, 0, ',', '.'),
                                $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                                number_format($rab->HargaSatuan, 0, ',', '.'),
                                number_format($rab->Jumlah, 0, ',', '.'),
                                $statusRAB
                            ]);

                            $hasSubItems = true;
                            // Reset kegiatan info for subsequent rows to avoid duplication
                            $kegiatanInfo = array_fill(0, 10, '');
                            $kegiatanInfo[0] = '';  // No column
                        }
                    } else {
                        // Sub Kegiatan without RABs
                        $this->data[] = array_merge($kegiatanInfo, [
                            $subKegiatan->Nama,
                            Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y'),
                            Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y'),
                            $statusSubKegiatan,
                            '', '', '', '', '', ''  // Empty RAB fields
                        ]);

                        $hasSubItems = true;
                        // Reset kegiatan info for subsequent rows
                        $kegiatanInfo = array_fill(0, 10, '');
                        $kegiatanInfo[0] = '';  // No column
                    }
                }
            }

            // Process direct RABs for this Kegiatan
            $directRabs = $kegiatan->rabs()->whereNull('SubKegiatanID')->get();
            if ($directRabs->count() > 0) {
                foreach ($directRabs as $rab) {
                    $statusRAB = $this->getStatusLabel($rab->Status);

                    $this->data[] = array_merge($kegiatanInfo, [
                        '', '', '', '',  // Empty sub-kegiatan fields
                        $rab->Komponen,
                        number_format($rab->Volume, 0, ',', '.'),
                        $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                        number_format($rab->HargaSatuan, 0, ',', '.'),
                        number_format($rab->Jumlah, 0, ',', '.'),
                        $statusRAB
                    ]);

                    $hasSubItems = true;
                    // Reset kegiatan info for subsequent rows
                    $kegiatanInfo = array_fill(0, 10, '');
                    $kegiatanInfo[0] = '';  // No column
                }
            }

            // If no sub-kegiatans and no direct RABs, just add the kegiatan info
            if (!$hasSubItems) {
                $this->data[] = array_merge($kegiatanInfo, ['', '', '', '', '', '', '', '', '', '']);
            }

            $rowNumber++;
        }
    }

    /**
     * Get status label text
     */
    private function getStatusLabel($status)
    {
        switch ($status) {
            case 'N': return 'Menunggu';
            case 'Y': return 'Disetujui';
            case 'T': return 'Ditolak';
            case 'R': return 'Revisi';
            default: return 'Unknown';
        }
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
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
            'Rincian Kegiatan',
            'Sub Kegiatan',
            'Jadwal Mulai Sub Kegiatan',
            'Jadwal Selesai Sub Kegiatan',
            'Status Sub Kegiatan',
            'Komponen RAB',
            'Volume',
            'Satuan',
            'Harga Satuan',
            'Jumlah',
            'Status RAB'
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
        $sheet->getStyle('L2:M' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Center the status columns
        $sheet->getStyle('N2:N' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('T2:T' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Right align numeric columns
        $sheet->getStyle('P2:P' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('R2:S' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
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
        
        // Group related columns with different background colors
        $sheet->getStyle('A1:J' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F2F2F2',
                ],
            ],
        ]);
        
        $sheet->getStyle('K1:N' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E6F2FF',
                ],
            ],
        ]);
        
        $sheet->getStyle('O1:T' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFF2E6',
                ],
            ],
        ]);
        
        // Freeze the header row and first column
        $sheet->freezePane('B2');
        
        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(15); // Renstra
        $sheet->getColumnDimension('C')->setWidth(15); // Pilar
        $sheet->getColumnDimension('D')->setWidth(20); // Isu Strategis
        $sheet->getColumnDimension('E')->setWidth(20); // Program Pengembangan
        $sheet->getColumnDimension('F')->setWidth(20); // Program Rektor
        $sheet->getColumnDimension('G')->setWidth(25); // Nama Kegiatan
        $sheet->getColumnDimension('H')->setWidth(12); // Tanggal Mulai
        $sheet->getColumnDimension('I')->setWidth(12); // Tanggal Selesai
        $sheet->getColumnDimension('J')->setWidth(30); // Rincian Kegiatan
        $sheet->getColumnDimension('K')->setWidth(25); // Sub Kegiatan
        $sheet->getColumnDimension('L')->setWidth(12); // Jadwal Mulai Sub Kegiatan
        $sheet->getColumnDimension('M')->setWidth(12); // Jadwal Selesai Sub Kegiatan
        $sheet->getColumnDimension('N')->setWidth(15); // Status Sub Kegiatan
        $sheet->getColumnDimension('O')->setWidth(30); // Komponen RAB
        $sheet->getColumnDimension('P')->setWidth(10); // Volume
        $sheet->getColumnDimension('Q')->setWidth(10); // Satuan
        $sheet->getColumnDimension('R')->setWidth(15); // Harga Satuan
        $sheet->getColumnDimension('S')->setWidth(15); // Jumlah
        $sheet->getColumnDimension('T')->setWidth(15); // Status RAB
    }
}
