<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use App\Exports\GuestBookExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    public function create()
    {
        return view('guest-book.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'keperluan' => 'required|string',
        ]);

        GuestBook::create([
            'nama' => $request->nama,
            'kelas' => $request->kelas,
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
                  ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $guestBooks = $query->latest()->paginate(10)->withQueryString();

        $totalKunjungan = GuestBook::count();
        $todayKunjungan = GuestBook::whereDate('created_at', today())->count();
        $monthKunjungan = GuestBook::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.guest-book.index', compact(
            'guestBooks',
            'totalKunjungan',
            'todayKunjungan',
            'monthKunjungan'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $filename = 'buku-tamu-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new GuestBookExport($startDate, $endDate), $filename);
    }

    public function destroy($id)
    {
        $guestBook = GuestBook::findOrFail($id);
        $guestBook->delete();

        return redirect()->route('admin.guest-book.index')
            ->with('success', 'Data buku tamu berhasil dihapus');
    }
}