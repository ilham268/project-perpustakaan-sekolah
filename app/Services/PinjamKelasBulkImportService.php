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
     * Kumpulkan semua label mapel unik dari file (tanpa proses simpan apapun).
     * Dipanggil setiap kali import, hasilnya SELALU ditanyakan ke admin.
     */
    public function extractSubjectLabels(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $labels = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $structure = $this->detectStructure($sheetName, $sheet);

            if (!$structure) {
                continue;
            }

            foreach ($structure['subjectColumns'] as $label) {
                $key = $label . '|' . ($structure['kelasLevel'] ?? '?');
                $labels[$key] = [
                    'label' => $label,
                    'kelas' => $structure['kelasLevel'],
                    'key' => $key,
                ];
            }
        }

        return array_values($labels);
    }

    /**
     * Proses import. $subjectBookMap adalah mapping SEMENTARA (key => book_id)
     * yang dikirim dari form konfirmasi, dipakai hanya untuk sesi import ini,
     * TIDAK disimpan ke database.
     */
    public function import(string $filePath, array $subjectBookMap): void
    {
        $spreadsheet = IOFactory::load($filePath);

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $this->processSheet($sheetName, $sheet, $subjectBookMap);
        }
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
                $this->processEntry($sheetName, $rowIndex, $user, $label, $kode, $kelasLevel, $subjectBookMap);
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
                $colStart = $this->shiftColumn($col, 1);
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

        preg_match('/\b(XII|XI|X)\b/', strtoupper($sheetName), $m);
        $kelasLevel = $m[1] ?? null;

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
            ->lockForUpdate()
            ->first();
    }

    protected function processEntry(string $sheetName, int $rowIndex, User $user, string $label, string $kode, ?string $kelasLevel, array $subjectBookMap): void
    {
        $key = $label . '|' . ($kelasLevel ?? '?');
        $bookId = $subjectBookMap[$key] ?? null;

        if (!$bookId) {
            $this->errors[] = "{$sheetName} baris {$rowIndex}: Mapel '{$label}' (Kelas {$kelasLevel}) belum dipetakan ke Buku Paket.";
            return;
        }

        try {
            DB::beginTransaction();

            $bookItem = BookItem::where('book_id', $bookId)
                ->whereRaw('UPPER(kode_buku) = ?', [$kode])
                ->lockForUpdate()
                ->first();

            if (!$bookItem) {
                $bookItem = BookItem::where('book_id', $bookId)
                    ->where(function ($q) {
                        $q->whereNull('kode_buku')->orWhere('kode_buku', '');
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$bookItem) {
                    DB::rollBack();
                    $this->errors[] = "{$sheetName} baris {$rowIndex}: Slot eksemplar buku '{$label}' sudah penuh, kode {$kode} tidak bisa ditambahkan.";
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

            $sedangDipinjam = PinjamKelas::where('book_id', $bookId)
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
                'book_id' => $bookId,
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

    protected function normalizeCode($value): string
    {
        if (is_numeric($value)) {
            return (string) (int) $value;
        }

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