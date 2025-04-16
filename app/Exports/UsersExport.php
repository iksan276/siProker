<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->users) {
            return $this->users;
        }
        
        return User::all();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Email',
            'Created At',
            'Updated At'
        ];
    }

    /**
    * @param mixed $user
    * @return array
    */
    public function map($user): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $user->name,
            $user->email,
            $user->created_at ? $user->created_at->format('d-m-Y H:i:s') : '-',
            $user->updated_at ? $user->updated_at->format('d-m-Y H:i:s') : '-'
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
        $sheet->getStyle('D2:E' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Dates
        
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
