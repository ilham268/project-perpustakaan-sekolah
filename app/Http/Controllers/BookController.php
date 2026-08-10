<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use App\Models\PinjamKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Book::query()->with('bookItems');

        if ($request->filled('search')) {
            $search = $request->search;

            $baseQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('sumber_buku', 'like', "%{$search}%")
                    ->orWhere('jenis_koleksi', 'like', "%{$search}%")
                    ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_koleksi')) {
            $baseQuery->where('jenis_koleksi', $request->jenis_koleksi);
        }

        if ($request->filled('sumber_buku')) {
            $baseQuery->where('sumber_buku', $request->sumber_buku);
        }

        if ($request->filled('tahun_pengadaan')) {
            $baseQuery->where('tahun_pengadaan', $request->tahun_pengadaan);
        }

        $booksBos = collect();

        $booksReferensi = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('jenis_koleksi', 'like', '%Referensi%')
                    ->orWhere('jenis_koleksi', 'like', '%Reference%')
                    ->orWhere('jenis_koleksi', 'like', '%Referance%')
                    ->orWhere('jenis_koleksi', 'like', '%Raferance%')
                    ->orWhere('jenis_koleksi', 'like', '%Referen%');
            })
            ->latest()
            ->get();

        $booksPaket = (clone $baseQuery)
            ->where('jenis_koleksi', 'like', '%Paket%')
            ->latest()
            ->get();

        $totalJudul = Book::count();
        $totalEksemplar = BookItem::count();

        $totalKategori = Book::whereNotNull('jenis_koleksi')
            ->where('jenis_koleksi', '!=', '')
            ->distinct('jenis_koleksi')
            ->count('jenis_koleksi');

        $jenisOptions = Book::whereNotNull('jenis_koleksi')
            ->where('jenis_koleksi', '!=', '')
            ->distinct()
            ->orderBy('jenis_koleksi')
            ->pluck('jenis_koleksi');

        $sumberOptions = Book::whereNotNull('sumber_buku')
            ->where('sumber_buku', '!=', '')
            ->distinct()
            ->orderBy('sumber_buku')
            ->pluck('sumber_buku');

        $tahunPengadaanOptions = Book::whereNotNull('tahun_pengadaan')
            ->distinct()
            ->orderByDesc('tahun_pengadaan')
            ->pluck('tahun_pengadaan');

        return view('admin.books.index', compact(
            'booksBos',
            'booksReferensi',
            'booksPaket',
            'totalJudul',
            'totalEksemplar',
            'totalKategori',
            'jenisOptions',
            'sumberOptions',
            'tahunPengadaanOptions'
        ));
    }

    public function importForm()
    {
        return view('admin.books.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'tahun_pengadaan' => ['required', 'integer', 'min:2020', 'max:2100'],
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        set_time_limit(0);

        $tahunPengadaan = (int) $request->tahun_pengadaan;
        $spreadsheet = IOFactory::load($request->file('file_excel')->getRealPath());

        $importedBooks = 0;
        $importedItems = 0;
        $processedSheets = 0;

        try {
            DB::transaction(function () use ($spreadsheet, $tahunPengadaan, &$importedBooks, &$importedItems, &$processedSheets) {
                foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                    $sheetName = trim($sheet->getTitle());

                    if (!$this->isAllowedSheet($sheetName)) {
                        continue;
                    }

                    $processedSheets++;

                    $jenisKoleksi = $this->getJenisKoleksiFromSheet($sheetName);
                    $highestRow = $sheet->getHighestRow();

                    for ($row = 8; $row <= $highestRow; $row++) {
                        $nomorKlasifikasi = trim((string) $sheet->getCell('B' . $row)->getFormattedValue());
                        $judul = trim((string) $sheet->getCell('C' . $row)->getFormattedValue());
                        $penulis = trim((string) $sheet->getCell('D' . $row)->getFormattedValue());
                        $penerbit = trim((string) $sheet->getCell('E' . $row)->getFormattedValue());

                        $jumlahRaw = $sheet->getCell('F' . $row)->getCalculatedValue();
                        $tahunRaw = $sheet->getCell('G' . $row)->getCalculatedValue();

                        $jumlahEksemplar = (int) preg_replace('/[^0-9]/', '', (string) $jumlahRaw);
                        $tahun = (int) preg_replace('/[^0-9]/', '', (string) $tahunRaw);

                        $sumberBuku = $this->getSumberBuku($sheet, $row);

                        if ($judul === '') {
                            continue;
                        }

                        $book = Book::updateOrCreate(
                            [
                                'tahun_pengadaan' => $tahunPengadaan,
                                'nomor_klasifikasi' => $nomorKlasifikasi,
                                'judul' => $judul,
                                'jenis_koleksi' => $jenisKoleksi,
                            ],
                            [
                                'penulis' => $penulis ?: null,
                                'penerbit' => $penerbit ?: null,
                                'tahun' => $tahun ?: null,
                                'sumber_buku' => $sumberBuku ?: null,
                                'jumlah_eksemplar' => $jumlahEksemplar,
                                'category_id' => null,
                                'nomor_rak' => null,
                                'synopsis' => null,
                                'foto' => null,
                            ]
                        );

                        $beforeItems = $book->bookItems()->count();

                        $this->syncBookItems($book, $jumlahEksemplar, false);

                        $afterItems = $book->bookItems()->count();

                        $importedBooks++;
                        $importedItems += max(0, $afterItems - $beforeItems);
                    }
                }
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('books.import.form')
                ->withInput()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }

        if ($processedSheets === 0) {
            return redirect()
                ->route('books.import.form')
                ->withInput()
                ->with('error', 'Import gagal: sheet Referensi atau Paket tidak ditemukan.');
        }

        if ($importedBooks === 0) {
            return redirect()
                ->route('books.import.form')
                ->withInput()
                ->with('error', 'Import gagal: sheet terbaca, tapi data buku kosong atau format kolom tidak sesuai.');
        }

        return redirect()
            ->route('books.index', ['tahun_pengadaan' => $tahunPengadaan])
            ->with('success', "Import data {$tahunPengadaan} berhasil. {$importedBooks} data buku diproses dan {$importedItems} item buku dibuat.");
    }

    public function create()
    {
        return redirect()->route('books.import.form');
    }

    public function store(Request $request)
    {
        return redirect()->route('books.import.form');
    }

    public function show(Book $book)
    {
        $book->load('bookItems');

        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'tahun_pengadaan' => ['nullable', 'integer', 'min:2020', 'max:2100'],
            'nomor_klasifikasi' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['nullable', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'sumber_buku' => ['nullable', 'string', 'max:255'],
            'jenis_koleksi' => ['nullable', 'string', 'max:255'],
            'jumlah_eksemplar' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated, $book) {
                $book->update($validated);

                $this->syncBookItems($book, (int) $validated['jumlah_eksemplar'], true);
            });
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('books.index')
            ->with('updated', true);
    }

    public function destroy(Book $book)
    {
        try {
            DB::transaction(function () use ($book) {
                if ($book->foto) {
                    Storage::disk('public')->delete($book->foto);
                }

                $book->bookItems()->delete();
                $book->delete();
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Data buku gagal dihapus: ' . $e->getMessage());
        }

        return redirect()
            ->route('books.index')
            ->with('deleted', true);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'selected_books' => ['required', 'array'],
            'selected_books.*' => ['integer', 'exists:books,id'],
        ], [
            'selected_books.required' => 'Pilih minimal satu buku yang ingin dihapus.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $books = Book::with('bookItems')
                    ->whereIn('id', $validated['selected_books'])
                    ->get();

                foreach ($books as $book) {
                    if ($book->foto) {
                        Storage::disk('public')->delete($book->foto);
                    }

                    $book->bookItems()->delete();
                    $book->delete();
                }
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Data buku gagal dihapus: ' . $e->getMessage());
        }

        return redirect()
            ->route('books.index')
            ->with('deleted', true);
    }

    /**
     * Gabungkan beberapa Book (judul beda tapi sebenarnya buku yang sama) menjadi 1 buku utama.
     * Semua BookItem dan riwayat PinjamKelas dipindah ke buku utama (target_id),
     * lalu buku-buku sumber lainnya dihapus. Ditolak kalau ada kode buku yang bentrok
     * antara buku utama dan buku sumber (karena constraint unik per book_id+kode_buku).
     */
    public function merge(Request $request)
    {
        $request->validate([
            'book_ids' => ['required', 'array', 'min:2'],
            'book_ids.*' => ['integer', 'exists:books,id'],
            'target_id' => ['required', 'integer'],
        ], [
            'book_ids.min' => 'Pilih minimal 2 buku untuk digabung.',
        ]);

        $bookIds = array_values(array_unique(array_map('intval', $request->book_ids)));
        $targetId = (int) $request->target_id;

        if (!in_array($targetId, $bookIds)) {
            return redirect()
                ->back()
                ->with('error', 'Buku utama harus salah satu dari buku yang dipilih.');
        }

        $sourceIds = array_values(array_diff($bookIds, [$targetId]));

        if (empty($sourceIds)) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada buku lain yang perlu digabung.');
        }

        try {
            DB::beginTransaction();

            // Cek bentrok kode buku antara buku utama dan buku sumber.
            $targetCodes = BookItem::where('book_id', $targetId)
                ->whereNotNull('kode_buku')
                ->where('kode_buku', '!=', '')
                ->pluck('kode_buku')
                ->map(fn ($c) => strtoupper(trim($c)))
                ->toArray();

            $conflicts = BookItem::whereIn('book_id', $sourceIds)
                ->whereNotNull('kode_buku')
                ->where('kode_buku', '!=', '')
                ->get()
                ->filter(fn ($item) => in_array(strtoupper(trim($item->kode_buku)), $targetCodes));

            if ($conflicts->isNotEmpty()) {
                DB::rollBack();

                $codes = $conflicts->pluck('kode_buku')->unique()->implode(', ');

                return redirect()
                    ->back()
                    ->with('error', "Gagal digabung: kode buku bentrok antar buku yang dipilih ({$codes}). Ganti salah satu kode itu dulu sebelum digabung.");
            }

            // Pindahkan semua eksemplar dari buku sumber ke buku utama.
            BookItem::whereIn('book_id', $sourceIds)->update(['book_id' => $targetId]);

            // Pindahkan riwayat peminjaman kelas yang masih menunjuk ke buku sumber.
            PinjamKelas::whereIn('book_id', $sourceIds)->update(['book_id' => $targetId]);

            // Buku sumber yang sudah kosong (semua eksemplarnya sudah pindah) dihapus.
            Book::whereIn('id', $sourceIds)->delete();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', count($sourceIds) . ' buku berhasil digabungkan ke buku utama.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menggabungkan buku: ' . $e->getMessage());
        }
    }

    public function list(Request $request)
    {
        $book = Book::with(['category', 'bookItems'])
            ->where(function ($q) {
                $q->where('jenis_koleksi', 'like', '%Referensi%')
                    ->orWhere('jenis_koleksi', 'like', '%Reference%')
                    ->orWhere('jenis_koleksi', 'like', '%Referance%')
                    ->orWhere('jenis_koleksi', 'like', '%Raferance%')
                    ->orWhere('jenis_koleksi', 'like', '%Referen%')
                    ->orWhere('jenis_koleksi', 'like', '%Ref%');
            })
            ->withCount([
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                }
            ]);

        $categories = Category::where('is_active', true)->get();

        if ($request->filled('search')) {
            $search = $request->search;

            $book->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('nomor_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('tahun_pengadaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tahun_pengadaan')) {
            $book->where('tahun_pengadaan', $request->tahun_pengadaan);
        }

        $books = $book->latest()->paginate(12)->withQueryString();

        return view('peminjam.book.index', compact('books', 'categories'));
    }

    private function isAllowedSheet(string $sheetName): bool
    {
        return $this->isReferensiSheet($sheetName)
            || $this->isPaketSheet($sheetName);
    }

    private function getJenisKoleksiFromSheet(string $sheetName): string
    {
        if ($this->isReferensiSheet($sheetName)) {
            return 'Referensi';
        }

        if ($this->isPaketSheet($sheetName)) {
            return 'Paket';
        }

        return $sheetName;
    }

    private function normalizeSheetName(string $sheetName): string
    {
        $name = strtoupper($sheetName);
        $name = str_replace('0', 'O', $name);
        $name = preg_replace('/[^A-Z\s]/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    private function isReferensiSheet(string $sheetName): bool
    {
        $name = $this->normalizeSheetName($sheetName);

        $keywords = [
            'REFERENSI',
            'REFRENSI',
            'REFRENS',
            'REFERENSEI',
            'REFERENSE',
            'REFENSI',
            'REFENS',
            'RESFERENSI',
            'RESFERENS',
            'RAFERENSI',
            'RAFERENS',
            'REFERANCE',
            'REFERENCE',
            'REFEREN',
            'REF',
            'RAF',
            'RES',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        foreach (explode(' ', $name) as $word) {
            if (strlen($word) < 4) {
                continue;
            }

            if (
                levenshtein($word, 'REFERENSI') <= 3 ||
                levenshtein($word, 'REFRENSI') <= 3 ||
                levenshtein($word, 'REFENSI') <= 3 ||
                levenshtein($word, 'RESFERENSI') <= 3 ||
                levenshtein($word, 'REFERANCE') <= 3 ||
                levenshtein($word, 'REFERENCE') <= 3
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPaketSheet(string $sheetName): bool
    {
        $name = $this->normalizeSheetName($sheetName);

        $keywords = [
            'PAKET',
            'PAKETT',
            'PAKETBOS',
            'PACKET',
            'PAK',
            'PKT',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        foreach (explode(' ', $name) as $word) {
            if (strlen($word) < 3) {
                continue;
            }

            if (
                levenshtein($word, 'PAKET') <= 2 ||
                levenshtein($word, 'PACKET') <= 2
            ) {
                return true;
            }
        }

        return false;
    }

    private function getSumberBuku($sheet, int $row): ?string
    {
        $columns = [
            'H' => 'BOS',
            'I' => 'BPOPP',
            'J' => 'SUMBANGAN',
        ];

        foreach ($columns as $column => $defaultSource) {
            $value = trim((string) $sheet->getCell($column . $row)->getFormattedValue());

            if ($value !== '') {
                $upperValue = strtoupper($value);

                if (in_array($upperValue, ['BOS', 'BPOPP', 'SUMBANGAN'])) {
                    return $upperValue;
                }

                return $defaultSource;
            }
        }

        return null;
    }

    private function syncBookItems(Book $book, int $targetTotal, bool $allowDecrease): void
    {
        $currentTotal = $book->bookItems()->count();

        if ($targetTotal > $currentTotal) {
            $newItems = [];
            $now = now();

            for ($i = 0; $i < ($targetTotal - $currentTotal); $i++) {
                $newItems[] = [
                    'book_id' => $book->id,
                    'kode_buku' => null,
                    'status' => 'available',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($newItems)) {
                BookItem::insert($newItems);
            }

            return;
        }

        if ($targetTotal < $currentTotal && $allowDecrease) {
            $deleteCount = $currentTotal - $targetTotal;

            $emptyItems = $book->bookItems()
                ->where(function ($q) {
                    $q->whereNull('kode_buku')
                        ->orWhere('kode_buku', '');
                })
                ->where('status', 'available')
                ->orderByDesc('id')
                ->limit($deleteCount)
                ->get();

            if ($emptyItems->count() < $deleteCount) {
                throw new \Exception('Jumlah eksemplar tidak bisa dikurangi karena ada item yang sudah punya kode buku atau sedang dipakai.');
            }

            foreach ($emptyItems as $item) {
                $item->delete();
            }
        }
    }
}