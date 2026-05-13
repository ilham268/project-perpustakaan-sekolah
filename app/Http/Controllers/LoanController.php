<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Cart;
use App\Models\BookItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LoanController extends Controller
{
    public function petugasIndex(Request $request)
    {
        $query = Loan::with(['user', 'bookItem.book.category', 'petugas'])
            ->whereIn('status', ['pending', 'disetujui', 'ditolak','dikembalikan']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bookItem.book', function($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bookItem', function($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(10)->withQueryString();

        $totalPeminjaman = Loan::whereIn('status', ['pending','disetujui','ditolak','dikembalikan'])->count();
        $totalPending    = Loan::where('status', 'pending')->count();
        $totalDisetujui  = Loan::where('status', 'disetujui')->count();

        return view('petugas.peminjaman.index', compact('loans', 'totalPeminjaman', 'totalPending', 'totalDisetujui'));
    }


    public function adminIndex(Request $request)
    {
        $query = Loan::with(['user', 'bookItem.book.category', 'petugas']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bookItem.book', function($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bookItem', function($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        $totalPeminjaman = Loan::count();
        $totalPending    = Loan::where('status', 'pending')->count();
        $totalDisetujui  = Loan::where('status', 'disetujui')->count();

        return view('admin.peminjaman.index', compact('loans', 'totalPeminjaman', 'totalPending', 'totalDisetujui'));
    }

    public function index()
    {
        $user = Auth::user();
        $loans = Loan::with(['bookItem.book.category', 'petugas'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('peminjam.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->with('book.bookItems')->get();

        if($carts->isEmpty()){
            return redirect()->route('cart.index')->with('error', 'Keranjang peminjaman anda kosong');
        }

        try {
            DB::beginTransaction();

            $createdLoans = 0;
            $errors = [];

            foreach($carts as $cart){
                $book = $cart->book;
                $requestedQuantity  = $cart->quantity;

                $availableItems = $book->bookItems()->where('status', 'available')->limit($requestedQuantity)->get();

                if($availableItems->count() < $requestedQuantity){
                    $errors[] = "Buku '{$book->judul}' tidak memiliki stok yang cukup.";
                    continue;
                }

                foreach($availableItems as $item){
                    Loan::create([
                        'user_id' => $user->id,
                        'book_item_id' => $item->id,
                        'tanggal_pinjam' => now(),
                        'tanggal_kembali' => now()->addDays(7),
                        'status' => 'pending',
                    ]);

                    $item->update(['status' => 'borrowed']);
                    $createdLoans++;
                }
            }

            if($createdLoans > 0){
                Cart::where('user_id', $user->id)->delete();
                DB::commit();
                return redirect()->route('cart.index')->with('success', "Berhasil mengajukan peminjaman {$createdLoans} buku. Silakan tunggu persetujuan petugas.");
            } else {
                DB::rollback();
                return redirect()->route('cart.index')->with('error', 'Gagal mengajukan peminjaman. ' . implode(' ', $errors));
            }

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('cart.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending yang dapat disetujui');
        }

        $loan->update([
            'status' => 'disetujui',
            'petugas_id' => Auth::id(),
        ]);

        return redirect()->back()->with([
            'success' => 'Pengajuan peminjaman berhasil disetujui',
            'loan_id' => $loan->id
        ]);
    }

    public function downloadKartu($id)
    {
        $loan = Loan::with(['user', 'bookItem.book.category', 'petugas'])
            ->findOrFail($id);

        $data = [
            'loan' => $loan,
            'user' => $loan->user,
            'petugas' => $loan->petugas,
        ];

        $pdf = Pdf::loadView('petugas.pdf.kartu-peminjaman', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu-Peminjaman-' . $loan->bookItem->kode_buku . '-' . now()->format('Ymd') . '.pdf');
    }

    public function downloadMemberCard()
    {
        $user = Auth::user();

        $pdf = Pdf::loadView('peminjam.pdf.kartu-anggota', [
            'user' => $user,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Kartu-Anggota-' . $user->nomor_identitas . '-' . now()->format('Ymd') . '.pdf');
    }


    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alasan_ditolak' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('reject_loan_id', $id);
        }

        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending yang dapat ditolak');
        }

        $loan->update([
            'status' => 'ditolak',
            'petugas_id' => Auth::id(),
            'alasan_ditolak' => $request->alasan_ditolak,
        ]);

        $loan->bookItem->update(['status' => 'available']);

        return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak');
    }

    public function exportPeminjaman(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'all');

        return Excel::download(
            new PeminjamanExport($startDate, $endDate, $status),
            'Laporan_Peminjaman_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
