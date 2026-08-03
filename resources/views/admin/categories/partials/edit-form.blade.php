<div x-data="{
    errors: {},
    isSubmitting: false,

    submitForm(event) {
        event.preventDefault();
        this.errors = {};
        this.isSubmitting = true;

        const formData = new FormData(event.target);
        formData.append('_method', 'PUT');

        fetch('{{ route('categories.update', $category->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('categories.index') }}?updated=1';
            } else if (data.errors) {
                this.errors = data.errors;
                this.isSubmitting = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSubmitting = false;
        });
    }
}">
<form @submit="submitForm" class="space-y-4">

    <!-- Nama Kategori -->
    <div>
        <label for="nama_kategori_{{ $category->id }}" class="mb-2 block text-sm font-semibold text-[var(--text)]">
            Nama Kategori<span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="nama_kategori_{{ $category->id }}"
            name="nama_kategori"
            value="{{ $category->nama_kategori }}"
            placeholder="Masukkan nama kategori"
            class="w-full rounded-xl border border-[var(--hairline)] px-4 py-2 text-sm text-[var(--text)] outline-none transition focus:border-[var(--emerald)] focus:ring-4 focus:ring-[var(--emerald-tint)]"
            :class="errors.nama_kategori ? 'border-red-500 focus:ring-red-100' : ''"
        >
        <p x-show="errors.nama_kategori" x-text="errors.nama_kategori ? errors.nama_kategori[0] : ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Submit -->
    <div class="flex justify-end pt-4">
        <button
            type="submit"
            :disabled="isSubmitting"
            class="rounded-xl bg-[var(--emerald-deep)] px-6 py-2 text-sm font-semibold text-white transition hover:bg-[var(--forest)] disabled:cursor-not-allowed disabled:opacity-50"
        >
            <span x-show="!isSubmitting">Simpan</span>
            <span x-show="isSubmitting">Menyimpan...</span>
        </button>
    </div>
</form>
</div>