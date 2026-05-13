<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()
            ->withCount('books')
            ->with(['books' => function ($bookQuery) {
                $bookQuery->select('id', 'category_id', 'judul', 'penulis', 'tahun')
                    ->orderBy('judul');
            }]);

        if($request->has('search') && $request->search != '') {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(6);

        return view('admin.categories.index', compact('categories'));

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        Category::create([
            'nama_kategori' => $validator->validated()['nama_kategori'],
            'is_active' => true,
        ]);

        return response()->json(['success' => true]);
    }

    public function toggle(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active
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
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'nama_kategori' => $validator->validated()['nama_kategori'],
        ]);

        return response()->json(['success' => true]);
    }

    public function show(Category $category)
    {
        $books = Book::where('category_id', $category->id)->get();
        return view('admin.categories.show', compact('category', 'books'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('deleted', true);
    }

}
