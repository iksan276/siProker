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

            // Add Kegiatan header row
            $this->data[] = [
                $rowNumber,
                'KEGIATAN',
                $kegiatan->Nama,
                '', '', '', '', '', '', ''
            ];

            // Add Kegiatan details
            $this->data[] = [
                '',
                'Renstra',
                $renstra ? $renstra->Nama : 'N/A',
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Pilar',
                $pilar ? $pilar->Nama : 'N/A',
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Isu Strategis',
                $isuStrategis ? $isuStrategis->Nama : 'N/A',
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Program Pengembangan',
                $programPengembangan ? $programPengembangan->Nama : 'N/A',
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Program Rektor',
                $programRektor ? $programRektor->Nama : 'N/A',
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Tanggal Mulai',
                Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Tanggal Selesai',
                Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
                '', '', '', '', '', '', ''
            ];
            
            $this->data[] = [
                '',
                'Rincian Kegiatan',
                $kegiatan->RincianKegiatan,
                '', '', '', '', '', '', ''
            ];

            // Process Sub Kegiatans
            if ($kegiatan->subKegiatans->count() > 0) {
                foreach ($kegiatan->subKegiatans as $index => $subKegiatan) {
                    $statusSubKegiatan = $this->getStatusLabel($subKegiatan->Status);
                    
                    // Add Sub Kegiatan header
                    $this->data[] = [
                        '',
                        'SUB KEGIATAN ' . ($index + 1),
                        $subKegiatan->Nama,
                        '', '', '', '', '', '', ''
                    ];
                    
                    // Add Sub Kegiatan details
                    $this->data[] = [
                        '',
                        'Jadwal Mulai',
                        Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y'),
                        '', '', '', '', '', '', ''
                    ];
                    
                    $this->data[] = [
                        '',
                        'Jadwal Selesai',
                        Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y'),
                        '', '', '', '', '', '', ''
                    ];
                    
                    $this->data[] = [
                        '',
                        'Status',
                        $statusSubKegiatan,
                        '', '', '', '', '', '', ''
                    ];
                    
                    // Add Catatan for Sub Kegiatan
                    $this->data[] = [
                        '',
                        'Catatan',
                        $subKegiatan->Catatan ?? '-',
                        '', '', '', '', '', '', ''
                    ];

                    // Process RABs for this Sub Kegiatan
                    if ($subKegiatan->rabs->count() > 0) {
                        // Add RAB header
                        $this->data[] = [
                            '',
                            'RAB SUB KEGIATAN',
                            'Komponen',
                            'Volume',
                            'Satuan',
                            'Harga Satuan',
                            'Jumlah',
                            'Status',
                            '',
                            ''
                        ];
                        
                        foreach ($subKegiatan->rabs as $rabIndex => $rab) {
                            $statusRAB = $this->getStatusLabel($rab->Status);
                            
                            $this->data[] = [
                                '',
                                ($rabIndex + 1),
                                $rab->Komponen,
                                number_format($rab->Volume, 0, ',', '.'),
                                $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                                number_format($rab->HargaSatuan, 0, ',', '.'),
                                number_format($rab->Jumlah, 0, ',', '.'),
                                $statusRAB,
                                '',
                                ''
                            ];
                        }
                    }
                }
            }

            // Process direct RABs for this Kegiatan
            $directRabs = $kegiatan->rabs()->whereNull('SubKegiatanID')->get();
            if ($directRabs->count() > 0) {
                // Add RAB header
                $this->data[] = [
                    '',
                    'RAB KEGIATAN',
                    'Komponen',
                    'Volume',
                    'Satuan',
                    'Harga Satuan',
                    'Jumlah',
                    'Status',
                    '',
                    ''
                ];
                
                foreach ($directRabs as $rabIndex => $rab) {
                    $statusRAB = $this->getStatusLabel($rab->Status);
                    
                    $this->data[] = [
                        '',
                        ($rabIndex + 1),
                        $rab->Komponen,
                        number_format($rab->Volume, 0, ',', '.'),
                        $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                        number_format($rab->HargaSatuan, 0, ',', '.'),
                        number_format($rab->Jumlah, 0, ',', '.'),
                        $statusRAB,
                        '',
                        ''
                    ];
                }
            }

            // Add separator row
            $this->data[] = [
                '', '', '', '', '', '', '', '', '', ''
            ];

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
            'Kategori',
            'Informasi',
            'Detail 1',
            'Detail 2',
            'Detail 3',
            'Detail 4',
            'Detail 5',
            'Detail 6',
            'Detail 7'
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
        
        // Style for main section headers (KEGIATAN, SUB KEGIATAN, RAB)
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            if (in_array($cellValue, ['KEGIATAN', 'SUB KEGIATAN 1', 'SUB KEGIATAN 2', 'SUB KEGIATAN 3', 'SUB KEGIATAN 4', 'SUB KEGIATAN 5', 'RAB KEGIATAN', 'RAB SUB KEGIATAN'])) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'D9D9D9',
                        ],
                    ],
                ]);
            }
            
            // Special styling for KEGIATAN headers
            if ($cellValue === 'KEGIATAN') {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'C6E0B4',
                        ],
                    ],
                ]);
            }
            
            // Special styling for SUB KEGIATAN headers
            if (strpos($cellValue, 'SUB KEGIATAN') === 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'DDEBF7',
                        ],
                    ],
                ]);
            }
            
            // Special styling for RAB headers
            if (strpos($cellValue, 'RAB') === 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FCE4D6',
                        ],
                    ],
                ]);
            }
        }
        
        // Center the 'No' column
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
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
        
        // Freeze the header row and first column
        $sheet->freezePane('B2');
        
        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(20);  // Kategori
        $sheet->getColumnDimension('C')->setWidth(40);  // Informasi
        $sheet->getColumnDimension('D')->setWidth(15);  // Detail 1
        $sheet->getColumnDimension('E')->setWidth(15);  // Detail 2
        $sheet->getColumnDimension('F')->setWidth(15);  // Detail 3
        $sheet->getColumnDimension('G')->setWidth(15);  // Detail 4
        $sheet->getColumnDimension('H')->setWidth(15);  // Detail 5
        $sheet->getColumnDimension('I')->setWidth(15);  // Detail 6
        $sheet->getColumnDimension('J')->setWidth(15);  // Detail 7
        
        // Right align numeric columns in RAB sections
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            // If this is a RAB item row (where B column is a number)
            if (is_numeric($cellValue) && $row > 1) {
                // Right align volume, harga satuan, and jumlah columns
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
        }
        
        // Center align status columns
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($sheet->getCell('B' . $row)->getValue() === 'Status' || 
                $sheet->getCell('H' . $row)->getValue() === 'Status') {
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Add alternating row colors for better readability
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            // Skip rows that already have specific styling
            if (in_array($cellValue, ['KEGIATAN', 'SUB KEGIATAN 1', 'SUB KEGIATAN 2', 'SUB KEGIATAN 3', 'SUB KEGIATAN 4', 'SUB KEGIATAN 5', 'RAB KEGIATAN', 'RAB SUB KEGIATAN'])) {
                continue;
            }
            
            // Apply light alternating colors to regular rows
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F9F9F9',
                        ],
                    ],
                ]);
            }
        }
        
        // Bold the category labels in column B
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            if (!is_numeric($cellValue) && $cellValue != '' && 
                !in_array($cellValue, ['KEGIATAN', 'SUB KEGIATAN 1', 'SUB KEGIATAN 2', 'SUB KEGIATAN 3', 'SUB KEGIATAN 4', 'SUB KEGIATAN 5', 'RAB KEGIATAN', 'RAB SUB KEGIATAN'])) {
                $sheet->getStyle('B' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            }
        }
        
        // Special styling for Catatan rows
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($sheet->getCell('B' . $row)->getValue() === 'Catatan') {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'font' => [
                        'italic' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFF2CC',
                        ],
                    ],
                ]);
            }
        }
    }
}

