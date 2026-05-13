@props(['title' => 'Hapus Data?', 'message' => 'Data yang dihapus tidak dapat dikembalikan.'])

<div
    x-data="{ show: false, deleteUrl: '' }"
    x-cloak
    x-show="show"
    @open-confirm-delete.window="show = true; deleteUrl = $event.detail.url"
    class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center"
    style="display: none;"
>
    <div @click.away="show = false" class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash text-3xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</h3>
            <p class="text-gray-500 text-sm mb-6">{{ $message }}</p>
            <div class="flex gap-3">
                <button @click="show = false" type="button"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Batal
                </button>
                <form x-ref="deleteForm" :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
