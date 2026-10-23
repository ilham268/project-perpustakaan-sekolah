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
            'qr_content' => 'required|string|max:5000',
            'keperluan'  => 'nullable|string|max:255',
        ]);

        $rawCode = (string) $request->qr_content;
        $code = trim($rawCode);
        $keperluan = $request->keperluan ?: 'Berkunjung atau keperluan lainnya';

        // QR dapat berisi satu nomor, teks berlabel, atau JSON. Ekstrak kandidat
        // tanpa mengubah tanda penting seperti slash/titik pada nomor identitas.
        $identities = ['nisn' => [], 'nis' => [], 'other' => []];
        $name = null;
        $json = json_decode($code, true);
        if (is_array($json)) {
            foreach ($json as $key => $value) {
                if (!is_scalar($value)) continue;
                $key = strtolower((string) $key);
                $value = trim((string) $value);
                if ($value === '') continue;
                if (str_contains($key, 'nisn')) $identities['nisn'][] = $value;
                elseif (preg_match('/(^|_)nis($|_)/', $key)) $identities['nis'][] = $value;
                elseif (preg_match('/identitas|nik|nis|student.?id|(^|_)id($|_)/', $key)) $identities['other'][] = $value;
                elseif (in_array($key, ['nama', 'name', 'student_name'], true)) $name = $value;
            }
        } else {
            foreach (preg_split('/[\r\n,;|]+/', $code) as $line) {
                $line = trim($line);
                if (preg_match('/^\s*(NISN)\s*[:=\-]\s*(.+?)\s*$/iu', $line, $m)) $identities['nisn'][] = trim($m[2]);
                elseif (preg_match('/^\s*NIS\s*[:=\-]\s*(.+?)\s*$/iu', $line, $m)) $identities['nis'][] = trim($m[1]);
                elseif (preg_match('/^\s*(NIK|NIP|NO\.?\s*IDENTITAS|NOMOR\s+IDENTITAS|ID)\s*[:=\-]\s*(.+?)\s*$/iu', $line, $m)) $identities['other'][] = trim($m[2]);
                elseif (preg_match('/^\s*(NAMA|NAME)\s*[:=\-]\s*(.+?)\s*$/iu', $line, $m)) $name = trim($m[2]);
                elseif (!$name && preg_match('/\pL/u', $line) && !preg_match('/\s*[^:]+\s*[:=]/u', $line)) $name = $line;
            }
            if (!$identities['nisn'] && !$identities['nis'] && !$identities['other'] && preg_match('/^\d+$/', $code)) {
                $identities['other'][] = $code;
            }
        }

        $user = null;
        foreach (['nisn', 'nis', 'other'] as $priority) {
            foreach (array_unique($identities[$priority]) as $identity) {
                $user = $this->findStudentByIdentity($identity);
                if ($user) break 2;
            }
        }

        // Nama hanya boleh mengidentifikasi siswa jika tepat satu record cocok.
        if (!$user && $name) {
            $matches = User::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])->limit(2)->get();
            if ($matches->count() === 1) $user = $matches->first();
            elseif ($matches->count() > 1) {
                return response()->json([
                    'success' => false,
                    'registered' => false,
                    'ambiguous' => true,
                    'decoded' => true,
                    'qr_code' => $rawCode,
                    'message' => 'Identitas QR terbaca, tetapi nama cocok dengan lebih dari satu siswa. Mohon verifikasi identitas.',
                    'data' => null,
                ], 409);
            }
        }

        // Jika siswa tidak ditemukan, jangan buat otomatis
        if (!$user) {
            return response()->json([
                'success' => false,
                'registered' => false,
                'decoded' => true,
                'qr_code' => $rawCode,
                'message' => 'Identitas QR berhasil dibaca, tetapi siswa belum terdaftar di database.',
                'data' => null,
            ], 404);
        }

        // Cegah duplikasi absensi: cek apakah siswa sudah mengisi buku tamu hari ini
        $alreadyAttended = GuestBook::where('nama', $user->name)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success'          => false,
                'registered'      => true,
                'already_attended' => true,
                'qr_code'          => $rawCode,
                'message'          => 'Siswa ' . $user->name . ' sudah mengisi buku tamu hari ini.',
                'data'             => [
                    'nama'    => $user->name,
                    'nomor_identitas' => $user->nomor_identitas,
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
            'registered' => true,
            'qr_code' => $rawCode,
            'message' => 'Absensi berhasil dicatat.',
            'data'    => [
                'nama'    => $user->name,
                'nomor_identitas' => $user->nomor_identitas,
                'kelas'   => $user->kelas,
                'jurusan' => $user->jurusan,
            ],
        ]);
    }

    private function findStudentByIdentity(string $identity): ?User
    {
        $identity = trim($identity);
        if ($identity === '') return null;
        $user = User::whereRaw('LOWER(TRIM(nomor_identitas)) = ?', [mb_strtolower($identity)])->first();
        if ($user) return $user;

        // Kartu kadang menyisipkan spasi pemformatan. Jangan membuang slash/titik:
        // keduanya dapat menjadi bagian sah dari NIS.
        $compact = preg_replace('/\s+/u', '', $identity);
        if ($compact !== $identity) {
            return User::whereRaw("REPLACE(LOWER(TRIM(nomor_identitas)), ' ', '') = ?", [mb_strtolower($compact)])->first();
        }
        return null;
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
