<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReturnBookController;
use App\Http\Controllers\GuestBookController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\KategoriPinjamController;
use App\Http\Controllers\PinjamKelasSiswaController;
use App\Http\Controllers\PetugasPinjamKelasController;
use App\Http\Controllers\Admin\KelasController;

Route::get('/', [BookController::class, 'list'])->name('home');
Route::get('list-buku', [BookController::class, 'list'])->name('peminjam.list-buku');

Route::get('/pinjam-cepat', [LoanController::class, 'quickCreate'])->name('peminjam.loan.quick-create');
Route::post('/pinjam-cepat', [LoanController::class, 'quickStore'])->name('peminjam.loan.quick-store');

Route::get('/buku-tamu', [GuestBookController::class, 'create'])->name('guest-book.create');
Route::post('/buku-tamu', [GuestBookController::class, 'store'])->name('guest-book.store');

Route::get('/panduan', [GuideController::class, 'peminjam'])->name('peminjam.guides.index');
Route::get('/panduan/{slug}', [GuideController::class, 'peminjamDetail'])->name('peminjam.guides.show');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/import-excel', [UserController::class, 'importExcel'])->name('users.import-excel');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/promote-selected', [UserController::class, 'promoteSelectedUsers'])->name('users.promote-selected');
    Route::post('/users/promote-classes', [UserController::class, 'promoteClasses'])->name('users.promote-classes');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/books/import-excel', [BookController::class, 'importForm'])->name('books.import.form');
    Route::post('/books/import-excel', [BookController::class, 'importExcel'])->name('books.import');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::post('/books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::post('/book-items', [BookItemController::class, 'store'])->name('book-items.store');
    Route::put('/book-items/{bookItem}', [BookItemController::class, 'update'])->name('book-items.update');
    Route::delete('/book-items/{bookItem}', [BookItemController::class, 'destroy'])->name('book-items.destroy');

    Route::get('/peminjaman', [LoanController::class, 'adminIndex'])->name('admin.peminjaman.index');
    Route::get('/peminjaman/export', [LoanController::class, 'exportPeminjaman'])->name('admin.peminjaman.export');
    Route::get('/peminjaman/input', [LoanController::class, 'adminCreateManual'])->name('admin.peminjaman.create');
    Route::post('/peminjaman/input', [LoanController::class, 'adminStoreManual'])->name('admin.peminjaman.store');
    Route::post('/peminjaman/{id}/approve', [LoanController::class, 'approve'])->name('admin.peminjaman.approve');
    Route::post('/peminjaman/{id}/reject', [LoanController::class, 'reject'])->name('admin.peminjaman.reject');
    Route::put('/peminjaman/{id}/tanggal-kembali', [LoanController::class, 'updateTanggalKembali'])->name('admin.peminjaman.update-tanggal-kembali');
    Route::get('/peminjaman/{id}/kartu-pdf', [LoanController::class, 'downloadKartu'])->name('admin.peminjaman.download-kartu');

    Route::get('/peminjaman/riwayat', [ReturnBookController::class, 'adminIndex'])->name('admin.peminjaman.riwayat');
    Route::get('/peminjaman/riwayat/{id}/invoice', [ReturnBookController::class, 'downloadInvoice'])->name('admin.pengembalian.invoice');

    Route::get('/pengembalian', [ReturnBookController::class, 'adminCreate'])->name('admin.pengembalian.index');
    Route::get('/pengembalian/create', [ReturnBookController::class, 'adminCreate'])->name('admin.pengembalian.create');
    Route::post('/pengembalian/search', [ReturnBookController::class, 'adminSearch'])->name('admin.pengembalian.search');

    Route::get('/pengembalian/search', function () {
        return redirect()->route('admin.pengembalian.index');
    });

    Route::post('/pengembalian', [ReturnBookController::class, 'adminStore'])->name('admin.pengembalian.store');

    Route::get('/denda', [ReturnBookController::class, 'adminDendaIndex'])->name('admin.denda.index');
    Route::put('/denda/setting', [ReturnBookController::class, 'adminUpdateDendaSetting'])->name('admin.denda.setting.update');
    Route::post('/denda/{tipe}/{id}/paid', [ReturnBookController::class, 'adminDendaPaid'])->name('admin.denda.paid');
    Route::get('/denda/kelas/{id}/invoice', [ReturnBookController::class, 'downloadInvoiceKelasAdmin'])->name('admin.denda.kelas.invoice');
    Route::get('/denda/export', [ReturnBookController::class, 'exportDenda'])->name('admin.denda.export');

    Route::get('/guest-book', [GuestBookController::class, 'adminIndex'])->name('admin.guest-book.index');
    Route::get('/guest-book/export', [GuestBookController::class, 'export'])->name('admin.guest-book.export');
    Route::delete('/guest-book/{id}', [GuestBookController::class, 'destroy'])->name('admin.guest-book.destroy');

    Route::prefix('pinjamkelas')->group(function () {
        Route::get('/kategori', [KategoriPinjamController::class, 'index'])->name('admin.pinjamkelas.kategori');
        Route::post('/kategori', [KategoriPinjamController::class, 'store'])->name('admin.pinjamkelas.kategori.store');
        Route::put('/kategori/{id}', [KategoriPinjamController::class, 'update'])->name('admin.pinjamkelas.kategori.update');
        Route::delete('/kategori/{id}', [KategoriPinjamController::class, 'destroy'])->name('admin.pinjamkelas.kategori.destroy');
        Route::get('/kategori/{id}/show', [KategoriPinjamController::class, 'show'])->name('admin.pinjamkelas.kategori.show');
        Route::post('/kategori/proses', [KategoriPinjamController::class, 'prosesPinjam'])->name('admin.pinjamkelas.kategori.proses');
        Route::get('/kelas', [KategoriPinjamController::class, 'kelasPinjam'])->name('admin.pinjamkelas.kelas');
        Route::get('/kelas/export', [KategoriPinjamController::class, 'exportKelasPinjam'])->name('admin.pinjamkelas.kelas.export');
        Route::post('/kelas/{id}/setujui', [KategoriPinjamController::class, 'setujuiKelas'])->name('admin.pinjamkelas.kelas.setujui');
        Route::get('/kelas/{id}/denda', [KategoriPinjamController::class, 'formDendaKelas'])->name('admin.pinjamkelas.kelas.denda');
        Route::post('/kelas/{id}/denda', [KategoriPinjamController::class, 'simpanDendaKelas'])->name('admin.pinjamkelas.kelas.denda.store');

        Route::get('/input-peminjaman', [KategoriPinjamController::class, 'formPinjam'])
            ->name('admin.pinjamkelas.input-peminjaman');
        Route::post('/import/preview', [KategoriPinjamController::class, 'previewImport'])
            ->name('admin.pinjamkelas.import.preview');
        Route::post('/import/confirm', [KategoriPinjamController::class, 'confirmImport'])
            ->name('admin.pinjamkelas.import.confirm');

        Route::view('/', 'admin.pinjamkelas.index')->name('admin.pinjamkelas.index');
        Route::view('/create', 'admin.pinjamkelas.create-kelas')->name('admin.pinjamkelas.create');
        Route::view('/edit', 'admin.pinjamkelas.edit-kelas')->name('admin.pinjamkelas.edit');
        Route::view('/buku', 'admin.pinjamkelas.buku')->name('admin.pinjamkelas.buku');
    });
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'petugasDashboard'])->name('petugas.dashboard');

    Route::get('/peminjaman', [LoanController::class, 'petugasIndex'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/approve', [LoanController::class, 'approve'])->name('peminjaman.approve');
    Route::post('/peminjaman/{id}/reject', [LoanController::class, 'reject'])->name('peminjaman.reject');
    Route::put('/peminjaman/{id}/tanggal-kembali', [LoanController::class, 'updateTanggalKembali'])->name('peminjaman.update-tanggal-kembali');
    Route::get('/peminjaman/riwayat', [ReturnBookController::class, 'index'])->name('peminjaman.riwayat');
    Route::get('/peminjaman/{id}/kartu-pdf', [LoanController::class, 'downloadKartu'])->name('peminjaman.download-kartu');

    Route::get('/pengembalian', [ReturnBookController::class, 'create'])->name('pengembalian.index');
    Route::get('/pengembalian/create', [ReturnBookController::class, 'create'])->name('pengembalian.create');
    Route::post('/pengembalian/search', [ReturnBookController::class, 'search'])->name('pengembalian.search');

    Route::get('/pengembalian/search', function () {
        return redirect()->route('pengembalian.index');
    });

    Route::post('/pengembalian', [ReturnBookController::class, 'store'])->name('pengembalian.store');
    Route::get('/pengembalian/{id}/invoice', [ReturnBookController::class, 'downloadInvoice'])->name('pengembalian.invoice');

    Route::get('/denda', [ReturnBookController::class, 'dendaIndex'])->name('denda.index');
    Route::post('/denda/{tipe}/{id}/paid', [ReturnBookController::class, 'markAsPaid'])->name('denda.paid');
    Route::get('/denda/kelas/{id}/invoice', [ReturnBookController::class, 'downloadInvoiceKelasPetugas'])->name('denda.kelas.invoice');

    Route::prefix('petugas/pinjamkelas')->group(function () {
        Route::get('/kategori', [PetugasPinjamKelasController::class, 'kategori'])->name('petugas.pinjamkelas.kategori');
        Route::get('/kategori/{id}/create', [PetugasPinjamKelasController::class, 'create'])->name('petugas.pinjamkelas.create');
        Route::post('/store', [PetugasPinjamKelasController::class, 'store'])->name('petugas.pinjamkelas.store');
        Route::get('/kelas', [PetugasPinjamKelasController::class, 'kelasPinjam'])->name('petugas.pinjamkelas.kelas');
        Route::get('/approve/{id}', [PetugasPinjamKelasController::class, 'approve'])->name('petugas.pinjamkelas.approve');
        Route::get('/reject/{id}', [PetugasPinjamKelasController::class, 'reject'])->name('petugas.pinjamkelas.reject');
    });
});

Route::middleware(['auth', 'role:siswa,admin'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{id}', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/loans', [LoanController::class, 'index'])->name('peminjam.loan.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/kartu-anggota', [LoanController::class, 'downloadMemberCard'])->name('peminjam.kartu-anggota');

    Route::get('/denda-saya', [ReturnBookController::class, 'siswaDendaIndex'])->name('siswa.denda.index');

    Route::prefix('pinjamkelas')->group(function () {
        Route::get('/', [PinjamKelasSiswaController::class, 'index'])->name('siswa.pinjamkelas.index');
        Route::get('/input', [PinjamKelasSiswaController::class, 'create'])->name('siswa.pinjamkelas.input');
        Route::post('/store', [PinjamKelasSiswaController::class, 'store'])->name('siswa.pinjamkelas.store');
    });
});

Route::get('/test', function () {
    return view('tes');
});