@extends($layout)

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6">
    <!-- Hero Section tanpa icon -->
    <section class="relative overflow-hidden rounded-3xl px-5 py-10 text-white shadow-sm sm:px-6 md:px-10"
             style="background-image: url('{{ asset('image/hero.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-emerald-500/25"></div>
        <div class="relative z-10 mx-auto max-w-3xl text-center">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $guideTitle }}</h1>
            <p class="mt-3 text-sm text-emerald-50 md:text-base">{{ $guideDescription }}</p>
            <p class="mt-3 text-sm text-emerald-100">Terakhir diperbarui: {{ $lastUpdated }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white px-5 py-5 sm:px-6 sm:py-6 shadow-sm">
        <a href="{{ route($backRoute) }}" class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700">
            <i class="fas fa-arrow-left"></i>
            Kembali ke daftar panduan
        </a>
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">{{ $guideTitle }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ $guideDescription }}</p>
    </section>

    @foreach($guideBlocks as $block)
    <section class="rounded-2xl border border-gray-200 bg-white px-5 py-5 sm:px-6 sm:py-6 shadow-sm">
        <h3 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $block['heading'] }}</h3>
        <p class="mt-2 text-sm text-gray-600">{{ $block['description'] }}</p>

        @foreach($block['subBlocks'] as $subBlock)
        <div class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900">{{ $subBlock['heading'] }}</h4>
            <ul class="mt-2 space-y-2 text-sm text-gray-600">
                @foreach($subBlock['items'] as $item)
                <li class="flex items-start gap-2">
                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ $item }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </section>
    @endforeach
</div>
@endsection