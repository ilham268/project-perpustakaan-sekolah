<?php

namespace App\Http\Controllers;

use App\Models\TeacherGuestBook;
use App\Exports\TeacherGuestBookExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TeacherGuestBookController extends Controller
{
    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            $term = trim((string) $request->query('q', ''));
            if (mb_strlen($term) < 2) {
                return response()->json([]);
            }

            $teachers = User::query()
                ->where('role', 'guru')
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name']);

            return response()->json($teachers);
        }

        $selectedGuru = old('user_id')
            ? User::where('role', 'guru')->find(old('user_id'))
            : null;

        return view('teacher-guest-book.create', compact('selectedGuru'));
    }

    public function store(Request $request)
    {
        $predefined = $this->teacherVisitPurposes();

        $validated = $request->validate([
            'user_id'          => 'required|integer|exists:users,id',
            'keperluan'        => 'required|string|in:' . implode(',', $predefined),
            'keperluan_custom' => 'nullable|string|max:500',
        ], [
            'user_id.required' => 'Guru belum dipilih.',
            'user_id.integer' => 'Guru belum dipilih.',
            'user_id.exists' => 'Guru belum dipilih.',
            'keperluan.required' => 'Keperluan wajib dipilih.',
            'keperluan.in' => 'Keperluan yang dipilih tidak valid.',
        ]);

        $teacher = User::find($validated['user_id']);
        if (!$teacher || $teacher->role !== 'guru') {
            return back()->withErrors(['user_id' => $teacher ? 'Data yang dipilih bukan data guru.' : 'Guru belum dipilih.'])->withInput();
        }

        $keperluan = $validated['keperluan'];

        if ($keperluan === 'Lainnya') {
            $request->validate([
                'keperluan_custom' => 'required|string|max:500',
            ], [
                'keperluan_custom.required' => 'Mohon jelaskan keperluan Anda jika memilih opsi Lainnya.',
            ]);
            $keperluan = 'Lainnya: ' . trim($request->keperluan_custom);
        }

        DB::transaction(function () use ($teacher, $keperluan) {
            TeacherGuestBook::create([
                'user_id' => $teacher->id,
                'nama' => $teacher->name,
                'keperluan' => $keperluan,
                'metode_absensi' => 'manual',
            ]);
        });

        return redirect()->route('teacher-guest-book.create')->with('success', 'Data kunjungan berhasil disimpan.');
    }

    private function teacherVisitPurposes(): array
    {
        return [
            'Berkunjung atau keperluan lainnya',
            'Rapat',
            'Konsultasi',
            'Mengajar',
            'Mengurus administrasi',
            'Bertemu guru/staf',
            'Bertemu kepala sekolah',
            'Mengantar dokumen',
            'Keperluan perpustakaan',
            'Lainnya',
        ];
    }

    /**
     * Halaman baca-saja (read-only) buku tamu guru untuk pengunjung
     */
    public function publicIndex(Request $request)
    {
        $query = TeacherGuestBook::with('user');

        // Pencarian nama atau keperluan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keperluan', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn ($userQuery) => $userQuery->where('nomor_identitas', 'like', '%' . $search . '%'));
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
        $query = TeacherGuestBook::with('user');

        // Pencarian nama atau keperluan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keperluan', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn ($userQuery) => $userQuery->where('nomor_identitas', 'like', '%' . $search . '%'));
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

}
