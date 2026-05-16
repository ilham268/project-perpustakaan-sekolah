<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PinjamKelas;
use App\Models\KategoriPinjam;
use App\Models\User;
use App\Models\Book;
use App\Models\Kelas;

class PetugasPinjamKelasController extends Controller
{
    // Halaman Kategori Buku
    public function kategori(Request $request)
    {
        $query = KategoriPinjam::query();

        /*
        |--------------------------------------------------------------------------
        | Filter Search
        |--------------------------------------------------------------------------
        | Search bisa cari:
        | - nama kategori
        | - kelas
        | - jurusan
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = $request->search;

            $kelasDariJurusanSearch = Kelas::where('jurusan', 'like', '%' . $search . '%')
                ->pluck('nama_kelas')
                ->toArray();

            $query->where(function ($q) use ($search, $kelasDariJurusanSearch) {
                $q->where('nama_kategori', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%');

                if (!empty($kelasDariJurusanSearch)) {
                    $q->orWhereIn('kelas', $kelasDariJurusanSearch);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Kelas
        |--------------------------------------------------------------------------
        */
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jurusan
        |--------------------------------------------------------------------------
        | Jurusan diambil dari tabel kelas.
        | Jika jurusan = RPL, maka ambil semua nama_kelas dari jurusan RPL,
        | lalu cocokkan dengan kolom kelas di kategori_pinjams.
        |--------------------------------------------------------------------------
        */
        if ($request->filled('jurusan')) {
            $kelasDariJurusan = Kelas::where('jurusan', $request->jurusan)
                ->pluck('nama_kelas')
                ->toArray();

            if (!empty($kelasDariJurusan)) {
                $query->whereIn('kelas', $kelasDariJurusan);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $kategoris = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $kelasJurusanMap = Kelas::pluck('jurusan', 'nama_kelas');

        return view('petugas.pinjamkelas.kategori', compact(
            'kategoris',
            'kelasList',
            'jurusanList',
            'kelasJurusanMap'
        ));
    }

    // Halaman Form Input Peminjaman
    public function create($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);

        $siswas = User::where('role', 'siswa')
            ->where('kelas', $kategori->kelas)
            ->orderBy('name')
            ->get();

        return view('petugas.pinjamkelas.show', compact('kategori', 'siswas'));
    }

    // Proses Simpan Peminjaman
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_pinjams,id',
            'user_id' => 'required|exists:users,id',
            'kode_buku' => 'required|string|max:100',
        ]);

        $kategori = KategoriPinjam::findOrFail($request->kategori_id);

        $book = Book::where('judul', 'LIKE', '%' . $kategori->nama_kategori . '%')
            ->first();

        PinjamKelas::create([
            'kategori_pinjam_id' => $request->kategori_id,
            'user_id' => $request->user_id,
            'book_id' => $book ? $book->id : null,
            'kode_buku' => strtoupper($request->kode_buku),
            'tanggal_pinjam' => now(),
            'tanggal_kembali' => now()->addDays(7),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('petugas.pinjamkelas.kategori')
            ->with('success', 'Peminjaman berhasil ditambahkan');
    }

    // Halaman Kelas Pinjam / Daftar Peminjaman
    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'kategori']);

        /*
        |--------------------------------------------------------------------------
        | Filter Search
        |--------------------------------------------------------------------------
        | Search bisa cari:
        | - kode buku
        | - nama siswa
        | - nomor identitas
        | - kelas
        | - nama kategori buku
        | - jurusan
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = $request->search;

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

        /*
        |--------------------------------------------------------------------------
        | Filter Kelas
        |--------------------------------------------------------------------------
        */
        if ($request->filled('kelas')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('kelas', $request->kelas);
                })
                ->orWhereHas('kategori', function ($kategoriQuery) use ($request) {
                    $kategoriQuery->where('kelas', $request->kelas);
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jurusan
        |--------------------------------------------------------------------------
        */
        if ($request->filled('jurusan')) {
            $kelasDariJurusan = Kelas::where('jurusan', $request->jurusan)
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

        $pinjamKelas = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kelasList = Kelas::whereNotNull('nama_kelas')
            ->where('nama_kelas', '!=', '')
            ->distinct()
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas');

        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $kelasJurusanMap = Kelas::pluck('jurusan', 'nama_kelas');

        return view('petugas.pinjamkelas.kelas-pinjam', compact(
            'pinjamKelas',
            'kelasList',
            'jurusanList',
            'kelasJurusanMap'
        ));
    }

    // Approve Peminjaman
    public function approve($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status == 'disetujui') {
            return redirect()
                ->back()
                ->with('error', 'Peminjaman kelas ini sudah disetujui.');
        }

        if ($pinjam->status == 'dikembalikan') {
            return redirect()
                ->back()
                ->with('error', 'Peminjaman kelas ini sudah dikembalikan.');
        }

        if ($pinjam->status == 'denda') {
            return redirect()
                ->back()
                ->with('error', 'Peminjaman kelas ini sudah masuk denda.');
        }

        $pinjam->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman kelas berhasil disetujui.');
    }

    // Reject Peminjaman
    public function reject($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);

        $pinjam->delete();

        return redirect()
            ->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman kelas ditolak');
    }
}