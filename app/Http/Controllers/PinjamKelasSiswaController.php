<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PinjamKelas;
use App\Models\KategoriPinjam;

class PinjamKelasSiswaController extends Controller
{
    // Halaman Buku Pinjaman (Lihat daftar peminjaman siswa)
    public function index()
    {
        $user = auth()->user();
        
        $pinjamKelas = PinjamKelas::with(['kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
        
        return view('peminjam.pinjamkelas.index', compact('pinjamKelas'));
    }

    // Halaman Input Buku (Form peminjaman)
    public function create()
    {
        $user = auth()->user();
        
        // Hanya ambil kategori yang kelasnya sama dengan kelas user
        $kategoris = KategoriPinjam::where('kelas', $user->kelas)->get();
        
        return view('peminjam.pinjamkelas.create', compact('kategoris'));
    }
    public function store(Request $request)
{
    $request->validate([
        'kategori_id' => 'required|exists:kategori_pinjams,id',
        'kode_buku' => 'required|string',
    ]);

    $user = auth()->user();
    $kategori = KategoriPinjam::find($request->kategori_id);
    
    if ($kategori->kelas !== $user->kelas) {
        return redirect()->back()->with('error', 'Kategori tidak sesuai dengan kelas Anda!');
    }

    PinjamKelas::create([
        'kategori_pinjam_id' => $request->kategori_id,
        'user_id' => $user->id,
        'kode_buku' => $request->kode_buku,
        'tanggal_pinjam' => now(),
        'tanggal_kembali' => now()->addDays(7),
        'status' => 'pending',
    ]);

    return redirect()->route('siswa.pinjamkelas.index')
        ->with('success', 'Peminjaman kelas berhasil diajukan');
}
}