@props(['type' => 'success'])

@php
    $config = [
        'success' => [
            'icon' => 'fa-circle-check',
            'iconSize' => 'text-4xl',
            'accent' => 'var(--emerald,#147A54)',
            'iconBg' => 'var(--emerald-tint,#E9F3EE)',
            'iconColor' => 'var(--emerald-deep,#0C5E40)',
            'title' => 'Data berhasil disimpan!',
            'message' => 'Data Anda telah tersimpan dengan aman'
        ],
        'deleted' => [
            'icon' => 'fa-trash-can',
            'iconSize' => 'text-3xl',
            'accent' => '#DC2626',
            'iconBg' => '#FEF2F2',
            'iconColor' => '#DC2626',
            'title' => 'Data berhasil dihapus!',
            'message' => 'Data Anda telah dihapus dari sistem'
        ],
        'updated' => [
            'icon' => 'fa-pen-to-square',
            'iconSize' => 'text-3xl',
            'accent' => 'var(--gold,#AC8752)',
            'iconBg' => '#F6EEE0',
            'iconColor' => 'var(--gold,#AC8752)',
            'title' => 'Perubahan Berhasil!',
            'message' => 'Perubahan yang Anda buat telah disimpan dengan sukses'
        ],
        'error' => [
            'icon' => 'fa-circle-xmark',
            'iconSize' => 'text-4xl',
            'accent' => '#DC2626',
            'iconBg' => '#FEF2F2',
            'iconColor' => '#DC2626',
            'title' => 'Terjadi Kesalahan!',
            'message' => 'Mohon coba lagi atau hubungi administrator'
        ]
    ];

    $current = $config[$type] ?? $config['success'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    @click.self="show = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    x-init="setTimeout(() => show = false, 5000)"
    class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--ink,#0E2620)]/60 px-4 backdrop-blur-sm"
    style="display: none;"
>
    <div class="relative w-full max-w-sm overflow-hidden rounded-[1.75rem] border border-[var(--hairline,#E7E2D6)] bg-white p-8 text-center shadow-2xl shadow-[var(--forest,#0F3D2E)]/10">

        {{-- Thin accent line --}}
        <div class="absolute inset-x-0 top-0 h-1" style="background-color: {{ $current['accent'] }};"></div>

        {{-- Close Button --}}
        <button
            @click="show = false"
            class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-lg text-[var(--muted,#6E7770)] transition-colors hover:bg-[var(--sand,#F1ECE0)]/60"
        >
            <i class="fas fa-times"></i>
        </button>

        {{-- Icon --}}
        <div class="mb-4 flex justify-center">
            <div
                class="relative flex h-20 w-20 items-center justify-center rounded-full"
                style="background-color: {{ $current['iconBg'] }}; color: {{ $current['iconColor'] }};"
            >
                <i class="fas {{ $current['icon'] }} {{ $current['iconSize'] }}"></i>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="font-display mb-2 text-xl font-semibold tracking-tight text-[var(--forest,#0F3D2E)]">
            {{ $slot->isEmpty() ? $current['title'] : $slot }}
        </h3>

        {{-- Message --}}
        <p class="text-sm leading-relaxed text-[var(--muted,#6E7770)]">
            {{ $attributes->get('message') ?? $current['message'] }}
        </p>

        {{-- Button --}}
        <button
            @click="show = false"
            class="mt-6 rounded-xl px-8 py-2.5 text-sm font-semibold text-white shadow-md shadow-[var(--forest,#0F3D2E)]/25 transition hover:-translate-y-0.5 hover:shadow-lg"
            style="background-image: linear-gradient(180deg, var(--emerald,#147A54) 0%, var(--emerald-deep,#0C5E40) 100%);"
        >
            Kembali
        </button>
    </div>
</div>