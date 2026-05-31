<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_identitas', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        if ($request->query('show') === 'all') {
            $users = $query->latest()->get();
        } else {
            $users = $query->latest()->paginate(10)->withQueryString();
        }

        $kelasList = Kelas::orderBy('nama_kelas')
            ->orderBy('jurusan')
            ->get();

        $kelasFilterList = Kelas::whereNotNull('nama_kelas')
            ->where('nama_kelas', '!=', '')
            ->select('nama_kelas')
            ->distinct()
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas');

        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->select('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $kelasRombelList = Kelas::whereNotNull('nama_kelas')
            ->where('nama_kelas', '!=', '')
            ->whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->orderByRaw("FIELD(nama_kelas, 'X', 'XI', 'XII')")
            ->orderBy('jurusan')
            ->get();

        return view('admin.users.index', compact(
            'users',
            'kelasList',
            'kelasFilterList',
            'jurusanList',
            'kelasRombelList'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => ['required', 'string', 'max:20', 'unique:users,nomor_identitas'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas sudah digunakan.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'siswa' && !$request->filled('kelas_id') && !$request->filled('kelas')) {
                $validator->errors()->add('kelas_id', 'Kelas wajib dipilih untuk siswa.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kelasNama = null;
        $jurusanNama = null;

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::findOrFail($request->kelas_id);

            $kelasNama = $kelas->nama_kelas;
            $jurusanNama = $kelas->jurusan;
        } else {
            $kelasNama = $request->kelas;
            $jurusanNama = $request->jurusan;
        }

        User::create([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelasNama,
            'jurusan' => $jurusanNama,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('users.index')->with('success', true);
    }

    public function importExcel(Request $request)
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $request->validate([
            'kelas' => ['required', 'string', 'in:X,XI,XII'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ], [
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.in' => 'Kelas tidak valid.',
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat xlsx atau xls.',
        ]);

        $kelas = $request->kelas;
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());

        $dataImport = [];
        $dilewati = 0;
        $sheetDilewati = [];
        $kelasDariExcel = [];

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $jurusan = preg_replace('/\s+/', ' ', trim($sheet->getTitle()));

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $kolomNisn = null;
            $kolomNama = null;
            $barisHeader = null;

            for ($row = 1; $row <= min(40, $highestRow); $row++) {
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = Coordinate::stringFromColumnIndex($col);
                    $value = trim((string) $sheet->getCell($columnLetter . $row)->getFormattedValue());

                    $key = strtolower($value);
                    $key = str_replace(["\n", "\r", " ", "_", "-", ".", ":"], '', $key);

                    if ($key === 'nisn') {
                        $kolomNisn = $columnLetter;
                        $barisHeader = $row;
                    }

                    if (in_array($key, ['nama', 'namasiswa', 'namalengkap'])) {
                        $kolomNama = $columnLetter;
                        $barisHeader = $row;
                    }
                }

                if ($kolomNisn && $kolomNama) {
                    break;
                }
            }

            if (!$kolomNisn || !$kolomNama || !$barisHeader) {
                $sheetDilewati[] = $jurusan;
                continue;
            }

            if ($jurusan !== '') {
                $kelasDariExcel[$kelas . '|' . $jurusan] = [
                    'nama_kelas' => $kelas,
                    'jurusan' => $jurusan,
                ];
            }

            for ($row = $barisHeader + 1; $row <= $highestRow; $row++) {
                $nisnRaw = trim((string) $sheet->getCell($kolomNisn . $row)->getFormattedValue());
                $namaRaw = trim((string) $sheet->getCell($kolomNama . $row)->getFormattedValue());

                $nisn = preg_replace('/[^0-9]/', '', $nisnRaw);
                $nama = preg_replace('/\s+/', ' ', trim($namaRaw));

                if ($nisn === '' || $nama === '') {
                    $dilewati++;
                    continue;
                }

                if (strlen($nisn) < 8) {
                    $dilewati++;
                    continue;
                }

                $dataImport[$nisn] = [
                    'name' => $nama,
                    'nomor_identitas' => $nisn,
                    'kelas' => $kelas,
                    'jurusan' => $jurusan,
                    'role' => 'siswa',
                ];
            }
        }

        if (count($dataImport) === 0) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak ada data siswa yang berhasil dibaca dari Excel.');
        }

        foreach ($kelasDariExcel as $dataKelas) {
            Kelas::firstOrCreate([
                'nama_kelas' => $dataKelas['nama_kelas'],
                'jurusan' => $dataKelas['jurusan'],
            ]);
        }

        $nisnList = array_keys($dataImport);

        $existingUsers = User::whereIn('nomor_identitas', $nisnList)
            ->get()
            ->keyBy('nomor_identitas');

        $berhasil = 0;
        $diupdate = 0;
        $insertData = [];
        $now = now();

        foreach ($dataImport as $nisn => $data) {
            if ($existingUsers->has($nisn)) {
                DB::table('users')
                    ->where('nomor_identitas', $nisn)
                    ->update([
                        'name' => $data['name'],
                        'kelas' => $data['kelas'],
                        'jurusan' => $data['jurusan'],
                        'role' => 'siswa',
                        'updated_at' => $now,
                    ]);

                $diupdate++;
            } else {
                $insertData[] = [
                    'name' => $data['name'],
                    'nomor_identitas' => $data['nomor_identitas'],
                    'kelas' => $data['kelas'],
                    'jurusan' => $data['jurusan'],
                    'password' => Hash::make($nisn, ['rounds' => 4]),
                    'role' => 'siswa',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $berhasil++;
            }
        }

        foreach (array_chunk($insertData, 100) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $pesan = "Import selesai. Data baru: {$berhasil}, diupdate: {$diupdate}, dilewati: {$dilewati}.";

        if (count($kelasDariExcel) > 0) {
            $pesan .= ' Kelas/jurusan tersinkron: ' . count($kelasDariExcel) . '.';
        }

        if (count($sheetDilewati) > 0) {
            $pesan .= ' Sheet dilewati: ' . implode(', ', $sheetDilewati) . '.';
        }

        return redirect()
            ->route('users.index')
            ->with('success', $pesan);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'user_ids.required' => 'Pilih minimal satu user yang ingin dihapus.',
        ]);

        $userIds = collect($request->user_ids)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->reject(fn ($id) => $id === auth()->id())
            ->values();

        if ($userIds->isEmpty()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak ada user yang bisa dihapus. Akun yang sedang login tidak boleh dihapus.');
        }

        $jumlah = User::whereIn('id', $userIds)->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "Berhasil menghapus {$jumlah} user.");
    }

    public function promoteSelectedUsers(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'user_ids.required' => 'Pilih minimal satu siswa yang ingin dinaikkan.',
        ]);

        $userIds = collect($request->user_ids)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $students = User::whereIn('id', $userIds)
            ->where('role', 'siswa')
            ->get();

        if ($students->isEmpty()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak ada siswa yang bisa dinaikkan dari pilihan tersebut.');
        }

        $hasil = [
            'x_ke_xi' => 0,
            'xi_ke_xii' => 0,
            'xii_dihapus' => 0,
            'dilewati' => 0,
        ];

        DB::transaction(function () use ($students, &$hasil) {
            $now = now();
            $pasanganKelasYangDicek = [];

            foreach ($students as $student) {
                $kelasLama = $student->kelas;
                $jurusan = $student->jurusan;

                if (!$jurusan || !in_array($kelasLama, ['X', 'XI', 'XII'])) {
                    $hasil['dilewati']++;
                    continue;
                }

                $pasanganKelasYangDicek[] = [
                    'nama_kelas' => $kelasLama,
                    'jurusan' => $jurusan,
                ];

                if ($kelasLama === 'X') {
                    DB::table('users')
                        ->where('id', $student->id)
                        ->update([
                            'kelas' => 'XI',
                            'updated_at' => $now,
                        ]);

                    Kelas::firstOrCreate([
                        'nama_kelas' => 'XI',
                        'jurusan' => $jurusan,
                    ]);

                    $pasanganKelasYangDicek[] = [
                        'nama_kelas' => 'XI',
                        'jurusan' => $jurusan,
                    ];

                    $hasil['x_ke_xi']++;
                    continue;
                }

                if ($kelasLama === 'XI') {
                    DB::table('users')
                        ->where('id', $student->id)
                        ->update([
                            'kelas' => 'XII',
                            'updated_at' => $now,
                        ]);

                    Kelas::firstOrCreate([
                        'nama_kelas' => 'XII',
                        'jurusan' => $jurusan,
                    ]);

                    $pasanganKelasYangDicek[] = [
                        'nama_kelas' => 'XII',
                        'jurusan' => $jurusan,
                    ];

                    $hasil['xi_ke_xii']++;
                    continue;
                }

                if ($kelasLama === 'XII') {
                    DB::table('users')
                        ->where('id', $student->id)
                        ->delete();

                    $hasil['xii_dihapus']++;
                    continue;
                }

                $hasil['dilewati']++;
            }

            foreach ($pasanganKelasYangDicek as $pasangan) {
                if (!$pasangan['nama_kelas'] || !$pasangan['jurusan']) {
                    continue;
                }

                $masihAdaSiswa = User::where('role', 'siswa')
                    ->where('kelas', $pasangan['nama_kelas'])
                    ->where('jurusan', $pasangan['jurusan'])
                    ->exists();

                if (!$masihAdaSiswa) {
                    Kelas::where('nama_kelas', $pasangan['nama_kelas'])
                        ->where('jurusan', $pasangan['jurusan'])
                        ->delete();
                }
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', "Naik siswa dipilih selesai. X ke XI: {$hasil['x_ke_xi']}, XI ke XII: {$hasil['xi_ke_xii']}, XII dihapus/lulus: {$hasil['xii_dihapus']}, dilewati: {$hasil['dilewati']}.");
    }

    public function promoteClasses(Request $request)
    {
        $request->validate([
            'confirm' => ['accepted'],
            'rombels' => ['required', 'array'],
            'rombels.*' => ['string'],
        ], [
            'confirm.accepted' => 'Centang konfirmasi terlebih dahulu.',
            'rombels.required' => 'Pilih minimal satu kelas/rombel yang ingin dinaikkan.',
        ]);

        $rombels = collect($request->rombels)
            ->map(function ($value) {
                $parts = explode('|', $value, 2);

                return [
                    'kelas' => $parts[0] ?? null,
                    'jurusan' => $parts[1] ?? null,
                ];
            })
            ->filter(function ($item) {
                return in_array($item['kelas'], ['X', 'XI', 'XII']) && !empty($item['jurusan']);
            })
            ->values();

        if ($rombels->isEmpty()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Pilihan kelas/rombel tidak valid.');
        }

        $hasil = [
            'x_ke_xi' => 0,
            'xi_ke_xii' => 0,
            'xii_dihapus' => 0,
        ];

        DB::transaction(function () use ($rombels, &$hasil) {
            $now = now();

            foreach ($rombels as $rombel) {
                $kelas = $rombel['kelas'];
                $jurusan = $rombel['jurusan'];

                if ($kelas === 'X') {
                    $jumlah = User::where('role', 'siswa')
                        ->where('kelas', 'X')
                        ->where('jurusan', $jurusan)
                        ->update([
                            'kelas' => 'XI',
                            'updated_at' => $now,
                        ]);

                    if ($jumlah > 0) {
                        Kelas::firstOrCreate([
                            'nama_kelas' => 'XI',
                            'jurusan' => $jurusan,
                        ]);

                        $masihAdaSiswa = User::where('role', 'siswa')
                            ->where('kelas', 'X')
                            ->where('jurusan', $jurusan)
                            ->exists();

                        if (!$masihAdaSiswa) {
                            Kelas::where('nama_kelas', 'X')
                                ->where('jurusan', $jurusan)
                                ->delete();
                        }
                    }

                    $hasil['x_ke_xi'] += $jumlah;
                }

                if ($kelas === 'XI') {
                    $jumlah = User::where('role', 'siswa')
                        ->where('kelas', 'XI')
                        ->where('jurusan', $jurusan)
                        ->update([
                            'kelas' => 'XII',
                            'updated_at' => $now,
                        ]);

                    if ($jumlah > 0) {
                        Kelas::firstOrCreate([
                            'nama_kelas' => 'XII',
                            'jurusan' => $jurusan,
                        ]);

                        $masihAdaSiswa = User::where('role', 'siswa')
                            ->where('kelas', 'XI')
                            ->where('jurusan', $jurusan)
                            ->exists();

                        if (!$masihAdaSiswa) {
                            Kelas::where('nama_kelas', 'XI')
                                ->where('jurusan', $jurusan)
                                ->delete();
                        }
                    }

                    $hasil['xi_ke_xii'] += $jumlah;
                }

                if ($kelas === 'XII') {
                    $jumlah = User::where('role', 'siswa')
                        ->where('kelas', 'XII')
                        ->where('jurusan', $jurusan)
                        ->delete();

                    if ($jumlah > 0) {
                        $masihAdaSiswa = User::where('role', 'siswa')
                            ->where('kelas', 'XII')
                            ->where('jurusan', $jurusan)
                            ->exists();

                        if (!$masihAdaSiswa) {
                            Kelas::where('nama_kelas', 'XII')
                                ->where('jurusan', $jurusan)
                                ->delete();
                        }
                    }

                    $hasil['xii_dihapus'] += $jumlah;
                }
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', "Naik kelas selesai. X ke XI: {$hasil['x_ke_xi']}, XI ke XII: {$hasil['xi_ke_xii']}, XII dihapus/lulus: {$hasil['xii_dihapus']}.");
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nomor_identitas')->ignore($user->id),
            ],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas sudah digunakan.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'siswa' && !$request->filled('kelas_id') && !$request->filled('kelas')) {
                $validator->errors()->add('kelas_id', 'Kelas wajib dipilih untuk siswa.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kelasNama = null;
        $jurusanNama = null;

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::findOrFail($request->kelas_id);

            $kelasNama = $kelas->nama_kelas;
            $jurusanNama = $kelas->jurusan;
        } else {
            $kelasNama = $request->kelas;
            $jurusanNama = $request->jurusan;
        }

        $user->update([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelasNama,
            'jurusan' => $jurusanNama,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('updated', true);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('deleted', true);
    }
}