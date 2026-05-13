<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPinjam;
use App\Models\User;
use App\Models\Book;
use App\Models\BookItem;
use App\Models\PinjamKelas;

class KategoriPinjamController extends Controller
{
    public function index()
    {
        $kategoris = KategoriPinjam::latest()->paginate(10);
        return view('admin.pinjamkelas.kategori', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_pinjams',
            'kelas' => 'required|string|max:50',
        ]);

        KategoriPinjam::create($request->all());

        return redirect()->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_pinjams,nama_kategori,' . $id,
            'kelas' => 'required|string|max:50',
        ]);

        $kategori = KategoriPinjam::findOrFail($id);
        $kategori->update($request->all());

        return redirect()->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.pinjamkelas.kategori')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function show($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);

        $siswas = User::where('role', 'siswa')
            ->where('kelas', $kategori->kelas)
            ->get();

        return view('admin.pinjamkelas.show', compact('kategori', 'siswas'));
    }

    public function prosesPinjam(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_pinjams,id',
            'user_id' => 'required|exists:users,id',
            'kode_buku' => 'required|string',
        ]);

        $kategori = KategoriPinjam::find($request->kategori_id);

        $book = Book::where('judul', 'LIKE', '%' . $kategori->nama_kategori . '%')
            ->first();

        PinjamKelas::create([
            'kategori_pinjam_id' => $request->kategori_id,
            'user_id' => $request->user_id,
            'book_id' => $book ? $book->id : null,
            'kode_buku' => $request->kode_buku,
            'tanggal_pinjam' => now(),
            'tanggal_kembali' => now()->addDays(7),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.pinjamkelas.kategori')
            ->with('success', 'Peminjaman berhasil ditambahkan');
    }

    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'kategori'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                })
                ->orWhere('kode_buku', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas')) {
            $kelas = $request->kelas;

            $query->whereHas('user', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $pinjamKelas = $query->paginate(10)->withQueryString();

        $kelasList = User::where('role', 'siswa')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return view('admin.pinjamkelas.kelas', compact('pinjamKelas', 'kelasList'));
    }

    public function setujuiKelas($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status == 'disetujui') {
            return redirect()->back()
                ->with('error', 'Peminjaman kelas ini sudah disetujui.');
        }

        if ($pinjam->status == 'dikembalikan') {
            return redirect()->back()
                ->with('error', 'Peminjaman kelas ini sudah dikembalikan.');
        }

        if ($pinjam->status == 'denda') {
            return redirect()->back()
                ->with('error', 'Peminjaman kelas ini sudah masuk denda.');
        }

        $pinjam->update([
            'status' => 'disetujui',
        ]);

        return redirect()->back()
            ->with('success', 'Peminjaman kelas berhasil disetujui.');
    }

    public function formDendaKelas($id)
    {
        $pinjam = PinjamKelas::with(['user', 'kategori'])
            ->findOrFail($id);

        if ($pinjam->status == 'dikembalikan') {
            return redirect()->route('admin.pinjamkelas.kelas')
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
            return redirect()->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $dendaKondisi = 0;

        if ($request->kondisi == 'rusak') {
            $dendaKondisi = (int) $request->input('denda_rusak', 10000);
        } elseif ($request->kondisi == 'hilang') {
            $dendaKondisi = (int) $request->input('denda_hilang', 100000);
        }

        $tanggalJatuhTempo = \Carbon\Carbon::parse($pinjam->tanggal_kembali)->startOfDay();
        $tanggalSekarang = \Carbon\Carbon::now()->startOfDay();

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

        return redirect()->route('admin.pinjamkelas.kelas')
            ->with('success', 'Denda peminjaman kelas berhasil diproses.');
    }
}