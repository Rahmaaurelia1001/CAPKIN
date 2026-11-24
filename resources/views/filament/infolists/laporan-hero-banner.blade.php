@php
    $record = $getRecord();
    $ikk = $record->ikk;
    $status = $record->status;
    $isUmper = (int) $record->unit_id === 6;

    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ][$record->bulan] ?? "Bulan {$record->bulan}";

    $statusColor = match ($status) {
        'final' => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0', 'label' => 'Final (Disetujui Kapus)', 'icon' => '✓'],
        'perlu_revisi' => ['bg' => '#fef2f2', 'text' => '#be123c', 'border' => '#fecaca', 'label' => 'Perlu Revisi PIC', 'icon' => '✕'],
        'draft' => ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0', 'label' => 'Draft (Belum Diajukan)', 'icon' => '📝'],
        'diajukan' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => $isUmper ? 'Menunggu Katim UMPer' : 'Menunggu Kabid/Kabag', 'icon' => '⏳'],
        'disetujui_kabid' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'Menunggu Katim UMPer', 'icon' => '⏳'],
        'disetujui_katim_umper' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'Menunggu Kabag Umum', 'icon' => '⏳'],
        'disetujui_kabag_umum' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'Menunggu Kapus', 'icon' => '⏳'],
        default => ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#cbd5e1', 'label' => ucwords(str_replace('_', ' ', $status)), 'icon' => '•'],
    };

    $realisasiStr = '-';
    if ($ikk) {
        $kodeIkk = strtoupper(str_replace([' ', '_', '-'], '', (string) $ikk->kode_ikk));
        if ($kodeIkk === 'IKK20') {
            $totalPeserta = (int) $record->rincianPelatihans()->sum('jumlah_peserta');
            $realisasiStr = number_format($totalPeserta, 0, ',', '.') . ' Orang';
        } elseif ($kodeIkk === 'IKK21') {
            $totalNspk = (int) $record->rincianNSPKs()->count();
            $realisasiStr = number_format($totalNspk, 0, ',', '.') . ' Dokumen';
        } elseif ($ikk->jenis_perhitungan === 'gabungan') {
            $angka = $record->angkaKinerjas()->with('ikkKomponen')->get();
            $capaian = 0;
            foreach ($angka as $item) {
                if ($item->penyebut > 0 && $item->ikkKomponen) {
                    $capaian += ((float) $item->pembilang / (float) $item->penyebut) * 100 * ((float) $item->ikkKomponen->bobot / 100);
                }
            }
            $realisasiStr = number_format($capaian, 2, ',', '.') . '%';
        } else {
            $satuan = $ikk->satuan ?: '';
            $realisasiStr = number_format((float) $record->realisasi, 2, ',', '.') . ($satuan ? " {$satuan}" : '');
        }
    }

    $anggaran = \App\Models\AnggaranBulanan::where('ikk_id', $record->ikk_id)
        ->where('bulan', $record->bulan)
        ->where('tahun', $record->tahun)
        ->first();
    $nominalAnggaran = $anggaran ? (float) $anggaran->realisasi_anggaran : (float) ($record->realisasi_anggaran ?? 0);
    $anggaranStr = $nominalAnggaran > 0 ? 'Rp ' . number_format($nominalAnggaran, 0, ',', '.') : 'Belum diisi';
@endphp

<div class="fi-in-hero-banner" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 60%, #0284c7 100%); border-radius: 18px; padding: 22px 26px; color: #ffffff; box-shadow: 0 10px 28px -4px rgba(2, 132, 199, 0.35); position: relative; overflow: hidden;">
    <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%); border-radius: 999px; pointer-events: none;"></div>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; position: relative; z-index: 1;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="background: rgba(255,255,255,0.22); backdrop-filter: blur(4px); font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 8px; letter-spacing: 0.05em;">
                    {{ $ikk?->kode_ikk ?? 'IKK' }}
                </span>
                <span style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.01em;">
                    {{ $ikk?->nama_ikk ?? 'Indikator Kinerja' }}
                </span>
            </div>
            <div style="font-size: 0.82rem; color: rgba(255,255,255,0.85); margin-top: 6px; font-weight: 500;">
                Unit Kerja: <strong style="color: #ffffff;">{{ $record->unit?->nama ?? '-' }}</strong> &bull;
                PIC: <strong style="color: #ffffff;">{{ $record->pic?->name ?? '-' }}</strong>
            </div>
        </div>

        <span style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; border: 1px solid {{ $statusColor['border'] }}; font-size: 12px; font-weight: 800; padding: 6px 14px; border-radius: 9999px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); white-space: nowrap;">
            {{ $statusColor['icon'] }} {{ $statusColor['label'] }}
        </span>
    </div>

    {{-- Grid 4 Metrik Ringkas --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-top: 20px; position: relative; z-index: 1;">
        <div style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.85);">Periode Pelaporan</span>
            <div style="font-size: 1.05rem; font-weight: 900; margin-top: 4px;">{{ $namaBulan }} {{ $record->tahun }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.85);">Realisasi Kinerja</span>
            <div style="font-size: 1.05rem; font-weight: 900; margin-top: 4px;">{{ $realisasiStr }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.85);">Realisasi Anggaran</span>
            <div style="font-size: 1.05rem; font-weight: 900; margin-top: 4px;">{{ $anggaranStr }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.14); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.85);">Sasaran Strategis</span>
            <div style="font-size: 0.95rem; font-weight: 800; margin-top: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $ikk?->sasaranKegiatan?->nama }}">
                {{ $ikk?->sasaranKegiatan?->nama ?? '-' }}
            </div>
        </div>
    </div>
</div>

