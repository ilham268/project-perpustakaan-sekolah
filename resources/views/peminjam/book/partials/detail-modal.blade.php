<!-- Modal Detail Buku -->
<div
    x-show="showDetail"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"
        @click="showDetail = false"
    ></div>

    {{-- Modal Content --}}
    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div
            @click.stop
            class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl"
        >
            {{-- Header --}}
            <div class="border-b border-slate-100 px-5 py-5 md:px-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">
                            Detail Buku Referensi
                        </p>

                        <h2
                            class="mt-2 text-xl font-extrabold leading-snug text-slate-900 md:text-2xl"
                            x-text="selectedBook.judul"
                        ></h2>
                    </div>

                    <button
                        type="button"
                        @click="showDetail = false"
                        class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Tutup
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="space-y-5 px-5 py-5 md:px-6">

                {{-- Info Utama --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Kategori
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-800" x-text="selectedBook.kategori"></p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Stok Tersedia
                        </p>

                        <p class="mt-1 text-sm font-bold" :class="selectedBook.stok > 0 ? 'text-emerald-700' : 'text-red-700'">
                            <span x-text="selectedBook.stok"></span>
                            <span>buku</span>
                        </p>
                    </div>
                </div>

                {{-- Detail Buku --}}
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                    <h3 class="text-sm font-extrabold text-slate-900">
                        Informasi Buku
                    </h3>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Penulis
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700" x-text="selectedBook.penulis"></p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Penerbit
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700" x-text="selectedBook.penerbit"></p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Tahun
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700" x-text="selectedBook.tahun"></p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Status
                            </p>

                            <p
                                class="mt-1 text-sm font-semibold"
                                :class="selectedBook.stok > 0 ? 'text-emerald-700' : 'text-red-700'"
                                x-text="selectedBook.stok > 0 ? 'Tersedia' : 'Belum Ada Kode'"
                            ></p>
                        </div>
                    </div>
                </div>

                {{-- Sinopsis --}}
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                    <h3 class="text-sm font-extrabold text-slate-900">
                        Sinopsis
                    </h3>

                    <p
                        class="mt-2 text-sm leading-relaxed text-slate-600"
                        x-text="selectedBook.synopsis"
                    ></p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-end md:px-6">
                <button
                    type="button"
                    @click="showDetail = false"
                    class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Tutup
                </button>

                @auth
                    <form :action="'/cart/' + selectedBook.id" method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:hover:bg-slate-300 sm:w-auto"
                            :disabled="selectedBook.stok === 0"
                        >
                            Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                    >
                        Login untuk Meminjam
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>