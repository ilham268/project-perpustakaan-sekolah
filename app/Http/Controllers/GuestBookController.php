<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use App\Models\User;
use App\Exports\GuestBookExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    public function create()
    {
        // Jika user sedang login (Siswa/Peminjam)
        if (auth()->check()) {
            $currentUser = auth()->user();
            return view('guest-book.create', compact('currentUser'));
        }

        // Ambil semua data user/siswa tanpa filter role yang terlalu ketat
        $siswaList = User::orderBy('name', 'asc')
            ->get(['id', 'name', 'kelas', 'jurusan']);

        return view('guest-book.create', compact('siswaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'kelas'     => 'nullable|string|max:255',
            'jurusan'   => 'nullable|string|max:255',
            'keperluan' => 'required|string',
        ]);

        $nama    = $request->nama;
        $kelas   = $request->kelas;
        $jurusan = $request->jurusan;

        // Jika user sedang login
        if (auth()->check()) {
            $user = auth()->user();
            $nama    = $user->name ?? $user->nama ?? $nama;
            $kelas   = $user->kelas ?? $kelas;
            $jurusan = $user->jurusan ?? $jurusan;
        }

        GuestBook::create([
            'nama'      => $nama,
            'kelas'     => $kelas,
            'jurusan'   => $jurusan,
            'keperluan' => $request->keperluan,
        ]);

        return redirect()->route('guest-book.create')
            ->with('success', 'Terima kasih telah mengisi buku tamu!');
    }

    public function adminIndex(Request $request)
    {
        $query = GuestBook::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('kelas', 'like', '%' . $search . '%')
                  ->orWhere('jurusan', 'like', '%' . $search . '%')
                  ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $guestBooks = $query->latest()->paginate(10)->withQueryString();

        $listKelas = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas', 'asc')
            ->pluck('kelas');

        $listJurusan = User::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan', 'asc')
            ->pluck('jurusan');

        $totalKunjungan = GuestBook::count();
        $todayKunjungan = GuestBook::whereDate('created_at', today())->count();
        $monthKunjungan = GuestBook::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.guest-book.index', compact(
            'guestBooks',
            'totalKunjungan',
            'todayKunjungan',
            'monthKunjungan',
            'listKelas',
            'listJurusan'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $kelas     = $request->kelas;
        $jurusan   = $request->jurusan;
        $filename  = 'buku-tamu-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new GuestBookExport($startDate, $endDate, $kelas, $jurusan), $filename);
    }

    public function destroy($id)
    {
        $guestBook = GuestBook::findOrFail($id);
        $guestBook->delete();

        return redirect()->route('admin.guest-book.index')
            ->with('success', 'Data buku tamu berhasil dihapus');
    }
}