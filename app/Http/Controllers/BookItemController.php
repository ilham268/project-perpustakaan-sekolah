<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookItemController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:books,id',
            ]);

            $kodeBukuInput = $request->input('kode_buku', []);
            if (!is_array($kodeBukuInput)) {
                $kodeBukuInput = [$kodeBukuInput];
            }

            $kodeBukuList = collect($kodeBukuInput)
                ->map(fn ($kode) => trim((string) $kode))
                ->filter()
                ->values();

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

            $existingCodes = BookItem::query()
                ->whereIn('kode_buku', $kodeBukuList)
                ->pluck('kode_buku')
                ->values();

            if ($existingCodes->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'kode_buku' => ['Kode buku sudah digunakan: ' . $existingCodes->implode(', ')],
                    ],
                ], 422);
            }

            $createdItems = DB::transaction(function () use ($request, $kodeBukuList) {
                $items = [];

                foreach ($kodeBukuList as $kode) {
                    $items[] = BookItem::create([
                        'book_id' => $request->book_id,
                        'kode_buku' => $kode,
                        'status' => 'available',
                    ]);
                }

                return collect($items);
            });

            return response()->json([
                'success' => true,
                'message' => $createdItems->count() . ' item buku berhasil ditambahkan',
                'created_count' => $createdItems->count(),
                'items' => $createdItems,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, BookItem $bookItem)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|string|max:50|unique:book_items,kode_buku,' . $bookItem->id,
            'status' => 'required|in:available,borrowed,damaged,lost',
        ]);

        $bookItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item buku berhasil diupdate'
        ]);
    }

    public function destroy(BookItem $bookItem)
    {
        if ($bookItem->status === 'borrowed') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus item yang sedang dipinjam'
            ], 400);
        }

        $bookItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item buku berhasil dihapus'
        ]);
    }
}
