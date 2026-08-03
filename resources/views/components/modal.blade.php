@props(['name', 'title', 'maxWidth' => 'md'])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[var(--ink,#0E2620)]/60 p-4 backdrop-blur-sm"
        @click="show = false"
    ></div>

    <!-- Modal Content -->
    <div class="flex min-h-screen items-center justify-center px-4">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full {{ $maxWidthClass }} overflow-hidden rounded-[1.5rem] border border-[var(--hairline,#E7E2D6)] bg-white shadow-2xl shadow-[var(--forest,#0F3D2E)]/10"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between px-6 py-4"
                style="background-image: linear-gradient(135deg, var(--forest,#0F3D2E) 0%, var(--emerald-deep,#0C5E40) 55%, var(--emerald,#147A54) 100%);"
            >
                <h3 class="font-display text-lg font-semibold tracking-tight text-white">{{ $title }}</h3>

                <button
                    @click="show = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-white/80 transition-colors hover:bg-white/15 hover:text-white"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>