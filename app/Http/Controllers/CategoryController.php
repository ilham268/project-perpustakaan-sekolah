<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.partials.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_pengadaan' => ['nullable', 'integer', 'min:2020', 'max:2100'],
            'nomor_klasifikasi' => ['nullable', 'string', 'max:255'],
            'jenis_koleksi' => ['required', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['nullable', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'sumber_buku' => ['nullable', 'string', 'max:255'],
            'jumlah_eksemplar' => ['required', 'integer', 'min:1', 'max:500'],
        ], [
            'jenis_koleksi.required' => 'Jenis koleksi wajib dipilih.',
            'judul.required' => 'Judul buku wajib diisi.',
            'jumlah_eksemplar.required' => 'Jumlah eksemplar wajib diisi.',
            'jumlah_eksemplar.integer' => 'Jumlah eksemplar harus berupa angka.',
            'jumlah_eksemplar.min' => 'Jumlah eksemplar minimal 1.',
            'jumlah_eksemplar.max' => 'Jumlah eksemplar maksimal 500.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $jumlahEksemplar = (int) $validated['jumlah_eksemplar'];

        try {
            DB::transaction(function () use ($validated, $jumlahEksemplar) {
                $category = Category::where('nama_kategori', 'BOS')->first();

                if (!$category) {
                    $category = new Category();
                    $category->nama_kategori = 'BOS';
                    $category->is_active = true;
                    $category->save();
                }

                $book = new Book();
                $book->category_id = $category->id;
                $book->tahun_pengadaan = $validated['tahun_pengadaan'] ?? null;
                $book->nomor_klasifikasi = $validated['nomor_klasifikasi'] ?? null;
                $book->jenis_koleksi = $validated['jenis_koleksi'];
                $book->judul = $validated['judul'];
                $book->penulis = $validated['penulis'] ?? null;
                $book->penerbit = $validated['penerbit'] ?? null;
                $book->tahun = $validated['tahun'] ?? null;
                $book->sumber_buku = $validated['sumber_buku'] ?? 'BOS';
                $book->nomor_rak = null;
                $book->synopsis = null;
                $book->foto = null;
                $book->save();

                for ($i = 1; $i <= $jumlahEksemplar; $i++) {
                    $bookItem = new BookItem();
                    $bookItem->book_id = $book->id;
                    $bookItem->kode_buku = null;
                    $bookItem->status = 'available';
                    $bookItem->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Data buku berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data buku gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggle(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $category->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $category->update([
            'nama_kategori' => $validator->validated()['nama_kategori'],
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function show(Category $category)
    {
        $books = Book::where('category_id', $category->id)->get();

        return view('admin.categories.show', compact('category', 'books'));
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('deleted', true);
    }
}