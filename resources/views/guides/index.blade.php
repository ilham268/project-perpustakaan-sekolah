@extends($layout)

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6">
    <section class="relative overflow-hidden rounded-3xl px-5 py-10 text-white shadow-sm sm:px-6 md:px-10"
             style="background-image: url('{{ asset('image/hero.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-emerald-500/25"></div>
        <div class="relative z-10 mx-auto max-w-3xl text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white text-emerald-600">
                <i class="fas fa-book-open text-2xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $heroTitle }}</h1>
            <p class="mt-3 text-sm text-emerald-50 md:text-base">{{ $heroSubtitle }}</p>
            <p class="mt-3 text-sm text-emerald-100">Terakhir diperbarui: {{ $lastUpdated }}</p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
        @foreach($sections as $section)
        <article class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
            <!-- HAPUS bagian icon, langsung tampilkan judul -->
            <div class="mb-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ $section['title'] }}</h3>
                <p class="text-sm text-gray-500">{{ $section['subtitle'] }}</p>
            </div>
            <ul class="space-y-2 text-sm text-gray-600">
                @foreach($section['points'] as $point)
                <li class="flex items-start gap-2">
                    <span class="mt-2 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ $point }}</span>
                </li>
                @endforeach
            </ul>
            <a href="{{ route($detailRouteName, $section['slug']) }}"
               class="mt-5 inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-emerald-600">
                Lihat Selengkapnya
                
            </a>
        </article>
        @endforeach
    </section>
</div>
@endsection