<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\PinjamKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamKelasSiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $pinjamKelas = PinjamKelas::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('peminjam.pinjamkelas.index', compact('pinjamKelas'));
    }

    public function create()
    {
        $booksPaket = Book::withCount([
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                }
            ])
            ->where(function ($q) {
                $q->where('jenis_koleksi', 'like', '%Paket%')
                    ->orWhere('jenis_koleksi', 'like', '%Packet%')
                    ->orWhere('jenis_koleksi', 'like', '%Pakett%')
                    ->orWhere('jenis_koleksi', 'like', '%PKT%');
            })
            ->orderBy('judul')
            ->get()
            ->unique(function ($book) {
                // Filter lebih ketat: abaikan huruf besar/kecil dan spasi tambahan
                // Jadi "MATEMATIKA", "Matematika", dan "Matematika " akan dianggap 1 buku yang sama
                return strtolower(trim($book->judul));
            });

        return view('peminjam.pinjamkelas.create', compact('booksPaket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'kode_buku' => ['required', 'string', 'max:100'],
        ]);

        $user = auth()->user();
        $kodeBuku = strtoupper(trim($request->kode_buku));

        try {
            DB::beginTransaction();

            $book = Book::where('id', $request->book_id)
                ->where(function ($q) {
                    $q->where('jenis_koleksi', 'like', '%Paket%')
                        ->orWhere('jenis_koleksi', 'like', '%Packet%')
                        ->orWhere('jenis_koleksi', 'like', '%Pakett%')
                        ->orWhere('jenis_koleksi', 'like', '%PKT%');
                })
                ->first();

            if (!$book) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Buku yang dipilih bukan Buku Paket.');
            }

            $bookItem = BookItem::where('book_id', $book->id)
                ->whereNotNull('kode_buku')
                ->where('kode_buku', '!=', '')
                ->whereRaw('UPPER(kode_buku) = ?', [$kodeBuku])
                ->lockForUpdate()
                ->first();

            if (!$bookItem) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku tidak valid atau tidak cocok dengan judul Buku Paket yang dipilih.');
            }

            if ($bookItem->status !== 'available') {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku ini sudah dipinjam atau tidak tersedia.');
            }

            $sedangDipinjam = PinjamKelas::where('kode_buku', $bookItem->kode_buku)
                ->whereIn('status', ['pending', 'disetujui'])
                ->lockForUpdate()
                ->exists();

            if ($sedangDipinjam) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku ini masih dalam proses peminjaman.');
            }

            PinjamKelas::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'kode_buku' => $bookItem->kode_buku,
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays(7),
                'status' => 'pending',
            ]);

            $bookItem->update([
                'status' => 'borrowed',
            ]);

            DB::commit();

            return redirect()
                ->route('siswa.pinjamkelas.index')
                ->with('success', 'Peminjaman Buku Paket berhasil diajukan. Silakan tunggu persetujuan petugas.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }
}