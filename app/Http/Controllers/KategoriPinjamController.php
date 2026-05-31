<?php

namespace App\Http\Controllers;

use App\Exports\PinjamKelasExport;
use App\Models\Book;
use App\Models\BookItem;
use App\Models\Kelas;
use App\Models\PinjamKelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KategoriPinjamController extends Controller
{
    public function index(Request $request)
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

        return view('admin.pinjamkelas.kategori', compact(
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

    public function store(Request $request)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Kategori kelas sudah tidak digunakan. Buku Paket diambil dari data import Excel.');
    }

    public function update(Request $request, $id)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Edit kategori kelas sudah tidak digunakan. Kelola data melalui menu Buku Paket.');
    }

    public function destroy($id)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Hapus kategori kelas sudah tidak digunakan. Buku Paket dikelola dari data buku.');
    }

    public function show($id)
    {
        $book = Book::where('id', $id)
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->first();

        if ($book) {
            return redirect()->route('books.show', $book->id);
        }

        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Buku Paket tidak ditemukan.');
    }

    public function prosesPinjam(Request $request)
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
                    ->with('error', 'User siswa tidak ditemukan.');
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
                ->route('admin.pinjamkelas.kelas')
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

        return view('admin.pinjamkelas.kelas', compact(
            'pinjamKelas',
            'kelasList',
            'jurusanList'
        ));
    }

    public function exportKelasPinjam(Request $request)
    {
        return Excel::download(
            new PinjamKelasExport(
                $request->search,
                $request->kelas_id,
                $request->jurusan
            ),
            'peminjaman-buku-paket-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function setujuiKelas($id)
    {
        $pinjam = PinjamKelas::with('book')->findOrFail($id);

        if ($pinjam->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya peminjaman pending yang bisa disetujui.');
        }

        $pinjam->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Peminjaman Buku Paket berhasil disetujui.');
    }

    public function formDendaKelas($id)
    {
        $pinjam = PinjamKelas::with(['user', 'book', 'kategori.kelasData'])
            ->findOrFail($id);

        if ($pinjam->status === 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        return view('admin.pinjamkelas.denda', compact('pinjam'));
    }

    public function simpanDendaKelas(Request $request, $id)
    {
        $request->validate([
            'kondisi' => ['required', 'in:baik,rusak,hilang'],
            'denda' => ['nullable', 'numeric', 'min:0'],
            'denda_rusak' => ['nullable', 'numeric', 'min:0'],
            'denda_hilang' => ['nullable', 'numeric', 'min:0'],
        ]);

        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status === 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $totalDenda = 0;

        if ($request->kondisi === 'rusak') {
            $totalDenda = (int) $request->input('denda_rusak', 10000);
        }

        if ($request->kondisi === 'hilang') {
            $totalDenda = (int) $request->input('denda_hilang', 100000);
        }

        DB::transaction(function () use ($pinjam, $request, $totalDenda) {
            if ($request->kondisi === 'baik' || $totalDenda <= 0) {
                $pinjam->update([
                    'status' => 'dikembalikan',
                    'kondisi' => 'baik',
                    'denda' => 0,
                    'status_denda' => null,
                ]);
            } else {
                $pinjam->update([
                    'status' => 'denda',
                    'status_denda' => 'pending',
                    'kondisi' => $request->kondisi,
                    'denda' => $totalDenda,
                ]);
            }

            $bookItem = BookItem::whereRaw('UPPER(kode_buku) = ?', [strtoupper($pinjam->kode_buku)])
                ->first();

            if ($bookItem && ($request->kondisi === 'baik' || $request->kondisi === 'rusak')) {
                $bookItem->update([
                    'status' => 'available',
                ]);
            }

            if ($bookItem && $request->kondisi === 'hilang') {
                $bookItem->update([
                    'status' => 'lost',
                ]);
            }
        });

        if ($request->kondisi === 'baik' || $totalDenda <= 0) {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('success', 'Peminjaman Buku Paket berhasil dikembalikan.');
        }

        return redirect()
            ->route('admin.pinjamkelas.kelas')
            ->with('success', 'Denda peminjaman Buku Paket berhasil diproses.');
    }

    private function filterPaket($q): void
    {
        $q->where('jenis_koleksi', 'like', '%Paket%')
            ->orWhere('jenis_koleksi', 'like', '%Packet%')
            ->orWhere('jenis_koleksi', 'like', '%Pakett%')
            ->orWhere('jenis_koleksi', 'like', '%PKT%');
    }
}