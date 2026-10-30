<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Throwable;

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

        $perPage = $request->query('show') === 'all' ? 50 : 25;
        $users = $query->latest()->paginate($perPage)->withQueryString();

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
        $isGuru = $request->input('role') === 'guru';
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => array_merge($isGuru ? ['nullable'] : ['required'], ['string', 'max:20', 'unique:users,nomor_identitas']),
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => $isGuru ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa,guru'],
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
            'nomor_identitas' => $request->filled('nomor_identitas') ? trim($request->nomor_identitas) : null,
            'kelas' => $isGuru ? null : $kelasNama,
            'jurusan' => $isGuru ? null : $jurusanNama,
            'password' => $request->filled('password') ? Hash::make($request->password) : Hash::make(Str::random(64)),
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

        $validated = $request->validate([
            'jenis_import' => ['required', 'in:siswa,guru'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ], [
            'jenis_import.required' => 'Jenis import wajib dipilih.',
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat xlsx atau xls.',
        ]);

        if ($validated['jenis_import'] === 'guru') {
            return $this->importTeachersFromExcel($request);
        }

        $request->validate([
            'kelas' => ['required', 'string', 'in:X,XI,XII'],
        ], [
            'kelas.required' => 'Kelas wajib dipilih untuk import siswa.',
            'kelas.in' => 'Kelas tidak valid.',
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

        $berhasil = 0;
        $diupdate = 0;
        $insertData = [];
        $now = now();
        DB::transaction(function () use ($kelasDariExcel, $dataImport, $now, &$berhasil, &$diupdate, &$insertData) {
            foreach ($kelasDariExcel as $dataKelas) {
                Kelas::firstOrCreate([
                    'nama_kelas' => $dataKelas['nama_kelas'],
                    'jurusan' => $dataKelas['jurusan'],
                ]);
            }

            $nisnList = array_keys($dataImport);
            $existingUsers = User::whereIn('nomor_identitas', $nisnList)->get()->keyBy('nomor_identitas');

            foreach ($dataImport as $nisn => $data) {
                if ($existingUsers->has($nisn)) {
                    DB::table('users')->where('nomor_identitas', $nisn)->update([
                        'name' => $data['name'], 'kelas' => $data['kelas'], 'jurusan' => $data['jurusan'],
                        'role' => 'siswa', 'updated_at' => $now,
                    ]);
                    $diupdate++;
                } else {
                    $insertData[] = [
                        'name' => $data['name'], 'nomor_identitas' => $data['nomor_identitas'],
                        'kelas' => $data['kelas'], 'jurusan' => $data['jurusan'],
                        'password' => Hash::make($nisn, ['rounds' => 4]), 'role' => 'siswa',
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                    $berhasil++;
                }
            }

            foreach (array_chunk($insertData, 100) as $chunk) {
                DB::table('users')->insert($chunk);
            }
        });

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

    private function importTeachersFromExcel(Request $request)
    {
        $sheetNamesWithoutName = [];
        $names = [];
        $skippedMetadataOrBlankRows = 0;
        $namesTooLong = 0;
        $existingDuplicates = 0;
        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $highestRow = $sheet->getHighestDataRow();
                $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
                $nameColumn = null;
                $headerRow = null;
                $nameAliasColumn = null;
                $nameAliasRow = null;

                for ($row = 1; $row <= min(60, $highestRow) && !$nameColumn; $row++) {
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $value = (string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row)->getFormattedValue();
                        $key = mb_strtolower(preg_replace('/[\s_\-.:]+/u', '', trim($value)));
                        if ($key === 'nama') {
                            $nameColumn = Coordinate::stringFromColumnIndex($col);
                            $headerRow = $row;
                            break;
                        }
                        if ($nameAliasColumn === null && in_array($key, ['namaguru', 'namalengkap'], true)) {
                            $nameAliasColumn = Coordinate::stringFromColumnIndex($col);
                            $nameAliasRow = $row;
                        }
                    }
                }

                if ($nameColumn === null && $nameAliasColumn !== null) {
                    $nameColumn = $nameAliasColumn;
                    $headerRow = $nameAliasRow;
                }

                if (!$nameColumn) {
                    $sheetNamesWithoutName[] = $sheet->getTitle();
                    continue;
                }

                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    $rowHasData = false;
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        if (trim((string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row)->getFormattedValue()) !== '') {
                            $rowHasData = true;
                            break;
                        }
                    }
                    if (!$rowHasData) {
                        $skippedMetadataOrBlankRows++;
                        continue;
                    }

                    $name = trim((string) $sheet->getCell($nameColumn . $row)->getFormattedValue());
                    if ($name === '') {
                        $skippedMetadataOrBlankRows++;
                        continue;
                    }

                    $normalizedName = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', '', $name));
                    $metadataValues = [
                        'no', 'nomor', 'nomorurut', 'nourut', 'urut', 'nip', 'nuptk',
                        'nama', 'namaguru', 'namalengkap', 'jumlah', 'total', 'rekap', 'daftar',
                        'guru', 'staf', 'karyawan', 'pegawai', 'keterangan', 'ket', 'tanggal',
                        'halaman', 'jabatan', 'pangkat', 'golongan', 'unitkerja', 'statuspegawai',
                    ];
                    if (!preg_match('/\p{L}/u', $name) || in_array($normalizedName, $metadataValues, true)) {
                        $skippedMetadataOrBlankRows++;
                        continue;
                    }

                    if (mb_strlen($name) > 255) {
                        $namesTooLong++;
                        continue;
                    }
                    $names[] = $name;
                }
            }
        } catch (Throwable $exception) {
            return redirect()->route('users.index')->with('error', 'File Excel tidak dapat dibaca. Pastikan file tidak rusak dan formatnya didukung.');
        }

        $uniqueNames = [];
        $duplicateRows = 0;
        foreach ($names as $name) {
            $key = mb_strtolower(trim($name));
            if (isset($uniqueNames[$key])) {
                $duplicateRows++;
                continue;
            }
            $uniqueNames[$key] = $name;
        }

        $imported = 0;
        try {
            DB::transaction(function () use ($uniqueNames, &$imported, &$existingDuplicates) {
                $existingNames = User::query()->where('role', 'guru')->get(['name'])
                    ->mapWithKeys(fn ($user) => [mb_strtolower(trim($user->name)) => true])->all();
                $insert = [];
                $now = now();

                foreach ($uniqueNames as $key => $name) {
                    if (isset($existingNames[$key])) {
                        $existingDuplicates++;
                        continue;
                    }
                    $insert[] = [
                        'name' => $name,
                        'nomor_identitas' => null,
                        'kelas' => null,
                        'jurusan' => null,
                        'rfid_uid' => null,
                        'password' => Hash::make(Str::random(64)),
                        'role' => 'guru',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (array_chunk($insert, 100) as $chunk) {
                    DB::table('users')->insert($chunk);
                    $imported += count($chunk);
                }
            });
        } catch (Throwable $exception) {
            return redirect()->route('users.index')->with('error', 'Database error: import guru dibatalkan seluruhnya dan tidak ada data baru yang disimpan. Periksa struktur database, lalu coba lagi.');
        }

        $duplicateRows += $existingDuplicates;
        $message = 'Import Guru berhasil. Berhasil: ' . $imported . ' guru. Dilewati: '
            . $duplicateRows . ' data duplikat. Baris metadata/kosong dilewati: '
            . $skippedMetadataOrBlankRows . '.';
        if ($namesTooLong > 0) {
            $message .= ' Nama melebihi batas 255 karakter: ' . $namesTooLong . ' baris.';
        }

        $details = [];
        if ($sheetNamesWithoutName) {
            $details[] = 'Sheet dilewati karena tidak memiliki kolom NAMA: ' . implode(', ', $sheetNamesWithoutName) . '.';
        }

        return redirect()->route('users.index')->with('success', $message)->with('import_errors', $details);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1', 'max:100'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ], [
            'user_ids.required' => 'Pilih minimal satu user yang ingin dihapus.',
        ]);

        $ids = collect($validated['user_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $query = User::query()->whereIn('id', $ids);

        $targetCount = (clone $query)->count();
        if ($targetCount === 0) {
            return redirect()->route('users.index')->with('error', 'Tidak ada user yang cocok dengan pilihan tersebut.');
        }

        $safeQuery = (clone $query)
            ->where('id', '!=', auth()->id())
            ->where('role', '!=', 'admin')
            ->whereDoesntHave('loans')
            ->whereDoesntHave('pinjamKelas')
            ->whereDoesntHave('teacherGuestBooks')
            ->whereDoesntHave('carts');
        $eligibleCount = (clone $safeQuery)->count();

        $deletedCount = DB::transaction(fn () => $safeQuery->delete());
        $protectedCount = $targetCount - $eligibleCount;
        $message = "{$deletedCount} user berhasil dihapus.";
        if ($protectedCount > 0) {
            $message .= " {$protectedCount} user dipertahankan karena akun admin/yang sedang digunakan atau masih memiliki histori/transaksi.";
        }

        return redirect()->route('users.index')->with('success', $message);
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
        $isGuru = $request->input('role') === 'guru';
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => array_merge($isGuru ? ['nullable'] : ['required'], [
                'string',
                'max:20',
                Rule::unique('users', 'nomor_identitas')->ignore($user->id),
            ]),
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa,guru'],
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
            'nomor_identitas' => $request->filled('nomor_identitas') ? trim($request->nomor_identitas) : null,
            'kelas' => $isGuru ? null : $kelasNama,
            'jurusan' => $isGuru ? null : $jurusanNama,
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
        if ($user->role === 'admin' || $user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Akun admin atau akun yang sedang digunakan tidak dapat dihapus.');
        }

        if ($user->loans()->exists() || $user->pinjamKelas()->exists() || $user->teacherGuestBooks()->exists() || $user->carts()->exists()) {
            return redirect()->route('users.index')->with('error', 'User tidak dapat dihapus karena masih memiliki histori atau transaksi.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('deleted', true);
    }
}
