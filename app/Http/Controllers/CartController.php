<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('book.category')
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

        $book = Book::findOrFail($id);
        $availableCount = $book->availableItems()->count();

        if ($availableCount < 1) {
            return back()->with('error', 'Buku tidak tersedia saat ini.');
        }

        $existingCart = Cart::where('book_id', $id)->where('user_id', $user->id)->first();

        if($existingCart){
            return back()->with('error', 'Buku sudah ada di keranjang. Atur jumlah di halaman keranjang.');
        }

        Cart::create([
            'book_id' => $id,
            'user_id' => $user->id,
            'quantity' => 1
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
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $availableCount = $cart->book->availableItems()->count();

        if ($validated['quantity'] > $availableCount) {
            return back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        $cart->update(['quantity' => $validated['quantity']]);

        return back()->with( 'Jumlah berhasil diupdate.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('deleted', true);
    }
}
