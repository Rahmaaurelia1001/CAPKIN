<x-filament-panels::page>
    @php
        $daftarTahun = $this->getDaftarTahun();
        $ringkasan = $this->ringkasan_kinerja;
        $sasaranList = $this->data_sasaran_dan_ikk;
        $masterSasaran = $this->master_sasaran_dan_ikk;
        $isTahunIni = ($tahun === (int) now()->year);
        $hasData = $sasaranList->isNotEmpty();
    @endphp

    <style>
        /* Container */
        .pk-wrap {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            font-family: inherit;
        }

        /* Hero Control Bar */
        .pk-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 60%, #0284c7 100%);
            border-radius: 1.25rem;
            padding: 1.75rem 2rem;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .pk-hero-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .pk-hero-desc {
            font-size: 0.875rem;
            color: #e0f2fe;
            margin-top: 0.35rem;
            max-width: 650px;
            line-height: 1.45;
        }

        /* Year Selector Pills */
        .pk-year-nav {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 0.35rem;
            border-radius: 0.875rem;
            gap: 0.35rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .pk-year-btn {
            padding: 0.45rem 1rem;
            border-radius: 0.65rem;
            font-size: 0.875rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #ffffff;
            background: transparent;
        }
        .pk-year-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .pk-year-btn.active {
            background: #ffffff;
            color: #0369a1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Button Action */
        .pk-btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.15rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .pk-btn-white {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .pk-btn-white:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }
        .pk-btn-primary {
            background: #0284c7;
            color: #ffffff;
        }
        .pk-btn-primary:hover {
            background: #0369a1;
        }

        /* Stats Grid */
        .pk-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .pk-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pk-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.06);
        }
        .pk-stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .pk-stat-value {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .pk-stat-sub {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* SK Group Card */
        .pk-sk-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.15rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }
        .pk-sk-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .pk-sk-title {
            font-size: 1rem;
            font-weight: 750;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .pk-sk-badge {
            background: #0284c7;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 0.5rem;
        }

        /* IKK Table */
        .pk-table-wrap {
            overflow-x: auto;
        }
        .pk-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }
        .pk-table th {
            background: #ffffff;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .pk-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .pk-table tr:last-child td {
            border-bottom: none;
        }
        .pk-table tr:hover td {
            background: #f8fafc;
        }

        /* IKK Code & Name */
        .pk-ikk-code {
            display: inline-block;
            font-weight: 700;
            color: #0369a1;
            background: #e0f2fe;
            padding: 0.2rem 0.55rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
        }
        .pk-ikk-name {
            font-weight: 650;
            color: #0f172a;
            margin-top: 0.25rem;
        }
        .pk-ikk-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.15rem;
        }

        /* Target Display (Read-Only) */
        .pk-target-val {
            font-weight: 800;
            font-size: 1rem;
            color: #0f172a;
        }

        /* Unit & PIC Badge */
        .pk-badge-unit {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 0.2rem 0.5rem;
            border-radius: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .pk-badge-pic {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.5rem;
            border-radius: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Empty State */
        .pk-empty-card {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 1.25rem;
            padding: 3.5rem 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .pk-empty-icon {
            width: 56px;
            height: 56px;
            color: #94a3b8;
            margin-bottom: 1rem;
        }
        .pk-empty-title {
            font-size: 1.15rem;
            font-weight: 750;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }
        .pk-empty-desc {
            font-size: 0.875rem;
            color: #64748b;
            max-width: 480px;
            margin: 0;
            line-height: 1.5;
        }

        /* Modal Overlay */
        .pk-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .pk-modal-box {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: pkModalIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .pk-modal-box-large {
            max-width: 760px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        @keyframes pkModalIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .pk-modal-header {
            padding: 1.25rem 1.75rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pk-modal-title {
            font-size: 1.15rem;
            font-weight: 750;
            color: #0f172a;
            margin: 0;
        }
        .pk-modal-body {
            padding: 1.5rem 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow-y: auto;
        }
        .pk-modal-footer {
            padding: 1rem 1.75rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        .pk-form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
            display: block;
        }
        .pk-form-input {
            width: 100%;
            padding: 0.6rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.6rem;
            font-size: 0.9rem;
            color: #0f172a;
            box-sizing: border-box;
            outline: none;
        }
        .pk-form-input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
    </style>

    <div class="pk-wrap">
        {{-- Hero Header --}}
        <div class="pk-hero">
            <div>
                <h1 class="pk-hero-title">
                    <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Rekapitulasi Kinerja Tahun Anggaran {{ $tahun }}
                </h1>
                <p class="pk-hero-desc">
                    Tampilan historis dan rekapitulasi struktur sasaran kegiatan, indikator kinerja utama (IKK), target tahunan, serta alokasi anggaran per tahun.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                {{-- Year Navigation Pills (Tahun yang sudah memiliki data) --}}
                <div class="pk-year-nav">
                    @foreach($daftarTahun as $thn)
                        <button 
                            type="button" 
                            wire:click="setTahun({{ $thn }})"
                            class="pk-year-btn {{ $thn === $tahun ? 'active' : '' }}">
                            {{ $thn }}
                            @if($thn === (int) now()->year)
                                <span style="font-size: 0.65rem; padding: 1px 4px; border-radius: 4px; background: rgba(2,132,199,0.2); margin-left: 2px;">Aktif</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                {{-- Action: Siapkan Periode Baru --}}
                <button 
                    type="button" 
                    wire:click="bukaSetupModal()" 
                    class="pk-btn-action pk-btn-white"
                    title="Siapkan dan masukkan target IKK untuk periode tahun baru">
                    <svg style="width: 16px; height: 16px; color: #0284c7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    + Siapkan Periode Baru
                </button>

                {{-- Action: Cek / Pilih Tahun Tertentu --}}
                <button 
                    type="button" 
                    wire:click="bukaPilihTahunModal" 
                    class="pk-btn-action"
                    style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.3);"
                    title="Cek tahun anggaran tertentu">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Pilih Tahun
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="pk-stats-grid">
            <div class="pk-stat-card">
                <span class="pk-stat-label">Tahun Anggaran</span>
                <span class="pk-stat-value">{{ $tahun }}</span>
                <span class="pk-stat-sub">
                    @if($hasData)
                        @if($isTahunIni)
                            <span style="color: #16a34a; font-weight: 700;">● Tahun Berjalan (Aktif)</span>
                        @elseif($tahun < now()->year)
                            <span style="color: #64748b; font-weight: 600;">● Arsip Historis</span>
                        @else
                            <span style="color: #0284c7; font-weight: 700;">● Tahun Perencanaan</span>
                        @endif
                    @else
                        <span style="color: #ef4444; font-weight: 600;">● Belum Ada Data</span>
                    @endif
                </span>
            </div>

            <div class="pk-stat-card">
                <span class="pk-stat-label">Sasaran Kegiatan</span>
                <span class="pk-stat-value">{{ $ringkasan['total_sk'] }}</span>
                <span class="pk-stat-sub">Sasaran Strategis Aktif</span>
            </div>

            <div class="pk-stat-card">
                <span class="pk-stat-label">Indikator Kinerja (IKK)</span>
                <span class="pk-stat-value">{{ $ringkasan['total_ikk'] }}</span>
                <span class="pk-stat-sub">IKK dengan Target Tahun {{ $tahun }}</span>
            </div>

            <div class="pk-stat-card">
                <span class="pk-stat-label">Total Pagu Anggaran</span>
                <span class="pk-stat-value" style="font-size: 1.35rem; color: #0284c7;">
                    Rp {{ number_format($ringkasan['total_pagu'], 0, ',', '.') }}
                </span>
                <span class="pk-stat-sub">Alokasi Pagu Tahun {{ $tahun }}</span>
            </div>
        </div>

        {{-- Hierarchical Sasaran Kegiatan & IKK List (Read-Only) --}}
        <div>
            @if($hasData)
                @foreach($sasaranList as $sk)
                    <div class="pk-sk-card">
                        {{-- Header Sasaran Kegiatan --}}
                        <div class="pk-sk-header">
                            <div class="pk-sk-title">
                                <span class="pk-sk-badge">{{ $sk['kode'] }}</span>
                                <span>{{ $sk['nama'] }}</span>
                            </div>
                            <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">
                                {{ $sk['ikks']->count() }} Indikator Kinerja
                            </span>
                        </div>

                        {{-- Tabel IKK di Bawah Sasaran Kegiatan --}}
                        <div class="pk-table-wrap">
                            <table class="pk-table">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Kode IKK</th>
                                        <th>Nama Indikator Kinerja</th>
                                        <th style="width: 180px;">Target Tahun {{ $tahun }}</th>
                                        <th>Unit & PIC</th>
                                        <th style="width: 200px;">Pagu Anggaran</th>
                                        <th style="width: 130px; text-align: center;">Laporan Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sk['ikks'] as $ikk)
                                        <tr>
                                            {{-- Kode IKK --}}
                                            <td>
                                                <span class="pk-ikk-code">{{ $ikk['kode_ikk'] }}</span>
                                            </td>

                                            {{-- Nama IKK --}}
                                            <td>
                                                <div class="pk-ikk-name">{{ $ikk['nama_ikk'] }}</div>
                                                <div class="pk-ikk-sub">
                                                    Metode: <strong style="color: #475569;">{{ ucfirst($ikk['jenis_perhitungan']) }}</strong> | Satuan: <strong style="color: #475569;">{{ $ikk['satuan'] ?: '-' }}</strong>
                                                </div>
                                            </td>

                                            {{-- Target Tahunan (Murni Read-Only) --}}
                                            <td>
                                                @if($ikk['target'] !== null)
                                                    <span class="pk-target-val">
                                                        {{ number_format($ikk['target'], 2, ',', '.') }}
                                                    </span>
                                                    <span style="font-size: 0.8rem; font-weight: 500; color: #64748b; margin-left: 2px;">
                                                        {{ $ikk['satuan'] }}
                                                    </span>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.85rem;">-</span>
                                                @endif
                                            </td>

                                            {{-- Unit & PIC --}}
                                            <td>
                                                <div>
                                                    @forelse($ikk['units'] as $u)
                                                        <span class="pk-badge-unit">{{ $u }}</span>
                                                    @empty
                                                        <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                                    @endforelse
                                                </div>
                                                <div style="margin-top: 0.25rem;">
                                                    @forelse($ikk['pics'] as $p)
                                                        <span class="pk-badge-pic">PIC: {{ $p }}</span>
                                                    @empty
                                                        <span style="color: #94a3b8; font-size: 0.75rem;">Belum ada PIC</span>
                                                    @endforelse
                                                </div>
                                            </td>

                                            {{-- Pagu Anggaran --}}
                                            <td>
                                                @if($ikk['pagu_aktif'] > 0)
                                                    <div style="font-weight: 700; color: #0f172a;">
                                                        Rp {{ number_format($ikk['pagu_aktif'], 0, ',', '.') }}
                                                    </div>
                                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                                                        @if($ikk['revisi_count'] > 0)
                                                            <span style="color: #0284c7; font-weight: 600;">Revisi ke-{{ $ikk['revisi_count'] }}</span>
                                                        @else
                                                            <span>Pagu DIPA Awal</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.85rem;">Rp 0 / Belum diatur</span>
                                                @endif
                                            </td>

                                            {{-- Laporan Final --}}
                                            <td style="text-align: center;">
                                                @if($ikk['laporan_count'] > 0)
                                                    <span style="background: #dcfce7; color: #166534; font-weight: 700; font-size: 0.8rem; padding: 0.25rem 0.6rem; border-radius: 0.4rem;">
                                                        {{ $ikk['laporan_count'] }} Bulan
                                                    </span>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.8rem;">0</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Clean Empty State Ketika Tahun Belum Memiliki Data --}}
                <div class="pk-empty-card">
                    <svg class="pk-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="pk-empty-title">Belum Ada Data Kinerja untuk Tahun {{ $tahun }}</h3>
                    <p class="pk-empty-desc">
                        Tidak ditemukan target indikator kinerja (IKK), pagu anggaran, maupun laporan bulanan yang tercatat untuk Tahun Anggaran {{ $tahun }}.
                    </p>
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: center;">
                        <button type="button" wire:click="bukaSetupModal({{ $tahun }})" class="pk-btn-action pk-btn-primary">
                            + Siapkan Data Periode Tahun {{ $tahun }}
                        </button>
                        @if($tahun !== 2026)
                            <button type="button" wire:click="setTahun(2026)" class="pk-btn-action" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                Kembali ke Tahun 2026
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL SIAPKAN PERIODE BARU (SATU LAYAR LENGKAP)          --}}
    {{-- ======================================================== --}}
    @if($showSetupModal)
        <div class="pk-modal-overlay">
            <div class="pk-modal-box pk-modal-box-large">
                <div class="pk-modal-header">
                    <div>
                        <h3 class="pk-modal-title">Siapkan Sasaran & Target IKK Periode Baru</h3>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0.2rem 0 0 0;">
                            Masukkan target kinerja tahunan dalam satu langkah mudah tanpa mengubah data tahun sebelumnya.
                        </p>
                    </div>
                    <button type="button" wire:click="tutupSetupModal" style="border: none; background: transparent; cursor: pointer; color: #64748b;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="pk-modal-body">
                    {{-- Row Tahun & Tombol Prefill Cepat --}}
                    <div style="display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: #f8fafc; padding: 1rem 1.25rem; border-radius: 0.875rem; border: 1px solid #e2e8f0;">
                        <div style="max-width: 200px;">
                            <label class="pk-form-label">Tahun Periode Baru</label>
                            <input 
                                type="number" 
                                wire:model.defer="setupTahunInput" 
                                class="pk-form-input" 
                                style="font-weight: 700; font-size: 1.1rem; color: #0284c7;"
                                min="2020" 
                                max="2100"
                                required>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <button 
                                type="button" 
                                wire:click="isiDariTahunLalu" 
                                class="pk-btn-action"
                                style="background: #e0f2fe; color: #0369a1; font-size: 0.75rem; padding: 0.4rem 0.75rem;"
                                title="Gunakan nilai target dari tahun 2026 sebagai acuan">
                                📋 Salin Nilai Target 2026
                            </button>
                            <button 
                                type="button" 
                                wire:click="kosongkanTargetInput" 
                                class="pk-btn-action"
                                style="background: #f1f5f9; color: #64748b; font-size: 0.75rem; padding: 0.4rem 0.75rem;"
                                title="Reset semua target ke 0">
                                ↺ Kosongkan
                            </button>
                        </div>
                    </div>

                    {{-- List Sasaran Kegiatan & IKK Inputs --}}
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">
                            Daftar Indikator Kinerja & Target untuk Tahun Ini:
                        </div>

                        @foreach($masterSasaran as $sk)
                            <div style="border: 1px solid #e2e8f0; border-radius: 0.875rem; overflow: hidden; margin-bottom: 1rem;">
                                <div style="background: #f1f5f9; padding: 0.65rem 1rem; font-size: 0.85rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="background: #0284c7; color: #ffffff; padding: 0.15rem 0.5rem; border-radius: 0.35rem; font-size: 0.75rem;">{{ $sk->kode }}</span>
                                    <span>{{ $sk->nama }}</span>
                                </div>

                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <tbody>
                                        @foreach($sk->ikks as $ikk)
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td style="padding: 0.65rem 1rem; width: 40px; text-align: center;">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model.defer="setupIkkChecked.{{ $ikk->id }}" 
                                                        style="border-radius: 4px; width: 16px; height: 16px; cursor: pointer;">
                                                </td>
                                                <td style="padding: 0.65rem 1rem; width: 100px;">
                                                    <span class="pk-ikk-code">{{ $ikk->kode_ikk }}</span>
                                                </td>
                                                <td style="padding: 0.65rem 1rem;">
                                                    <div style="font-weight: 600; color: #0f172a;">{{ $ikk->nama_ikk }}</div>
                                                    <div style="font-size: 0.75rem; color: #64748b;">Satuan: {{ $ikk->satuan ?: '-' }}</div>
                                                </td>
                                                <td style="padding: 0.65rem 1rem; width: 200px;">
                                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                                        <input 
                                                            type="number" 
                                                            step="0.01" 
                                                            wire:model.defer="setupTargetInputs.{{ $ikk->id }}" 
                                                            class="pk-form-input" 
                                                            style="padding: 0.4rem 0.65rem; font-size: 0.85rem; font-weight: 700; text-align: right;"
                                                            placeholder="0.00">
                                                        <span style="font-size: 0.8rem; color: #64748b; min-width: 45px;">{{ $ikk->satuan }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pk-modal-footer">
                    <button type="button" wire:click="tutupSetupModal" class="pk-btn-action" style="background: #e2e8f0; color: #334155;">
                        Batal
                    </button>
                    <button type="button" wire:click="simpanSetupPeriode" class="pk-btn-action pk-btn-primary">
                        Simpan & Buka Periode Baru
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Pilih Tahun Lain --}}
    @if($showPilihTahunModal)
        <div class="pk-modal-overlay">
            <div class="pk-modal-box">
                <div class="pk-modal-header">
                    <h3 class="pk-modal-title">Pilih / Cek Tahun Anggaran</h3>
                    <button type="button" wire:click="tutupPilihTahunModal" style="border: none; background: transparent; cursor: pointer; color: #64748b;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="pk-modal-body">
                    <div>
                        <label class="pk-form-label">Tahun Anggaran</label>
                        <input 
                            type="number" 
                            wire:model.defer="tahunCariInput" 
                            class="pk-form-input" 
                            placeholder="Contoh: 2025, 2026, 2027"
                            min="2020" 
                            max="2100"
                            required>
                        <span style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block;">
                            Masukkan tahun yang ingin Anda lihat rekapitulasi datanya.
                        </span>
                    </div>
                </div>
                <div class="pk-modal-footer">
                    <button type="button" wire:click="tutupPilihTahunModal" class="pk-btn-action" style="background: #e2e8f0; color: #334155;">
                        Batal
                    </button>
                    <button type="button" wire:click="cariTahun" class="pk-btn-action pk-btn-primary">
                        Buka Tahun
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
