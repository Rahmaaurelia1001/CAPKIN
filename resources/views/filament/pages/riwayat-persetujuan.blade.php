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
        /* ══════════════════════════════════════════════════════
           RIWAYAT PERSETUJUAN — MODERN DESIGN (Light + Dark)
           ══════════════════════════════════════════════════════ */

        .rp-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ── Page Header Card ── */
        .rp-header-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 60%, #0284c7 100%);
            border-radius: 1.25rem;
            padding: 1.5rem 2rem;
            box-shadow: 0 10px 30px -5px rgba(2, 132, 199, 0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }
        .rp-header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
            pointer-events: none;
        }
        .rp-header-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .rp-header-desc {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.75);
            margin: 0.2rem 0 0;
        }
        .rp-header-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .rp-badge-total {
            background: rgba(255,255,255,0.18);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.25);
            white-space: nowrap;
        }

        /* ── Laporan Card ── */
        .rp-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .rp-card {
            background: #111827;
            border-color: rgba(51,65,85,0.6);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.03);
        }
        .rp-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px -4px rgba(2,132,199,0.12), 0 4px 10px rgba(15,23,42,0.05);
            border-color: #93c5fd;
        }
        .dark .rp-card:hover {
            box-shadow: 0 12px 32px -4px rgba(2,132,199,0.2), 0 4px 10px rgba(0,0,0,0.3);
            border-color: rgba(59,130,246,0.4);
        }

        /* ── Card Color Accent Strip (top border per status) ── */
        .rp-card-strip {
            height: 4px;
            background: linear-gradient(90deg, #94a3b8, #cbd5e1);
        }
        .rp-card-strip.final    { background: linear-gradient(90deg, #047857, #10b981); }
        .rp-card-strip.revisi   { background: linear-gradient(90deg, #be123c, #f43f5e); }
        .rp-card-strip.draft    { background: linear-gradient(90deg, #64748b, #94a3b8); }
        .rp-card-strip.menunggu { background: linear-gradient(90deg, #b45309, #f59e0b); }

        /* ── Card Header ── */
        .rp-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(241,245,249,0.9);
        }
        .dark .rp-card-header {
            border-bottom-color: rgba(51,65,85,0.5);
        }
        .rp-tag-ikk {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1d4ed8;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
            letter-spacing: 0.03em;
        }
        .dark .rp-tag-ikk {
            background: rgba(30,58,138,0.4);
            color: #93c5fd;
            border-color: rgba(59,130,246,0.3);
        }
        .rp-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }
        .dark .rp-card-title {
            color: #e2e8f0;
        }
        .rp-card-meta {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 0.3rem;
        }
        .dark .rp-card-meta {
            color: #64748b;
        }
        .rp-card-meta strong {
            color: #334155;
            font-weight: 600;
        }
        .dark .rp-card-meta strong {
            color: #94a3b8;
        }

        /* ── Status Badge ── */
        .rp-status-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 6px;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }

        /* ── Stepper ── */
        .rp-stepper-wrap {
            padding: 1.25rem 1.5rem;
            background: rgba(248,250,252,0.6);
            border-bottom: 1px solid rgba(241,245,249,0.9);
        }
        .dark .rp-stepper-wrap {
            background: rgba(17,24,39,0.4);
            border-bottom-color: rgba(51,65,85,0.4);
        }
        .rp-stepper {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
            position: relative;
        }
        @media (max-width: 640px) {
            .rp-stepper { grid-template-columns: repeat(3, 1fr); }
        }
        .rp-step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            gap: 0.4rem;
        }
        /* Connector line between steps */
        .rp-step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: calc(50% + 20px);
            right: calc(-50% + 20px);
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        .dark .rp-step-item:not(:last-child)::after {
            background: rgba(51,65,85,0.5);
        }
        .rp-step-item.done:not(:last-child)::after {
            background: linear-gradient(90deg, #10b981, #e2e8f0);
        }
        .dark .rp-step-item.done:not(:last-child)::after {
            background: linear-gradient(90deg, #059669, rgba(51,65,85,0.5));
        }
        .rp-step-circle {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.82rem;
            z-index: 1;
            position: relative;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .rp-step-done {
            background: linear-gradient(135deg, #047857, #10b981);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16,185,129,0.4);
        }
        .rp-step-active {
            background: linear-gradient(135deg, #1e40af, #0284c7);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.2), 0 4px 14px rgba(2,132,199,0.4);
        }
        .rp-step-pending {
            background: #f8fafc;
            color: #94a3b8;
            border: 2px solid #e2e8f0;
        }
        .dark .rp-step-pending {
            background: rgba(30,41,59,0.6);
            color: #475569;
            border-color: rgba(51,65,85,0.5);
        }
        .rp-step-skipped {
            background: transparent;
            color: #cbd5e1;
            border: 2px dashed #cbd5e1;
        }
        .dark .rp-step-skipped {
            color: rgba(51,65,85,0.6);
            border-color: rgba(51,65,85,0.4);
        }
        .rp-step-rejected {
            background: linear-gradient(135deg, #9f1239, #e11d48);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(244,63,94,0.35);
        }
        .rp-step-title {
            font-size: 0.72rem;
            font-weight: 700;
            color: #374151;
            line-height: 1.3;
        }
        .dark .rp-step-title {
            color: #9ca3af;
        }
        .rp-step-title.active-txt { color: #1d4ed8; }
        .dark .rp-step-title.active-txt { color: #60a5fa; }
        .rp-step-title.done-txt { color: #047857; }
        .dark .rp-step-title.done-txt { color: #34d399; }
        .rp-step-sub {
            font-size: 0.65rem;
            color: #9ca3af;
            margin-top: 1px;
        }
        .dark .rp-step-sub { color: #4b5563; }

        /* ── Data Meta Row ── */
        .rp-meta-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            border-bottom: 1px solid rgba(241,245,249,0.9);
        }
        .dark .rp-meta-row {
            border-bottom-color: rgba(51,65,85,0.4);
        }
        @media (max-width: 640px) {
            .rp-meta-row { grid-template-columns: 1fr; }
        }
        .rp-meta-cell {
            padding: 1rem 1.5rem;
            border-right: 1px solid rgba(241,245,249,0.9);
        }
        .dark .rp-meta-cell {
            border-right-color: rgba(51,65,85,0.4);
        }
        .rp-meta-cell:last-child { border-right: none; }
        .rp-meta-label {
            font-size: 0.68rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: block;
            margin-bottom: 0.3rem;
        }
        .rp-meta-value {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
        }
        .dark .rp-meta-value { color: #e2e8f0; }
        .rp-meta-value .unit-txt {
            font-size: 0.72rem;
            font-weight: 500;
            color: #64748b;
        }

        /* ── History / Approval Log ── */
        .rp-history-wrap {
            padding: 1rem 1.5rem;
        }
        .rp-history-box {
            background: rgba(248,250,252,0.8);
            border: 1px solid rgba(226,232,240,0.7);
            border-radius: 10px;
            padding: 0.9rem 1.1rem;
            font-size: 0.8rem;
        }
        .dark .rp-history-box {
            background: rgba(17,24,39,0.6);
            border-color: rgba(51,65,85,0.5);
        }
        .rp-history-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .rp-history-who {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.82rem;
        }
        .dark .rp-history-who { color: #cbd5e1; }
        .rp-history-action {
            font-size: 0.75rem;
            color: #64748b;
        }
        .rp-history-time {
            font-size: 0.68rem;
            color: #94a3b8;
            white-space: nowrap;
        }
        .rp-note-box {
            margin-top: 0.6rem;
            padding: 0.6rem 0.9rem;
            background: rgba(254,249,195,0.5);
            border: 1px solid rgba(253,230,138,0.6);
            border-left: 3px solid #f59e0b;
            border-radius: 6px;
            font-size: 0.78rem;
            color: #78350f;
        }
        .dark .rp-note-box {
            background: rgba(120,53,15,0.15);
            border-color: rgba(245,158,11,0.3);
            border-left-color: #f59e0b;
            color: #fcd34d;
        }
        .rp-kapus-note {
            margin-top: 0.6rem;
            padding: 0.6rem 0.9rem;
            background: rgba(240,253,244,0.8);
            border: 1px solid rgba(167,243,208,0.6);
            border-left: 3px solid #10b981;
            border-radius: 6px;
            font-size: 0.78rem;
            color: #065f46;
        }
        .dark .rp-kapus-note {
            background: rgba(6,78,59,0.2);
            border-color: rgba(16,185,129,0.3);
            border-left-color: #10b981;
            color: #6ee7b7;
        }

        /* ── Card Footer (action button) ── */
        .rp-card-footer {
            padding: 0.9rem 1.5rem;
            display: flex;
            justify-content: flex-end;
            background: rgba(248,250,252,0.4);
            border-top: 1px solid rgba(241,245,249,0.9);
        }
        .dark .rp-card-footer {
            background: rgba(17,24,39,0.3);
            border-top-color: rgba(51,65,85,0.4);
        }
        .rp-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1d4ed8;
            background: rgba(239,246,255,0.8);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 0.45rem 1rem;
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .dark .rp-btn-detail {
            color: #93c5fd;
            background: rgba(30,58,138,0.25);
            border-color: rgba(59,130,246,0.3);
        }
        .rp-btn-detail:hover {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(29,78,216,0.3);
            transform: translateY(-1px);
        }
        .dark .rp-btn-detail:hover {
            background: #1e40af;
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        }

        /* ── Empty state ── */
        .rp-empty {
            background: #ffffff;
            border: 1px solid rgba(226,232,240,0.8);
            border-radius: 1.25rem;
            padding: 4rem 1.5rem;
            text-align: center;
        }
        .dark .rp-empty {
            background: #111827;
            border-color: rgba(51,65,85,0.5);
        }
        .rp-empty-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0.75rem;
        }
        .dark .rp-empty-title { color: #e2e8f0; }
        .rp-empty-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.3rem;
        }

        /* ── Filter Bar ── */
        .rp-filter-bar {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .dark .rp-filter-bar {
            background: #111827;
            border-color: rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .rp-filter-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .rp-filter-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
            flex-shrink: 0;
        }
        .dark .rp-filter-icon {
            background: rgba(30, 58, 138, 0.4);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .rp-filter-label {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0f172a;
            display: block;
        }
        .dark .rp-filter-label {
            color: #f1f5f9;
        }
        .rp-filter-sublabel {
            font-size: 0.72rem;
            color: #64748b;
            display: block;
        }
        .dark .rp-filter-sublabel {
            color: #94a3b8;
        }
        .rp-filter-active-pill {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 800;
            background: #ecfdf5;
            color: #047857;
            padding: 0.15rem 0.55rem;
            border-radius: 9999px;
            border: 1px solid #a7f3d0;
            margin-top: 0.15rem;
        }
        .dark .rp-filter-active-pill {
            background: rgba(6, 78, 59, 0.4);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }
        .rp-filter-controls {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .rp-filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .rp-select-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .dark .rp-select-label {
            color: #94a3b8;
        }
        .rp-select {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            padding: 0.45rem 2rem 0.45rem 0.75rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 170px;
        }
        .rp-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            background: #ffffff;
        }
        .dark .rp-select {
            background: #1e293b;
            border-color: #475569;
            color: #f1f5f9;
        }
        .dark .rp-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
            background: #0f172a;
        }
        .rp-btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            border-radius: 9px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 34px;
        }
        .rp-btn-reset:hover {
            background: #ffe4e6;
            color: #9f1239;
        }
        .dark .rp-btn-reset {
            background: rgba(159, 18, 57, 0.25);
            color: #fda4af;
            border-color: rgba(244, 63, 94, 0.35);
        }
        .dark .rp-btn-reset:hover {
            background: rgba(159, 18, 57, 0.4);
            color: #fecdd3;
        }
    </style>

    <div class="rp-container">

        {{-- ═══ Header Card ═══ --}}
        <div class="rp-header-card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="rp-header-icon-wrap">
                    <x-filament::icon icon="heroicon-o-clock" style="width: 22px; height: 22px; color: #ffffff;" />
                </div>
                <div>
                    <h2 class="rp-header-title">Riwayat Pelaporan & Persetujuan</h2>
                    <p class="rp-header-desc">Pantau status berjenjang dan catatan riwayat verifikasi setiap laporan kinerja.</p>
                </div>
            </div>
            <span class="rp-badge-total">{{ $laporan->count() }} Laporan</span>
        </div>

        {{-- ═══ Filter Toolbar (Bulan & Per-IKK) ═══ --}}
        <div class="rp-filter-bar">
            <div class="rp-filter-title-wrap">
                <div class="rp-filter-icon">
                    <x-filament::icon icon="heroicon-m-funnel" style="width: 20px; height: 20px; color: #0284c7;" />
                </div>
                <div>
                    <span class="rp-filter-label">Filter Riwayat</span>
                    @if($this->selectedBulan || $this->selectedIkk)
                        <span class="rp-filter-active-pill">Filter Diterapkan</span>
                    @else
                        <span class="rp-filter-sublabel">Saring berdasarkan Indikator dan Bulan</span>
                    @endif
                </div>
            </div>

            <div class="rp-filter-controls">
                {{-- Filter IKK --}}
                <div class="rp-filter-group">
                    <label class="rp-select-label">Pilih IKK</label>
                    <select wire:model.live="selectedIkk" class="rp-select" style="max-width: 320px;">
                        <option value="">Semua Indikator (IKK)</option>
                        @foreach($this->getIkkOptions() as $optIkk)
                            <option value="{{ $optIkk->id }}">
                                {{ $optIkk->kode_ikk }} — {{ \Illuminate\Support\Str::limit($optIkk->nama_ikk, 45) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div class="rp-filter-group">
                    <label class="rp-select-label">Pilih Bulan</label>
                    <select wire:model.live="selectedBulan" class="rp-select">
                        <option value="">Semua Bulan</option>
                        @php
                            $namaBulanList = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach($namaBulanList as $num => $nama)
                            <option value="{{ $num }}">Bulan {{ $num }} — {{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                @if($this->selectedBulan || $this->selectedIkk)
                    <button type="button" wire:click="resetFilters" class="rp-btn-reset" title="Hapus semua filter">
                        <x-filament::icon icon="heroicon-m-x-mark" style="width: 14px; height: 14px;" />
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- ═══ Daftar Kartu ═══ --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse ($laporan as $item)
                @php
                    $isUmper   = (int) $item->unit_id === 6;
                    $status    = $item->status;

                    $stripClass = match ($status) {
                        'final'       => 'final',
                        'perlu_revisi'=> 'revisi',
                        'draft'       => 'draft',
                        default       => 'menunggu',
                    };

                    $badgeStyle = match ($status) {
                        'final'       => 'background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;',
                        'perlu_revisi'=> 'background:#fff1f2;color:#be123c;border:1px solid #fecdd3;',
                        'draft'       => 'background:#f8fafc;color:#475569;border:1px solid #e2e8f0;',
                        default       => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
                    };
                    $badgeStyleDark = match ($status) {
                        'final'       => 'background:rgba(6,78,59,0.4);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3);',
                        'perlu_revisi'=> 'background:rgba(127,29,29,0.4);color:#fca5a5;border:1px solid rgba(239,68,68,0.3);',
                        'draft'       => 'background:rgba(30,41,59,0.5);color:#94a3b8;border:1px solid rgba(51,65,85,0.5);',
                        default       => 'background:rgba(120,53,15,0.4);color:#fcd34d;border:1px solid rgba(245,158,11,0.3);',
                    };

                    $statusText = match ($status) {
                        'final'                 => '✅ Final — Disetujui Kapus',
                        'perlu_revisi'          => '↩ Perlu Revisi PIC',
                        'draft'                 => '📝 Draft',
                        'diajukan'              => $isUmper ? '⏳ Menunggu Katim UMPer' : '⏳ Menunggu Kabid/Kabag',
                        'disetujui_kabid'       => '⏳ Menunggu Katim UMPer',
                        'disetujui_katim_umper' => '⏳ Menunggu Kabag Umum',
                        'disetujui_kabag_umum'  => '⏳ Menunggu Kapus',
                        default                 => ucwords(str_replace('_', ' ', $status)),
                    };

                    $namaBulan = [
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ][$item->bulan] ?? "Bulan {$item->bulan}";

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
                        ['label' => 'PIC Input',     'sub' => 'Diajukan'],
                        ['label' => 'Kabid/Kabag',   'sub' => $isUmper ? 'Dilewati' : 'Verifikasi'],
                        ['label' => 'Katim UMPer',   'sub' => 'Verifikasi'],
                        ['label' => 'Kabag Umum',    'sub' => 'Verifikasi'],
                        ['label' => 'Kapus',         'sub' => 'Final'],
                    ];
                @endphp

                <div class="rp-card">
                    {{-- Color strip --}}
                    <div class="rp-card-strip {{ $stripClass }}"></div>

                    {{-- Card Header --}}
                    <div class="rp-card-header">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.4rem;">
                                <span class="rp-tag-ikk">{{ $item->ikk?->kode_ikk ?? 'IKK' }}</span>
                                <span class="rp-card-title">{{ $item->ikk?->nama_ikk ?? 'Indikator Kinerja' }}</span>
                            </div>
                            <div class="rp-card-meta">
                                Unit: <strong>{{ $item->unit?->nama ?? '-' }}</strong>
                                &bull; Periode: <strong>{{ $namaBulan }} {{ $item->tahun }}</strong>
                                &bull; PIC: <strong>{{ $item->pic?->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <span class="rp-status-badge" style="{{ $badgeStyle }}">{{ $statusText }}</span>
                    </div>

                    {{-- Stepper --}}
                    <div class="rp-stepper-wrap">
                        <div class="rp-stepper">
                            @foreach ($steps as $i => $step)
                                @php
                                    $stepNum = $i + 1;
                                    $isSkipped = ($stepNum === 2 && $isUmper);

                                    if ($currentStep === -1) {
                                        $circleClass = $stepNum === 1 ? 'rp-step-rejected' : 'rp-step-pending';
                                        $titleClass  = '';
                                    } elseif ($isSkipped) {
                                        $circleClass = 'rp-step-skipped';
                                        $titleClass  = '';
                                    } elseif ($currentStep > $stepNum) {
                                        $circleClass = 'rp-step-done';
                                        $titleClass  = 'done-txt';
                                    } elseif ($currentStep === $stepNum) {
                                        $circleClass = 'rp-step-active';
                                        $titleClass  = 'active-txt';
                                    } else {
                                        $circleClass = 'rp-step-pending';
                                        $titleClass  = '';
                                    }

                                    $doneClass = ($currentStep > $stepNum && !$isSkipped) ? 'done' : '';

                                    $circleContent = match(true) {
                                        $currentStep === -1 && $stepNum === 1 => '✕',
                                        $isSkipped => '—',
                                        $currentStep > $stepNum => '✓',
                                        default => (string)$stepNum,
                                    };
                                @endphp
                                <div class="rp-step-item {{ $doneClass }}" style="{{ $isSkipped ? 'opacity:0.4;' : '' }}">
                                    <div class="rp-step-circle {{ $circleClass }}">{{ $circleContent }}</div>
                                    <span class="rp-step-title {{ $titleClass }}">{{ $stepNum }}. {{ $step['label'] }}</span>
                                    <span class="rp-step-sub">{{ $step['sub'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Meta Data Row --}}
                    <div class="rp-meta-row">
                        <div class="rp-meta-cell">
                            <span class="rp-meta-label">Realisasi Kinerja</span>
                            <div class="rp-meta-value">
                                {{ $item->realisasi !== null ? number_format((float) $item->realisasi, 2, ',', '.') : '0,00' }}
                                <span class="unit-txt">{{ $item->ikk?->satuan ?? '' }}</span>
                            </div>
                        </div>
                        <div class="rp-meta-cell">
                            <span class="rp-meta-label">Realisasi Anggaran</span>
                            <div class="rp-meta-value">
                                {{ $item->realisasi_anggaran ? 'Rp ' . number_format((float) $item->realisasi_anggaran, 0, ',', '.') : '—' }}
                            </div>
                        </div>
                        <div class="rp-meta-cell">
                            <span class="rp-meta-label">Catatan PIC</span>
                            <div class="rp-meta-value" style="font-size:0.8rem; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px;" title="{{ $item->catatan }}">
                                {{ $item->catatan ?: '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- Approval History --}}
                    @php $riwayatTerakhir = $item->persetujuan->sortByDesc('id')->first(); @endphp
                    @if ($riwayatTerakhir)
                        <div class="rp-history-wrap">
                            <div class="rp-history-box">
                                <div class="rp-history-top">
                                    <div>
                                        <span class="rp-history-who">{{ $riwayatTerakhir->user?->name ?? 'Pejabat' }}</span>
                                        <span class="rp-history-action">&nbsp;—&nbsp;{{ ucwords(str_replace('_', ' ', $riwayatTerakhir->keputusan)) }}</span>
                                    </div>
                                    <span class="rp-history-time">
                                        🕐 {{ $riwayatTerakhir->diproses_pada ? $riwayatTerakhir->diproses_pada->translatedFormat('d M Y, H:i') : '-' }}
                                    </span>
                                </div>

                                @if ($riwayatTerakhir->catatan && $riwayatTerakhir->tahap !== 5)
                                    <div class="rp-note-box">
                                        <strong>Catatan:</strong> {{ $riwayatTerakhir->catatan }}
                                    </div>
                                @endif

                                @php
                                    $catatanKapus = $item->persetujuan->where('tahap', 5)->filter(fn($p) => filled($p->catatan))->last();
                                @endphp
                                @if ($catatanKapus)
                                    <div class="rp-kapus-note">
                                        <strong>Arahan Kepala Pusat:</strong> {{ $catatanKapus->catatan }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Footer Actions --}}
                    <div class="rp-card-footer">
                        <a
                            href="{{ \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('view', ['record' => $item]) }}"
                            class="rp-btn-detail"
                        >
                            <x-filament::icon icon="heroicon-o-eye" style="width: 13px; height: 13px;" />
                            Lihat Detail Lengkap
                        </a>
                    </div>
                </div>

            @empty
                <div class="rp-empty">
                    <x-filament::icon icon="heroicon-o-document-text" style="width: 52px; height: 52px; color: #cbd5e1; margin: 0 auto; display: block;" />
                    <h3 class="rp-empty-title">
                        @if($this->selectedBulan || $this->selectedIkk)
                            Tidak Ada Laporan yang Sesuai Filter
                        @else
                            Belum Ada Riwayat Laporan
                        @endif
                    </h3>
                    <p class="rp-empty-desc">
                        @if($this->selectedBulan || $this->selectedIkk)
                            Silakan sesuaikan pilihan IKK atau Bulan, atau klik Reset Filter untuk menampilkan seluruh laporan.
                        @else
                            Laporan yang diajukan atau diproses akan muncul di sini secara berjenjang.
                        @endif
                    </p>
                    @if($this->selectedBulan || $this->selectedIkk)
                        <div style="margin-top: 1.25rem;">
                            <button type="button" wire:click="resetFilters" class="rp-btn-reset" style="height: auto; padding: 0.55rem 1.15rem; font-size: 0.82rem;">
                                <x-filament::icon icon="heroicon-m-arrow-path" style="width: 15px; height: 15px;" />
                                <span>Reset Filter</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
