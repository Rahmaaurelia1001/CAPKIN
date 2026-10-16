<x-filament-panels::page>
    @php
        $laporan = $this->getLaporanQuery()
            ->with([
                'persetujuan.user',
                'ikk',
                'unit',
                'pic',
                'rincianPelatihans',
                'rincianNSPKs',
                'rincianKerjasamas',
            ])
            ->latest('id')
            ->get();
    @endphp

    <div class="space-y-6">
        {{-- Header Ringkasan Halaman --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400">
                        <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            Riwayat Input & Persetujuan Laporan
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Pantau status berjenjang dan catatan riwayat verifikasi setiap laporan kinerja.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                        Total: {{ $laporan->count() }} Laporan
                    </span>
                </div>
            </div>
        </div>

        {{-- Daftar Kartu Laporan --}}
        <div class="grid grid-cols-1 gap-5">
            @forelse ($laporan as $item)
                @php
                    $isUmper = (int) $item->unit_id === 6;
                    $status = $item->status;
                    
                    $statusColor = match ($status) {
                        'final' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
                        'perlu_revisi' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
                        'draft' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        default => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800',
                    };

                    $statusText = match ($status) {
                        'final' => 'Sudah Final (Disetujui Kapus)',
                        'perlu_revisi' => 'Perlu Revisi PIC',
                        'draft' => 'Draft (Belum Diajukan)',
                        'diajukan' => $isUmper ? 'Menunggu Katim UMPer' : 'Menunggu Kabid/Kabag',
                        'disetujui_kabid' => 'Menunggu Katim UMPer',
                        'disetujui_katim_umper' => 'Menunggu Kabag Umum',
                        'disetujui_kabag_umum' => 'Menunggu Kapus',
                        default => ucwords(str_replace('_', ' ', $status)),
                    };

                    $namaBulan = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ][$item->bulan] ?? "Bulan {$item->bulan}";

                    // Tentukan active step index (1 s.d. 5)
                    $currentStep = match ($status) {
                        'draft' => 0,
                        'diajukan' => $isUmper ? 3 : 2,
                        'disetujui_kabid' => 3,
                        'disetujui_katim_umper' => 4,
                        'disetujui_kabag_umum' => 5,
                        'final' => 6,
                        'perlu_revisi' => -1,
                        default => 1,
                    };
                @endphp

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    {{-- Baris Atas: Header Kartu --}}
                    <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-start sm:justify-between border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                    {{ $item->ikk?->kode_ikk ?? 'IKK' }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $item->ikk?->nama_ikk ?? 'Indikator Kinerja' }}
                                </h3>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Unit: <strong class="text-slate-700 dark:text-slate-300">{{ $item->unit?->nama ?? '-' }}</strong> &bull; 
                                Periode: <strong class="text-slate-700 dark:text-slate-300">{{ $namaBulan }} {{ $item->tahun }}</strong> &bull; 
                                PIC: <strong class="text-slate-700 dark:text-slate-300">{{ $item->pic?->name ?? '-' }}</strong>
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    {{-- Stepper / Timeline Visual Alur Persetujuan --}}
                    <div class="my-5">
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-5 text-center text-xs">
                            {{-- Step 1: PIC --}}
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $currentStep >= 1 || $currentStep == -1 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                    ✓
                                </div>
                                <span class="mt-1 font-semibold text-slate-800 dark:text-slate-200">1. PIC Input</span>
                                <span class="text-[10px] text-slate-400">Diajukan</span>
                            </div>

                            {{-- Step 2: Kabid (Skip if UMPer) --}}
                            <div class="flex flex-col items-center {{ $isUmper ? 'opacity-40' : '' }}">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $isUmper ? 'bg-slate-200 text-slate-400 dark:bg-slate-800' : ($currentStep > 2 ? 'bg-emerald-500 text-white' : ($currentStep == 2 ? 'bg-sky-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400')) }}">
                                    {{ $isUmper ? '-' : ($currentStep > 2 ? '✓' : '2') }}
                                </div>
                                <span class="mt-1 font-semibold text-slate-800 dark:text-slate-200">2. Kabid/Kabag</span>
                                <span class="text-[10px] text-slate-400">{{ $isUmper ? 'Dilewati (UMPer)' : 'Verifikasi Bidang' }}</span>
                            </div>

                            {{-- Step 3: Katim UMPer --}}
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $currentStep > 3 ? 'bg-emerald-500 text-white' : ($currentStep == 3 ? 'bg-sky-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400') }}">
                                    {{ $currentStep > 3 ? '✓' : '3' }}
                                </div>
                                <span class="mt-1 font-semibold text-slate-800 dark:text-slate-200">3. Katim UMPer</span>
                                <span class="text-[10px] text-slate-400">Verifikasi Umper</span>
                            </div>

                            {{-- Step 4: Kabag Umum --}}
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $currentStep > 4 ? 'bg-emerald-500 text-white' : ($currentStep == 4 ? 'bg-sky-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400') }}">
                                    {{ $currentStep > 4 ? '✓' : '4' }}
                                </div>
                                <span class="mt-1 font-semibold text-slate-800 dark:text-slate-200">4. Kabag Umum</span>
                                <span class="text-[10px] text-slate-400">Verifikasi Kabag</span>
                            </div>

                            {{-- Step 5: Kapus --}}
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $currentStep >= 6 ? 'bg-emerald-500 text-white' : ($currentStep == 5 ? 'bg-sky-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400') }}">
                                    {{ $currentStep >= 6 ? '✓' : '5' }}
                                </div>
                                <span class="mt-1 font-semibold text-slate-800 dark:text-slate-200">5. Kapus</span>
                                <span class="text-[10px] text-slate-400">Persetujuan Final</span>
                            </div>
                        </div>
                    </div>

                    {{-- Data Realisasi & Catatan Terakhir --}}
                    <div class="grid grid-cols-1 gap-4 rounded-lg bg-slate-50 p-4 sm:grid-cols-3 dark:bg-slate-800/60">
                        <div>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Realisasi Capaian</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $item->realisasi !== null ? number_format((float) $item->realisasi, 2, ',', '.') : '0,00' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Realisasi Anggaran</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $item->realisasi_anggaran ? 'Rp ' . number_format((float) $item->realisasi_anggaran, 0, ',', '.') : '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Catatan PIC</span>
                            <p class="truncate text-sm text-slate-700 dark:text-slate-300" title="{{ $item->catatan }}">
                                {{ $item->catatan ?: '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Riwayat Catatan Verifikasi / Catatan Pengembalian --}}
                    @php
                        $riwayatTerakhir = $item->persetujuan->sortByDesc('id')->first();
                    @endphp
                    @if ($riwayatTerakhir)
                        <div class="mt-4 rounded-lg border border-slate-100 bg-white p-3 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between text-xs">
                                <div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                                        Proses Terakhir: {{ $riwayatTerakhir->user?->name ?? 'Pejabat' }}
                                    </span>
                                    <span class="ml-1 text-slate-400">
                                        ({{ ucwords(str_replace('_', ' ', $riwayatTerakhir->keputusan)) }})
                                    </span>
                                </div>
                                <span class="text-slate-400">
                                    {{ $riwayatTerakhir->diproses_pada ? $riwayatTerakhir->diproses_pada->translatedFormat('d M Y H:i') : '-' }}
                                </span>
                            </div>
                            @if ($riwayatTerakhir->catatan)
                                <div class="mt-2 rounded bg-amber-50 p-2 text-xs text-amber-900 dark:bg-amber-950/50 dark:text-amber-200">
                                    <strong>Catatan:</strong> {{ $riwayatTerakhir->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Tombol Aksi Detail --}}
                    <div class="mt-4 flex justify-end">
                        <a 
                            href="{{ \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('view', ['record' => $item]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            <x-filament::icon icon="heroicon-o-eye" class="h-4 w-4 text-slate-500" />
                            Lihat Detail Lengkap
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <x-filament::icon icon="heroicon-o-document-text" class="mx-auto h-12 w-12 text-slate-400" />
                    <h3 class="mt-3 text-base font-bold text-slate-900 dark:text-white">
                        Belum Ada Riwayat
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Laporan yang diajukan atau diproses akan muncul di sini secara terstruktur.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>