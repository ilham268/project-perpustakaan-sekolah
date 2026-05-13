<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use App\Models\ReturnBook;
use App\Models\BookItem;
use App\Models\Category;
use App\Models\GuestBook;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks       = Book::count();
        // UBAH: role 'peminjam' menjadi 'siswa'
        $totalAnggota     = User::where('role', 'siswa')->count();  // ← PERUBAHAN
        $totalDendaSudahBayar = ReturnBook::where('status', 'paid')->where('denda', '>', 0)->sum('denda');
        $totalKunjungan   = GuestBook::count();

        $chartData   = [];
        $chartLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M');  // tambah translatedFormat biar bahasa Indonesia
            $chartData[]   = Loan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $popularBooks = Book::withCount(['bookItems as total_pinjam' => function ($q) {
                $q->whereHas('loans');
            }])
            ->orderByDesc('total_pinjam')
            ->take(5)  // ubah dari 3 jadi 5
            ->get();

        $unpaidDenda = ReturnBook::with(['loan.user', 'loan.bookItem.book'])
            ->where('status', 'pending')
            ->where('denda', '>', 0)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalAnggota',
            'totalDendaSudahBayar',
            'totalKunjungan',
            'chartLabels',
            'chartData',
            'popularBooks',
            'unpaidDenda'
        ));
    }

    public function petugasDashboard()
    {
        $totalPending      = Loan::where('status', 'pending')->count();
        $activeLoan        = Loan::where('status', 'disetujui')->count();
        $todayReturns      = ReturnBook::whereDate('tanggal_pengembalian', today())->count();
        $unpaidDendaCount  = ReturnBook::where('status', 'pending')->where('denda', '>', 0)->count();

        $chartData   = [];
        $chartLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M');  // tambah translatedFormat
            $chartData[]   = Loan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $pendingLoans = Loan::with(['user', 'bookItem.book'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalPending',
            'activeLoan',
            'todayReturns',
            'unpaidDendaCount',
            'chartLabels',
            'chartData',
            'pendingLoans'
        ));
    }
}