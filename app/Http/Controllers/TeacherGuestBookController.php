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
        $predefined = [
            'Meminjam buku',
            'Mengembalikan buku',
            'Membaca buku di tempat',
            'Belajar atau mengerjakan tugas',
            'Mencari referensi atau informasi',
            'Berkunjung atau keperluan lainnya',
        ];

        $request->validate([
            'nama'             => 'required|string|max:255',
            'keperluan'        => 'required|string',
            'keperluan_custom' => 'nullable|string|max:500',
        ]);

        $keperluan = $request->keperluan;

        if ($keperluan === 'Lainnya') {
            $request->validate([
                'keperluan_custom' => 'required|string|max:500',
            ], [
                'keperluan_custom.required' => 'Mohon jelaskan keperluan Anda jika memilih opsi Lainnya.',
            ]);
            $keperluan = 'Lainnya: ' . trim($request->keperluan_custom);
        } elseif (!in_array($keperluan, $predefined) && !str_starts_with($keperluan, 'Lainnya: ')) {
            $keperluan = 'Lainnya: ' . trim($keperluan);
        }

        TeacherGuestBook::create([
            'nama'      => $request->nama,
            'keperluan' => $keperluan,
        ]);

        return redirect()->back()->with('success', 'Terima kasih, buku tamu guru berhasil diisi!');
    }

    /**
     * Halaman baca-saja (read-only) buku tamu guru untuk pengunjung
     */
    public function publicIndex(Request $request)
    {
        $query = TeacherGuestBook::query();

        // Pencarian nama atau keperluan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $guestBooks = $query->latest()->paginate(15)->withQueryString();

        $totalKunjungan = TeacherGuestBook::count();
        $todayKunjungan = TeacherGuestBook::whereDate('created_at', now()->today())->count();
        $kunjunganHariIni = $todayKunjungan;

        return view('teacher-guest-book.public-index', compact(
            'guestBooks',
            'totalKunjungan',
            'todayKunjungan',
            'kunjunganHariIni'
        ));
    }

    public function adminIndex(Request $request)
    {
        $query = TeacherGuestBook::query();

        // Pencarian nama atau keperluan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $guestBooks = $query->latest()->paginate(10)->withQueryString();

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

    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $search    = $request->search;

        $filename = 'buku-tamu-guru-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new TeacherGuestBookExport($startDate, $endDate, $search), $filename);
    }

    public function destroy($id)
    {
        $guestBook = TeacherGuestBook::findOrFail($id);
        $guestBook->delete();

        return redirect()->back()->with('success', 'Data buku tamu guru berhasil dihapus!');
    }
}