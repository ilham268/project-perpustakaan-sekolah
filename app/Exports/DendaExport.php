<?php

namespace App\Exports;

use App\Models\ReturnBook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class DendaExport implements FromCollection, WithMapping, WithEvents
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
        $query = ReturnBook::with(['loan.user', 'loan.bookItem.book.category', 'loan.petugas'])
            ->where('denda', '>', 0);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_pengembalian', [$this->startDate, $this->endDate]);
        }

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query->latest()->get();
    }

    public function map($return): array
    {
        static $no = 0;
        $no++;

        $loan = $return->loan;
        $tanggalTempo = Carbon::parse($loan->tanggal_kembali); // Tempo dari database loans
        $tanggalKembali = Carbon::parse($return->tanggal_pengembalian);
        
        // Calculate days late (hitung selisih hari jika telat)
        $daysLate = 0;
        if ($tanggalKembali->gt($tanggalTempo)) {
            $daysLate = $tanggalTempo->diffInDays($tanggalKembali);
        }

        // Determine jenis denda dan keterlambatan
        $jenisDenda = '-';
        $keterlambatanDisplay = '-';
        
        if ($return->kondisi === 'rusak') {
            $jenisDenda = 'Rusak';
            if ($daysLate > 0) {
                $keterlambatanDisplay = $daysLate . ' Hari';
            }
        } elseif ($return->kondisi === 'hilang') {
            $jenisDenda = 'Hilang';
            if ($daysLate > 0) {
                $keterlambatanDisplay = $daysLate . ' Hari';
            }
        } elseif ($daysLate > 0) {
            // Jika ada keterlambatan (terlepas dari kondisi)
            $jenisDenda = 'Keterlambatan';
            $keterlambatanDisplay = $daysLate . ' Hari';
        } elseif ($return->kondisi === 'baik' && $return->denda > 0) {
            // Jika ada denda tapi tidak terlambat
            $jenisDenda = 'Lainnya';
        }

        return [
            $no,
            $loan->user->name ?? '-',
            $loan->user->nomor_identitas ?? '-',
            $loan->bookItem->book->judul ?? '-',
            $loan->bookItem->kode_buku ?? '-',
            Carbon::parse($loan->tanggal_peminjaman)->format('d/m/Y'),
            $tanggalTempo->format('d/m/Y'),
            $jenisDenda,
            $keterlambatanDisplay,
            'Rp. ' . number_format($return->denda, 0, ',', '.'),
            $return->status === 'paid' ? 'Lunas' : 'Pending',
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
                
                // Merge cells for header (11 kolom: A-K)
                $sheet->mergeCells('A1:K1'); // LANTERA
                $sheet->mergeCells('A2:K2'); // 
                $sheet->mergeCells('A3:K3'); // LAPORAN DATA REKAP DENDA
                $sheet->mergeCells('A4:K4'); // Periode

                // Set header text
                $sheet->setCellValue('A1', 'LANTERA');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMKN 1 CERME');
                $sheet->setCellValue('A3', 'LAPORAN DATA REKAP DENDA');
                
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
                
                // Set column headings at row 6 (tanpa Tanggal Kembali)
                $headings = ['No', 'Nama', 'Nomor Identitas', 'Judul Buku', 'Kode Buku', 
                            'Tanggal Peminjaman', 'Tempo', 'Jenis Denda', 
                            'Jumlah Keterlambatan', 'Jumlah Denda', 'Status'];
                $col = 'A';
                foreach ($headings as $heading) {
                    $sheet->setCellValue($col . '6', $heading);
                    $col++;
                }

                // Style heading row (11 kolom: A-K)
                $sheet->getStyle('A6:K6')->applyFromArray([
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
                
                // Apply borders to data range (including header, 11 kolom)
                if ($highestRow >= 6) {
                    $dataRange = 'A6:K' . $highestRow;
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
                    $sheet->getStyle('F6:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('I6:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K6:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Auto size columns (11 kolom: A-K)
                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Set minimum width for certain columns
                $sheet->getColumnDimension('D')->setWidth(35); // Judul Buku
            },
        ];
    }
}
