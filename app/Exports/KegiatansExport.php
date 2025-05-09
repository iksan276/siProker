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
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];

            // Add Kegiatan details
            $this->data[] = [
                '',
                'Renstra',
                $renstra ? $renstra->Nama : 'N/A',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Pilar',
                $pilar ? $pilar->Nama : 'N/A',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Isu Strategis',
                $isuStrategis ? $isuStrategis->Nama : 'N/A',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Program Pengembangan',
                $programPengembangan ? $programPengembangan->Nama : 'N/A',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Program Rektor',
                $programRektor ? $programRektor->Nama : 'N/A',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Tanggal Mulai',
                Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Tanggal Selesai',
                Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            $this->data[] = [
                '',
                'Rincian Kegiatan',
                $kegiatan->RincianKegiatan,
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];
            
            // Add Feedback for Kegiatan
            $this->data[] = [
                '',
                'Feedback Kegiatan',
                $kegiatan->Feedback ?? '-',
                '', '', '', '', '', '', '',
                '' // Feedback column
            ];

            // Process direct RABs for this Kegiatan
            $directRabs = $kegiatan->rabs()->whereNull('SubKegiatanID')->get();
            if ($directRabs->count() > 0) {
                // Add RAB header
                $this->data[] = [
                    '',
                    'RAB KEGIATAN',
                    '',
                    '', '', '', '', '', '', '',
                    '' // Feedback column
                ];
                
                foreach ($directRabs as $rabIndex => $rab) {
                    $statusRAB = $this->getStatusLabel($rab->Status);
                    
                    // Add RAB item number
                    $this->data[] = [
                        '',
                        'Item ' . ($rabIndex + 1),
                        '',
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    // Add RAB details in vertical format
                    $this->data[] = [
                        '',
                        'Komponen',
                        $rab->Komponen,
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Volume',
                        number_format($rab->Volume, 0, ',', '.'),
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Satuan',
                        $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Harga Satuan',
                        number_format($rab->HargaSatuan, 0, ',', '.'),
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Jumlah',
                        number_format($rab->Jumlah, 0, ',', '.'),
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Status',
                        $statusRAB,
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Feedback RAB',
                        $rab->Feedback ?? '-',
                        '', '', '', '', '', '', '',
                        $rab->Feedback ?? '-' // Feedback column
                    ];
                }
            }

            // Process Sub Kegiatans
            if ($kegiatan->subKegiatans->count() > 0) {
                foreach ($kegiatan->subKegiatans as $index => $subKegiatan) {
                    $statusSubKegiatan = $this->getStatusLabel($subKegiatan->Status);
                    
                    // Add Sub Kegiatan header
                    $this->data[] = [
                        '',
                        'SUB KEGIATAN ' . ($index + 1),
                        $subKegiatan->Nama,
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    // Add Sub Kegiatan details
                    $this->data[] = [
                        '',
                        'Jadwal Mulai',
                        Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y'),
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Jadwal Selesai',
                        Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y'),
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    $this->data[] = [
                        '',
                        'Status',
                        $statusSubKegiatan,
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    // Add Catatan for Sub Kegiatan
                    $this->data[] = [
                        '',
                        'Catatan',
                        $subKegiatan->Catatan ?? '-',
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];
                    
                    // Add Feedback for Sub Kegiatan
                    $this->data[] = [
                        '',
                        'Feedback Sub Kegiatan',
                        $subKegiatan->Feedback ?? '-',
                        '', '', '', '', '', '', '',
                        '' // Feedback column
                    ];

                    // Process RABs for this Sub Kegiatan
                    if ($subKegiatan->rabs->count() > 0) {
                        // Add RAB header
                        $this->data[] = [
                            '',
                            'RAB SUB KEGIATAN',
                            '',
                            '', '', '', '', '', '', '',
                            '' // Feedback column
                        ];
                        
                        foreach ($subKegiatan->rabs as $rabIndex => $rab) {
                            $statusRAB = $this->getStatusLabel($rab->Status);
                            
                            // Add RAB item number
                            $this->data[] = [
                                '',
                                'Item ' . ($rabIndex + 1),
                                '',
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            // Add RAB details in vertical format
                            $this->data[] = [
                                '',
                                'Komponen',
                                $rab->Komponen,
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Volume',
                                number_format($rab->Volume, 0, ',', '.'),
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Satuan',
                                $rab->satuanRelation ? $rab->satuanRelation->Nama : '-',
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Harga Satuan',
                                number_format($rab->HargaSatuan, 0, ',', '.'),
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Jumlah',
                                number_format($rab->Jumlah, 0, ',', '.'),
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Status',
                                $statusRAB,
                                '', '', '', '', '', '', '',
                                '' // Feedback column
                            ];
                            
                            $this->data[] = [
                                '',
                                'Feedback RAB',
                                $rab->Feedback ?? '-',
                                '', '', '', '', '', '', '',
                                $rab->Feedback ?? '-' // Feedback column
                            ];
                        }
                    }
                }
            }

            // Add separator row
            $this->data[] = [
                '', '', '', '', '', '', '', '', '', '', ''
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
            'Detail 7',
            'Feedback' // Added Feedback column to headings
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
            
            // Special styling for RAB item headers
            if (strpos($cellValue, 'Item ') === 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
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
        $sheet->getColumnDimension('K')->setWidth(30);  // Feedback - wider column for feedback
        
        // Add alternating row colors for better readability
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            // Skip rows that already have specific styling
            if (in_array($cellValue, ['KEGIATAN', 'SUB KEGIATAN 1', 'SUB KEGIATAN 2', 'SUB KEGIATAN 3', 'SUB KEGIATAN 4', 'SUB KEGIATAN 5', 'RAB KEGIATAN', 'RAB SUB KEGIATAN']) || 
                strpos($cellValue, 'Item ') === 0) {
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
                !in_array($cellValue, ['KEGIATAN', 'SUB KEGIATAN 1', 'SUB KEGIATAN 2', 'SUB KEGIATAN 3', 'SUB KEGIATAN 4', 'SUB KEGIATAN 5', 'RAB KEGIATAN', 'RAB SUB KEGIATAN']) &&
                strpos($cellValue, 'Item ') !== 0) {
                $sheet->getStyle('B' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            }
        }
        
        // Special styling for Catatan and Feedback rows
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
            
            // Special styling for Feedback rows
            if (strpos($sheet->getCell('B' . $row)->getValue(), 'Feedback') === 0) {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'font' => [
                        'italic' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFE6E6', // Light red for feedback
                        ],
                    ],
                ]);
            }
        }
        
        // Special styling for numeric values in RAB sections
        for ($row = 2; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            if (in_array($cellValue, ['Volume', 'Harga Satuan', 'Jumlah'])) {
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
            
            if ($cellValue === 'Status') {
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
    }
}
  
     