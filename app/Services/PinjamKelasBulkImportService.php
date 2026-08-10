<?php

namespace App\Services;

use App\Models\BookItem;
use App\Models\PinjamKelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PinjamKelasBulkImportService
{
    protected int $successCount = 0;
    protected array $errors = [];

    /**
     * Daftar semua sheet (jurusan) yang terdeteksi di file, lengkap dengan
     * kelas, jurusan, dan jumlah siswa -- dipakai untuk halaman "Pilih Jurusan".
     */
    public function listSheetsInfo(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheets = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $structure = $this->detectStructure($sheetName, $sheet);

            if (!$structure) {
                continue;
            }

            $jumlahSiswa = 0;

            foreach ($structure['rows'] as $rowIndex => $row) {
                if ($rowIndex < $structure['dataStart']) {
                    continue;
                }

                $nisn = trim((string) ($row[$structure['colNisn']] ?? ''));

                if ($nisn !== '' && preg_match('/^\d+$/', $nisn)) {
                    $jumlahSiswa++;
                }
            }

            $sheets[] = [
                'sheet_name' => $sheetName,
                'kelas' => $structure['kelasLevel'],
                'jurusan' => $structure['jurusan'],
                'jumlah_siswa' => $jumlahSiswa,
                'jumlah_mapel' => count($structure['subjectColumns']),
            ];
        }

        return $sheets;
    }

    /**
     * Ambil label mapel untuk SATU sheet saja.
     */
    public function extractSubjectLabelsForSheet(string $filePath, string $onlySheetName): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $labels = [];

        $sheet = $spreadsheet->getSheetByName($onlySheetName);

        if (!$sheet) {
            return [];
        }

        $structure = $this->detectStructure($onlySheetName, $sheet);

        if (!$structure) {
            return [];
        }

        foreach ($structure['subjectColumns'] as $label) {
            $key = $this->buildKey($label, $structure['kelasLevel'], null);

            if (!isset($labels[$key])) {
                $labels[$key] = [
                    'label' => $label,
                    'kelas' => $structure['kelasLevel'],
                    'jurusan' => null,
                    'key' => $key,
                ];
            }
        }

        return array_values($labels);
    }

    /**
     * Import hanya SATU sheet saja (jurusan yang dipilih admin).
     * $subjectBookMap: key mapel (label|kelas|jurusan) => ARRAY of book_id.
     */
    public function importSheet(string $filePath, string $onlySheetName, array $subjectBookMap): void
    {
        $spreadsheet = IOFactory::load($filePath);

        $sheet = $spreadsheet->getSheetByName($onlySheetName);

        if (!$sheet) {
            $this->errors[] = "Sheet '{$onlySheetName}' tidak ditemukan di file.";
            return;
        }

        $this->processSheet($onlySheetName, $sheet, $subjectBookMap);
    }

    protected function processSheet(string $sheetName, $sheet, array $subjectBookMap): void
    {
        $structure = $this->detectStructure($sheetName, $sheet);

        if (!$structure) {
            return;
        }

        $rows = $structure['rows'];
        $colNisn = $structure['colNisn'];
        $subjectColumns = $structure['subjectColumns'];
        $kelasLevel = $structure['kelasLevel'];
        $jurusan = $structure['jurusan'];
        $dataStart = $structure['dataStart'];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < $dataStart) {
                continue;
            }

            $nisnRaw = trim((string) ($row[$colNisn] ?? ''));

            if ($nisnRaw === '' || !preg_match('/^\d+$/', $nisnRaw)) {
                continue;
            }

            $user = $this->findUserByNisn($nisnRaw);

            if (!$user) {
                $this->errors[] = "{$sheetName} baris {$rowIndex}: Siswa NISN {$nisnRaw} tidak ditemukan.";
                continue;
            }

            foreach ($subjectColumns as $col => $label) {
                $rawValue = $row[$col] ?? null;

                if ($rawValue === null || trim((string) $rawValue) === '') {
                    continue;
                }

                $kode = $this->normalizeCode($rawValue);
                $this->processEntry($sheetName, $rowIndex, $user, $label, $kode, $kelasLevel, $jurusan, $subjectBookMap);
            }
        }
    }

    protected function detectStructure(string $sheetName, $sheet): ?array
    {
        $rows = $sheet->toArray(null, true, true, true);

        $headerRow = null;

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $value) {
                if (is_string($value) && strtoupper(trim($value)) === 'NISN') {
                    $headerRow = $rowIndex;
                    break 2;
                }
            }
        }

        if (!$headerRow) {
            return null;
        }

        $headerRowData = $rows[$headerRow];
        $subjectRowData = $rows[$headerRow + 1] ?? [];

        $colNisn = null;
        $colStart = null;
        $colEnd = null;

        foreach ($headerRowData as $col => $value) {
            $val = strtoupper(trim((string) $value));

            if ($val === 'NISN') {
                $colNisn = $col;
            }

            if (str_contains($val, 'BUKU YANG DIPINJAM') && $colStart === null) {
                $colStart = $col;
            }

            if (str_contains($val, 'TANGGAL PEMINJAMAN') && $colEnd === null) {
                $colEnd = $this->shiftColumn($col, -1);
            }
        }

        if (!$colNisn || !$colStart) {
            return null;
        }

        if (!$colEnd) {
            $colEnd = $this->shiftColumn($colStart, 9);
        }

        $upperSheet = strtoupper($sheetName);
        preg_match('/\b(XII|XI|X)\b\s*([A-Z]+)?/', $upperSheet, $m);
        $kelasLevel = $m[1] ?? null;
        $jurusan = trim($m[2] ?? '') ?: null;

        $subjectColumns = [];

        foreach ($this->columnRange($colStart, $colEnd) as $col) {
            $label = strtoupper(trim((string) ($subjectRowData[$col] ?? '')));

            if ($label !== '') {
                $subjectColumns[$col] = $label;
            }
        }

        if (empty($subjectColumns)) {
            return null;
        }

        return [
            'rows' => $rows,
            'colNisn' => $colNisn,
            'subjectColumns' => $subjectColumns,
            'kelasLevel' => $kelasLevel,
            'jurusan' => $jurusan,
            'dataStart' => $headerRow + 2,
        ];
    }

    protected function findUserByNisn(string $nisnRaw): ?User
    {
        $variants = array_unique(array_filter([
            $nisnRaw,
            ltrim($nisnRaw, '0'),
            str_pad(ltrim($nisnRaw, '0'), 10, '0', STR_PAD_LEFT),
        ]));

        return User::where('role', 'siswa')
            ->whereIn('nomor_identitas', $variants)
            ->first();
    }

    protected function processEntry(string $sheetName, int $rowIndex, User $user, string $label, string $kode, ?string $kelasLevel, ?string $jurusan, array $subjectBookMap): void
    {
        $key = $this->buildKey($label, $kelasLevel, null);
        $bookIds = $subjectBookMap[$key] ?? null;

        if (empty($bookIds)) {
            $this->errors[] = "{$sheetName} baris {$rowIndex}: Mapel '{$label}' (Kelas {$kelasLevel}) belum dipetakan ke Buku Paket.";
            return;
        }

        try {
            DB::beginTransaction();

            $bookItem = BookItem::whereIn('book_id', $bookIds)
                ->whereRaw('UPPER(kode_buku) = ?', [$kode])
                ->lockForUpdate()
                ->first();

            if (!$bookItem) {
                $bookItem = BookItem::whereIn('book_id', $bookIds)
                    ->where(function ($q) {
                        $q->whereNull('kode_buku')->orWhere('kode_buku', '');
                    })
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (!$bookItem) {
                    DB::rollBack();
                    $this->errors[] = "{$sheetName} baris {$rowIndex}: Slot eksemplar buku '{$label}' sudah penuh (seluruh salinan judul ini), kode {$kode} tidak bisa ditambahkan.";
                    return;
                }

                $bookItem->update([
                    'kode_buku' => $kode,
                    'status' => 'borrowed',
                ]);
            } else {
                if ($bookItem->status !== 'available') {
                    DB::rollBack();
                    $this->errors[] = "{$sheetName} baris {$rowIndex}: Kode buku {$kode} sudah dipinjam/tidak tersedia.";
                    return;
                }

                $bookItem->update(['status' => 'borrowed']);
            }

            $sedangDipinjam = PinjamKelas::whereIn('book_id', $bookIds)
                ->where('kode_buku', $bookItem->kode_buku)
                ->whereIn('status', ['pending', 'disetujui'])
                ->lockForUpdate()
                ->exists();

            if ($sedangDipinjam) {
                DB::rollBack();
                $this->errors[] = "{$sheetName} baris {$rowIndex}: Kode buku {$kode} masih dalam proses peminjaman.";
                return;
            }

            PinjamKelas::create([
                'book_id' => $bookItem->book_id,
                'user_id' => $user->id,
                'kode_buku' => $bookItem->kode_buku,
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays(7),
                'status' => 'disetujui',
            ]);

            DB::commit();
            $this->successCount++;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errors[] = "{$sheetName} baris {$rowIndex}: Gagal diproses ({$e->getMessage()}).";
        }
    }

    protected function buildKey(string $label, ?string $kelas, ?string $jurusan): string
    {
        return $label . '|' . ($kelas ?? '?') . '|' . ($jurusan ?? '');
    }

    protected function normalizeCode($value): string
    {
        // Sel Excel yang diformat sebagai angka murni (int/float) sampai ke PHP
        // sebagai tipe int/float, bukan string -- leading zero-nya sudah hilang
        // dari sisi Excel sendiri, jadi di sini kita hanya rapikan representasinya
        // (buang ".0" pada float seperti 7.0 -> "7").
        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return (string) (int) $value;
        }

        // Untuk semua sel bertipe string (termasuk "007", "12A", "A12B", dst),
        // kita PERTAHANKAN apa adanya (cuma trim + uppercase), jangan dikonversi
        // ke angka -- supaya leading zero dan kode alfanumerik tidak rusak.
        return strtoupper(trim((string) $value));
    }

    protected function shiftColumn(string $col, int $offset): string
    {
        $index = Coordinate::columnIndexFromString($col) + $offset;

        return Coordinate::stringFromColumnIndex(max($index, 1));
    }

    protected function columnRange(string $start, string $end): array
    {
        $startIndex = Coordinate::columnIndexFromString($start);
        $endIndex = Coordinate::columnIndexFromString($end);

        $cols = [];

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $cols[] = Coordinate::stringFromColumnIndex($i);
        }

        return $cols;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}