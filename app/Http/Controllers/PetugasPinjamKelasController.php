<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Kelas;
use App\Models\PinjamKelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PetugasPinjamKelasController extends Controller
{
    public function kategori(Request $request)
    {
        $booksPaketQuery = Book::query()
            ->withCount([
                'bookItems as total_eksemplar',
                'bookItems as kode_terisi' => function ($q) {
                    $q->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
            ])
            ->where(function ($q) {
                $this->filterPaket($q);
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $booksPaketQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tahun_pengadaan')) {
            $booksPaketQuery->where('tahun_pengadaan', $request->tahun_pengadaan);
        }

        $booksPaket = $booksPaketQuery
            ->orderByDesc('tahun_pengadaan')
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        $tahunOptions = Book::query()
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->whereNotNull('tahun_pengadaan')
            ->distinct()
            ->orderByDesc('tahun_pengadaan')
            ->pluck('tahun_pengadaan');

        $totalPaket = Book::query()
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->count();

        $totalEksemplar = BookItem::whereHas('book', function ($q) {
                $this->filterPaket($q);
            })
            ->count();

        $totalKodeTerisi = BookItem::whereHas('book', function ($q) {
                $this->filterPaket($q);
            })
            ->whereNotNull('kode_buku')
            ->where('kode_buku', '!=', '')
            ->count();

        $kategoris = new LengthAwarePaginator([], 0, 10, 1, [
            'path' => request()->url(),
        ]);

        $kelasList = collect();
        $jurusanList = collect();

        return view('petugas.pinjamkelas.kategori', compact(
            'booksPaket',
            'tahunOptions',
            'totalPaket',
            'totalEksemplar',
            'totalKodeTerisi',
            'kategoris',
            'kelasList',
            'jurusanList'
        ));
    }

    public function create($id)
    {
        $book = Book::query()
            ->withCount([
                'bookItems as total_eksemplar',
                'bookItems as kode_terisi' => function ($q) {
                    $q->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
            ])
            ->where('id', $id)
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->firstOrFail();

        $siswas = User::where('role', 'siswa')
            ->orderBy('name')
            ->get();

        $kategori = null;

        return view('petugas.pinjamkelas.show', compact('book', 'siswas', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'user_id' => ['required', 'exists:users,id'],
            'kode_buku' => ['required', 'string', 'max:100'],
        ]);

        $kodeBuku = strtoupper(trim($request->kode_buku));

        try {
            DB::beginTransaction();

            $book = Book::where('id', $request->book_id)
                ->where(function ($q) {
                    $this->filterPaket($q);
                })
                ->first();

            if (!$book) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Buku yang dipilih bukan Buku Paket.');
            }

            $user = User::where('id', $request->user_id)
                ->where('role', 'siswa')
                ->first();

            if (!$user) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Siswa tidak ditemukan.');
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
                    ->with('error', 'Kode buku tidak valid atau tidak cocok dengan judul Buku Paket.');
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
                'kategori_pinjam_id' => null,
                'book_id' => $book->id,
                'user_id' => $user->id,
                'kode_buku' => $bookItem->kode_buku,
                'status' => 'pending',
            ]);

            $bookItem->update([
                'status' => 'borrowed',
            ]);

            DB::commit();

            return redirect()
                ->route('petugas.pinjamkelas.kelas')
                ->with('success', 'Peminjaman Buku Paket berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan peminjaman: ' . $e->getMessage());
        }
    }

    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'book', 'kategori.kelasData']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_buku', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nomor_identitas', 'like', "%{$search}%")
                            ->orWhere('kelas', 'like', "%{$search}%")
                            ->orWhere('jurusan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('book', function ($bookQuery) use ($search) {
                        $bookQuery->where('judul', 'like', "%{$search}%")
                            ->orWhere('penulis', 'like', "%{$search}%")
                            ->orWhere('penerbit', 'like', "%{$search}%")
                            ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);

            if ($kelas) {
                $query->whereHas('user', function ($q) use ($kelas) {
                    $q->where('kelas', $kelas->nama_kelas)
                        ->where('jurusan', $kelas->jurusan);
                });
            }
        }

        if ($request->filled('jurusan')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('jurusan', $request->jurusan);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pinjamKelas = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kelasList = Kelas::orderBy('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        return view('petugas.pinjamkelas.kelas-pinjam', compact(
            'pinjamKelas',
            'kelasList',
            'jurusanList'
        ));
    }

    public function approve($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya peminjaman pending yang bisa disetujui.');
        }

        $pinjam->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman Buku Paket berhasil disetujui.');
    }

    public function reject($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);

        DB::transaction(function () use ($pinjam) {
            $bookItem = BookItem::where('book_id', $pinjam->book_id)
                ->whereRaw('UPPER(kode_buku) = ?', [strtoupper($pinjam->kode_buku)])
                ->first();

            if ($bookItem) {
                $bookItem->update([
                    'status' => 'available',
                ]);
            }

            $pinjam->delete();
        });

        return redirect()
            ->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman Buku Paket ditolak.');
    }

    private function filterPaket($q): void
    {
        $q->where('jenis_koleksi', 'like', '%Paket%')
            ->orWhere('jenis_koleksi', 'like', '%Packet%')
            ->orWhere('jenis_koleksi', 'like', '%Pakett%')
            ->orWhere('jenis_koleksi', 'like', '%PKT%');
    }
}