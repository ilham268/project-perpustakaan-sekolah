<?php

namespace App\Http\Controllers;

use App\Models\TeacherGuestBook;
use App\Exports\TeacherGuestBookExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherGuestBookController extends Controller
{
    public function create()
    {
        return view('teacher-guest-book.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'keperluan' => 'required|string|max:1000',
        ]);

        TeacherGuestBook::create([
            'nama' => $request->nama,
            'keperluan' => $request->keperluan,
        ]);

        return redirect()->back()->with('success', 'Terima kasih, buku tamu guru berhasil diisi!');
    }

    public function adminIndex()
    {
        $guestBooks = TeacherGuestBook::latest()->paginate(10);

        // Perhitungan Statistik
        $totalKunjungan = TeacherGuestBook::count();
        
        // Statistik Hari Ini (disesuaikan dengan nama variabel di view)
        $todayKunjungan = TeacherGuestBook::whereDate('created_at', now()->today())->count();
        $kunjunganHariIni = $todayKunjungan;

        // Statistik Bulan Ini (disesuaikan dengan nama variabel di view)
        $monthKunjungan = TeacherGuestBook::whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->count();
        $kunjunganBulanIni = $monthKunjungan;

        return view('admin.teacher-guest-book.index', compact(
            'guestBooks',
            'totalKunjungan',
            'todayKunjungan',
            'kunjunganHariIni',
            'monthKunjungan',
            'kunjunganBulanIni'
        ));
    }

    public function export()
    {
        return Excel::download(new TeacherGuestBookExport, 'buku-tamu-guru.xlsx');
    }

    public function destroy($id)
    {
        $guestBook = TeacherGuestBook::findOrFail($id);
        $guestBook->delete();

        return redirect()->back()->with('success', 'Data buku tamu guru berhasil dihapus!');
    }
}