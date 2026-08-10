<?php

namespace App\Http\Controllers;

use App\Exports\PinjamKelasExport;
use App\Models\Book;
use App\Models\BookItem;
use App\Models\Kelas;
use App\Models\PinjamKelas;
use App\Models\User;
use App\Services\PinjamKelasBulkImportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class KategoriPinjamController extends Controller
{
    public function index(Request $request)
    {
        $booksPaketQuery = Book::query()
            ->withCount([
                'bookItems as total_eksemplar',
                'bookItems as kode_terisi' => function ($q) {
                    $q->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
            ])
            ->where(function ($q) {
                $this->filterPaket($q);
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $booksPaketQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tahun_pengadaan')) {
            $booksPaketQuery->where('tahun_pengadaan', $request->tahun_pengadaan);
        }

        $booksPaket = $booksPaketQuery
            ->orderByDesc('tahun_pengadaan')
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        $tahunOptions = Book::query()
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->whereNotNull('tahun_pengadaan')
            ->distinct()
            ->orderByDesc('tahun_pengadaan')
            ->pluck('tahun_pengadaan');

        $totalPaket = Book::query()
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->count();

        $totalEksemplar = BookItem::whereHas('book', function ($q) {
                $this->filterPaket($q);
            })
            ->count();

        $totalKodeTerisi = BookItem::whereHas('book', function ($q) {
                $this->filterPaket($q);
            })
            ->whereNotNull('kode_buku')
            ->where('kode_buku', '!=', '')
            ->count();

        $kategoris = new LengthAwarePaginator([], 0, 10, 1, [
            'path' => request()->url(),
        ]);

        $kelasList = collect();
        $jurusanList = collect();

        return view('admin.pinjamkelas.kategori', compact(
            'booksPaket',
            'tahunOptions',
            'totalPaket',
            'totalEksemplar',
            'totalKodeTerisi',
            'kategoris',
            'kelasList',
            'jurusanList'
        ));
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Kategori kelas sudah tidak digunakan. Buku Paket diambil dari data import Excel.');
    }

    public function update(Request $request, $id)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Edit kategori kelas sudah tidak digunakan. Kelola data melalui menu Buku Paket.');
    }

    public function destroy($id)
    {
        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Hapus kategori kelas sudah tidak digunakan. Buku Paket dikelola dari data buku.');
    }

    public function show($id)
    {
        $book = Book::where('id', $id)
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->first();

        if ($book) {
            return redirect()->route('books.show', $book->id);
        }

        return redirect()
            ->route('admin.pinjamkelas.kategori')
            ->with('error', 'Buku Paket tidak ditemukan.');
    }

    public function formPinjam(Request $request)
    {
        $siswas = User::where('role', 'siswa')
            ->orderBy('name')
            ->get();

        $booksPaketRaw = Book::query()
            ->withCount([
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                },
            ])
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->orderBy('judul')
            ->get();

        $booksPaket = $booksPaketRaw->groupBy(function ($item) {
            return strtolower(trim($item->judul));
        })->map(function ($group) {
            $first = $group->first();
            $first->stok_tersedia = $group->sum('stok_tersedia');
            $first->all_ids = $group->pluck('id')->implode(',');
            return $first;
        })->values();

        return view('admin.pinjamkelas.input.peminjaman', compact('siswas', 'booksPaket'));
    }

    public function prosesPinjam(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'user_id' => ['required', 'exists:users,id'],
            'kode_buku' => ['required', 'string', 'max:100'],
        ]);

        $kodeBuku = strtoupper(trim($request->kode_buku));

        try {
            DB::beginTransaction();

            $bookDropdown = Book::where('id', $request->book_id)->first();

            if (!$bookDropdown) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Buku yang dipilih tidak ditemukan.');
            }

            $user = User::where('id', $request->user_id)
                ->where('role', 'siswa')
                ->first();

            if (!$user) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'User siswa tidak ditemukan.');
            }

            // Dropdown di-grouping by judul (banyak book_id bisa berjudul sama),
            // jadi cari SEMUA id buku yang judulnya persis sama dengan yang dipilih,
            // bukan cuma id tunggal dari dropdown.
            $relatedBookIds = Book::where(function ($q) {
                    $this->filterPaket($q);
                })
                ->whereRaw('LOWER(TRIM(judul)) = ?', [strtolower(trim($bookDropdown->judul))])
                ->pluck('id');

            $bookItem = BookItem::with('book')
                ->whereIn('book_id', $relatedBookIds)
                ->whereNotNull('kode_buku')
                ->where('kode_buku', '!=', '')
                ->whereRaw('UPPER(kode_buku) = ?', [$kodeBuku])
                ->lockForUpdate()
                ->first();

            if (!$bookItem) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku fisik tidak ditemukan untuk judul buku yang dipilih di dropdown.');
            }

            if ($bookItem->status !== 'available') {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku ini sudah dipinjam atau tidak tersedia.');
            }

            $sedangDipinjam = PinjamKelas::whereIn('book_id', $relatedBookIds)
                ->where('kode_buku', $bookItem->kode_buku)
                ->whereIn('status', ['pending', 'disetujui'])
                ->lockForUpdate()
                ->exists();

            if ($sedangDipinjam) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku ini masih dalam proses peminjaman.');
            }

            PinjamKelas::create([
                'book_id' => $bookItem->book_id,
                'user_id' => $user->id,
                'kode_buku' => $bookItem->kode_buku,
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays(7),
                'status' => 'pending',
            ]);

            $bookItem->update([
                'status' => 'borrowed',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('success', 'Peminjaman Buku Paket berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan peminjaman: ' . $e->getMessage());
        }
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $path = $request->file('file')->store('temp-import', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $service = new PinjamKelasBulkImportService();
        $sheets = $service->listSheetsInfo($fullPath);

        if (empty($sheets)) {
            @unlink($fullPath);

            return redirect()
                ->route('admin.pinjamkelas.input-peminjaman')
                ->with('error', 'Format file tidak dikenali. Pastikan file sesuai template data siswa peminjam.');
        }

        return view('admin.pinjamkelas.input.pilih-jurusan', [
            'tempFile' => $path,
            'sheets' => $sheets,
            'doneSheets' => [],
        ]);
    }

    public function selectSheet(Request $request)
    {
        $request->validate([
            'temp_file' => ['required', 'string'],
            'sheet_name' => ['required', 'string'],
            'done_sheets' => ['nullable', 'string'],
        ]);

        $fullPath = Storage::disk('local')->path($request->temp_file);

        if (!file_exists($fullPath)) {
            return redirect()
                ->route('admin.pinjamkelas.input-peminjaman')
                ->with('error', 'File sementara sudah tidak ditemukan, silakan upload ulang.');
        }

        $service = new PinjamKelasBulkImportService();
        $found = $service->extractSubjectLabelsForSheet($fullPath, $request->sheet_name);

        if (empty($found)) {
            // Sheet ini tidak ada mapel untuk dipetakan, langsung proses.
            return $this->runImportSheet($fullPath, $request->temp_file, $request->sheet_name, [], $request->done_sheets);
        }

        $booksPaket = Book::query()
            ->withCount('bookItems')
            ->where(function ($q) {
                $this->filterPaket($q);
            })
            ->orderBy('judul')
            ->get();

        return view('admin.pinjamkelas.input.mapping', [
            'tempFile' => $request->temp_file,
            'sheetName' => $request->sheet_name,
            'doneSheets' => $request->done_sheets,
            'unmapped' => $found,
            'booksPaket' => $booksPaket,
        ]);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'temp_file' => ['required', 'string'],
            'sheet_name' => ['required', 'string'],
            'keys' => ['required', 'array'],
            'book_ids' => ['required', 'array'],
        ]);

        $fullPath = Storage::disk('local')->path($request->temp_file);

        if (!file_exists($fullPath)) {
            return redirect()
                ->route('admin.pinjamkelas.input-peminjaman')
                ->with('error', 'File sementara sudah tidak ditemukan, silakan upload ulang.');
        }

        // Mapping ini HANYA dipakai untuk sesi import saat ini, tidak disimpan ke database.
        // Untuk tiap mapel, gabungkan (pool) SEMUA book_id yang judulnya persis sama dengan
        // buku yang dipilih admin -- karena satu judul buku bisa tercatat di beberapa baris
        // Book berbeda (misal hasil import Excel BOS tahun berbeda), sehingga stok eksemplarnya
        // terpecah. Kalau tidak digabung, sistem bisa keliru bilang "slot penuh" padahal
        // sebenarnya masih ada eksemplar kosong di book_id lain dengan judul sama.
        $subjectBookMap = [];

        foreach ($request->keys as $i => $key) {
            $bookId = $request->book_ids[$i] ?? null;

            if (!$bookId) {
                continue;
            }

            $selectedBook = Book::find($bookId);

            if (!$selectedBook) {
                continue;
            }

            $pooledIds = Book::where(function ($q) {
                    $this->filterPaket($q);
                })
                ->whereRaw('LOWER(TRIM(judul)) = ?', [strtolower(trim($selectedBook->judul))])
                ->pluck('id')
                ->toArray();

            $subjectBookMap[$key] = $pooledIds;
        }

        return $this->runImportSheet($fullPath, $request->temp_file, $request->sheet_name, $subjectBookMap, $request->done_sheets);
    }

    /**
     * Proses SATU sheet (jurusan) saja, lalu kembali ke halaman "Pilih Jurusan"
     * dengan status sheet ini ditandai sudah selesai.
     */
    protected function runImportSheet(string $fullPath, string $tempFileRelative, string $sheetName, array $subjectBookMap, ?string $doneSheetsStr)
    {
        $service = new PinjamKelasBulkImportService();
        $service->importSheet($fullPath, $sheetName, $subjectBookMap);

        $success = $service->getSuccessCount();
        $errors = $service->getErrors();

        $doneList = array_filter(explode(',', $doneSheetsStr ?? ''));
        $doneList[] = $sheetName;
        $doneList = array_unique($doneList);
        $doneStr = implode(',', $doneList);

        $message = "Sheet '{$sheetName}': {$success} peminjaman berhasil.";

        if (count($errors) > 0) {
            $message .= ' ' . count($errors) . ' baris/kolom gagal: ' . implode('; ', array_slice($errors, 0, 3));

            if (count($errors) > 3) {
                $message .= ' ... dan ' . (count($errors) - 3) . ' lainnya.';
            }
        }

        return redirect()
            ->route('admin.pinjamkelas.import.pilih-jurusan-view', [
                'temp_file' => $tempFileRelative,
                'done' => $doneStr,
            ])
            ->with(count($errors) > 0 ? 'error' : 'success', $message);
    }

    /**
     * Halaman "Pilih Jurusan" -- daftar semua sheet di file, mana yang sudah
     * diproses dan mana yang belum, supaya admin bisa proses satu per satu.
     */
    public function pilihJurusanView(Request $request)
    {
        $request->validate([
            'temp_file' => ['required', 'string'],
        ]);

        $fullPath = Storage::disk('local')->path($request->temp_file);

        if (!file_exists($fullPath)) {
            return redirect()
                ->route('admin.pinjamkelas.input-peminjaman')
                ->with('error', 'File sementara sudah tidak ditemukan, silakan upload ulang.');
        }

        $service = new PinjamKelasBulkImportService();
        $sheets = $service->listSheetsInfo($fullPath);

        $doneSheets = array_filter(explode(',', $request->query('done', '')));

        return view('admin.pinjamkelas.input.pilih-jurusan', [
            'tempFile' => $request->temp_file,
            'sheets' => $sheets,
            'doneSheets' => $doneSheets,
        ]);
    }

    /**
     * Selesai import semua jurusan -- bersihkan file sementara.
     */
    public function finishImport(Request $request)
    {
        $request->validate([
            'temp_file' => ['required', 'string'],
        ]);

        $fullPath = Storage::disk('local')->path($request->temp_file);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        return redirect()
            ->route('admin.pinjamkelas.input-peminjaman')
            ->with('success', 'Import selesai. File sementara sudah dibersihkan.');
    }

    public function kelasPinjam(Request $request)
    {
        $query = PinjamKelas::with(['user', 'book']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_buku', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nomor_identitas', 'like', "%{$search}%")
                            ->orWhere('kelas', 'like', "%{$search}%")
                            ->orWhere('jurusan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('book', function ($bookQuery) use ($search) {
                        $bookQuery->where('judul', 'like', "%{$search}%")
                            ->orWhere('penulis', 'like', "%{$search}%")
                            ->orWhere('penerbit', 'like', "%{$search}%")
                            ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);

            if ($kelas) {
                $query->whereHas('user', function ($q) use ($kelas) {
                    $q->where('kelas', $kelas->nama_kelas)
                        ->where('jurusan', $kelas->jurusan);
                });
            }
        }

        if ($request->filled('jurusan')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('jurusan', $request->jurusan);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pinjamKelas = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kelasList = Kelas::orderBy('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        return view('admin.pinjamkelas.kelas', compact(
            'pinjamKelas',
            'kelasList',
            'jurusanList'
        ));
    }

    public function exportKelasPinjam(Request $request)
    {
        return Excel::download(
            new PinjamKelasExport(
                $request->search,
                $request->kelas_id,
                $request->jurusan
            ),
            'peminjaman-buku-paket-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function setujuiKelas($id)
    {
        $pinjam = PinjamKelas::with('book')->findOrFail($id);

        if ($pinjam->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya peminjaman pending yang bisa disetujui.');
        }

        $pinjam->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Peminjaman Buku Paket berhasil disetujui.');
    }

    public function formDendaKelas($id)
    {
        $pinjam = PinjamKelas::with(['user', 'book'])
            ->findOrFail($id);

        if ($pinjam->status === 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        return view('admin.pinjamkelas.denda', compact('pinjam'));
    }

    public function simpanDendaKelas(Request $request, $id)
    {
        $request->validate([
            'kondisi' => ['required', 'in:baik,rusak,hilang'],
            'denda' => ['nullable', 'numeric', 'min:0'],
            'denda_rusak' => ['nullable', 'numeric', 'min:0'],
            'denda_hilang' => ['nullable', 'numeric', 'min:0'],
        ]);

        $pinjam = PinjamKelas::findOrFail($id);

        if ($pinjam->status === 'dikembalikan') {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $totalDenda = 0;

        if ($request->kondisi === 'rusak') {
            $totalDenda = (int) $request->input('denda_rusak', 10000);
        }

        if ($request->kondisi === 'hilang') {
            $totalDenda = (int) $request->input('denda_hilang', 100000);
        }

        DB::transaction(function () use ($pinjam, $request, $totalDenda) {
            if ($request->kondisi === 'baik' || $totalDenda <= 0) {
                $pinjam->update([
                    'status' => 'dikembalikan',
                    'kondisi' => 'baik',
                    'denda' => 0,
                    'status_denda' => null,
                ]);
            } else {
                $pinjam->update([
                    'status' => 'denda',
                    'status_denda' => 'pending',
                    'kondisi' => $request->kondisi,
                    'denda' => $totalDenda,
                ]);
            }

            $bookItem = BookItem::whereRaw('UPPER(kode_buku) = ?', [strtoupper($pinjam->kode_buku)])
                ->first();

            if ($bookItem && ($request->kondisi === 'baik' || $request->kondisi === 'rusak')) {
                $bookItem->update([
                    'status' => 'available',
                ]);
            }

            if ($bookItem && $request->kondisi === 'hilang') {
                $bookItem->update([
                    'status' => 'lost',
                ]);
            }
        });

        if ($request->kondisi === 'baik' || $totalDenda <= 0) {
            return redirect()
                ->route('admin.pinjamkelas.kelas')
                ->with('success', 'Peminjaman Buku Paket berhasil dikembalikan.');
        }

        return redirect()
            ->route('admin.pinjamkelas.kelas')
            ->with('success', 'Denda peminjaman Buku Paket berhasil diproses.');
    }

    private function filterPaket($q): void
    {
        $q->where('jenis_koleksi', 'like', '%Paket%')
            ->orWhere('jenis_koleksi', 'like', '%Packet%')
            ->orWhere('jenis_koleksi', 'like', '%Pakett%')
            ->orWhere('jenis_koleksi', 'like', '%PKT%');
    }
}