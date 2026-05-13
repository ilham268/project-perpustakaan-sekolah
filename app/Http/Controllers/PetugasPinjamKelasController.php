<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PinjamKelas;
use App\Models\KategoriPinjam;
use App\Models\User;
use App\Models\Book;

class PetugasPinjamKelasController extends Controller
{
    // Halaman Kategori Buku
    public function kategori()
    {
        $kategoris = KategoriPinjam::latest()->paginate(10);
        return view('petugas.pinjamkelas.kategori', compact('kategoris'));
    }

    // Halaman Form Input Peminjaman
    public function create($id)
    {
        $kategori = KategoriPinjam::findOrFail($id);
        $siswas = User::where('role', 'siswa')
            ->where('kelas', $kategori->kelas)
            ->get();
        
        return view('petugas.pinjamkelas.show', compact('kategori', 'siswas'));
    }

    // Proses Simpan Peminjaman (SAMA SEPERTI ADMIN)
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_pinjams,id',
            'user_id' => 'required|exists:users,id',
            'kode_buku' => 'required|string',
        ]);

        $kategori = KategoriPinjam::find($request->kategori_id);
        
        // CARI BUKU BERDASARKAN KATEGORI (SAMA SEPERTI ADMIN)
        $book = Book::where('judul', 'LIKE', '%' . $kategori->nama_kategori . '%')->first();

        PinjamKelas::create([
            'kategori_pinjam_id' => $request->kategori_id,
            'user_id' => $request->user_id,
            'book_id' => $book ? $book->id : null,
            'kode_buku' => $request->kode_buku,
            'tanggal_pinjam' => now(),
            'tanggal_kembali' => now()->addDays(7),
            'status' => 'pending',
        ]);

        return redirect()->route('petugas.pinjamkelas.kategori')
            ->with('success', 'Peminjaman berhasil ditambahkan');
    }

    // Halaman Kelas Pinjam (Daftar Peminjaman)
    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'kategori'])
            ->latest();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })->orWhereHas('kategori', function($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                })->orWhere('kode_buku', 'like', '%' . $search . '%');
            });
        }

        $pinjamKelas = $query->paginate(10);

        return view('petugas.pinjamkelas.kelas-pinjam', compact('pinjamKelas'));
    }

    // Approve Peminjaman
    public function approve($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);
        $pinjam->update(['status' => 'disetujui']);
        
        return redirect()->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman kelas disetujui');
    }

    // Reject Peminjaman
    public function reject($id)
    {
        $pinjam = PinjamKelas::findOrFail($id);
        $pinjam->delete();
        
        return redirect()->route('petugas.pinjamkelas.kelas')
            ->with('success', 'Peminjaman kelas ditolak');
    }
}