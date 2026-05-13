@props(['type' => 'success'])

@php
    $config = [
        'success' => [
            'icon' => '<div class="w-20 h-20 rounded-full bg-sky-100 text-sky-500 flex items-center justify-center"><i class="fas fa-circle-check text-5xl"></i></div>',
            'title' => 'Data berhasil disimpan!',
            'message' => 'Data Anda telah tersimpan dengan aman'
        ],
        'deleted' => [
            'icon' => '<div class="w-20 h-20 rounded-full bg-red-100 text-red-500 flex items-center justify-center"><i class="fas fa-trash-can text-4xl"></i></div>',
            'title' => 'Data berhasil dihapus!',
            'message' => 'Data Anda telah dihapus dari sistem'
        ],
        'updated' => [
            'icon' => '<div class="w-20 h-20 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center"><i class="fas fa-pen-to-square text-4xl"></i></div>',
            'title' => 'Perubahan Berhasil!',
            'message' => 'Perubahan yang Anda buat telah disimpan dengan sukses'
        ],
        'error' => [
            'icon' => '<div class="w-20 h-20 rounded-full bg-red-100 text-red-600 flex items-center justify-center"><i class="fas fa-circle-xmark text-5xl"></i></div>',
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
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
    style="display: none;"
>
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 p-8 text-center relative">
        <!-- Close Button -->
        <button
            @click="show = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
        >
            <i class="fas fa-times w-5 h-5"></i>
        </button>

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            {!! $current['icon'] !!}
        </div>

        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-2">
            {{ $slot->isEmpty() ? $current['title'] : $slot }}
        </h3>

        <!-- Message -->
        <p class="text-sm text-gray-600">
            {{ $attributes->get('message') ?? $current['message'] }}
        </p>

        <!-- Button -->
        <button
            @click="show = false"
            class="mt-6 px-8 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors font-medium"
        >
            Kembali
        </button>
    </div>
</div>
