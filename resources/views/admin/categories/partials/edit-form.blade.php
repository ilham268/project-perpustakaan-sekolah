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
        <label for="nama_kategori_{{ $category->id }}" class="block text-sm font-medium text-gray-700 mb-2">
            Nama Kategori<span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="nama_kategori_{{ $category->id }}"
            name="nama_kategori"
            value="{{ $category->nama_kategori }}"
            placeholder="Masukkan nama kategori"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 border-gray-300 focus:ring-cyan-500"
            :class="errors.nama_kategori ? 'border-red-500 focus:ring-red-500' : ''"
        >
        <p x-show="errors.nama_kategori" x-text="errors.nama_kategori ? errors.nama_kategori[0] : ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <!-- Submit -->
    <div class="flex justify-end pt-4">
        <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!isSubmitting">Simpan</span>
            <span x-show="isSubmitting">Menyimpan...</span>
        </button>
    </div>
</form>
</div>
