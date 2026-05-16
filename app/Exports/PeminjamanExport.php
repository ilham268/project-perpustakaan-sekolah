<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PeminjamanExport implements FromCollection, WithMapping, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = 'all')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Loan::with(['user', 'bookItem.book.category', 'petugas', 'returnBook']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_pinjam', [$this->startDate, $this->endDate]);
        }

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query->latest()->get();
    }

    public function map($loan): array
    {
        static $no = 0;
        $no++;

        $tanggalTempo = Carbon::parse($loan->tanggal_kembali); // Tempo dari database
        $tanggalKembali = $loan->returnBook ? Carbon::parse($loan->returnBook->tanggal_pengembalian)->format('d/m/Y') : '-';

        // Determine status display
        $statusDisplay = match($loan->status) {
            'pending' => 'Pending',
            'disetujui' => $loan->returnBook ? 'Dikembalikan' : 'Dipinjam',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Dikembalikan',
            default => $loan->status,
        };

        // Check if terlambat
        if ($loan->status === 'disetujui' && $loan->returnBook) {
            $returnDate = Carbon::parse($loan->returnBook->tanggal_pengembalian);
            if ($returnDate->gt($tanggalTempo)) {
                $statusDisplay = 'Terlambat';
            }
        }

        return [
            $no,
            $loan->user->nomor_identitas ?? '-',
            $loan->user->name ?? '-',
            $loan->bookItem->book->judul ?? '-',
            Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y'),
            $tanggalTempo->format('d/m/Y'),
            $tanggalKembali,
            $statusDisplay,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set page orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Insert header rows at the top (6 rows: 5 for header + 1 for column headings)
                $sheet->insertNewRowBefore(1, 6);

                // Merge cells for header
                $sheet->mergeCells('A1:H1'); // LANTERA
                $sheet->mergeCells('A2:H2'); 
                $sheet->mergeCells('A3:H3'); // LAPORAN DATA PEMINJAMAN BUKU
                $sheet->mergeCells('A4:H4'); // Periode

                // Set header text
                $sheet->setCellValue('A1', 'LANTERA');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMK NEGERI 1 CERME');
                $sheet->setCellValue('A3', 'LAPORAN DATA PEMINJAMAN BUKU');

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
                $headings = ['No', 'Nomor Identitas', 'Nama', 'Judul Buku',
                            'Tanggal Peminjaman', 'Tanggal Tempo', 'Tanggal Kembali', 'Status'];
                $col = 'A';
                foreach ($headings as $heading) {
                    $sheet->setCellValue($col . '6', $heading);
                    $col++;
                }

                // Style heading row
                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F3A93'], // Dark blue
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Get the highest row
                $highestRow = $sheet->getHighestRow();

                // Apply borders to data range (including header)
                if ($highestRow >= 6) {
                    $dataRange = 'A6:H' . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    // Center align specific columns (from row 6 to end)
                    $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E6:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Auto size columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Set minimum width for certain columns
                $sheet->getColumnDimension('D')->setWidth(35); // Judul Buku
            },
        ];
    }
}
