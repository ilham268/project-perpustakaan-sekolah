<?php

namespace App\Exports;

use App\Models\GuestBook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class GuestBookExport implements FromCollection, WithMapping, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = GuestBook::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        return $query->latest()->get();
    }

    public function map($guest): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $guest->nama,
            $guest->keperluan,
            Carbon::parse($guest->created_at)->format('d/m/Y'),
            Carbon::parse($guest->created_at)->format('H:i'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set page orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Insert header rows at the top (6 rows: 5 for header + 1 for column headings)
                $sheet->insertNewRowBefore(1, 6);

                // Merge cells for header (5 kolom: A-E)
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                // Set header text
                $sheet->setCellValue('A1', 'LANTERA');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMK NEGERI 1 CERME');
                $sheet->setCellValue('A3', 'LAPORAN DATA BUKU TAMU');

                // Set periode
                if ($this->startDate && $this->endDate) {
                    $periode = 'Periode: ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
                } else {
                    $periode = 'Periode: Semua Data';
                }
                $sheet->setCellValue('A4', $periode);

                // Style header
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F3A93']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 5 is empty

                // Set column headings at row 6
                $headings = ['No', 'Nama', 'Keperluan', 'Tanggal', 'Jam'];
                $col = 'A';
                foreach ($headings as $heading) {
                    $sheet->setCellValue($col . '6', $heading);
                    $col++;
                }

                // Style heading row (5 kolom: A-E)
                $sheet->getStyle('A6:E6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F3A93'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Get the highest row
                $highestRow = $sheet->getHighestRow();

                // Apply borders to data range
                if ($highestRow >= 6) {
                    $dataRange = 'A6:E' . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    // Center align specific columns
                    $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D6:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Wrap text for keperluan column
                    $sheet->getStyle('C6:C' . $highestRow)->getAlignment()->setWrapText(true);
                }

                // Auto size columns
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Set minimum width for keperluan column
                $sheet->getColumnDimension('C')->setWidth(45);
            },
        ];
    }
}
