<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['book.category', 'book.bookItems'])
            ->where('user_id', Auth::id())
            ->get();

        return view('peminjam.cart.index', compact('carts'));
    }

    public function create()
    {
    }

    public function store(Request $request, $id)
    {
        $user = Auth::user();

        $book = Book::with('bookItems')->findOrFail($id);

        if (!$this->isReferensiBook($book)) {
            return back()->with('error', 'Buku Paket tidak bisa dipinjam oleh siswa.');
        }

        $availableCount = $this->availableCodedItemsCount($book);

        if ($availableCount < 1) {
            return back()->with('error', 'Buku belum bisa dipinjam karena kode buku belum tersedia.');
        }

        $existingCart = Cart::where('book_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingCart) {
            return back()->with('error', 'Buku sudah ada di keranjang.');
        }

        Cart::create([
            'book_id' => $id,
            'user_id' => $user->id,
            'quantity' => 1,
        ]);

        return back()->with('success', 'Buku berhasil ditambahkan ke keranjang.');
    }

    public function show(string $id)
    {
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart->load('book.bookItems');

        if (!$this->isReferensiBook($cart->book)) {
            return back()->with('error', 'Buku Paket tidak bisa dipinjam oleh siswa.');
        }

        $availableCount = $this->availableCodedItemsCount($cart->book);

        if ($availableCount < 1) {
            return back()->with('error', 'Buku belum bisa dipinjam karena kode buku belum tersedia.');
        }

        if ($validated['quantity'] > $availableCount) {
            return back()->with('error', 'Jumlah melebihi stok kode buku yang tersedia.');
        }

        $cart->update([
            'quantity' => $validated['quantity'],
        ]);

        return back()->with('success', 'Jumlah berhasil diupdate.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('deleted', true);
    }

    private function isReferensiBook(Book $book): bool
    {
        $jenis = strtoupper(trim((string) $book->jenis_koleksi));

        return str_contains($jenis, 'REFERENSI')
            || str_contains($jenis, 'REFERENCE')
            || str_contains($jenis, 'REFERANCE')
            || str_contains($jenis, 'RAFERANCE')
            || str_contains($jenis, 'REFEREN')
            || str_contains($jenis, 'REF');
    }

    private function availableCodedItemsCount(Book $book): int
    {
        return $book->bookItems
            ->filter(function ($item) {
                return $item->status === 'available'
                    && !empty($item->kode_buku);
            })
            ->count();
    }
}