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

        {{-- Ringkasan halaman --}}
        <x-filament::section>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <x-filament::icon
                        icon="heroicon-o-document-check"
                        class="h-5 w-5"
                    />
                </div>

                <div>
                    <h2 class="text-lg font-semibold">
                        Riwayat Persetujuan
                    </h2>
                    <p class="text-sm text-gray-500">
                        Daftar pengajuan dan proses persetujuan yang dapat kamu akses.
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <x-filament::badge color="gray">
                    Total laporan: {{ $laporan->count() }}
                </x-filament::badge>
            </div>
        </x-filament::section>

        {{-- Daftar laporan --}}
        @forelse ($laporan as $item)
            @php
                $statusLabel = str($item->status)
                    ->replace('_', ' ')
                    ->title();

                $statusColor = match ($item->status) {
                    'final' => 'success',
                    'perlu_revisi' => 'danger',
                    'draft' => 'gray',
                    default => 'info',
                };

                $kodeIkk = $item->ikk?->kode_ikk ?? 'IKK';
            @endphp

            <x-filament::section
                :heading="$kodeIkk . ' — ' . ($item->ikk?->nama_ikk ?? 'Laporan')"
                :key="'riwayat-laporan-' . $item->id"
            >
                {{-- Informasi utama --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">
                            {{ $item->unit?->nama ?? 'Unit tidak tersedia' }}
                        </p>

                        <p class="text-sm">
                            Periode:
                            <span class="font-medium">
                                {{ str((string) $item->bulan)->padLeft(2, '0') }}/{{ $item->tahun }}
                            </span>
                        </p>

                        <p class="text-sm">
                            PIC:
                            <span class="font-medium">
                                {{ $item->pic?->name ?? 'Tidak tersedia' }}
                            </span>
                        </p>
                    </div>

                    <x-filament::badge :color="$statusColor">
                        {{ $statusLabel }}
                    </x-filament::badge>
                </div>

                {{-- Riwayat proses persetujuan --}}
                <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-700">
                    <h3 class="text-base font-semibold">
                        Riwayat Proses
                    </h3>

                    <div class="mt-4 space-y-4">
                        @forelse ($item->persetujuan->sortBy('id') as $proses)
                            <div
                                wire:key="riwayat-proses-{{ $proses->id }}"
                                class="flex gap-3"
                            >
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold dark:bg-gray-800">
                                    {{ $proses->tahap }}
                                </div>

                                <div class="min-w-0 flex-1 border-b border-gray-100 pb-4 last:border-0 last:pb-0 dark:border-gray-800">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-sm font-semibold">
                                            {{ $proses->user?->name ?? 'Pengguna' }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $proses->diproses_pada?->format('d/m/Y H:i') ?? '-' }}
                                        </p>
                                    </div>

                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ str($proses->keputusan)->replace('_', ' ')->title() }}
                                    </p>

                                    @if ($proses->catatan)
                                        <div class="mt-2 rounded-lg bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-200">
                                            <span class="font-semibold">Catatan:</span>
                                            {{ $proses->catatan }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">
                                Belum ada riwayat proses.
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Detail pengajuan yang dapat dibuka --}}
                <details class="group mt-5 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <span class="font-semibold">
                            Lihat Detail Pengajuan
                        </span>

                        <x-filament::icon
                            icon="heroicon-o-chevron-down"
                            class="h-5 w-5 text-gray-500 transition-transform group-open:rotate-180"
                        />
                    </summary>

                    <div class="mt-4 space-y-5">

                        {{-- Informasi pengajuan --}}
                        <section>
                            <h4 class="font-semibold">
                                Informasi Pengajuan
                            </h4>

                            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs text-gray-500">Realisasi</p>
                                    <p class="mt-1 text-sm font-medium">
                                        {{ $item->realisasi ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Realisasi Anggaran</p>
                                    <p class="mt-1 text-sm font-medium">
                                        {{ $item->realisasi_anggaran ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Catatan Pengajuan</p>
                                    <p class="mt-1 whitespace-pre-line text-sm">
                                        {{ $item->catatan ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Narasi Capaian</p>
                                    <p class="mt-1 whitespace-pre-line text-sm">
                                        {{ $item->narasi_capaian ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Kendala</p>
                                    <p class="mt-1 whitespace-pre-line text-sm">
                                        {{ $item->kendala ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Tindak Lanjut</p>
                                    <p class="mt-1 whitespace-pre-line text-sm">
                                        {{ $item->tindak_lanjut ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Bukti dukung --}}
                        @if ($item->bukti_dukung)
                            <section class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <h4 class="font-semibold">
                                    Bukti Dukung
                                </h4>

                                <p class="mt-2 break-all text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item->bukti_dukung }}
                                </p>
                            </section>
                        @endif

                        {{-- Rincian IKK 20 --}}
                        @if ($kodeIkk === 'IKK 20')
                            <section class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <h4 class="font-semibold">
                                    Rincian Pelatihan
                                </h4>

                                @forelse ($item->rincianPelatihans as $rincian)
                                    <div class="mt-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <p class="font-semibold">
                                            {{ $rincian->nama_pelatihan }}
                                        </p>

                                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <div>
                                                <p class="text-xs text-gray-500">Sumber Dana</p>
                                                <p class="text-sm font-medium">
                                                    {{ $rincian->sumber_dana ?: '-' }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500">Jumlah Peserta</p>
                                                <p class="text-sm font-medium">
                                                    {{ number_format($rincian->jumlah_peserta ?? 0, 0, ',', '.') }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500">Laki-laki</p>
                                                <p class="text-sm font-medium">
                                                    {{ number_format($rincian->jumlah_laki_laki ?? 0, 0, ',', '.') }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500">Perempuan</p>
                                                <p class="text-sm font-medium">
                                                    {{ number_format($rincian->jumlah_perempuan ?? 0, 0, ',', '.') }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500">Jumlah Lulus</p>
                                                <p class="text-sm font-medium">
                                                    {{ number_format($rincian->jumlah_lulus ?? 0, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="mt-2 text-sm text-gray-500">
                                        Belum ada rincian pelatihan.
                                    </p>
                                @endforelse
                            </section>
                        @endif

                        {{-- Rincian IKK 21 --}}
                        @if ($kodeIkk === 'IKK 21')
                            <section class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <h4 class="font-semibold">
                                    Rincian NSPK
                                </h4>

                                @forelse ($item->rincianNSPKs as $rincian)
                                    <div class="mt-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div>
                                            <p class="text-xs text-gray-500">Jenis NSPK</p>
                                            <p class="text-sm font-medium">
                                                {{ $rincian->jenis_nspk ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500">Nama NSPK</p>
                                            <p class="text-sm font-medium">
                                                {{ $rincian->nama_nspk ?: '-' }}
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500">Keterangan</p>
                                            <p class="whitespace-pre-line text-sm">
                                                {{ $rincian->keterangan ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="mt-2 text-sm text-gray-500">
                                        Belum ada rincian NSPK.
                                    </p>
                                @endforelse
                            </section>
                        @endif

                        {{-- Rincian IKK 28 --}}
                        @if ($kodeIkk === 'IKK 28')
                            <section class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <h4 class="font-semibold">
                                    Rincian Kerja Sama
                                </h4>

                                @forelse ($item->rincianKerjasamas as $rincian)
                                    <div class="mt-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <p class="text-xs text-gray-500">
                                            Judul Kerja Sama
                                        </p>

                                        <p class="mt-1 text-sm font-medium">
                                            {{ $rincian->judul_kerjasama ?: '-' }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="mt-2 text-sm text-gray-500">
                                        Belum ada rincian kerja sama.
                                    </p>
                                @endforelse
                            </section>
                        @endif

                        <p class="border-t border-gray-200 pt-4 text-xs text-gray-500 dark:border-gray-700">
                            Halaman ini hanya untuk melihat data. Perubahan dan keputusan persetujuan tidak dilakukan dari menu riwayat.
                        </p>
                    </div>
                </details>
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="py-6 text-center">
                    <p class="font-semibold">
                        Belum ada riwayat pengajuan atau persetujuan.
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Laporan akan muncul di sini setelah diajukan atau diproses.
                    </p>
                </div>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>