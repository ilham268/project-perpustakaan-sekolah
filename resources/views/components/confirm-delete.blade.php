@props(['title' => 'Hapus Data?', 'message' => 'Data yang dihapus tidak dapat dikembalikan.'])

<div
    x-data="{ show: false, deleteUrl: '' }"
    x-cloak
    x-show="show"
    x-transition.opacity.duration.200ms
    @open-confirm-delete.window="show = true; deleteUrl = $event.detail.url"
    class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--ink,#0E2620)]/60 px-4 backdrop-blur-sm"
    style="display: none;"
>
    <div
        @click.away="show = false"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="relative w-full max-w-sm overflow-hidden rounded-[1.75rem] border border-red-100 bg-white shadow-2xl shadow-red-950/10"
    >
        {{-- Thin accent line --}}
        <div class="h-1 w-full bg-gradient-to-r from-red-500 via-red-400 to-amber-400"></div>

        <div class="px-7 pb-7 pt-8 text-center">
            <div class="relative mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 ring-1 ring-red-100">
                <div class="absolute inset-0 rounded-full border border-red-200/60" style="animation: pulseRingConfirm 2.2s ease-out infinite;"></div>
                <i class="fas fa-trash-can text-xl text-red-600"></i>
            </div>

            <h3 class="font-display text-xl font-semibold tracking-tight text-slate-900">
                {{ $title }}
            </h3>

            <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-slate-500">
                {{ $message }}
            </p>

            <div class="mt-7 flex gap-3">
                <button
                    @click="show = false"
                    type="button"
                    class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                >
                    Batal
                </button>

                <form x-ref="deleteForm" :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-gradient-to-b from-red-600 to-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-red-600/25 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-600/30"
                    >
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulseRingConfirm {
            0% { transform: scale(0.9); opacity: .6; }
            70% { transform: scale(1.35); opacity: 0; }
            100% { opacity: 0; }
        }
    </style>
</div>