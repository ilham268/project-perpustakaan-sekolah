<?php

namespace App\Exports;

use App\Models\PinjamKelas;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PinjamKelasExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    protected $search;
    protected $kelas;
    protected $jurusan;
    protected $kelasJurusanMap;

    public function __construct($search = null, $kelas = null, $jurusan = null)
    {
        $this->search = $search;
        $this->kelas = $kelas;
        $this->jurusan = $jurusan;
        $this->kelasJurusanMap = Kelas::pluck('jurusan', 'nama_kelas');
    }

    public function collection()
    {
        $query = PinjamKelas::with(['user', 'kategori']);

        if ($this->search) {
            $search = $this->search;

            $kelasDariJurusanSearch = Kelas::where('jurusan', 'like', '%' . $search . '%')
                ->pluck('nama_kelas')
                ->toArray();

            $query->where(function ($q) use ($search, $kelasDariJurusanSearch) {
                $q->where('kode_buku', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nomor_identitas', 'like', '%' . $search . '%')
                            ->orWhere('kelas', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                        $kategoriQuery->where('nama_kategori', 'like', '%' . $search . '%')
                            ->orWhere('kelas', 'like', '%' . $search . '%');
                    });

                if (!empty($kelasDariJurusanSearch)) {
                    $q->orWhereHas('user', function ($userQuery) use ($kelasDariJurusanSearch) {
                        $userQuery->whereIn('kelas', $kelasDariJurusanSearch);
                    })
                    ->orWhereHas('kategori', function ($kategoriQuery) use ($kelasDariJurusanSearch) {
                        $kategoriQuery->whereIn('kelas', $kelasDariJurusanSearch);
                    });
                }
            });
        }

        if ($this->kelas) {
            $kelas = $this->kelas;

            $query->where(function ($q) use ($kelas) {
                $q->whereHas('user', function ($userQuery) use ($kelas) {
                    $userQuery->where('kelas', $kelas);
                })
                ->orWhereHas('kategori', function ($kategoriQuery) use ($kelas) {
                    $kategoriQuery->where('kelas', $kelas);
                });
            });
        }

        if ($this->jurusan) {
            $kelasDariJurusan = Kelas::where('jurusan', $this->jurusan)
                ->pluck('nama_kelas')
                ->toArray();

            if (!empty($kelasDariJurusan)) {
                $query->where(function ($q) use ($kelasDariJurusan) {
                    $q->whereHas('user', function ($userQuery) use ($kelasDariJurusan) {
                        $userQuery->whereIn('kelas', $kelasDariJurusan);
                    })
                    ->orWhereHas('kategori', function ($kategoriQuery) use ($kelasDariJurusan) {
                        $kategoriQuery->whereIn('kelas', $kelasDariJurusan);
                    });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Nomor Identitas',
            'Kelas',
            'Jurusan',
            'Judul Buku',
            'Kode Buku',
            'Status',
            'Kondisi',
            'Denda',
            'Tanggal Pinjam',
            'Tanggal Kembali',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $kelasData = $item->user->kelas ?? $item->kategori->kelas ?? '-';
        $jurusanData = $kelasData ? $this->kelasJurusanMap->get($kelasData, '-') : '-';

        $statusText = 'Pending';

        if ($item->status === 'disetujui') {
            $statusText = 'Disetujui';
        } elseif ($item->status === 'dikembalikan') {
            $statusText = 'Dikembalikan';
        } elseif ($item->status === 'denda') {
            $statusText = 'Denda';
        }

        return [
            $no,
            $item->user->name ?? '-',
            $item->user->nomor_identitas ?? '-',
            $kelasData,
            $jurusanData,
            $item->kategori->nama_kategori ?? '-',
            $item->kode_buku ?? '-',
            $statusText,
            $item->kondisi ?? '-',
            'Rp ' . number_format($item->denda ?? 0, 0, ',', '.'),
            $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
            $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('A2:L2');
                $sheet->mergeCells('A3:L3');
                $sheet->mergeCells('A4:L4');

                $sheet->setCellValue('A1', 'LANTERA');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMKN 1 CERME');
                $sheet->setCellValue('A3', 'LAPORAN DATA KELAS PINJAM');

                $filterText = 'Filter: ';

                $filterText .= $this->kelas
                    ? 'Kelas ' . $this->kelas
                    : 'Semua Kelas';

                $filterText .= ' | ';

                $filterText .= $this->jurusan
                    ? 'Jurusan ' . $this->jurusan
                    : 'Semua Jurusan';

                if ($this->search) {
                    $filterText .= ' | Pencarian: ' . $this->search;
                }

                $sheet->setCellValue('A4', $filterText);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '047857'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A6:L6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '047857'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();

                if ($highestRow >= 6) {
                    $sheet->getStyle('A6:L' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle('A6:A' . $highestRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle('D6:E' . $highestRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle('G6:L' . $highestRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(35);
            },
        ];
    }
}