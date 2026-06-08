<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Models\Book;
use App\Models\BookItem;
use App\Models\Cart;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function petugasIndex(Request $request)
    {
        $query = Loan::with(['user', 'bookItem.book.category', 'petugas'])
            ->whereIn('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('bookItem.book', function ($q) use ($search) {
                        $q->where('judul', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookItem', function ($q) use ($search) {
                        $q->where('kode_buku', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalPeminjaman = Loan::whereIn('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])->count();
        $totalPending = Loan::where('status', 'pending')->count();
        $totalDisetujui = Loan::where('status', 'disetujui')->count();
        $lamaPinjamDefault = $this->lamaPinjamDefault();

        return view('petugas.peminjaman.index', compact(
            'loans',
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'lamaPinjamDefault'
        ));
    }

    public function adminIndex(Request $request)
    {
        $query = Loan::with(['user', 'bookItem.book.category', 'petugas']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('bookItem.book', function ($q) use ($search) {
                        $q->where('judul', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookItem', function ($q) use ($search) {
                        $q->where('kode_buku', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        $totalPeminjaman = Loan::count();
        $totalPending = Loan::where('status', 'pending')->count();
        $totalDisetujui = Loan::where('status', 'disetujui')->count();
        $lamaPinjamDefault = $this->lamaPinjamDefault();

        return view('admin.peminjaman.index', compact(
            'loans',
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'lamaPinjamDefault'
        ));
    }

    public function adminCreateManual()
    {
        $siswas = User::where('role', 'siswa')
            ->orderBy('name')
            ->get();

        $books = Book::withCount([
                'bookItems as stok_tersedia' => function ($q) {
                    $q->where('status', 'available')
                        ->whereNotNull('kode_buku')
                        ->where('kode_buku', '!=', '');
                }
            ])
            ->where(function ($q) {
                $q->where('jenis_koleksi', 'like', '%Referensi%')
                    ->orWhere('jenis_koleksi', 'like', '%Reference%')
                    ->orWhere('jenis_koleksi', 'like', '%Referance%')
                    ->orWhere('jenis_koleksi', 'like', '%Raferance%')
                    ->orWhere('jenis_koleksi', 'like', '%Referen%')
                    ->orWhere('jenis_koleksi', 'like', '%Ref%');
            })
            ->orderBy('judul')
            ->get();

        $lamaPinjamDefault = $this->lamaPinjamDefault();

        return view('admin.peminjaman.create', compact(
            'siswas',
            'books',
            'lamaPinjamDefault'
        ));
    }

    public function adminStoreManual(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'kode_buku' => ['required', 'string', 'max:100'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
        ], [
            'user_id.required' => 'Siswa wajib dipilih.',
            'book_id.required' => 'Buku wajib dipilih.',
            'kode_buku.required' => 'Kode buku wajib diisi.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh lebih kecil dari tanggal pinjam.',
        ]);

        $kodeBuku = strtoupper(trim($request->kode_buku));

        try {
            DB::beginTransaction();

            $user = User::where('id', $request->user_id)
                ->where('role', 'siswa')
                ->first();

            if (!$user) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'User yang dipilih bukan siswa.');
            }

            $book = Book::where('id', $request->book_id)
                ->where(function ($q) {
                    $q->where('jenis_koleksi', 'like', '%Referensi%')
                        ->orWhere('jenis_koleksi', 'like', '%Reference%')
                        ->orWhere('jenis_koleksi', 'like', '%Referance%')
                        ->orWhere('jenis_koleksi', 'like', '%Raferance%')
                        ->orWhere('jenis_koleksi', 'like', '%Referen%')
                        ->orWhere('jenis_koleksi', 'like', '%Ref%');
                })
                ->first();

            if (!$book) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Buku yang dipilih bukan Buku Referensi.');
            }

            $bookItem = BookItem::where('book_id', $book->id)
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
                    ->with('error', 'Kode buku tidak valid atau tidak cocok dengan judul buku yang dipilih.');
            }

            if ($bookItem->status !== 'available') {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kode buku ini sudah dipinjam atau tidak tersedia.');
            }

            Loan::create([
                'user_id' => $user->id,
                'book_item_id' => $bookItem->id,
                'tanggal_pinjam' => Carbon::parse($request->tanggal_pinjam)->format('Y-m-d'),
                'tanggal_kembali' => Carbon::parse($request->tanggal_kembali)->format('Y-m-d'),
                'status' => 'disetujui',
                'petugas_id' => Auth::id(),
            ]);

            $bookItem->update([
                'status' => 'borrowed',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with('success', 'Peminjaman buku berhasil diinput oleh admin.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal input peminjaman: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $user = Auth::user();

        $loans = Loan::with(['bookItem.book.category', 'petugas'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('peminjam.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $carts = Cart::where('user_id', $user->id)
            ->with('book.bookItems')
            ->get();

        if ($carts->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang peminjaman anda kosong.');
        }

        try {
            DB::beginTransaction();

            $selectedItems = [];

            foreach ($carts as $cart) {
                $book = $cart->book;
                $requestedQuantity = (int) $cart->quantity;

                if (!$book) {
                    throw new \Exception('Ada buku di keranjang yang tidak ditemukan.');
                }

                if (!$this->isReferensiBook($book)) {
                    throw new \Exception("Buku '{$book->judul}' bukan buku Referensi, jadi tidak bisa dipinjam siswa.");
                }

                $availableItems = $book->bookItems()
                    ->where('status', 'available')
                    ->whereNotNull('kode_buku')
                    ->where('kode_buku', '!=', '')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->limit($requestedQuantity)
                    ->get();

                if ($availableItems->count() < $requestedQuantity) {
                    throw new \Exception("Buku '{$book->judul}' belum punya stok kode buku yang cukup.");
                }

                foreach ($availableItems as $item) {
                    $selectedItems[] = $item;
                }
            }

            $createdLoans = 0;
            $lamaPinjamDefault = $this->lamaPinjamDefault();

            foreach ($selectedItems as $item) {
                $tanggalPinjam = now();
                $tanggalKembali = $tanggalPinjam->copy()->addDays($lamaPinjamDefault);

                Loan::create([
                    'user_id' => $user->id,
                    'book_item_id' => $item->id,
                    'tanggal_pinjam' => $tanggalPinjam,
                    'tanggal_kembali' => $tanggalKembali,
                    'status' => 'pending',
                ]);

                $item->update([
                    'status' => 'borrowed',
                ]);

                $createdLoans++;
            }

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return redirect()
                ->route('cart.index')
                ->with('success', "Berhasil mengajukan peminjaman {$createdLoans} buku. Silakan tunggu persetujuan petugas.");
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('cart.index')
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $loan = Loan::with('bookItem')->findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya pengajuan dengan status pending yang dapat disetujui.');
        }

        $data = [
            'status' => 'disetujui',
            'petugas_id' => Auth::id(),
        ];

        if (!$loan->tanggal_pinjam) {
            $data['tanggal_pinjam'] = now();
        }

        if (!$loan->tanggal_kembali) {
            $data['tanggal_kembali'] = now()->addDays($this->lamaPinjamDefault());
        }

        $loan->update($data);

        return redirect()->back()->with([
            'success' => 'Pengajuan peminjaman berhasil disetujui.',
            'loan_id' => $loan->id,
        ]);
    }

    public function updateTanggalKembali(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali' => ['required', 'date'],
        ], [
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.date' => 'Tanggal kembali tidak valid.',
        ]);

        $loan = Loan::findOrFail($id);

        if (in_array($loan->status, ['ditolak', 'dikembalikan'])) {
            return redirect()
                ->back()
                ->with('error', 'Tanggal kembali tidak bisa diedit karena peminjaman sudah selesai atau ditolak.');
        }

        $tanggalKembali = Carbon::parse($request->tanggal_kembali)->startOfDay();

        if ($loan->tanggal_pinjam) {
            $tanggalPinjam = Carbon::parse($loan->tanggal_pinjam)->startOfDay();

            if ($tanggalKembali->lt($tanggalPinjam)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tanggal kembali tidak boleh lebih kecil dari tanggal pinjam.');
            }
        }

        $loan->update([
            'tanggal_kembali' => $tanggalKembali->toDateString(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Tanggal kembali berhasil diubah.');
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

        $kodeBuku = $loan->bookItem?->kode_buku ?? 'Tanpa-Kode';

        $pdf = Pdf::loadView('petugas.pdf.kartu-peminjaman', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu-Peminjaman-' . $kodeBuku . '-' . now()->format('Ymd') . '.pdf');
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
            'alasan_ditolak' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('reject_loan_id', $id);
        }

        $loan = Loan::with('bookItem')->findOrFail($id);

        if ($loan->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya pengajuan dengan status pending yang dapat ditolak.');
        }

        DB::transaction(function () use ($loan, $request) {
            $loan->update([
                'status' => 'ditolak',
                'petugas_id' => Auth::id(),
                'alasan_ditolak' => $request->alasan_ditolak,
            ]);

            if ($loan->bookItem) {
                $loan->bookItem->update([
                    'status' => 'available',
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Pengajuan peminjaman berhasil ditolak.');
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

    private function lamaPinjamDefault(): int
    {
        return $this->settingInteger('lama_pinjam_default', 7);
    }

    private function settingInteger(string $key, int $default): int
    {
        try {
            if (!Schema::hasTable('library_settings')) {
                return $default;
            }

            $value = DB::table('library_settings')
                ->where('key', $key)
                ->value('value');

            if ($value === null || $value === '') {
                return $default;
            }

            $value = (int) $value;

            return $value > 0 ? $value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    private function isReferensiBook($book): bool
    {
        $jenis = strtoupper(trim((string) $book->jenis_koleksi));

        return str_contains($jenis, 'REFERENSI')
            || str_contains($jenis, 'REFERENCE')
            || str_contains($jenis, 'REFERANCE')
            || str_contains($jenis, 'RAFERANCE')
            || str_contains($jenis, 'REFEREN')
            || str_contains($jenis, 'REF');
    }
}