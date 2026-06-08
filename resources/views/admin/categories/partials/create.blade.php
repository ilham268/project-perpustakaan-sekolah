@extends('layouts.admin')

@section('title', 'Input Buku Manual')
@section('page-title', 'Input Buku Manual')

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-2xl font-bold text-gray-900">
            Input Buku Manual
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Input data buku satu per satu. Jumlah eksemplar akan otomatis membuat item buku.
        </p>
    </div>

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
        x-data="{
            errors: {},
            isSubmitting: false,

            submitForm(event) {
                event.preventDefault();

                this.errors = {};
                this.isSubmitting = true;

                const formData = new FormData(event.target);

                fetch('{{ route('categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        window.location.href = '{{ route('categories.index') }}?created=1';
                        return;
                    }

                    if (data.errors) {
                        this.errors = data.errors;
                        this.isSubmitting = false;
                        return;
                    }

                    alert(data.message || 'Data buku gagal disimpan.');
                    this.isSubmitting = false;
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan data.');
                    this.isSubmitting = false;
                });
            }
        }"
    >
        <form @submit="submitForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="tahun_pengadaan" class="block text-sm text-gray-700 mb-1.5">
                        Tahun Pengadaan
                    </label>

                    <input
                        type="number"
                        name="tahun_pengadaan"
                        id="tahun_pengadaan"
                        value="{{ old('tahun_pengadaan', date('Y')) }}"
                        min="2020"
                        max="2100"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.tahun_pengadaan ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Contoh: 2026"
                    >

                    <p x-show="errors.tahun_pengadaan" x-text="errors.tahun_pengadaan ? errors.tahun_pengadaan[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="nomor_klasifikasi" class="block text-sm text-gray-700 mb-1.5">
                        Nomor Klasifikasi
                    </label>

                    <input
                        type="text"
                        name="nomor_klasifikasi"
                        id="nomor_klasifikasi"
                        value="{{ old('nomor_klasifikasi') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.nomor_klasifikasi ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Contoh: 2019"
                    >

                    <p x-show="errors.nomor_klasifikasi" x-text="errors.nomor_klasifikasi ? errors.nomor_klasifikasi[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="jenis_koleksi" class="block text-sm text-gray-700 mb-1.5">
                        Jenis Koleksi <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="jenis_koleksi"
                        id="jenis_koleksi"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.jenis_koleksi ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                    >
                        <option value="Referensi" {{ old('jenis_koleksi', 'Referensi') === 'Referensi' ? 'selected' : '' }}>
                            Referensi
                        </option>
                        <option value="Paket" {{ old('jenis_koleksi') === 'Paket' ? 'selected' : '' }}>
                            Paket
                        </option>
                    </select>

                    <p x-show="errors.jenis_koleksi" x-text="errors.jenis_koleksi ? errors.jenis_koleksi[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>
            </div>

            <div>
                <label for="judul" class="block text-sm text-gray-700 mb-1.5">
                    Judul Buku <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="judul"
                    id="judul"
                    value="{{ old('judul') }}"
                    required
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                    :class="errors.judul ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                    placeholder="Masukkan judul buku"
                >

                <p x-show="errors.judul" x-text="errors.judul ? errors.judul[0] : ''" class="text-red-600 text-xs mt-1"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="penulis" class="block text-sm text-gray-700 mb-1.5">
                        Pengarang / Penulis
                    </label>

                    <input
                        type="text"
                        name="penulis"
                        id="penulis"
                        value="{{ old('penulis') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.penulis ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Nama pengarang"
                    >

                    <p x-show="errors.penulis" x-text="errors.penulis ? errors.penulis[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="penerbit" class="block text-sm text-gray-700 mb-1.5">
                        Penerbit
                    </label>

                    <input
                        type="text"
                        name="penerbit"
                        id="penerbit"
                        value="{{ old('penerbit') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.penerbit ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Nama penerbit"
                    >

                    <p x-show="errors.penerbit" x-text="errors.penerbit ? errors.penerbit[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="tahun" class="block text-sm text-gray-700 mb-1.5">
                        Tahun Terbit
                    </label>

                    <input
                        type="number"
                        name="tahun"
                        id="tahun"
                        value="{{ old('tahun') }}"
                        min="1900"
                        max="{{ date('Y') + 1 }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.tahun ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Contoh: 2019"
                    >

                    <p x-show="errors.tahun" x-text="errors.tahun ? errors.tahun[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="sumber_buku" class="block text-sm text-gray-700 mb-1.5">
                        Sumber Buku
                    </label>

                    <input
                        type="text"
                        name="sumber_buku"
                        id="sumber_buku"
                        value="{{ old('sumber_buku', 'BOS') }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.sumber_buku ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="BOS / BPOPP / SUMBANGAN"
                    >

                    <p x-show="errors.sumber_buku" x-text="errors.sumber_buku ? errors.sumber_buku[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="jumlah_eksemplar" class="block text-sm text-gray-700 mb-1.5">
                        Jumlah Eksemplar <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="number"
                        name="jumlah_eksemplar"
                        id="jumlah_eksemplar"
                        value="{{ old('jumlah_eksemplar', 1) }}"
                        min="1"
                        max="500"
                        required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm"
                        :class="errors.jumlah_eksemplar ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                        placeholder="Contoh: 10"
                    >

                    <p x-show="errors.jumlah_eksemplar" x-text="errors.jumlah_eksemplar ? errors.jumlah_eksemplar[0] : ''" class="text-red-600 text-xs mt-1"></p>
                </div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Jumlah eksemplar akan otomatis membuat item buku. Kode buku bisa diisi nanti lewat Detail Buku.
            </div>

            <div class="flex items-center justify-center gap-3 pt-4">
                <a
                    href="{{ route('categories.index') }}"
                    class="px-8 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                >
                    Kembali
                </a>

                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-8 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!isSubmitting">Simpan</span>
                    <span x-show="isSubmitting">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection