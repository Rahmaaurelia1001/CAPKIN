<x-filament-panels::page>
    {{-- Memastikan Chart.js termuat dengan andal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ══════════════════════════════════════════════════════════════════
           DETAIL IKK — MODERN & DYNAMIC THEME (LIGHT & DARK)
           ══════════════════════════════════════════════════════════════════ */
        .di-container {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* ── Top Navigation Bar ── */
        .di-nav-card {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .dark .di-nav-card {
            background: #111827;
            border-color: rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .di-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #1e40af;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            transition: all 0.2s ease;
        }
        .di-back-btn:hover {
            background: #1e40af;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
            transform: translateY(-1px);
        }
        .dark .di-back-btn {
            background: rgba(30, 58, 138, 0.3);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        .dark .di-back-btn:hover {
            background: #1e40af;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.4);
        }

        .di-select-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .di-select-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .di-select-label {
            color: #94a3b8;
        }
        .di-select {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
            max-width: 320px;
        }
        .di-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18);
        }
        .dark .di-select {
            background: #0f172a;
            color: #e2e8f0;
            border-color: rgba(51, 65, 85, 0.7);
        }
        .dark .di-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        /* ── Metric Executive Cards ── */
        .di-metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
        }
        .di-stat-card {
            border-radius: 18px;
            padding: 22px 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.22);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: default;
        }
        .di-stat-card:hover {
            transform: translateY(-5px) scale(1.015);
        }
        .di-stat-card.c1 {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d6fbd 60%, #0284c7 100%);
            box-shadow: 0 10px 28px -4px rgba(2, 132, 199, 0.5), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        .di-stat-card.c2 {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 60%, #8b5cf6 100%);
            box-shadow: 0 10px 28px -4px rgba(139, 92, 246, 0.5), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        .di-stat-card.c3 {
            background: linear-gradient(135deg, #92400e 0%, #d97706 60%, #f59e0b 100%);
            box-shadow: 0 10px 28px -4px rgba(245, 158, 11, 0.5), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        .di-stat-card.c4 {
            background: linear-gradient(135deg, #064e3b 0%, #059669 60%, #10b981 100%);
            box-shadow: 0 10px 28px -4px rgba(16, 185, 129, 0.5), inset 0 1px 0 rgba(255,255,255,0.15);
        }

        .di-stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .di-stat-label {
            font-size: 11px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .di-stat-pill {
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(4px);
            padding: 3px 9px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
        }
        .di-stat-value {
            font-size: 26px;
            font-weight: 900;
            margin-top: 10px;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            line-height: 1.15;
        }
        .di-stat-unit {
            font-size: 13px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            margin-left: 3px;
        }
        .di-stat-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 6px;
            font-weight: 600;
        }

        /* ── Chart Container Cards ── */
        .di-chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(460px, 1fr));
            gap: 18px;
        }
        @media (max-width: 640px) {
            .di-chart-grid {
                grid-template-columns: 1fr;
            }
        }
        .di-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .dark .di-card {
            background: #111827;
            border-color: rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }
        .di-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 10px;
        }
        .di-chart-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .dark .di-chart-title {
            color: #f1f5f9;
        }
        .di-chart-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }
        .dark .di-chart-sub {
            color: #94a3b8;
        }
        .di-chart-badge {
            font-size: 11px;
            font-weight: 700;
            color: #1e40af;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 9999px;
            white-space: nowrap;
        }
        .dark .di-chart-badge {
            background: rgba(30, 58, 138, 0.3);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        .di-chart-canvas-box {
            height: 280px;
            position: relative;
        }

        /* ── Table Rincian 12 Bulan ── */
        .di-table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.03);
        }
        .dark .di-table-card {
            background: #111827;
            border-color: rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }
        .di-table-top {
            padding: 16px 22px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .dark .di-table-top {
            background: rgba(17, 24, 39, 0.8);
            border-bottom-color: rgba(51, 65, 85, 0.6);
        }
        .di-table-h3 {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .dark .di-table-h3 {
            color: #f1f5f9;
        }
        .di-table-desc {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0;
        }
        .dark .di-table-desc {
            color: #94a3b8;
        }
        .di-posisi-pill {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 4px 14px;
            border-radius: 9999px;
        }
        .dark .di-posisi-pill {
            background: #0f172a;
            color: #cbd5e1;
            border-color: rgba(51, 65, 85, 0.7);
        }

        .di-table-wrap {
            overflow-x: auto;
        }
        .di-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 12px;
        }
        .di-thead-tr {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }
        .dark .di-thead-tr {
            background: #0f172a;
            color: #94a3b8;
            border-bottom-color: rgba(51, 65, 85, 0.7);
        }
        .di-th {
            padding: 13px 18px;
            white-space: nowrap;
        }
        .di-tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }
        .dark .di-tr {
            border-bottom-color: rgba(30, 41, 59, 0.8);
        }
        .di-tr.aktif {
            background: #ffffff;
        }
        .dark .di-tr.aktif {
            background: transparent;
        }
        .di-tr.inaktif {
            background: #fafafa;
            opacity: 0.55;
        }
        .dark .di-tr.inaktif {
            background: rgba(15, 23, 42, 0.4);
            opacity: 0.45;
        }
        .di-tr:hover {
            background: #f0f7ff !important;
        }
        .dark .di-tr:hover {
            background: rgba(59, 130, 246, 0.08) !important;
        }
        .di-td {
            padding: 13px 18px;
            color: #334155;
        }
        .dark .di-td {
            color: #cbd5e1;
        }
        .di-td-bulan {
            font-weight: 800;
            color: #0f172a;
        }
        .dark .di-td-bulan {
            color: #f1f5f9;
        }

        /* Matriks Pelatihan (IKK 20) */
        .di-matriks-header {
            background: #e2e8f0;
            color: #1e293b;
            border-bottom: 2px solid #cbd5e1;
        }
        .dark .di-matriks-header {
            background: #1e293b;
            color: #94a3b8;
            border-bottom-color: rgba(51, 65, 85, 0.7);
        }
        .di-matriks-th-aktif {
            background: #dbeafe !important;
            color: #1e40af !important;
        }
        .dark .di-matriks-th-aktif {
            background: rgba(30, 58, 138, 0.45) !important;
            color: #93c5fd !important;
        }
        .di-matriks-td-aktif {
            background: rgba(240, 249, 255, 0.5);
        }
        .dark .di-matriks-td-aktif {
            background: rgba(15, 23, 42, 0.25);
        }
        .di-matriks-foot {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
        }
        .dark .di-matriks-foot {
            background: #0f172a;
            border-top-color: rgba(51, 65, 85, 0.7);
        }

        /* ── Early Warning Alert Box (Kesenjangan Kinerja vs Anggaran) ── */
        .di-alert-box {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 16px 20px;
            border-radius: 14px;
            transition: all 0.25s ease;
        }
        .di-alert-danger {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            border: 1px solid #fecdd3;
            box-shadow: 0 4px 16px -2px rgba(225, 29, 72, 0.08);
        }
        .dark .di-alert-danger {
            background: linear-gradient(135deg, rgba(136, 19, 55, 0.25) 0%, rgba(76, 5, 25, 0.35) 100%);
            border-color: rgba(225, 29, 72, 0.4);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.3);
        }

        .di-alert-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            box-shadow: 0 4px 16px -2px rgba(217, 119, 6, 0.08);
        }
        .dark .di-alert-warning {
            background: linear-gradient(135deg, rgba(120, 53, 15, 0.25) 0%, rgba(69, 26, 3, 0.35) 100%);
            border-color: rgba(217, 119, 6, 0.4);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.3);
        }

        .di-alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            box-shadow: 0 4px 16px -2px rgba(16, 185, 129, 0.08);
        }
        .dark .di-alert-success {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.25) 0%, rgba(2, 44, 34, 0.35) 100%);
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.3);
        }

        .di-alert-icon-box {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .di-alert-icon-danger {
            background: #e11d48;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
        }
        .di-alert-icon-warning {
            background: #d97706;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
        }
        .di-alert-icon-success {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
        }

        .di-alert-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .di-alert-title-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .di-alert-title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .di-title-danger { color: #9f1239; }
        .dark .di-title-danger { color: #fecdd3; }
        .di-title-warning { color: #92400e; }
        .dark .di-title-warning { color: #fde68a; }
        .di-title-success { color: #065f46; }
        .dark .di-title-success { color: #a7f3d0; }

        .di-alert-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 9999px;
            letter-spacing: 0.02em;
        }
        .di-badge-danger {
            background: #e11d48;
            color: #ffffff;
        }
        .di-badge-warning {
            background: #d97706;
            color: #ffffff;
        }
        .di-badge-success {
            background: #059669;
            color: #ffffff;
        }

        .di-alert-desc {
            font-size: 13px;
            line-height: 1.5;
            color: #475569;
        }
        .dark .di-alert-desc {
            color: #cbd5e1;
        }

        .di-alert-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }
        .di-alert-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(0, 0, 0, 0.06);
            color: #1e293b;
        }
        .dark .di-alert-chip {
            background: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }
    </style>

    <div class="di-container">

        {{-- Bar Navigasi Atas & Pemilih IKK Cepat --}}
        <div class="di-nav-card">
            <a href="{{ \App\Filament\Pages\Dashboard::getUrl() }}" class="di-back-btn">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard Utama
            </a>

            <div class="di-select-wrap">
                <span class="di-select-label">Pilih Indikator:</span>
                <select wire:model.live="record" class="di-select">
                    @foreach(\App\Models\Ikk::where('is_active', true)->orderBy('kode_ikk')->get() as $item)
                        <option value="{{ $item->id }}">{{ $item->kode_ikk }} — {{ $item->nama_ikk }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 1. Kartu Ringkasan 4 Metrik Eksekutif (VIBRANT MODERN CARDS) --}}
        <div class="di-metric-grid">
            {{-- Card 1: Target (Vibrant Ocean Navy) --}}
            <div class="di-stat-card c1">
                <div class="di-stat-top">
                    <span class="di-stat-label">Target Tahunan</span>
                    <span class="di-stat-pill">DIPA {{ $tahun }}</span>
                </div>
                <div class="di-stat-value">
                    @php $decSatuan = (str_contains($dataDetail['satuan'] ?? '', '%') || stripos($dataDetail['satuan'] ?? '', 'indeks') !== false || stripos($dataDetail['satuan'] ?? '', 'nilai') !== false || stripos($dataDetail['satuan'] ?? '', 'poin') !== false || ($dataDetail['target'] != (int)$dataDetail['target'])) ? 2 : 0; @endphp
                    {{ number_format($dataDetail['target'], $decSatuan, ',', '.') }}
                    <span class="di-stat-unit">{{ $dataDetail['satuan'] }}</span>
                </div>
                <div class="di-stat-sub">Target Sasaran Strategis</div>
            </div>

            {{-- Card 2: Realisasi Kinerja (Vibrant Indigo Purple) --}}
            <div class="di-stat-card c2">
                <div class="di-stat-top">
                    <span class="di-stat-label">Realisasi Kinerja</span>
                    <span class="di-stat-pill">{{ number_format($dataDetail['capaian_kinerja'], 2, ',', '.') }}%</span>
                </div>
                <div class="di-stat-value">
                    {{ number_format($dataDetail['realisasi_kinerja'], $decSatuan ?? ((str_contains($dataDetail['satuan'] ?? '', '%') || stripos($dataDetail['satuan'] ?? '', 'indeks') !== false) ? 2 : 0), ',', '.') }}
                    <span class="di-stat-unit">{{ $dataDetail['satuan'] }}</span>
                </div>
                <div class="di-stat-sub">
                    @if($dataDetail['capaian_kinerja'] >= 100)
                        ✨ Target Tercapai
                    @elseif($dataDetail['capaian_kinerja'] >= 70)
                        📈 On Track (Mendekati)
                    @else
                        ⚠️ Perlu Perhatian
                    @endif
                </div>
            </div>

            {{-- Card 3: Pagu Anggaran (Vibrant Amber Gold) --}}
            <div class="di-stat-card c3">
                <div class="di-stat-top">
                    <span class="di-stat-label">Pagu Anggaran</span>
                    <span class="di-stat-pill">TA {{ $tahun }}</span>
                </div>
                <div class="di-stat-value">
                    Rp {{ number_format($dataDetail['pagu'], 0, ',', '.') }}
                </div>
            </div>

            {{-- Card 4: Realisasi Anggaran (Vibrant Emerald Teal) --}}
            <div class="di-stat-card c4">
                <div class="di-stat-top">
                    <span class="di-stat-label">Realisasi Anggaran</span>
                    <span class="di-stat-pill">{{ number_format($dataDetail['persen_anggaran'], 1, ',', '.') }}%</span>
                </div>
                <div class="di-stat-value">
                    Rp {{ number_format($dataDetail['realisasi_anggaran'], 0, ',', '.') }}
                </div>
                <div class="di-stat-sub">Terserap s.d. Bulan {{ $dataDetail['max_bulan'] }}</div>
            </div>
        </div>

        {{-- Banner Peringatan Dini Kesenjangan Kinerja vs Anggaran (Early Warning System) --}}
        @php
            $persenAngg = (float) ($dataDetail['persen_anggaran'] ?? 0);
            $capaianKin = (float) ($dataDetail['capaian_kinerja'] ?? 0);
            $gapKinerjaAnggaran = $persenAngg - $capaianKin;

            // Kondisi Kritis: Serapan tinggi tapi capaian tertinggal jauh
            $isCriticalGap = ($persenAngg >= 40 && $capaianKin < 40 && $gapKinerjaAnggaran >= 20) 
                          || ($persenAngg >= 60 && $gapKinerjaAnggaran >= 25);

            // Kondisi Perhatian: Serapan mulai mendahului output
            $isModerateGap = ! $isCriticalGap && ($gapKinerjaAnggaran >= 15 && $persenAngg >= 30);

            // Kondisi Sangat Efisien: Output tercapai/melampaui target dengan serapan hemat
            $isEfisien = ($capaianKin >= 90 && $persenAngg < 70);
        @endphp

        @if($isCriticalGap)
            <div class="di-alert-box di-alert-danger">
                <div class="di-alert-icon-box di-alert-icon-danger">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="di-alert-content">
                    <div class="di-alert-title-row">
                        <span class="di-alert-title di-title-danger">
                            ⚠️ Peringatan Dini: Penyerapan Anggaran Mendahului Realisasi Output
                        </span>
                        <span class="di-alert-badge di-badge-danger">
                            Kesenjangan Tinggi (+{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%)
                        </span>
                    </div>
                    <div class="di-alert-desc">
                        Penyerapan anggaran telah mencapai <strong>{{ number_format($persenAngg, 1, ',', '.') }}%</strong> (Rp {{ number_format($dataDetail['realisasi_anggaran'], 0, ',', '.') }}), namun realisasi kinerja fisik output baru mencapai <strong>{{ number_format($capaianKin, 2, ',', '.') }}%</strong> (terdapat deviasi kesenjangan <strong>+{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%</strong>).
                        <br>
                        <strong>Rekomendasi Tindak Lanjut:</strong> Segera lakukan percepatan pelaksanaan kegiatan dan verifikasi kelengkapan dokumen pendukung/eviden agar capaian fisik segera meningkat berimbang dengan belanja anggaran.
                    </div>
                    <div class="di-alert-chips">
                        <span class="di-alert-chip">
                            💰 Serapan: {{ number_format($persenAngg, 1, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip">
                            🎯 Output: {{ number_format($capaianKin, 2, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip" style="color: #e11d48; font-weight: 800;">
                            ⚠️ Gap: +{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%
                        </span>
                    </div>
                </div>
            </div>
        @elseif($isModerateGap)
            <div class="di-alert-box di-alert-warning">
                <div class="di-alert-icon-box di-alert-icon-warning">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="di-alert-content">
                    <div class="di-alert-title-row">
                        <span class="di-alert-title di-title-warning">
                            ⚡ Perhatian: Laju Serapan Anggaran Sedikit di Atas Capaian Output
                        </span>
                        <span class="di-alert-badge di-badge-warning">
                            Deviasi (+{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%)
                        </span>
                    </div>
                    <div class="di-alert-desc">
                        Penyerapan anggaran telah mencapai <strong>{{ number_format($persenAngg, 1, ',', '.') }}%</strong>, mendahului capaian output kinerja yang berada di posisi <strong>{{ number_format($capaianKin, 2, ',', '.') }}%</strong> (selisih <strong>+{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%</strong>).
                        <br>
                        <strong>Rekomendasi:</strong> Disarankan melakukan monitoring berkala untuk memastikan penyelesaian sisa target fisik selesai tepat waktu sebelum akhir tahun anggaran.
                    </div>
                    <div class="di-alert-chips">
                        <span class="di-alert-chip">
                            💰 Serapan: {{ number_format($persenAngg, 1, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip">
                            🎯 Output: {{ number_format($capaianKin, 2, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip" style="color: #d97706; font-weight: 800;">
                            ⚡ Gap: +{{ number_format($gapKinerjaAnggaran, 1, ',', '.') }}%
                        </span>
                    </div>
                </div>
            </div>
        @elseif($isEfisien)
            <div class="di-alert-box di-alert-success">
                <div class="di-alert-icon-box di-alert-icon-success">
                    <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="di-alert-content">
                    <div class="di-alert-title-row">
                        <span class="di-alert-title di-title-success">
                            ✨ Kinerja Sangat Efisien: Realisasi Output Melampaui Laju Anggaran
                        </span>
                        <span class="di-alert-badge di-badge-success">
                            Efisien
                        </span>
                    </div>
                    <div class="di-alert-desc">
                        Realisasi output kinerja telah mencapai <strong>{{ number_format($capaianKin, 2, ',', '.') }}%</strong> (melampaui target tahunan), dengan serapan anggaran sebesar <strong>{{ number_format($persenAngg, 1, ',', '.') }}%</strong> (Rp {{ number_format($dataDetail['realisasi_anggaran'], 0, ',', '.') }}). Pelaksanaan kegiatan terlaksana secara produktif dan efisien.
                    </div>
                    <div class="di-alert-chips">
                        <span class="di-alert-chip">
                            🎯 Output: {{ number_format($capaianKin, 2, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip">
                            💰 Serapan: {{ number_format($persenAngg, 1, ',', '.') }}%
                        </span>
                        <span class="di-alert-chip" style="color: #059669; font-weight: 800;">
                            ✨ Efisiensi Tinggi
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- 2. Dua Grafik Chart.js Interaktif Lengkap dengan TOOLTIP --}}
        <div class="di-chart-grid">

            {{-- Grafik 1: Tren Capaian Kinerja (Garis Tegas) --}}
            <div class="di-card">
                <div class="di-chart-header">
                    <div>
                        <div class="di-chart-title">Tren Capaian Kinerja Bulanan (%)</div>
                        <div class="di-chart-sub">Garis lurus tegas capaian kumulatif vs Target 100%</div>
                    </div>
                    <span class="di-chart-badge">
                        Interactive Tooltip Aktif
                    </span>
                </div>
                <div class="di-chart-canvas-box">
                    <canvas id="chartDetailKinerja"></canvas>
                </div>
            </div>

            {{-- Grafik 2: Penyerapan Anggaran (Kurva S) --}}
            <div class="di-card">
                <div class="di-chart-header">
                    <div>
                        <div class="di-chart-title">Kurva S Penyerapan Anggaran (%)</div>
                        <div class="di-chart-sub">Tren realisasi penyerapan kumulatif terhadap Target 100%</div>
                    </div>
                    <span class="di-chart-badge">
                        Kurva S Aktif
                    </span>
                </div>
                <div class="di-chart-canvas-box">
                    <canvas id="chartDetailAnggaran"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Tabel Rincian 12 Bulan Lengkap --}}
        <div class="di-table-card">
            <div class="di-table-top">
                <div>
                    <h3 class="di-table-h3">
                        Tabel Rincian Bulanan: {{ $ikk->kode_ikk }}
                    </h3>
                    <p class="di-table-desc">Detail output realisasi kinerja, penyerapan anggaran, dan status pemeriksaan</p>
                </div>
                <span class="di-posisi-pill">
                    Posisi Laporan Final: s.d. Bulan {{ $dataDetail['max_bulan'] }}
                </span>
            </div>

            <div class="di-table-wrap">
                <table class="di-table">
                    <thead>
                        <tr class="di-thead-tr">
                            <th class="di-th">Bulan</th>
                            <th class="di-th" style="text-align: center;">Output Realisasi</th>
                            <th class="di-th" style="text-align: center;">Capaian Kinerja (%)</th>
                            <th class="di-th" style="text-align: right;">Pengeluaran Bulanan</th>
                            <th class="di-th" style="text-align: right;">Realisasi Anggaran Kumulatif</th>
                            <th class="di-th" style="text-align: center;">Penyerapan (%)</th>
                            <th class="di-th" style="text-align: center;">Status</th>
                            <th class="di-th">Catatan Narasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataDetail['tabel_bulanan'] as $row)
                            @php $isAktif = $row['bulan'] <= $dataDetail['max_bulan']; @endphp
                            <tr class="di-tr {{ $isAktif ? 'aktif' : 'inaktif' }}">
                                <td class="di-td di-td-bulan">
                                    {{ $row['nama_bulan'] }}
                                </td>
                                <td class="di-td" style="text-align: center; font-weight: 700;">
                                    @if($row['realisasi_kinerja'] !== null)
                                        @php
                                            $satuanRk = $dataDetail['satuan'] ?? '';
                                            $decRk = (str_contains($satuanRk, '%') || stripos($satuanRk, 'indeks') !== false || stripos($satuanRk, 'nilai') !== false || stripos($satuanRk, 'poin') !== false) ? 2 : 0;
                                        @endphp
                                        {{ number_format($row['realisasi_kinerja'], $decRk, ',', '.') }}
                                        <span style="font-size: 11px; opacity: 0.7; font-weight: 500;">{{ $dataDetail['satuan'] }}</span>
                                    @else
                                        <span style="opacity: 0.4;">—</span>
                                    @endif
                                </td>
                                <td class="di-td" style="text-align: center;">
                                    @if($row['capaian_kinerja'] !== null)
                                        @php
                                            $cVal = $row['capaian_kinerja'];
                                            $bgC = $cVal >= 100 ? '#ecfdf5' : ($cVal >= 70 ? '#eff6ff' : '#fef2f2');
                                            $txC = $cVal >= 100 ? '#047857' : ($cVal >= 70 ? '#1d4ed8' : '#b91c1c');
                                            $bdC = $cVal >= 100 ? '#a7f3d0' : ($cVal >= 70 ? '#bfdbfe' : '#fecaca');
                                        @endphp
                                        <span style="display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background: {{ $bgC }}; color: {{ $txC }}; border: 1px solid {{ $bdC }};">
                                            {{ number_format($cVal, 2, ',', '.') }}%
                                        </span>
                                    @else
                                        <span style="opacity: 0.4;">—</span>
                                    @endif
                                </td>
                                <td class="di-td" style="text-align: right; font-weight: 600;">
                                    @if($row['anggaran_bulanan'] !== null && $row['anggaran_bulanan'] > 0)
                                        Rp {{ number_format($row['anggaran_bulanan'], 0, ',', '.') }}
                                    @else
                                        <span style="opacity: 0.4;">—</span>
                                    @endif
                                </td>
                                <td class="di-td di-td-bulan" style="text-align: right;">
                                    @if($row['realisasi_anggaran'] !== null)
                                        Rp {{ number_format($row['realisasi_anggaran'], 0, ',', '.') }}
                                    @else
                                        <span style="opacity: 0.4;">—</span>
                                    @endif
                                </td>
                                <td class="di-td" style="text-align: center; font-weight: 800; color: #10b981;">
                                    @if($row['persen_anggaran'] !== null)
                                        {{ number_format($row['persen_anggaran'], 2, ',', '.') }}%
                                    @else
                                        <span style="opacity: 0.4;">—</span>
                                    @endif
                                </td>
                                <td class="di-td" style="text-align: center;">
                                    @if($row['status_laporan'] === 'final')
                                        <span style="display: inline-block; padding: 3px 9px; border-radius: 9999px; font-size: 10px; font-weight: 800; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; letter-spacing: 0.05em;">
                                            FINAL
                                        </span>
                                    @elseif($row['status_laporan'] === 'belum_ada')
                                        <span style="font-size: 11px; opacity: 0.5;">Belum Ada</span>
                                    @else
                                        <span style="display: inline-block; padding: 3px 9px; border-radius: 9999px; font-size: 10px; font-weight: 800; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; letter-spacing: 0.05em;">
                                            {{ strtoupper($row['status_laporan']) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="di-td" style="font-size: 11px; max-width: 250px;">
                                    {{ $row['narasi'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($dataDetail['matriks_pelatihan']))
        {{-- 4. Matriks Kumulatif Pelatihan IKK 20 (Januari s.d. Desember) --}}
        <div class="di-table-card" style="margin-top: 6px;">
            <div class="di-table-top" style="background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 100%);">
                <div>
                    <h3 class="di-table-h3" style="color: #0369a1; display: flex; align-items: center; gap: 8px;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; display: inline-block;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Matriks Rincian Realisasi Peserta Pelatihan Kumulatif (IKK 20)
                    </h3>
                    <p class="di-table-desc" style="color: #0284c7;">
                        Rekapitulasi kumulatif jumlah peserta menurut nama pelatihan dan sumber pembiayaan — Tahun Anggaran {{ $tahun }}
                    </p>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <span class="di-posisi-pill" style="border-color: #38bdf8; background: #ffffff; color: #0284c7;">
                        Data Terisi: s.d. Bulan {{ $dataDetail['matriks_pelatihan']['max_bulan'] }} (Agustus)
                    </span>
                    <span style="font-size: 11px; padding: 4px 12px; border-radius: 9999px; background: #e0f2fe; color: #0369a1; font-weight: 700; border: 1px solid #bae6fd;">
                        Target: {{ number_format($dataDetail['target'], 0, ',', '.') }} Peserta
                    </span>
                </div>
            </div>

            <div class="di-table-wrap">
                <table class="di-table di-matriks-table" style="font-size: 12px;">
                    <thead>
                        <tr class="di-matriks-header">
                            <th class="di-th" style="width: 38px; text-align: center; border-right: 1px solid #cbd5e1;">No</th>
                            <th class="di-th" style="min-width: 280px; border-right: 1px solid #cbd5e1;">Nama Pelatihan</th>
                            @php
                                $colBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($colBulan as $bIdx => $bName)
                                <th class="di-th {{ $bIdx <= $dataDetail['matriks_pelatihan']['max_bulan'] ? 'di-matriks-th-aktif' : '' }}" style="text-align: center; min-width: 72px; border-right: 1px solid #cbd5e1;">
                                    {{ $bName }}
                                </th>
                            @endforeach
                            <th class="di-th" style="text-align: center; min-width: 100px;">Sumber Dana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataDetail['matriks_pelatihan']['rows'] as $rIdx => $row)
                            <tr class="di-tr" style="border-bottom: 1px solid #e2e8f0;">
                                <td class="di-td" style="text-align: center; font-weight: 600; color: #64748b; border-right: 1px solid #f1f5f9;">
                                    {{ $rIdx + 1 }}
                                </td>
                                <td class="di-td" style="font-weight: 600; color: #1e293b; border-right: 1px solid #f1f5f9; white-space: nowrap;">
                                    {{ $row['nama'] }}
                                </td>
                                @foreach($colBulan as $bIdx => $bName)
                                    @php $val = $row['bulan'][$bIdx] ?? null; @endphp
                                    <td class="di-td {{ $bIdx <= $dataDetail['matriks_pelatihan']['max_bulan'] ? 'di-matriks-td-aktif' : '' }}" style="text-align: center; font-weight: 600; border-right: 1px solid #f1f5f9;">
                                        @if($val !== null)
                                            <span style="font-weight: 700; color: #0284c7;">{{ number_format($val, 0, ',', '.') }}</span>
                                        @else
                                            <span style="color: #cbd5e1;">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="di-td" style="text-align: center; white-space: nowrap;">
                                    @php
                                        $dana = $row['dana'];
                                        $badgeBg = match($dana) {
                                            'PNBP/RM' => '#f3e8ff',
                                            'PNBP' => '#fef3c7',
                                            'RM' => '#ecfdf5',
                                            default => '#f1f5f9'
                                        };
                                        $badgeColor = match($dana) {
                                            'PNBP/RM' => '#7e22ce',
                                            'PNBP' => '#b45309',
                                            'RM' => '#047857',
                                            default => '#475569'
                                        };
                                        $badgeBorder = match($dana) {
                                            'PNBP/RM' => '#d8b4fe',
                                            'PNBP' => '#fde68a',
                                            'RM' => '#a7f3d0',
                                            default => '#cbd5e1'
                                        };
                                    @endphp
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                        {{ $dana }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="di-matriks-foot" style="font-weight: 800;">
                            <td class="di-td" colspan="2" style="text-align: center; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; border-right: 1px solid #cbd5e1;">
                                Total Kumulatif
                            </td>
                            @foreach($colBulan as $bIdx => $bName)
                                @php $tot = $dataDetail['matriks_pelatihan']['totals'][$bIdx] ?? null; @endphp
                                <td class="di-td {{ $bIdx <= $dataDetail['matriks_pelatihan']['max_bulan'] ? 'di-matriks-th-aktif' : '' }}" style="text-align: center; font-weight: 800; font-size: 13px; border-right: 1px solid #cbd5e1;">
                                    @if($tot !== null)
                                        {{ number_format($tot, 0, ',', '.') }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="di-td" style="text-align: center; color: #64748b; font-size: 11px;">
                                —
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- Inisialisasi Script Chart.js dengan TOOLTIP LENGKAP --}}
    @php
        $chartMonths = json_encode($dataDetail['short_months']);
        $chartKinerja = [];
        $chartAnggaran = [];
        $chartTarget = array_fill(0, 12, 100);

        for ($b = 1; $b <= 12; $b++) {
            if ($b <= $dataDetail['max_bulan']) {
                $tb = $dataDetail['tabel_bulanan'][$b - 1];
                $chartKinerja[] = $tb['capaian_kinerja'] !== null ? round($tb['capaian_kinerja'], 2) : null;
                $chartAnggaran[] = $tb['persen_anggaran'] !== null ? round($tb['persen_anggaran'], 2) : null;
            } else {
                $chartKinerja[] = null;
                $chartAnggaran[] = null;
            }
        }
    @endphp

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        initDetailCharts();
    });

    document.addEventListener('livewire:navigated', function () {
        initDetailCharts();
    });

    function initDetailCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initDetailCharts, 200);
            return;
        }

        var months = {!! $chartMonths !!};
        var kinerjaData = {!! json_encode($chartKinerja) !!};
        var anggaranData = {!! json_encode($chartAnggaran) !!};
        var targetData = {!! json_encode($chartTarget) !!};
        var namaIkk = "{{ $ikk->kode_ikk }}";
        var satuan = "{{ $dataDetail['satuan'] }}";

        var isDark = document.documentElement.classList.contains('dark');
        var gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : '#f1f5f9';
        var tickColor = isDark ? '#94a3b8' : '#64748b';

        // 1. Chart Kinerja
        var ctxK = document.getElementById('chartDetailKinerja');
        if (ctxK) {
            if (window.myChartKinerja) window.myChartKinerja.destroy();
            window.myChartKinerja = new Chart(ctxK, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Capaian Kinerja (%)',
                            data: kinerjaData,
                            borderColor: '#38bdf8',
                            backgroundColor: '#38bdf8',
                            borderWidth: 3,
                            tension: 0,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0284c7',
                            pointRadius: 6,
                            pointHoverRadius: 9,
                            pointBorderWidth: 2.5,
                            spanGaps: false,
                        },
                        {
                            label: 'Target 100%',
                            data: targetData,
                            borderColor: '#f43f5e',
                            borderWidth: 1.5,
                            borderDash: [6, 6],
                            pointRadius: 0,
                            pointHoverRadius: 0,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { color: tickColor, usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#e0e7ff',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12, weight: '600' },
                            callbacks: {
                                title: function(items) {
                                    return namaIkk + ' — Bulan ' + items[0].label;
                                },
                                label: function(item) {
                                    if (item.dataset.label.includes('Target')) return 'Target: 100%';
                                    var val = item.parsed.y;
                                    if (val === null || val === undefined) return null;
                                    return 'Capaian Kinerja: ' + Number(val).toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 120, ticks: { color: tickColor, callback: v => v + '%' }, grid: { color: gridColor } },
                        x: { ticks: { color: tickColor }, grid: { display: false } }
                    }
                }
            });
        }

        // 2. Chart Anggaran
        var ctxA = document.getElementById('chartDetailAnggaran');
        if (ctxA) {
            if (window.myChartAnggaran) window.myChartAnggaran.destroy();
            window.myChartAnggaran = new Chart(ctxA, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Realisasi Kumulatif (%)',
                            data: anggaranData,
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            borderWidth: 3,
                            tension: 0.25,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#059669',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBorderWidth: 2.5,
                            order: 1,
                        },
                        {
                            label: 'Target 100%',
                            data: targetData,
                            borderColor: '#f43f5e',
                            backgroundColor: '#f43f5e',
                            borderWidth: 2,
                            borderDash: [6, 6],
                            pointRadius: 0,
                            pointHoverRadius: 0,
                            order: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { color: tickColor, usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#e0e7ff',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12, weight: '600' },
                            callbacks: {
                                title: function(items) {
                                    return namaIkk + ' — Bulan ' + items[0].label;
                                },
                                label: function(item) {
                                    if (item.dataset.label.includes('Target')) return 'Target: 100%';
                                    var val = item.parsed.y;
                                    if (val === null || val === undefined) return null;
                                    return 'Penyerapan: ' + Number(val).toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { color: tickColor, callback: v => v + '%' }, grid: { color: gridColor } },
                        x: { ticks: { color: tickColor }, grid: { display: false } }
                    }
                }
            });
        }
    }

    if (window.Livewire) {
        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                setTimeout(initDetailCharts, 100);
            });
        });
    }
    </script>
</x-filament-panels::page>
