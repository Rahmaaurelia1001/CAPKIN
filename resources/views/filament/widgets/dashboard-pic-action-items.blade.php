@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
        5 => 'Mei',     6 => 'Juni',     7 => 'Juli',      8 => 'Agustus',
        9 => 'September',10=> 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp

<x-filament-widgets::widget>
    <div data-widget="pic-action-items" style="display: flex; flex-direction: column; gap: 14px; width: 100%;">

        {{-- 1. ALERT: Laporan Perlu Revisi --}}
        @if($revisiLaporans->isNotEmpty())
            <div style="background: #ffffff; border: 1px solid #fecaca; border-left: 5px solid #e11d48; border-radius: 14px; padding: 18px 22px; box-shadow: 0 4px 16px rgba(225, 29, 72, 0.05); width: 100%;" class="dark:bg-gray-900 dark:border-rose-900/60">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff1f2; border: 1px solid #fecaca; color: #be123c; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            ⚠️
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 1.02rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;" class="dark:text-white">
                                    Perlu Tindak Lanjut: {{ $revisiLaporans->count() }} Laporan Dikembalikan untuk Diperbaiki
                                </span>
                                <span style="background: #fff1f2; color: #be123c; border: 1px solid #fecaca; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px; text-transform: uppercase;">
                                    Revisi
                                </span>
                            </div>
                            <div style="font-size: 0.82rem; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                                Silakan tinjau catatan verifikator di bawah ini dan perbarui data laporan Anda sebelum diajukan kembali.
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($revisiLaporans as $rep)
                        @php
                            $lastReject = $rep->persetujuan->first();
                            $pejabatName = $lastReject?->user?->name ?? 'Verifikator';
                            $tahapLabel = match((int) $lastReject?->tahap) {
                                2 => 'Kabid/Kabag',
                                3 => 'Katim UMPer',
                                4 => 'Kabag Umum',
                                5 => 'Kepala Pusat',
                                default => 'Pejabat Verifikator',
                            };
                            $editUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('edit', ['record' => $rep]);
                            $viewUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('view', ['record' => $rep]);
                        @endphp
                        <div style="background: #fafafa; border: 1px solid #f1f5f9; border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;" class="dark:bg-slate-800/60 dark:border-slate-800">
                            <div style="flex: 1; min-width: 260px;">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">
                                        {{ $rep->ikk?->kode_ikk ?? 'IKK' }}
                                    </span>
                                    <span style="font-size: 0.92rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                                        {{ $rep->ikk?->nama_ikk ?? 'Laporan Kinerja' }}
                                    </span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">
                                        &bull; Periode {{ $namaBulan[$rep->bulan] ?? "Bulan {$rep->bulan}" }} {{ $rep->tahun }}
                                    </span>
                                </div>

                                @if($lastReject && $lastReject->catatan)
                                    <div style="margin-top: 8px; padding: 8px 12px; background: #fff1f2; border-left: 3px solid #f43f5e; border-radius: 0 6px 6px 0; font-size: 0.82rem; color: #881337;" class="dark:bg-rose-950/40 dark:text-rose-200">
                                        <span style="font-weight: 800; font-size: 0.74rem; text-transform: uppercase; color: #be123c; display: block; margin-bottom: 2px;">
                                            Catatan dari {{ $pejabatName }} ({{ $tahapLabel }}):
                                        </span>
                                        <div style="line-height: 1.4;">{{ $lastReject->catatan }}</div>
                                    </div>
                                @endif
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <a href="{{ $viewUrl }}" style="font-size: 0.82rem; font-weight: 700; color: #475569; padding: 7px 14px; border-radius: 8px; text-decoration: none; border: 1px solid #e2e8f0; background: #ffffff;" class="dark:bg-gray-800 dark:border-slate-700 dark:text-slate-200">
                                    Lihat Detail
                                </a>
                                <a href="{{ $editUrl }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.84rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #e11d48, #be123c); padding: 8px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(225, 29, 72, 0.3);">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Perbaiki Laporan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 2. PENGINGAT: Laporan Bulan Berjalan Belum Diinput (AESTHETIC LIGHT CARD) --}}
        @if($bulanBelumLapor)
            @php
                $createUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('create');
                $namaBulanIni = $namaBulan[$bulanBelumLapor] ?? "Bulan {$bulanBelumLapor}";
            @endphp
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 5px solid #0284c7; border-radius: 14px; padding: 16px 22px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; transition: all 0.2s ease;" class="dark:bg-gray-900 dark:border-slate-800">
                <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 280px;">
                    {{-- Soft Pastel Icon Badge --}}
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #eff6ff, #e0f2fe); border: 1px solid #bfdbfe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.1);">
                        📅
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span style="font-size: 0.98rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;" class="dark:text-white">
                                Pengingat Pelaporan: Laporan Bulan {{ $namaBulanIni }} {{ $tahun }} Belum Dibuat
                            </span>
                            <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.04em; text-transform: uppercase;">
                                Perlu Diinput
                            </span>
                        </div>
                        <div style="font-size: 0.82rem; color: #64748b; margin-top: 3px; font-weight: 500;" class="dark:text-slate-400">
                            Segera catat realisasi kinerja bulanan dan lampirkan bukti pendukung agar proses verifikasi berjenjang berjalan tepat waktu.
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <a href="{{ $createUrl }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%); padding: 9px 18px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3); transition: all 0.2s ease; white-space: nowrap;">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Laporan Sekarang
                </a>
            </div>
        @endif

    </div>
</x-filament-widgets::widget>
