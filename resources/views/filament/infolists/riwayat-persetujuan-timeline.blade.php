@php
    $record = $getRecord();
    $isUmper = (int) $record->unit_id === 6;
    $status = $record->status;
    $persetujuans = $record->persetujuan->sortBy('id');

    $currentStep = match ($status) {
        'draft'                 => 0,
        'diajukan'              => $isUmper ? 3 : 2,
        'disetujui_kabid'       => 3,
        'disetujui_katim_umper' => 4,
        'disetujui_kabag_umum'  => 5,
        'final'                 => 6,
        'perlu_revisi'          => -1,
        default                 => 1,
    };

    $steps = [
        ['tahap' => 1, 'role' => 'PIC Input',     'sub' => 'Pengajuan Laporan'],
        ['tahap' => 2, 'role' => 'Kabid/Kabag',   'sub' => $isUmper ? 'Dilewati (UMPer)' : 'Verifikasi Bidang'],
        ['tahap' => 3, 'role' => 'Katim UMPer',   'sub' => 'Verifikasi Anggaran'],
        ['tahap' => 4, 'role' => 'Kabag Umum',    'sub' => 'Verifikasi Final'],
        ['tahap' => 5, 'role' => 'Kepala Pusat',  'sub' => 'Pengesahan Akhir'],
    ];
@endphp

<div class="fi-in-timeline-wrapper" style="display: flex; flex-direction: column; gap: 20px;">

    {{-- 1. Stepper Visual Alur Persetujuan 5 Tahap --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px;" class="dark:bg-slate-800/60 dark:border-slate-800">
        <div style="font-size: 0.76rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 14px;">
            Alur Verifikasi Berjenjang
        </div>

        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; position: relative;">
            @foreach ($steps as $i => $step)
                @php
                    $stepNum = $i + 1;
                    $isSkipped = ($stepNum === 2 && $isUmper);

                    if ($currentStep === -1) {
                        $circleClass = $stepNum === 1 ? 'background: linear-gradient(135deg, #9f1239, #e11d48); color: #fff;' : 'background: #f1f5f9; color: #94a3b8; border: 1.5px solid #cbd5e1;';
                        $circleText = $stepNum === 1 ? '✕' : (string)$stepNum;
                    } elseif ($isSkipped) {
                        $circleClass = 'background: transparent; color: #94a3b8; border: 1.5px dashed #cbd5e1;';
                        $circleText = '—';
                    } elseif ($currentStep > $stepNum) {
                        $circleClass = 'background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 4px 10px rgba(16,185,129,0.3);';
                        $circleText = '✓';
                    } elseif ($currentStep === $stepNum) {
                        $circleClass = 'background: linear-gradient(135deg, #1e40af, #0284c7); color: #fff; box-shadow: 0 0 0 4px rgba(59,130,246,0.25);';
                        $circleText = (string)$stepNum;
                    } else {
                        $circleClass = 'background: #f8fafc; color: #94a3b8; border: 1.5px solid #e2e8f0;';
                        $circleText = (string)$stepNum;
                    }
                @endphp

                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; {{ $isSkipped ? 'opacity: 0.45;' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.82rem; margin-bottom: 6px; {{ $circleClass }}">
                        {{ $circleText }}
                    </div>
                    <span style="font-size: 0.74rem; font-weight: 700; color: #1e293b; line-height: 1.3;" class="dark:text-slate-200">
                        {{ $stepNum }}. {{ $step['role'] }}
                    </span>
                    <span style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">
                        {{ $step['sub'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 2. Daftar Log Riwayat Catatan Persetujuan (Timeline Cards) --}}
    @if ($persetujuans->isNotEmpty())
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="font-size: 0.8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px;">
                Daftar Riwayat Keputusan & Catatan Pejabat ({{ $persetujuans->count() }} Proses)
            </div>

            @foreach ($persetujuans as $idx => $p)
                @php
                    $keputusan = $p->keputusan;
                    $badgeStyle = match ($keputusan) {
                        'disetujui'    => 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;',
                        'dikembalikan' => 'background: #fef2f2; color: #be123c; border: 1px solid #fecaca;',
                        'diajukan'     => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
                        default        => 'background: #fffbeb; color: #b45309; border: 1px solid #fde68a;',
                    };

                    $tahapLabel = match ((int) $p->tahap) {
                        1 => 'Tahap 1 — PIC Input',
                        2 => 'Tahap 2 — Kabid / Kabag',
                        3 => 'Tahap 3 — Katim UMPer',
                        4 => 'Tahap 4 — Kabag Umum',
                        5 => 'Tahap 5 — Kepala Pusat',
                        default => "Tahap {$p->tahap}",
                    };

                    $pejabatName = $p->user?->name ?? 'Pejabat Sistem';
                    $initials = collect(explode(' ', $pejabatName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                @endphp

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(15,23,42,0.03); transition: all 0.2s ease;" class="dark:bg-gray-900 dark:border-slate-800">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            {{-- Avatar Initials --}}
                            <div style="width: 40px; height: 40px; border-radius: 999px; background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 800; flex-shrink: 0; box-shadow: 0 2px 8px rgba(2,132,199,0.3);">
                                {{ $initials ?: 'PJ' }}
                            </div>

                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="font-size: 0.92rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                                        {{ $pejabatName }}
                                    </span>
                                    <span style="font-size: 0.72rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 6px;">
                                        {{ $tahapLabel }}
                                    </span>
                                </div>
                                <div style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 5px;">
                                    <svg style="width: 13px; height: 13px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $p->diproses_pada ? $p->diproses_pada->translatedFormat('l, d F Y — H:i') . ' WIB' : '-' }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <span style="font-size: 0.76rem; font-weight: 800; padding: 4px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px; {{ $badgeStyle }}">
                                {{ ucwords(str_replace('_', ' ', $keputusan)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Catatan Pejabat --}}
                    @if ($p->catatan)
                        <div style="margin-top: 14px; padding: 12px 16px; background: #f8fafc; border-left: 3.5px solid #0284c7; border-radius: 0 8px 8px 0; font-size: 0.84rem; color: #334155;" class="dark:bg-slate-800/60 dark:text-slate-200">
                            <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #0284c7; display: block; margin-bottom: 3px;">
                                Catatan / Masukan Pejabat:
                            </span>
                            {{ $p->catatan }}
                        </div>
                    @endif

                    {{-- Highlight Khusus Arahan Kapus (Tahap 5) --}}
                    @if ($p->tahap == 5 && $p->catatan)
                        <div style="margin-top: 10px; padding: 12px 16px; background: #ecfdf5; border-left: 3.5px solid #10b981; border-radius: 0 8px 8px 0; font-size: 0.84rem; color: #065f46;" class="dark:bg-emerald-950/30 dark:text-emerald-300">
                            <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #047857; display: block; margin-bottom: 3px;">
                                👑 Catatan & Arahan Kepala Pusat PPSDMAP:
                            </span>
                            {{ $p->catatan }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="background: #ffffff; border: 1.5px dashed #cbd5e1; border-radius: 14px; padding: 32px 20px; text-align: center;" class="dark:bg-gray-900 dark:border-slate-800">
            <svg style="width: 42px; height: 42px; color: #94a3b8; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-top: 10px;" class="dark:text-slate-200">
                Belum Ada Catatan Persetujuan
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">
                Laporan ini masih berstatus draf awal dan belum diajukan untuk proses verifikasi.
            </div>
        </div>
    @endif
</div>

