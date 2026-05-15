<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPinjam;
use App\Models\User;
use App\Models\Book;
use App\Models\PinjamKelas;
use App\Models\Kelas;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PinjamKelasExport;

class KategoriPinjamController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriPinjam::query();

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

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

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

        return view('admin.pinjamkelas.kategori', compact(
            'kategoris',
            'kelasList',
            'jurusanList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_pinjams,nama_kategori',
            'kelas' => 'required|string|exists:kelas,nama_kelas',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.exists' => 'Kelas tidak ditemukan.',
        ]);

        KategoriPinjam::create([
            'nama_kategori' => $request->nama_kategori,
            'kelas' => $request->kelas,
        ]);

        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_pinjams,nama_kategori,' . $id,
            'kelas' => 'required|string|exists:kelas,nama_kelas',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.exists' => 'Kelas tidak ditemukan.',
        ]);

        $kategori = KategoriPinjam::findOrFail($id);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'kelas' => $request->kelas,
        ]);

        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);
        $kategori->delete();

        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function show($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);

        $siswas = User::where('role', 'siswa')
            ->where('kelas', $kategori->kelas)
            ->orderBy('name')
            ->get();

        return view('admin.pinjamkelas.show', compact('kategori', 'siswas'));
    }

    public function prosesPinjam(Request $request)
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
            ->route('admin.pinjamkelas.kategori')
            ->with('success', 'Peminjaman berhasil ditambahkan');
    }

    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'kategori']);

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

        return view('admin.pinjamkelas.kelas', compact(
            'pinjamKelas',
            'kelasList',
            'jurusanList',
            'kelasJurusanMap'
        ));
    }

    public function exportKelasPinjam(Request $request)
    {
        return Excel::download(
            new PinjamKelasExport(
                $request->search,
                $request->kelas,
                $request->jurusan
            ),
            'kelas-pinjam-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function setujuiKelas($id)
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
            ->back()
            ->with('success', 'Peminjaman kelas berhasil disetujui.');
    }

    public function formDendaKelas($id)
    {
        $pinjam = PinjamKelas::with(['user', 'kategori'])
            ->findOrFail($id);

        if ($pinjam->status == 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        return view('admin.pinjamkelas.denda', compact('pinjam'));
    }

    public function simpanDendaKelas(Request $request, $id)
    {
        $request->validate([
            'kondisi' => 'required|in:baik,rusak,hilang',
            'denda' => 'nullable|numeric|min:0',
            'denda_rusak' => 'nullable|numeric|min:0',
            'denda_hilang' => 'nullable|numeric|min:0',
        ]);

        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status == 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $dendaKondisi = 0;

        if ($request->kondisi == 'rusak') {
            $dendaKondisi = (int) $request->input('denda_rusak', 10000);
        } elseif ($request->kondisi == 'hilang') {
            $dendaKondisi = (int) $request->input('denda_hilang', 100000);
        }

        $tanggalJatuhTempo = Carbon::parse($pinjam->tanggal_kembali)->startOfDay();
        $tanggalSekarang = Carbon::now()->startOfDay();

        $dendaKeterlambatan = 0;

        if ($tanggalSekarang->gt($tanggalJatuhTempo)) {
            $daysLate = $tanggalJatuhTempo->diffInDays($tanggalSekarang);
            $dendaKeterlambatan = $daysLate * 10000;
        }

        $totalDenda = $dendaKeterlambatan + $dendaKondisi;

        $pinjam->update([
            'status' => 'denda',
            'kondisi' => $request->kondisi,
            'denda' => $totalDenda,
            'tanggal_denda' => now(),
        ]);

        return redirect()
            ->route('admin.pinjamkelas.kelas')
            ->with('success', 'Denda peminjaman kelas berhasil diproses.');
    }
}