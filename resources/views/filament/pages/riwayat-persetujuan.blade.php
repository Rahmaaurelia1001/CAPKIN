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

    <style>
        .rp-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .rp-header-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .rp-badge-total {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid #dbeafe;
        }
        .rp-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .rp-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }
        .rp-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .rp-tag-ikk {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            display: inline-block;
        }
        .rp-stepper {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
            margin: 1.25rem 0;
            text-align: center;
        }
        @media (max-width: 640px) {
            .rp-stepper {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .rp-step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .rp-step-circle {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            margin-bottom: 0.35rem;
            transition: all 0.2s ease;
        }
        .rp-step-done {
            background-color: #10b981;
            color: #ffffff;
        }
        .rp-step-active {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 0 0 4px #dbeafe;
        }
        .rp-step-pending {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .rp-step-skipped {
            background-color: #f8fafc;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
        }
        .rp-step-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }
        .rp-step-sub {
            font-size: 0.65rem;
            color: #64748b;
            margin-top: 0.15rem;
        }
        .rp-meta-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 0.5rem;
            padding: 0.85rem 1rem;
            margin-top: 1rem;
        }
        .rp-meta-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .rp-meta-val {
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0.15rem;
        }
        .rp-history-box {
            margin-top: 0.85rem;
            padding: 0.75rem 1rem;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.75rem;
        }
        .rp-note-box {
            margin-top: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
            border-radius: 0.375rem;
            font-size: 0.75rem;
        }
        .rp-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .rp-btn-detail:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }
    </style>

    <div class="rp-container">
        {{-- Header Ringkasan Halaman --}}
        <div class="rp-header-card">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center;">
                    <x-filament::icon icon="heroicon-o-clock" style="width: 22px; height: 22px;" />
                </div>
                <div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">
                        Riwayat Pelaporan & Persetujuan
                    </h2>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0 0;">
                        Pantau status berjenjang dan catatan riwayat verifikasi setiap laporan kinerja.
                    </p>
                </div>
            </div>
            <div>
                <span class="rp-badge-total">
                    Total: {{ $laporan->count() }} Laporan
                </span>
            </div>
        </div>

        {{-- Daftar Kartu Laporan --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse ($laporan as $item)
                @php
                    $isUmper = (int) $item->unit_id === 6;
                    $status = $item->status;
                    
                    $badgeStyle = match ($status) {
                        'final' => 'background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;',
                        'perlu_revisi' => 'background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3;',
                        'draft' => 'background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0;',
                        default => 'background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a;',
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

                <div class="rp-card">
                    {{-- Header Kartu --}}
                    <div class="rp-card-header">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="rp-tag-ikk">
                                    {{ $item->ikk?->kode_ikk ?? 'IKK' }}
                                </span>
                                <span style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">
                                    {{ $item->ikk?->nama_ikk ?? 'Indikator Kinerja' }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
                                Unit: <strong style="color: #334155;">{{ $item->unit?->nama ?? '-' }}</strong> &bull; 
                                Periode: <strong style="color: #334155;">{{ $namaBulan }} {{ $item->tahun }}</strong> &bull; 
                                PIC: <strong style="color: #334155;">{{ $item->pic?->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 0.375rem; display: inline-block; {{ $badgeStyle }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    {{-- Stepper / Timeline Visual Alur Persetujuan (5 Tahap Berjejer Rapi) --}}
                    <div class="rp-stepper">
                        {{-- Step 1: PIC --}}
                        <div class="rp-step-item">
                            <div class="rp-step-circle {{ $currentStep >= 1 || $currentStep == -1 ? 'rp-step-done' : 'rp-step-pending' }}">
                                ✓
                            </div>
                            <span class="rp-step-title">1. PIC Input</span>
                            <span class="rp-step-sub">Diajukan</span>
                        </div>

                        {{-- Step 2: Kabid (Skip if UMPer) --}}
                        <div class="rp-step-item" style="{{ $isUmper ? 'opacity: 0.5;' : '' }}">
                            <div class="rp-step-circle {{ $isUmper ? 'rp-step-skipped' : ($currentStep > 2 ? 'rp-step-done' : ($currentStep == 2 ? 'rp-step-active' : 'rp-step-pending')) }}">
                                {{ $isUmper ? '-' : ($currentStep > 2 ? '✓' : '2') }}
                            </div>
                            <span class="rp-step-title">2. Kabid/Kabag</span>
                            <span class="rp-step-sub">{{ $isUmper ? 'Dilewati' : 'Verifikasi' }}</span>
                        </div>

                        {{-- Step 3: Katim UMPer --}}
                        <div class="rp-step-item">
                            <div class="rp-step-circle {{ $currentStep > 3 ? 'rp-step-done' : ($currentStep == 3 ? 'rp-step-active' : 'rp-step-pending') }}">
                                {{ $currentStep > 3 ? '✓' : '3' }}
                            </div>
                            <span class="rp-step-title">3. Katim UMPer</span>
                            <span class="rp-step-sub">Verifikasi</span>
                        </div>

                        {{-- Step 4: Kabag Umum --}}
                        <div class="rp-step-item">
                            <div class="rp-step-circle {{ $currentStep > 4 ? 'rp-step-done' : ($currentStep == 4 ? 'rp-step-active' : 'rp-step-pending') }}">
                                {{ $currentStep > 4 ? '✓' : '4' }}
                            </div>
                            <span class="rp-step-title">4. Kabag Umum</span>
                            <span class="rp-step-sub">Verifikasi</span>
                        </div>

                        {{-- Step 5: Kapus --}}
                        <div class="rp-step-item">
                            <div class="rp-step-circle {{ $currentStep >= 6 ? 'rp-step-done' : ($currentStep == 5 ? 'rp-step-active' : 'rp-step-pending') }}">
                                {{ $currentStep >= 6 ? '✓' : '5' }}
                            </div>
                            <span class="rp-step-title">5. Kapus</span>
                            <span class="rp-step-sub">Final</span>
                        </div>
                    </div>

                    {{-- Data Realisasi & Catatan Terakhir --}}
                    <div class="rp-meta-box">
                        <div>
                            <span class="rp-meta-label">Realisasi Kinerja</span>
                            <div class="rp-meta-val">
                                {{ $item->realisasi !== null ? number_format((float) $item->realisasi, 2, ',', '.') : '0,00' }}
                                <span style="font-size: 0.75rem; font-weight: normal; color: #64748b;">{{ $item->ikk?->satuan ?? '' }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="rp-meta-label">Realisasi Anggaran</span>
                            <div class="rp-meta-val">
                                {{ $item->realisasi_anggaran ? 'Rp ' . number_format((float) $item->realisasi_anggaran, 0, ',', '.') : '—' }}
                            </div>
                        </div>
                        <div>
                            <span class="rp-meta-label">Catatan PIC</span>
                            <div style="font-size: 0.8rem; color: #334155; margin-top: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->catatan }}">
                                {{ $item->catatan ?: '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Catatan Verifikasi / Catatan Pengembalian --}}
                    @php
                        $riwayatTerakhir = $item->persetujuan->sortByDesc('id')->first();
                    @endphp
                    @if ($riwayatTerakhir)
                        <div class="rp-history-box">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <strong style="color: #1e293b;">Proses Terakhir:</strong> {{ $riwayatTerakhir->user?->name ?? 'Pejabat' }}
                                    <span style="color: #64748b;">({{ ucwords(str_replace('_', ' ', $riwayatTerakhir->keputusan)) }})</span>
                                </div>
                                <span style="color: #94a3b8; font-size: 0.7rem;">
                                    {{ $riwayatTerakhir->diproses_pada ? $riwayatTerakhir->diproses_pada->translatedFormat('d M Y H:i') : '-' }}
                                </span>
                            </div>
                            @if ($riwayatTerakhir->catatan)
                                <div class="rp-note-box">
                                    <strong>Catatan:</strong> {{ $riwayatTerakhir->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Tombol Aksi Detail --}}
                    <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                        <a 
                            href="{{ \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('view', ['record' => $item]) }}"
                            class="rp-btn-detail"
                        >
                            <x-filament::icon icon="heroicon-o-eye" style="width: 14px; height: 14px;" />
                            Lihat Detail Lengkap
                        </a>
                    </div>
                </div>
            @empty
                <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 3rem 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <x-filament::icon icon="heroicon-o-document-text" style="width: 48px; height: 48px; color: #94a3b8; margin: 0 auto;" />
                    <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-top: 0.75rem;">
                        Belum Ada Riwayat Laporan
                    </h3>
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                        Laporan yang diajukan atau diproses akan muncul di sini secara berjenjang.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>