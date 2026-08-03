@props(['route', 'title' => 'Export Laporan', 'hasStatus' => true, 'statusOptions' => []])

<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[var(--ink,#0E2620)]/60 px-4 backdrop-blur-sm">
    <div class="relative w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[var(--hairline,#E7E2D6)] bg-white shadow-2xl shadow-[var(--forest,#0F3D2E)]/10">

        {{-- Thin accent line --}}
        <div class="h-1 w-full" style="background-image: linear-gradient(90deg, var(--forest,#0F3D2E) 0%, var(--emerald-deep,#0C5E40) 50%, var(--gold,#AC8752) 100%);"></div>

        <div class="px-6 pb-6 pt-6">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[var(--emerald-tint,#E9F3EE)] text-[var(--emerald-deep,#0C5E40)] ring-1 ring-[var(--emerald,#147A54)]/15">
                        <i class="fas fa-file-export"></i>
                    </div>

                    <div>
                        <h3 class="font-display text-lg font-semibold text-[var(--forest,#0F3D2E)]">{{ $title }}</h3>
                        <p class="text-xs text-[var(--muted,#6E7770)]">Unduh data dalam format Excel</p>
                    </div>
                </div>

                <button onclick="closeExportModal()" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[var(--muted,#6E7770)] transition hover:bg-[var(--sand,#F1ECE0)]/60">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ $route }}" method="GET">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-[var(--text,#1B2420)]">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="start_date"
                                class="w-full rounded-xl border border-[var(--hairline,#E7E2D6)] bg-[var(--paper,#FAF8F3)] px-3 py-2.5 text-sm text-[var(--text,#1B2420)] outline-none transition focus:border-[var(--emerald,#147A54)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint,#E9F3EE)]"
                            >
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-[var(--text,#1B2420)]">Tanggal Akhir</label>
                            <input
                                type="date"
                                name="end_date"
                                class="w-full rounded-xl border border-[var(--hairline,#E7E2D6)] bg-[var(--paper,#FAF8F3)] px-3 py-2.5 text-sm text-[var(--text,#1B2420)] outline-none transition focus:border-[var(--emerald,#147A54)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint,#E9F3EE)]"
                            >
                        </div>
                    </div>

                    @if($hasStatus)
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-[var(--text,#1B2420)]">Status</label>
                        <select
                            name="status"
                            class="w-full rounded-xl border border-[var(--hairline,#E7E2D6)] bg-[var(--paper,#FAF8F3)] px-3 py-2.5 text-sm text-[var(--text,#1B2420)] outline-none transition focus:border-[var(--emerald,#147A54)] focus:bg-white focus:ring-4 focus:ring-[var(--emerald-tint,#E9F3EE)]"
                        >
                            <option value="all">Semua</option>
                            @forelse($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @empty
                                <option value="pending">Pending</option>
                                <option value="paid">Lunas</option>
                            @endforelse
                        </select>
                    </div>
                    @endif

                    <p class="flex items-start gap-1.5 text-xs leading-relaxed text-[var(--muted,#6E7770)]">
                        <i class="fas fa-circle-info mt-0.5 text-[10px]"></i>
                        <span>Kosongkan tanggal untuk export semua data.</span>
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-2 border-t border-[var(--hairline,#E7E2D6)] pt-5">
                    <button
                        type="button"
                        onclick="closeExportModal()"
                        class="flex-1 rounded-xl border border-[var(--hairline,#E7E2D6)] bg-white px-4 py-2.5 text-sm font-semibold text-[var(--text,#1B2420)]/80 shadow-sm transition hover:bg-[var(--sand,#F1ECE0)]/50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-[var(--forest,#0F3D2E)]/25 transition hover:-translate-y-0.5 hover:shadow-lg"
                        style="background-image: linear-gradient(180deg, var(--emerald,#147A54) 0%, var(--emerald-deep,#0C5E40) 100%);"
                    >
                        <i class="fas fa-download mr-2"></i>Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openExportModal() {
        var modal = document.getElementById('exportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeExportModal() {
        var modal = document.getElementById('exportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>