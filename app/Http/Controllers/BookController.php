<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('penulis', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->latest()->paginate(10);
        $categories = Category::where('is_active', true)->get();

        $totalJudul = Book::count();
        $totalEksemplar = BookItem::count();
        $totalKategori = Category::where('is_active', true)->count();

        return view('admin.books.index', compact('books', 'categories', 'totalJudul', 'totalEksemplar', 'totalKategori'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'judul' => 'required|string|max:255',
            'synopsis' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_rak' => 'required|string|max:50',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('books', 'public');
        }

        $book = Book::create($validated);

        return redirect()->route('books.index', ['created' => 1])->with('success', true);
    }

    public function edit(Book $book)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load('category', 'bookItems');
        return view('admin.books.show', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'judul' => 'required|string|max:255',
            'synopsis' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_rak' => 'required|string|max:50',
        ]);

        if ($request->hasFile('foto')) {
            if ($book->foto) {
                Storage::disk('public')->delete($book->foto);
            }
            $validated['foto'] = $request->file('foto')->store('books', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('updated', true);
    }

    public function destroy(Book $book)
    {
        if ($book->foto) {
            Storage::disk('public')->delete($book->foto);
        }

        $book->delete();

        return redirect()->route('books.index')->with('deleted', true);
    }

    public function list(Request $request)
    {
        $book = Book::with('category');
        $categories = Category::where('is_active', true)->get();

        if ($request->has('search') && $request->search != '') {
            $book->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('penulis', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $book->where('category_id', $request->category_id);
        }

        $books = $book->latest()->paginate(10);

        return view('peminjam.book.index', compact('books', 'categories'));
    }
}
