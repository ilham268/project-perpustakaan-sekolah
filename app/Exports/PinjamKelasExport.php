<?php

namespace App\Exports;

use App\Models\PinjamKelas;
use App\Models\KategoriPinjam;
use App\Models\User;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PinjamKelasExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    protected $search;
    protected $kelas;
    protected $jurusan;
    protected $kelasJurusanMap;

    protected $prepared = false;
    protected $rows = [];
    protected $bookHeadings = [];

    public function __construct($search = null, $kelas = null, $jurusan = null)
    {
        $this->search = $search;
        $this->kelas = $kelas;
        $this->jurusan = $jurusan;
        $this->kelasJurusanMap = Kelas::pluck('jurusan', 'nama_kelas');
    }

    protected function getKelasFilter()
    {
        if ($this->kelas) {
            return [$this->kelas];
        }

        if ($this->jurusan) {
            return Kelas::where('jurusan', $this->jurusan)
                ->pluck('nama_kelas')
                ->toArray();
        }

        return [];
    }

    protected function prepareData()
    {
        if ($this->prepared) {
            return;
        }

        $kelasFilter = $this->getKelasFilter();

        /*
        |--------------------------------------------------------------------------
        | Ambil daftar kategori buku untuk heading kolom buku
        |--------------------------------------------------------------------------
        */
        $kategoriQuery = KategoriPinjam::query();

        if (!empty($kelasFilter)) {
            $kategoriQuery->whereIn('kelas', $kelasFilter);
        }

        $this->bookHeadings = $kategoriQuery
            ->orderBy('id')
            ->pluck('nama_kategori')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($this->bookHeadings)) {
            $this->bookHeadings = ['BUKU'];
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil daftar siswa
        |--------------------------------------------------------------------------
        */
        $siswaQuery = User::where('role', 'siswa');

        if (!empty($kelasFilter)) {
            $siswaQuery->whereIn('kelas', $kelasFilter);
        }

        if ($this->search) {
            $search = $this->search;

            $siswaQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nomor_identitas', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%');
            });
        }

        $siswas = $siswaQuery
            ->orderBy('kelas')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ambil data pinjam untuk isi kode buku
        |--------------------------------------------------------------------------
        */
        $pinjamQuery = PinjamKelas::with(['user', 'kategori']);

        if (!empty($kelasFilter)) {
            $pinjamQuery->whereHas('user', function ($q) use ($kelasFilter) {
                $q->whereIn('kelas', $kelasFilter);
            });
        }

        $pinjamItems = $pinjamQuery
            ->orderBy('tanggal_pinjam')
            ->get();

        $pinjamMap = [];

        foreach ($pinjamItems as $item) {
            if (!$item->user || !$item->kategori) {
                continue;
            }

            $userId = $item->user->id;
            $bookName = $item->kategori->nama_kategori ?? 'BUKU';

            $pinjamMap[$userId][$bookName][] = $item->kode_buku ?? '';
        }

        /*
        |--------------------------------------------------------------------------
        | Susun data baris siswa
        |--------------------------------------------------------------------------
        */
        $rows = [];
        $no = 1;

        foreach ($siswas as $siswa) {
            $rows[] = [
                'no' => $no++,
                'nisn' => $siswa->nomor_identitas ?? '',
                'nama_siswa' => $siswa->name ?? '',
                'books' => $pinjamMap[$siswa->id] ?? [],
            ];
        }

        $this->rows = $rows;
        $this->prepared = true;
    }

    public function collection()
    {
        $this->prepareData();

        return collect($this->rows);
    }

    public function headings(): array
    {
        $this->prepareData();

        $header1 = [
            'NO',
            'NISN',
            'NAMA',
        ];

        foreach ($this->bookHeadings as $bookName) {
            $header1[] = '';
        }

        $header1[] = 'TANGGAL PEMINJAMAN';
        $header1[] = 'TANGGAL PENGEMBALIAN';
        $header1[] = 'KET.';

        $header2 = [
            '',
            '',
            '',
        ];

        foreach ($this->bookHeadings as $bookName) {
            $header2[] = $bookName;
        }

        $header2[] = '';
        $header2[] = '';
        $header2[] = '';

        return [
            $header1,
            $header2,
        ];
    }

    public function map($row): array
    {
        $data = [
            $row['no'],
            $row['nisn'],
            $row['nama_siswa'],
        ];

        foreach ($this->bookHeadings as $bookName) {
            $data[] = isset($row['books'][$bookName])
                ? implode("\n", array_filter($row['books'][$bookName]))
                : '';
        }

        // Kolom tanggal dan keterangan sengaja dikosongkan.
        $data[] = '';
        $data[] = '';
        $data[] = '';

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->prepareData();

                $sheet = $event->sheet->getDelegate();

                $bookCount = count($this->bookHeadings);
                $totalColumns = 3 + $bookCount + 3;

                $lastColumn = Coordinate::stringFromColumnIndex($totalColumns);

                $firstBookColumnIndex = 4;
                $lastBookColumnIndex = 3 + $bookCount;

                $firstBookColumn = Coordinate::stringFromColumnIndex($firstBookColumnIndex);
                $lastBookColumn = Coordinate::stringFromColumnIndex($lastBookColumnIndex);

                $tanggalPinjamColumn = Coordinate::stringFromColumnIndex($lastBookColumnIndex + 1);
                $tanggalKembaliColumn = Coordinate::stringFromColumnIndex($lastBookColumnIndex + 2);
                $ketColumn = Coordinate::stringFromColumnIndex($lastBookColumnIndex + 3);

                /*
                |--------------------------------------------------------------------------
                | Tambah 8 baris untuk kop sekolah dan identitas kelas
                |--------------------------------------------------------------------------
                */
                $sheet->insertNewRowBefore(1, 8);

                /*
                |--------------------------------------------------------------------------
                | Setup halaman
                |--------------------------------------------------------------------------
                */
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.35);
                $sheet->getPageMargins()->setRight(0.25);
                $sheet->getPageMargins()->setLeft(0.25);
                $sheet->getPageMargins()->setBottom(0.35);

                $sheet->setShowGridlines(true);
                $sheet->getSheetView()->setZoomScale(90);

                /*
                |--------------------------------------------------------------------------
                | Font default seluruh sheet
                |--------------------------------------------------------------------------
                */
                $sheet->getParent()->getDefaultStyle()->getFont()
                    ->setName('Times New Roman')
                    ->setSize(11);

                /*
                |--------------------------------------------------------------------------
                | Kop sekolah
                |--------------------------------------------------------------------------
                */
                for ($row = 1; $row <= 5; $row++) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                }

                $sheet->setCellValue('A1', 'PEMERINTAH PROVINSI JAWA TIMUR');
                $sheet->setCellValue('A2', 'DINAS PENDIDIKAN');
                $sheet->setCellValue('A3', 'SMK NEGERI 1 CERME GRESIK');
                $sheet->setCellValue('A4', 'Jalan Jurit Kec. Cerme, Gresik, Jawa Timur 61171');
                $sheet->setCellValue('A5', 'Telepon (031) 7992471; Fax. (031) 7994569');

                $sheet->getStyle("A1:{$lastColumn}5")->applyFromArray([
                    'font' => [
                        'name' => 'Times New Roman',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                ]);

                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 20,
                    ],
                ]);

                $sheet->getStyle('A4:A5')->applyFromArray([
                    'font' => [
                        'size' => 11,
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Identitas kelas dan jurusan di kiri
                |--------------------------------------------------------------------------
                */
                $kelasText = $this->kelas ?: 'SEMUA KELAS';

                $jurusanText = $this->jurusan;

                if (!$jurusanText && $this->kelas) {
                    $jurusanText = $this->kelasJurusanMap->get($this->kelas, '-');
                }

                if (!$jurusanText) {
                    $jurusanText = 'SEMUA JURUSAN';
                }

                $sheet->setCellValue('B6', 'KELAS');
                $sheet->setCellValue('C6', ':');
                $sheet->setCellValue('D6', $kelasText);

                $sheet->setCellValue('B7', 'JURUSAN');
                $sheet->setCellValue('C7', ':');
                $sheet->setCellValue('D7', $jurusanText);

                $sheet->getStyle('B6:D7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'name' => 'Times New Roman',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('C6:C7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Posisi tabel
                |--------------------------------------------------------------------------
                */
                $headerRow1 = 9;
                $headerRow2 = 10;
                $dataStartRow = 11;

                /*
                |--------------------------------------------------------------------------
                | Merge header tabel
                |--------------------------------------------------------------------------
                */
                $sheet->mergeCells("A{$headerRow1}:A{$headerRow2}");
                $sheet->mergeCells("B{$headerRow1}:B{$headerRow2}");
                $sheet->mergeCells("C{$headerRow1}:C{$headerRow2}");

                if ($bookCount > 0) {
                    $sheet->mergeCells("{$firstBookColumn}{$headerRow1}:{$lastBookColumn}{$headerRow1}");
                    $sheet->setCellValue("{$firstBookColumn}{$headerRow1}", 'BUKU YANG DIPINJAM SISWA');
                }

                $sheet->mergeCells("{$tanggalPinjamColumn}{$headerRow1}:{$tanggalPinjamColumn}{$headerRow2}");
                $sheet->mergeCells("{$tanggalKembaliColumn}{$headerRow1}:{$tanggalKembaliColumn}{$headerRow2}");
                $sheet->mergeCells("{$ketColumn}{$headerRow1}:{$ketColumn}{$headerRow2}");

                /*
                |--------------------------------------------------------------------------
                | Style header tabel
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle("A{$headerRow1}:{$lastColumn}{$headerRow2}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'name' => 'Times New Roman',
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Style isi data
                |--------------------------------------------------------------------------
                */
                $highestRow = $sheet->getHighestRow();

                if ($highestRow >= $dataStartRow) {
                    $sheet->getStyle("A{$dataStartRow}:{$lastColumn}{$highestRow}")->applyFromArray([
                        'font' => [
                            'size' => 11,
                            'name' => 'Times New Roman',
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("A{$dataStartRow}:B{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("D{$dataStartRow}:{$lastColumn}{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("C{$dataStartRow}:C{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                /*
                |--------------------------------------------------------------------------
                | Tinggi baris
                |--------------------------------------------------------------------------
                */
                $sheet->getRowDimension(1)->setRowHeight(23);
                $sheet->getRowDimension(2)->setRowHeight(23);
                $sheet->getRowDimension(3)->setRowHeight(30);
                $sheet->getRowDimension(4)->setRowHeight(19);
                $sheet->getRowDimension(5)->setRowHeight(19);
                $sheet->getRowDimension(6)->setRowHeight(20);
                $sheet->getRowDimension(7)->setRowHeight(20);
                $sheet->getRowDimension(8)->setRowHeight(8);
                $sheet->getRowDimension(9)->setRowHeight(32);
                $sheet->getRowDimension(10)->setRowHeight(30);

                for ($row = $dataStartRow; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(28);
                }

                /*
                |--------------------------------------------------------------------------
                | Lebar kolom
                |--------------------------------------------------------------------------
                */
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(34);

                for ($col = $firstBookColumnIndex; $col <= $lastBookColumnIndex; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($colLetter)->setWidth(13);
                }

                $sheet->getColumnDimension($tanggalPinjamColumn)->setWidth(18);
                $sheet->getColumnDimension($tanggalKembaliColumn)->setWidth(18);
                $sheet->getColumnDimension($ketColumn)->setWidth(12);

                /*
                |--------------------------------------------------------------------------
                | Border luar tabel
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle("A{$headerRow1}:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Print area
                |--------------------------------------------------------------------------
                */
                $sheet->setSelectedCell('A1');
                $sheet->getPageSetup()->setPrintArea("A1:{$lastColumn}{$highestRow}");
            },
        ];
    }
}