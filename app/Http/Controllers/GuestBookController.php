<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use App\Models\User;
use App\Exports\GuestBookExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    /**
     * Halaman buku tamu / absensi manual
     */
    public function create()
    {
        // Jika user sedang login (Siswa/Peminjam)
        if (auth()->check()) {
            $currentUser = auth()->user();

            return view('guest-book.create', compact('currentUser'));
        }

        // Ambil semua data user/siswa
        $siswaList = User::orderBy('name', 'asc')
            ->get([
                'id',
                'name',
                'nomor_identitas',
                'kelas',
                'jurusan'
            ]);

        return view('guest-book.create', compact('siswaList'));
    }

    /**
     * Simpan absensi manual
     */
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
            'kelas'            => 'nullable|string|max:255',
            'jurusan'          => 'nullable|string|max:255',
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
            'nama'           => $nama,
            'kelas'          => $kelas,
            'jurusan'        => $jurusan,
            'keperluan'      => $keperluan,
            'metode_absensi' => 'manual',
        ]);

        return redirect()
            ->route('guest-book.create')
            ->with('success', 'Terima kasih telah mengisi buku tamu!');
    }

    /**
     * Menerima hasil scan QR code dari webcam
     */
    public function qrScan(Request $request)
    {
        $request->validate([
            'qr_content' => 'required|string|max:255',
            'keperluan'  => 'nullable|string|max:255',
        ]);

        $code = trim($request->qr_content);
        $keperluan = $request->keperluan ?: 'Berkunjung atau keperluan lainnya';

        // Cek jika format QR adalah JSON (misal {"nisn":"..."} atau {"nomor_identitas":"..."})
        $user = null;
        $json = json_decode($code, true);
        if (is_array($json)) {
            $nisn = $json['nisn'] ?? $json['nomor_identitas'] ?? $json['nis'] ?? $json['id'] ?? null;
            $nama = $json['nama'] ?? $json['name'] ?? null;
            if ($nisn) {
                $user = User::where('nomor_identitas', $nisn)->first() ?: (is_numeric($nisn) ? User::where('id', $nisn)->first() : null);
            }
            if (!$user && $nama) {
                $user = User::where('name', $nama)->first();
            }
        }

        // Cari siswa berdasarkan nomor_identitas terlebih dahulu, lalu id, lalu nama
        if (!$user) {
            $user = User::where('nomor_identitas', $code)->first();
        }

        if (!$user && is_numeric($code)) {
            $user = User::where('id', $code)->first();
        }

        if (!$user) {
            $user = User::where('name', $code)->first();
        }

        // Jika siswa tidak ditemukan, jangan buat otomatis
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan untuk QR Code: ' . $code,
            ], 404);
        }

        // Cegah duplikasi absensi: cek apakah siswa sudah mengisi buku tamu hari ini
        $alreadyAttended = GuestBook::where('nama', $user->name)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success'          => false,
                'already_attended' => true,
                'message'          => 'Siswa ' . $user->name . ' sudah mengisi buku tamu hari ini.',
                'data'             => [
                    'nama'    => $user->name,
                    'kelas'   => $user->kelas,
                    'jurusan' => $user->jurusan,
                ],
            ], 200);
        }

        // Catat absensi
        GuestBook::create([
            'nama'           => $user->name,
            'kelas'          => $user->kelas,
            'jurusan'        => $user->jurusan,
            'keperluan'      => $keperluan,
            'metode_absensi' => 'QR Code',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data'    => [
                'nama'    => $user->name,
                'kelas'   => $user->kelas,
                'jurusan' => $user->jurusan,
            ],
        ]);
    }

    /**
     * Menerima UID dari RFID reader / ESP32
     */
    public function rfid(Request $request)
    {
        $request->validate([
            'rfid_uid'  => 'required|string|max:255',
            'keperluan' => 'nullable|string|max:255',
        ]);

        // Cari siswa berdasarkan UID RFID
        $user = User::where('rfid_uid', $request->rfid_uid)->first();

        // Jika kartu RFID belum terdaftar
        if (!$user) {
            return response()->json([
                'success'    => false,
                'registered' => false,
                'message'    => 'Kartu RFID belum terdaftar.',
                'rfid_uid'   => $request->rfid_uid,
            ]);
        }

        $keperluan = $request->keperluan ?: 'Berkunjung atau keperluan lainnya';

        // Jika kartu sudah terdaftar,
        // langsung catat absensi
        GuestBook::create([
            'nama'           => $user->name,
            'kelas'          => $user->kelas,
            'jurusan'        => $user->jurusan,
            'keperluan'      => $keperluan,
            'metode_absensi' => 'RFID',
        ]);

        return response()->json([
            'success'    => true,
            'registered' => true,
            'message'    => 'Absensi berhasil.',
            'data'       => [
                'nama'    => $user->name,
                'kelas'   => $user->kelas,
                'jurusan' => $user->jurusan,
            ],
        ]);
    }

    /**
     * Mendaftarkan UID RFID ke siswa
     * sekaligus mencatat absensi pertama.
     */
    public function registerRfid(Request $request)
    {
        $request->validate([
            'rfid_uid'        => 'required|string|max:255',
            'user_id'         => 'required|integer|exists:users,id',
            'nomor_identitas' => 'required|string|max:255',
            'keperluan'       => 'nullable|string|max:255',
        ]);

        // Cari siswa yang dipilih
        $user = User::findOrFail($request->user_id);

        // Verifikasi nomor identitas
        if ($user->nomor_identitas !== $request->nomor_identitas) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor identitas tidak sesuai dengan siswa yang dipilih.',
            ], 422);
        }

        // Cek apakah UID sudah digunakan siswa lain
        $uidSudahDipakai = User::where('rfid_uid', $request->rfid_uid)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($uidSudahDipakai) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID ini sudah terdaftar pada siswa lain.',
            ], 422);
        }

        // Simpan UID ke data siswa
        $user->rfid_uid = $request->rfid_uid;
        $user->save();

        $keperluan = $request->keperluan ?: 'Berkunjung atau keperluan lainnya';

        // Catat absensi pertama
        GuestBook::create([
            'nama'           => $user->name,
            'kelas'          => $user->kelas,
            'jurusan'        => $user->jurusan,
            'keperluan'      => $keperluan,
            'metode_absensi' => 'RFID',
        ]);

        return response()->json([
            'success'    => true,
            'registered' => true,
            'message'    => 'Kartu RFID berhasil didaftarkan dan absensi pertama berhasil dicatat.',
            'data'       => [
                'nama'    => $user->name,
                'kelas'   => $user->kelas,
                'jurusan' => $user->jurusan,
            ],
        ]);
    }

    /**
     * Halaman baca-saja (read-only) buku tamu siswa untuk pengunjung
     */
    public function publicIndex(Request $request)
    {
        $query = GuestBook::query();

        // Pencarian nama, kelas, jurusan, atau keperluan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%')
                    ->orWhere('jurusan', 'like', '%' . $search . '%')
                    ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
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

        $totalKunjungan = GuestBook::count();
        $todayKunjungan = GuestBook::whereDate('created_at', today())->count();

        $kelasList = GuestBook::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas');

        return view('guest-book.public-index', compact(
            'guestBooks',
            'totalKunjungan',
            'todayKunjungan',
            'kelasList'
        ));
    }

    /**
     * Halaman admin buku tamu
     */
    public function adminIndex(Request $request)
    {
        $query = GuestBook::query();

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%')
                    ->orWhere('jurusan', 'like', '%' . $search . '%')
                    ->orWhere('keperluan', 'like', '%' . $search . '%');
            });
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        // Ambil data terbaru
        $guestBooks = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Daftar kelas
        $listKelas = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas', 'asc')
            ->pluck('kelas');

        // Daftar jurusan
        $listJurusan = User::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan', 'asc')
            ->pluck('jurusan');

        // Statistik
        $totalKunjungan = GuestBook::count();

        $todayKunjungan = GuestBook::whereDate(
            'created_at',
            today()
        )->count();

        $monthKunjungan = GuestBook::whereMonth(
            'created_at',
            now()->month
        )
            ->whereYear(
                'created_at',
                now()->year
            )
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

    /**
     * Export data buku tamu
     */
    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $kelas     = $request->kelas;
        $jurusan   = $request->jurusan;

        $filename = 'buku-tamu-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new GuestBookExport(
                $startDate,
                $endDate,
                $kelas,
                $jurusan
            ),
            $filename
        );
    }

    /**
     * Hapus data buku tamu
     */
    public function destroy($id)
    {
        $guestBook = GuestBook::findOrFail($id);

        $guestBook->delete();

        return redirect()
            ->route('admin.guest-book.index')
            ->with('success', 'Data buku tamu berhasil dihapus');
    }
}