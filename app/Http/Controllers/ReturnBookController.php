<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\ReturnBook;
use App\Models\PinjamKelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DendaExport;
use Carbon\Carbon;

class ReturnBookController extends Controller
{
    public function create()
{
    $loans = Loan::with(['user', 'bookItem.book.category'])
        ->where('status', 'disetujui')
        ->latest()
        ->get();

    return view('petugas.pengembalian.create', compact('loans'));
}

    public function search(Request $request)
    {
        if ($request->filled('loan_id')) {
            $loan = Loan::with(['user', 'bookItem.book.category'])
                ->where('status', 'disetujui')
                ->findOrFail($request->loan_id);

            return view('petugas.pengembalian.create', compact('loan'));
        }

        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $search = $request->search;

        $loans = Loan::with(['user', 'bookItem.book.category'])
            ->where('status', 'disetujui')
            ->where(function ($q) use ($search) {
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
            })
            ->latest()
            ->get();

        if ($loans->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada peminjaman aktif yang sesuai dengan pencarian');
        }

        if ($loans->count() === 1) {
            $loan = $loans->first();

            return view('petugas.pengembalian.create', compact('loan'));
        }

        return view('petugas.pengembalian.create', compact('loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'kondisi' => 'required|in:baik,rusak,hilang',
        ]);

        $loan = Loan::with('bookItem')->findOrFail($request->loan_id);

        if ($loan->status === 'dikembalikan') {
            return redirect()
                ->back()
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }

        $dendaKondisi = 0;

        if ($request->kondisi === 'rusak') {
            $dendaKondisi = (int) $request->input('denda_rusak', 10000);
        } elseif ($request->kondisi === 'hilang') {
            $dendaKondisi = (int) $request->input('denda_hilang', 100000);
        }

        $hasilDendaTelat = $this->hitungDendaKeterlambatan($loan->tanggal_kembali);
        $dendaKeterlambatan = $hasilDendaTelat['denda'];

        $totalDenda = $dendaKeterlambatan + $dendaKondisi;

        DB::transaction(function () use ($request, $loan, $totalDenda) {
            $loan->update([
                'status' => 'dikembalikan',
            ]);

            ReturnBook::create([
                'loan_id' => $loan->id,
                'tanggal_pengembalian' => now(),
                'kondisi' => $request->kondisi,
                'denda' => $totalDenda,
                'status' => 'pending',
            ]);

            if ($loan->bookItem) {
                if ($request->kondisi === 'hilang') {
                    $loan->bookItem->update(['status' => 'lost']);
                } elseif ($request->kondisi === 'rusak') {
                    $loan->bookItem->update(['status' => 'damaged']);
                } else {
                    $loan->bookItem->update(['status' => 'available']);
                }
            }
        });

        $message = 'Buku berhasil dikembalikan';

        if ($totalDenda > 0) {
            $message .= ' dengan denda Rp ' . number_format($totalDenda, 0, ',', '.');
        }

        return redirect()
            ->route('peminjaman.riwayat')
            ->with('success', $message);
    }

    public function adminCreate()
{
    $loans = Loan::with(['user', 'bookItem.book.category'])
        ->where('status', 'disetujui')
        ->latest()
        ->get();

    return view('admin.pengembalian.create', compact('loans'));
}

    public function adminSearch(Request $request)
    {
        if ($request->filled('loan_id')) {
            $loan = Loan::with(['user', 'bookItem.book.category'])
                ->where('status', 'disetujui')
                ->findOrFail($request->loan_id);

            return view('admin.pengembalian.create', compact('loan'));
        }

        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $search = $request->search;

        $loans = Loan::with(['user', 'bookItem.book.category'])
            ->where('status', 'disetujui')
            ->where(function ($q) use ($search) {
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
            })
            ->latest()
            ->get();

        if ($loans->isEmpty()) {
            return redirect()
                ->route('admin.pengembalian.create')
                ->with('error', 'Tidak ada peminjaman aktif yang sesuai dengan pencarian.');
        }

        if ($loans->count() === 1) {
            $loan = $loans->first();

            return view('admin.pengembalian.create', compact('loan'));
        }

        return view('admin.pengembalian.create', compact('loans'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'kondisi' => 'required|in:baik,rusak,hilang',
        ]);

        $loan = Loan::with('bookItem')->findOrFail($request->loan_id);

        if ($loan->status === 'dikembalikan') {
            return redirect()
                ->route('admin.pengembalian.create')
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }

        if ($loan->status !== 'disetujui') {
            return redirect()
                ->route('admin.pengembalian.create')
                ->with('error', 'Peminjaman ini belum disetujui atau tidak aktif.');
        }

        $sudahAdaPengembalian = ReturnBook::where('loan_id', $loan->id)->exists();

        if ($sudahAdaPengembalian) {
            return redirect()
                ->route('admin.pengembalian.create')
                ->with('error', 'Data pengembalian untuk buku ini sudah ada.');
        }

        $dendaKondisi = 0;

        if ($request->kondisi === 'rusak') {
            $dendaKondisi = (int) $request->input('denda_rusak', 10000);
        } elseif ($request->kondisi === 'hilang') {
            $dendaKondisi = (int) $request->input('denda_hilang', 100000);
        }

        $hasilDendaTelat = $this->hitungDendaKeterlambatan($loan->tanggal_kembali);
        $dendaKeterlambatan = $hasilDendaTelat['denda'];

        $totalDenda = $dendaKeterlambatan + $dendaKondisi;

        DB::transaction(function () use ($request, $loan, $totalDenda) {
            $loan->update([
                'status' => 'dikembalikan',
            ]);

            ReturnBook::create([
                'loan_id' => $loan->id,
                'tanggal_pengembalian' => now(),
                'kondisi' => $request->kondisi,
                'denda' => $totalDenda,
                'status' => 'pending',
            ]);

            if ($loan->bookItem) {
                if ($request->kondisi === 'hilang') {
                    $loan->bookItem->update(['status' => 'lost']);
                } elseif ($request->kondisi === 'rusak') {
                    $loan->bookItem->update(['status' => 'damaged']);
                } else {
                    $loan->bookItem->update(['status' => 'available']);
                }
            }
        });

        $message = 'Pengembalian berhasil diproses oleh admin';

        if ($totalDenda > 0) {
            $message .= ' dengan denda Rp ' . number_format($totalDenda, 0, ',', '.');
        }

        return redirect()
            ->route('admin.peminjaman.riwayat')
            ->with('success', $message);
    }

    public function index(Request $request)
    {
        $query = ReturnBook::with(['loan.user', 'loan.bookItem.book.category']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('loan.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem.book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem', function ($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->has('kondisi') && $request->kondisi) {
            $query->where('kondisi', $request->kondisi);
        }

        $returns = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalPengembalian = ReturnBook::count();
        $totalBermasalah = ReturnBook::whereIn('kondisi', ['rusak', 'hilang'])->count();
        $totalDendaSum = ReturnBook::where('denda', '>', 0)->sum('denda');

        return view('petugas.peminjaman.riwayat', compact(
            'returns',
            'totalPengembalian',
            'totalBermasalah',
            'totalDendaSum'
        ));
    }

    public function downloadInvoice($id)
    {
        $return = ReturnBook::with(['loan.user', 'loan.bookItem.book.category'])
            ->findOrFail($id);

        $hasilDendaTelat = $this->hitungDendaKeterlambatan(
            $return->loan->tanggal_kembali,
            $return->tanggal_pengembalian
        );

        $daysLate = $hasilDendaTelat['hari'];
        $dendaPerHari = $hasilDendaTelat['per_hari'];
        $dendaKeterlambatan = $hasilDendaTelat['denda'];

        $dendaKondisi = $return->denda - $dendaKeterlambatan;

        if ($dendaKondisi < 0) {
            $dendaKondisi = 0;
        }

        $kondisiLabel = 'Baik';

        if ($return->kondisi === 'rusak') {
            $kondisiLabel = 'Rusak';
        } elseif ($return->kondisi === 'hilang') {
            $kondisiLabel = 'Hilang';
        }

        $items = [];

        if ($dendaKeterlambatan > 0) {
            $items[] = [
                'label' => 'Denda Keterlambatan',
                'description' => $daysLate . ' hari x Rp ' . number_format($dendaPerHari, 0, ',', '.'),
                'nominal' => $dendaKeterlambatan,
            ];
        }

        if ($dendaKondisi > 0) {
            $items[] = [
                'label' => $return->kondisi === 'hilang' ? 'Ganti Rugi Buku Hilang' : 'Denda Kerusakan Buku',
                'description' => 'Kondisi buku: ' . $kondisiLabel,
                'nominal' => $dendaKondisi,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'label' => 'Pengembalian Buku',
                'description' => 'Tidak ada denda',
                'nominal' => 0,
            ];
        }

        $data = [
            'return' => $return,
            'user' => $return->loan->user,
            'invoiceNumber' => str_pad($return->id, 5, '0', STR_PAD_LEFT),
            'items' => $items,
            'dendaKeterlambatan' => $dendaKeterlambatan,
            'dendaKondisi' => $dendaKondisi,
            'daysLate' => $daysLate,
            'kondisiLabel' => $kondisiLabel,
            'total' => $return->denda,
        ];

        $pdf = Pdf::loadView('petugas.pdf.invoice', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'Nota-Pengembalian-' . ($return->loan->user->name ?? 'Peminjam') . '-' . now()->format('Ymd') . '.pdf'
        );
    }

    public function dendaIndex(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        if ($status === 'all') {
            $status = null;
        }

        $returnBooksQuery = ReturnBook::with(['loan.user', 'loan.bookItem.book.category'])
            ->where('denda', '>', 0);

        if ($search) {
            $returnBooksQuery->where(function ($q) use ($search) {
                $q->whereHas('loan.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem.book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem', function ($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($status) {
            $returnBooksQuery->where('status', $status);
        }

        $returnBooks = collect(
            $returnBooksQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'buku',
                        'id' => $item->id,
                        'nama_peminjam' => $item->loan->user->name ?? '-',
                        'nomor_identitas' => $item->loan->user->nomor_identitas ?? '-',
                        'judul' => $item->loan->bookItem->book->judul ?? '-',
                        'kode_buku' => $item->loan->bookItem->kode_buku ?? '-',
                        'foto' => $item->loan->bookItem->book->foto ?? null,
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => $item->denda ?? 0,
                        'status' => $item->status ?? 'pending',
                        'tanggal_pengembalian' => $item->tanggal_pengembalian ?? $item->created_at,
                        'created_at' => $item->created_at,
                        'invoice_route' => route('pengembalian.invoice', $item->id),
                    ];
                })
                ->all()
        );

        $pinjamKelasQuery = PinjamKelas::with(['user', 'kategori', 'book'])
            ->where('denda', '>', 0);

        if ($search) {
            $pinjamKelasQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                })
                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhere('kode_buku', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            if ($status === 'pending') {
                $pinjamKelasQuery->where(function ($query) {
                    $query->whereNull('status_denda')
                        ->orWhere('status_denda', 'pending');
                });
            } else {
                $pinjamKelasQuery->where('status_denda', $status);
            }
        }

        $pinjamKelas = collect(
            $pinjamKelasQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'kelas',
                        'id' => $item->id,
                        'nama_peminjam' => $item->user->name ?? '-',
                        'nomor_identitas' => $item->user->nomor_identitas ?? '-',
                        'judul' => $item->book->judul ?? $item->kategori->nama_kategori ?? '-',
                        'kode_buku' => $item->kode_buku ?? '-',
                        'foto' => null,
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => $item->denda ?? 0,
                        'status' => $item->status_denda ?? 'pending',
                        'tanggal_pengembalian' => $item->updated_at ?? $item->created_at,
                        'created_at' => $item->updated_at ?? $item->created_at,
                        'invoice_route' => route('denda.kelas.invoice', $item->id),
                    ];
                })
                ->all()
        );

        $gabungan = collect($returnBooks)
            ->merge(collect($pinjamKelas))
            ->sortByDesc('created_at')
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $currentItems = $gabungan
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $denda = new LengthAwarePaginator(
            $currentItems,
            $gabungan->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalDendaReturnBook = ReturnBook::where('denda', '>', 0)->sum('denda');
        $totalDendaKelas = PinjamKelas::where('denda', '>', 0)->sum('denda');
        $totalDenda = $totalDendaReturnBook + $totalDendaKelas;

        $totalPendingReturnBook = ReturnBook::where('denda', '>', 0)
            ->where('status', 'pending')
            ->sum('denda');

        $totalPendingKelas = PinjamKelas::where('denda', '>', 0)
            ->where(function ($query) {
                $query->whereNull('status_denda')
                    ->orWhere('status_denda', 'pending');
            })
            ->sum('denda');

        $totalPending = $totalPendingReturnBook + $totalPendingKelas;

        $totalPaidReturnBook = ReturnBook::where('denda', '>', 0)
            ->where('status', 'paid')
            ->sum('denda');

        $totalPaidKelas = PinjamKelas::where('denda', '>', 0)
            ->where('status_denda', 'paid')
            ->sum('denda');

        $totalPaid = $totalPaidReturnBook + $totalPaidKelas;

        return view('petugas.denda.index', compact(
            'denda',
            'totalDenda',
            'totalPending',
            'totalPaid'
        ));
    }

    public function markAsPaid($tipe, $id)
    {
        if ($tipe === 'buku') {
            $return = ReturnBook::findOrFail($id);

            $return->update([
                'status' => 'paid',
            ]);

            return redirect()
                ->route('denda.index')
                ->with('success', 'Denda buku berhasil ditandai sebagai lunas.');
        }

        if ($tipe === 'kelas') {
            $pinjamKelas = PinjamKelas::findOrFail($id);

            if (($pinjamKelas->denda ?? 0) <= 0) {
                return redirect()
                    ->route('denda.index')
                    ->with('error', 'Data pinjam kelas ini tidak memiliki denda.');
            }

            $pinjamKelas->forceFill([
                'status_denda' => 'paid',
            ])->save();

            return redirect()
                ->route('denda.index')
                ->with('success', 'Denda kelas berhasil ditandai sebagai lunas.');
        }

        return redirect()
            ->route('denda.index')
            ->with('error', 'Tipe denda tidak valid.');
    }

    public function downloadInvoiceKelasPetugas($id)
    {
        $pinjamKelas = PinjamKelas::with(['user', 'kategori', 'book'])->findOrFail($id);

        $judul = $pinjamKelas->book->judul ?? $pinjamKelas->kategori->nama_kategori ?? '-';

        $items = [
            [
                'label' => 'Denda Peminjaman Buku Paket',
                'description' => 'Buku: ' . $judul . ' | Kondisi: ' . ucfirst($pinjamKelas->kondisi ?? '-'),
                'nominal' => $pinjamKelas->denda ?? 0,
            ],
        ];

        $data = [
            'pinjamKelas' => $pinjamKelas,
            'user' => $pinjamKelas->user,
            'invoiceNumber' => 'KLS-' . str_pad($pinjamKelas->id, 5, '0', STR_PAD_LEFT),
            'items' => $items,
            'total' => $pinjamKelas->denda ?? 0,
            'tanggalBayar' => $pinjamKelas->updated_at ?? now(),
        ];

        $pdf = Pdf::loadView('petugas.pdf.invoice-kelas', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'Nota-Denda-Kelas-' . ($pinjamKelas->user->name ?? 'Peminjam') . '-' . now()->format('Ymd') . '.pdf'
        );
    }

    public function adminIndex(Request $request)
    {
        $query = ReturnBook::with(['loan.user', 'loan.bookItem.book.category', 'loan.petugas']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('loan.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem.book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem', function ($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->has('kondisi') && $request->kondisi) {
            $query->where('kondisi', $request->kondisi);
        }

        $returns = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalPengembalian = ReturnBook::count();
        $totalBermasalah = ReturnBook::whereIn('kondisi', ['rusak', 'hilang'])->count();
        $totalDendaSum = ReturnBook::where('denda', '>', 0)->sum('denda');

        return view('admin.peminjaman.riwayat', compact(
            'returns',
            'totalPengembalian',
            'totalBermasalah',
            'totalDendaSum'
        ));
    }

    public function adminDendaIndex(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        if ($status === 'all') {
            $status = null;
        }

        $returnBooksQuery = ReturnBook::with(['loan.user', 'loan.bookItem.book.category'])
            ->where('denda', '>', 0);

        if ($search) {
            $returnBooksQuery->where(function ($q) use ($search) {
                $q->whereHas('loan.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem.book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem', function ($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        if ($status) {
            $returnBooksQuery->where('status', $status);
        }

        $returnBooks = collect(
            $returnBooksQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'buku',
                        'id' => $item->id,
                        'nama_peminjam' => $item->loan->user->name ?? '-',
                        'nomor_identitas' => $item->loan->user->nomor_identitas ?? '-',
                        'judul' => $item->loan->bookItem->book->judul ?? '-',
                        'kode_buku' => $item->loan->bookItem->kode_buku ?? '-',
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => $item->denda ?? 0,
                        'status' => $item->status ?? 'pending',
                        'tanggal_kembali' => $item->tanggal_pengembalian ?? $item->created_at,
                        'created_at' => $item->created_at,
                        'invoice_route' => route('admin.pengembalian.invoice', $item->id),
                    ];
                })
                ->all()
        );

        $pinjamKelasQuery = PinjamKelas::with(['user', 'kategori', 'book'])
            ->where('denda', '>', 0);

        if ($search) {
            $pinjamKelasQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nomor_identitas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                })
                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhere('kode_buku', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            if ($status === 'pending') {
                $pinjamKelasQuery->where(function ($query) {
                    $query->whereNull('status_denda')
                        ->orWhere('status_denda', 'pending');
                });
            } else {
                $pinjamKelasQuery->where('status_denda', $status);
            }
        }

        $pinjamKelas = collect(
            $pinjamKelasQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'kelas',
                        'id' => $item->id,
                        'nama_peminjam' => $item->user->name ?? '-',
                        'nomor_identitas' => $item->user->nomor_identitas ?? '-',
                        'judul' => $item->book->judul ?? $item->kategori->nama_kategori ?? '-',
                        'kode_buku' => $item->kode_buku ?? '-',
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => $item->denda ?? 0,
                        'status' => $item->status_denda ?? 'pending',
                        'tanggal_kembali' => $item->updated_at ?? $item->created_at,
                        'created_at' => $item->updated_at ?? $item->created_at,
                        'invoice_route' => route('admin.denda.kelas.invoice', $item->id),
                    ];
                })
                ->all()
        );

        $gabungan = collect($returnBooks)
            ->merge(collect($pinjamKelas))
            ->sortByDesc('created_at')
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $currentItems = $gabungan
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $denda = new LengthAwarePaginator(
            $currentItems,
            $gabungan->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalDendaReturnBook = ReturnBook::where('denda', '>', 0)->sum('denda');
        $totalDendaKelas = PinjamKelas::where('denda', '>', 0)->sum('denda');
        $totalDenda = $totalDendaReturnBook + $totalDendaKelas;

        $totalPendingReturnBook = ReturnBook::where('denda', '>', 0)
            ->where('status', 'pending')
            ->sum('denda');

        $totalPendingKelas = PinjamKelas::where('denda', '>', 0)
            ->where(function ($query) {
                $query->whereNull('status_denda')
                    ->orWhere('status_denda', 'pending');
            })
            ->sum('denda');

        $totalPending = $totalPendingReturnBook + $totalPendingKelas;

        $totalPaidReturnBook = ReturnBook::where('denda', '>', 0)
            ->where('status', 'paid')
            ->sum('denda');

        $totalPaidKelas = PinjamKelas::where('denda', '>', 0)
            ->where('status_denda', 'paid')
            ->sum('denda');

        $totalPaid = $totalPaidReturnBook + $totalPaidKelas;

        $lamaPinjamDefault = $this->settingInteger('lama_pinjam_default', 7);
        $dendaTelatPerHari = $this->settingInteger('denda_telat_per_hari', 10000);

        return view('admin.denda.index', compact(
            'denda',
            'totalDenda',
            'totalPending',
            'totalPaid',
            'lamaPinjamDefault',
            'dendaTelatPerHari'
        ));
    }

    public function adminUpdateDendaSetting(Request $request)
    {
        if (!Schema::hasTable('library_settings')) {
            return redirect()
                ->route('admin.denda.index')
                ->with('error', 'Tabel setting belum dibuat. Jalankan migration library_settings dulu.');
        }

        $validated = $request->validate([
            'lama_pinjam_default' => ['required', 'integer', 'min:1', 'max:365'],
            'denda_telat_per_hari' => ['required', 'integer', 'min:0', 'max:10000000'],
        ], [
            'lama_pinjam_default.required' => 'Lama pinjam wajib diisi.',
            'lama_pinjam_default.integer' => 'Lama pinjam harus berupa angka.',
            'lama_pinjam_default.min' => 'Lama pinjam minimal 1 hari.',
            'denda_telat_per_hari.required' => 'Denda telat per hari wajib diisi.',
            'denda_telat_per_hari.integer' => 'Denda telat per hari harus berupa angka.',
            'denda_telat_per_hari.min' => 'Denda telat per hari minimal 0.',
        ]);

        DB::table('library_settings')->updateOrInsert(
            ['key' => 'lama_pinjam_default'],
            [
                'value' => $validated['lama_pinjam_default'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('library_settings')->updateOrInsert(
            ['key' => 'denda_telat_per_hari'],
            [
                'value' => $validated['denda_telat_per_hari'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('admin.denda.index')
            ->with('success', 'Setting denda berhasil disimpan.');
    }

    public function adminDendaPaid($tipe, $id)
    {
        if ($tipe === 'buku') {
            $return = ReturnBook::findOrFail($id);

            $return->update([
                'status' => 'paid',
            ]);

            return redirect()
                ->route('admin.denda.index')
                ->with('success', 'Denda buku berhasil ditandai sebagai lunas.');
        }

        if ($tipe === 'kelas') {
            $pinjamKelas = PinjamKelas::findOrFail($id);

            if (($pinjamKelas->denda ?? 0) <= 0) {
                return redirect()
                    ->route('admin.denda.index')
                    ->with('error', 'Data pinjam kelas ini tidak memiliki denda.');
            }

            $pinjamKelas->forceFill([
                'status_denda' => 'paid',
            ])->save();

            return redirect()
                ->route('admin.denda.index')
                ->with('success', 'Denda kelas berhasil ditandai sebagai lunas.');
        }

        return redirect()
            ->route('admin.denda.index')
            ->with('error', 'Tipe denda tidak valid.');
    }

    public function downloadInvoiceKelasAdmin($id)
    {
        $pinjamKelas = PinjamKelas::with(['user', 'kategori', 'book'])->findOrFail($id);

        $judul = $pinjamKelas->book->judul ?? $pinjamKelas->kategori->nama_kategori ?? '-';

        $items = [
            [
                'label' => 'Denda Peminjaman Buku Paket',
                'description' => 'Buku: ' . $judul . ' | Kondisi: ' . ucfirst($pinjamKelas->kondisi ?? '-'),
                'nominal' => $pinjamKelas->denda ?? 0,
            ],
        ];

        $data = [
            'pinjamKelas' => $pinjamKelas,
            'user' => $pinjamKelas->user,
            'invoiceNumber' => 'KLS-' . str_pad($pinjamKelas->id, 5, '0', STR_PAD_LEFT),
            'items' => $items,
            'total' => $pinjamKelas->denda ?? 0,
            'tanggalBayar' => $pinjamKelas->updated_at ?? now(),
        ];

        $pdf = Pdf::loadView('admin.pdf.invoice-kelas', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'Nota-Denda-Kelas-' . ($pinjamKelas->user->name ?? 'Peminjam') . '-' . now()->format('Ymd') . '.pdf'
        );
    }

    public function exportDenda(Request $request)
    {
        return Excel::download(
            new DendaExport(
                $request->start_date ?? null,
                $request->end_date ?? null,
                $request->status ?? 'all'
            ),
            'rekap-denda-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function siswaDendaIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->search;

        $returnBooksQuery = ReturnBook::with(['loan.bookItem.book'])
            ->whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('denda', '>', 0);

        if (!empty($search)) {
            $returnBooksQuery->where(function ($q) use ($search) {
                $q->whereHas('loan.bookItem.book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhereHas('loan.bookItem', function ($q) use ($search) {
                    $q->where('kode_buku', 'like', '%' . $search . '%');
                });
            });
        }

        $returnBooks = collect(
            $returnBooksQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'buku',
                        'judul' => $item->loan->bookItem->book->judul ?? '-',
                        'kode_buku' => $item->loan->bookItem->kode_buku ?? '-',
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => (int) ($item->denda ?? 0),
                        'status' => $item->status ?: 'pending',
                        'tanggal' => $item->tanggal_pengembalian ?? $item->created_at,
                        'created_at' => $item->created_at,
                    ];
                })
                ->all()
        );

        $pinjamKelasQuery = PinjamKelas::with(['kategori', 'book'])
            ->where('user_id', $user->id)
            ->where('denda', '>', 0);

        if (!empty($search)) {
            $pinjamKelasQuery->where(function ($q) use ($search) {
                $q->whereHas('kategori', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                })
                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('judul', 'like', '%' . $search . '%');
                })
                ->orWhere('kode_buku', 'like', '%' . $search . '%');
            });
        }

        $pinjamKelas = collect(
            $pinjamKelasQuery
                ->latest()
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'tipe' => 'kelas',
                        'judul' => $item->book->judul ?? $item->kategori->nama_kategori ?? '-',
                        'kode_buku' => $item->kode_buku ?? '-',
                        'kondisi' => $item->kondisi ?? '-',
                        'denda' => (int) ($item->denda ?? 0),
                        'status' => $item->status_denda ?: 'pending',
                        'tanggal' => $item->updated_at ?? $item->created_at,
                        'created_at' => $item->updated_at ?? $item->created_at,
                    ];
                })
                ->all()
        );

        $gabungan = collect($returnBooks)
            ->merge(collect($pinjamKelas))
            ->sortByDesc('created_at')
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $currentItems = $gabungan
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $denda = new LengthAwarePaginator(
            $currentItems,
            $gabungan->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalDenda = $gabungan
            ->filter(function ($item) {
                return ($item->status ?? 'pending') !== 'paid';
            })
            ->sum('denda');

        return view('peminjam.denda_saya.index', compact(
            'denda',
            'totalDenda'
        ));
    }

    private function hitungDendaKeterlambatan($tanggalKembali, $tanggalPengembalian = null): array
    {
        $dendaPerHari = $this->dendaTelatPerHari();

        if (!$tanggalKembali) {
            return [
                'hari' => 0,
                'denda' => 0,
                'per_hari' => $dendaPerHari,
            ];
        }

        $tanggalJatuhTempo = Carbon::parse($tanggalKembali)->startOfDay();
        $tanggalAcuan = $tanggalPengembalian
            ? Carbon::parse($tanggalPengembalian)->startOfDay()
            : Carbon::now()->startOfDay();

        if (!$tanggalAcuan->gt($tanggalJatuhTempo)) {
            return [
                'hari' => 0,
                'denda' => 0,
                'per_hari' => $dendaPerHari,
            ];
        }

        $hariTerlambat = (int) $tanggalJatuhTempo->diffInDays($tanggalAcuan);

        return [
            'hari' => $hariTerlambat,
            'denda' => $hariTerlambat * $dendaPerHari,
            'per_hari' => $dendaPerHari,
        ];
    }

    private function dendaTelatPerHari(): int
    {
        return $this->settingInteger('denda_telat_per_hari', 10000);
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

            return $value >= 0 ? $value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}