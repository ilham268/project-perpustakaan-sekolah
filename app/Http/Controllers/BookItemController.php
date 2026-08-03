<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookItemController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'book_id' => ['required', 'exists:books,id'],
            ]);

            $book = Book::findOrFail($request->book_id);

            $kodeBukuList = $this->extractCodes($request);

            if ($kodeBukuList->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'kode_buku' => ['Minimal satu kode buku wajib diisi.'],
                    ],
                ], 422);
            }

            foreach ($kodeBukuList as $kode) {
                if (mb_strlen($kode) > 50) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => [
                            'kode_buku' => ["Kode buku '{$kode}' melebihi 50 karakter."],
                        ],
                    ], 422);
                }
            }

            $duplicateInput = $kodeBukuList
                ->countBy()
                ->filter(fn ($count) => $count > 1)
                ->keys()
                ->values();

            if ($duplicateInput->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'kode_buku' => ['Ada kode buku duplikat dalam input: ' . $duplicateInput->implode(', ')],
                    ],
                ], 422);
            }

            // Cek duplikat HANYA di dalam buku yang sama, bukan ke semua buku.
            $existingCodes = BookItem::query()
                ->where('book_id', $book->id)
                ->whereIn('kode_buku', $kodeBukuList)
                ->whereNotNull('kode_buku')
                ->where('kode_buku', '!=', '')
                ->pluck('kode_buku')
                ->values();

            if ($existingCodes->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'kode_buku' => ['Kode buku sudah digunakan di buku ini: ' . $existingCodes->implode(', ')],
                    ],
                ], 422);
            }

            $emptyItems = $book->bookItems()
                ->where(function ($q) {
                    $q->whereNull('kode_buku')
                        ->orWhere('kode_buku', '');
                })
                ->where('status', 'available')
                ->orderBy('id')
                ->limit($kodeBukuList->count())
                ->get();

            if ($emptyItems->count() < $kodeBukuList->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah kode melebihi item kosong. Item kosong tersedia hanya ' . $emptyItems->count() . '.',
                    'errors' => [
                        'kode_buku' => ['Jumlah kode buku yang diinput terlalu banyak.'],
                    ],
                ], 422);
            }

            DB::transaction(function () use ($emptyItems, $kodeBukuList) {
                foreach ($emptyItems as $index => $item) {
                    $item->update([
                        'kode_buku' => $kodeBukuList[$index],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => $kodeBukuList->count() . ' kode buku berhasil disimpan',
                'updated_count' => $kodeBukuList->count(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, BookItem $bookItem)
    {
        try {
            $validated = $request->validate([
                'kode_buku' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('book_items', 'kode_buku')
                        ->where(fn ($query) => $query->where('book_id', $bookItem->book_id))
                        ->ignore($bookItem->id),
                ],
                'status' => 'required|in:available,borrowed,damaged,lost',
            ], [
                'kode_buku.unique' => 'Kode buku ini sudah dipakai di buku yang sama.',
            ]);

            $bookItem->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item buku berhasil diupdate',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(BookItem $bookItem)
    {
        try {
            if ($bookItem->status === 'borrowed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus item yang sedang dipinjam',
                ], 400);
            }

            $bookItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item buku berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function extractCodes(Request $request)
    {
        $codes = collect();

        if ($request->filled('kode_buku_text')) {
            $textCodes = preg_split('/[\r\n,;]+/', $request->kode_buku_text);

            $codes = $codes->merge($textCodes);
        }

        if ($request->has('kode_buku')) {
            $kodeBukuInput = $request->input('kode_buku', []);

            if (!is_array($kodeBukuInput)) {
                $kodeBukuInput = [$kodeBukuInput];
            }

            $codes = $codes->merge($kodeBukuInput);
        }

        return $codes
            ->map(fn ($kode) => trim((string) $kode))
            ->filter()
            ->values();
    }
}