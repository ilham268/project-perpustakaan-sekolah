<?php

namespace App\Imports;

use App\Models\BookItem;
use App\Models\PinjamKelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PinjamKelasImport implements ToCollection, WithHeadingRow
{
    protected int $successCount = 0;
    protected array $errors = [];

    /**
     * Format Excel yang diharapkan (header di baris pertama):
     * | NISN | KODE_BUKU |
     */
    public function collection($rows)
    {
        foreach ($rows as $index => $row) {
            // +2 karena heading row dipakai sebagai baris 1, dan index koleksi mulai dari 0
            $rowNumber = $index + 2;

            $nisn = trim((string) ($row['nisn'] ?? ''));
            $kodeBuku = strtoupper(trim((string) ($row['kode_buku'] ?? '')));

            if (!$nisn || !$kodeBuku) {
                $this->errors[] = "Baris {$rowNumber}: NISN atau KODE_BUKU kosong.";
                continue;
            }

            try {
                DB::beginTransaction();

                $user = User::where('role', 'siswa')
                    ->where('nomor_identitas', $nisn)
                    ->lockForUpdate()
                    ->first();

                if (!$user) {
                    DB::rollBack();
                    $this->errors[] = "Baris {$rowNumber}: Siswa dengan NISN {$nisn} tidak ditemukan.";
                    continue;
                }

                $bookItem = BookItem::whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '')
                    ->whereRaw('UPPER(kode_buku) = ?', [$kodeBuku])
                    ->lockForUpdate()
                    ->first();

                if (!$bookItem) {
                    DB::rollBack();
                    $this->errors[] = "Baris {$rowNumber}: Kode buku {$kodeBuku} tidak ditemukan.";
                    continue;
                }

                if ($bookItem->status !== 'available') {
                    DB::rollBack();
                    $this->errors[] = "Baris {$rowNumber}: Kode buku {$kodeBuku} sedang tidak tersedia.";
                    continue;
                }

                $sedangDipinjam = PinjamKelas::where('kode_buku', $bookItem->kode_buku)
                    ->whereIn('status', ['pending', 'disetujui'])
                    ->lockForUpdate()
                    ->exists();

                if ($sedangDipinjam) {
                    DB::rollBack();
                    $this->errors[] = "Baris {$rowNumber}: Kode buku {$kodeBuku} masih dalam proses peminjaman.";
                    continue;
                }

                PinjamKelas::create([
                    'kategori_pinjam_id' => null,
                    'book_id' => $bookItem->book_id,
                    'user_id' => $user->id,
                    'kode_buku' => $bookItem->kode_buku,
                    'status' => 'pending',
                ]);

                $bookItem->update([
                    'status' => 'borrowed',
                ]);

                DB::commit();
                $this->successCount++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->errors[] = "Baris {$rowNumber}: Gagal diproses ({$e->getMessage()}).";
            }
        }
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