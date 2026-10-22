<div style="font-family: inherit; color: #1e293b; display: flex; flex-direction: column; gap: 20px;">

    {{-- 1. Kartu Ringkasan 4 Kolom --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Target Tahunan</div>
            <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                {{ number_format($dataDetail['target'], 0, ',', '.') }}
                <span style="font-size: 12px; font-weight: 500; color: #64748b;">{{ $dataDetail['satuan'] }}</span>
            </div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Target DIPA TA {{ $tahun }}</div>
        </div>

        <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 14px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <div style="font-size: 11px; font-weight: 600; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">Realisasi Kinerja</div>
            <div style="font-size: 20px; font-weight: 800; color: #312e81; margin-top: 4px;">
                {{ number_format($dataDetail['realisasi_kinerja'], 2, ',', '.') }}
                <span style="font-size: 12px; font-weight: 500; color: #6366f1;">{{ $dataDetail['satuan'] }}</span>
            </div>
            <div style="font-size: 12px; font-weight: 700; color: {{ $dataDetail['capaian_kinerja'] >= 100 ? '#15803d' : ($dataDetail['capaian_kinerja'] >= 70 ? '#2563eb' : '#dc2626') }}; margin-top: 4px;">
                Capaian: {{ number_format($dataDetail['capaian_kinerja'], 2, ',', '.') }}%
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Pagu Anggaran</div>
            <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                Rp {{ number_format($dataDetail['pagu'], 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Pagu DIPA & Revisi TA {{ $tahun }}</div>
        </div>

        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <div style="font-size: 11px; font-weight: 600; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px;">Realisasi Anggaran</div>
            <div style="font-size: 18px; font-weight: 800; color: #064e3b; margin-top: 4px;">
                Rp {{ number_format($dataDetail['realisasi_anggaran'], 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; font-weight: 700; color: #059669; margin-top: 4px;">
                Penyerapan: {{ number_format($dataDetail['persen_anggaran'], 2, ',', '.') }}%
            </div>
        </div>
    </div>

    {{-- 2. Dua Grafik Tren Visualisasi Berdampingan --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 16px;">

        {{-- Grafik 1: Tren Capaian Kinerja (Garis Tegas) --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Tren Capaian Kinerja (%)</div>
                    <div style="font-size: 11px; color: #64748b;">Perkembangan realisasi kumulatif terhadap target 100%</div>
                </div>
                <div style="display: flex; gap: 10px; font-size: 11px; font-weight: 600;">
                    <span style="color: #4f46e5; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #4f46e5;"></span> Realisasi
                    </span>
                    <span style="color: #f43f5e; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 12px; height: 2px; border-top: 2px dashed #f43f5e;"></span> Target 100%
                    </span>
                </div>
            </div>

            {{-- SVG Chart Kinerja --}}
            <svg viewBox="0 0 530 205" style="width: 100%; height: 200px; overflow: visible;">
                <!-- Grid Lines Horizontal -->
                <line x1="45" y1="30" x2="510" y2="30" stroke="#fecdd3" stroke-width="1.5" stroke-dasharray="5,4" />
                <text x="38" y="34" font-size="10" fill="#f43f5e" text-anchor="end" font-weight="600">100%</text>

                <line x1="45" y1="65" x2="510" y2="65" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="69" font-size="10" fill="#94a3b8" text-anchor="end">75%</text>

                <line x1="45" y1="100" x2="510" y2="100" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="104" font-size="10" fill="#94a3b8" text-anchor="end">50%</text>

                <line x1="45" y1="135" x2="510" y2="135" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="139" font-size="10" fill="#94a3b8" text-anchor="end">25%</text>

                <line x1="45" y1="170" x2="510" y2="170" stroke="#e2e8f0" stroke-width="1.5" />
                <text x="38" y="174" font-size="10" fill="#94a3b8" text-anchor="end">0%</text>

                <!-- Polyline Kinerja (Garis Tegas) -->
                @if(!empty($dataDetail['kinerja_polyline']))
                    <polyline points="{{ $dataDetail['kinerja_polyline'] }}" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                @endif

                <!-- Titik & Label Data Kinerja -->
                @foreach($dataDetail['kinerja_points'] as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />
                    <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" font-size="10" font-weight="700" fill="#4338ca" text-anchor="middle">
                        {{ $pt['val'] }}%
                    </text>
                @endforeach

                <!-- Sumbu X: Nama Bulan -->
                @foreach($dataDetail['short_months'] as $idx => $m)
                    @php $posX = $dataDetail['plot_x_start'] + ($idx * ($dataDetail['plot_x_width'] / 11)); @endphp
                    <text x="{{ $posX }}" y="190" font-size="11" fill="{{ ($idx + 1) <= $dataDetail['max_bulan'] ? '#334155' : '#94a3b8' }}" text-anchor="middle" font-weight="{{ ($idx + 1) <= $dataDetail['max_bulan'] ? '600' : '400' }}">
                        {{ $m }}
                    </text>
                @endforeach
            </svg>
        </div>

        {{-- Grafik 2: Kurva S Penyerapan Anggaran (Bar + Line Menghubungkan Ujung Bar) --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Kurva S Penyerapan Anggaran (%)</div>
                    <div style="font-size: 11px; color: #64748b;">Realisasi kumulatif anggaran terhadap target 100%</div>
                </div>
                <div style="display: flex; gap: 10px; font-size: 11px; font-weight: 600;">
                    <span style="color: #4f46e5; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: rgba(99, 102, 241, 0.45); border: 1px solid #6366f1;"></span> Batang
                    </span>
                    <span style="color: #4f46e5; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #4f46e5;"></span> Kurva S
                    </span>
                    <span style="color: #f43f5e; display: flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 12px; height: 2px; border-top: 2px dashed #f43f5e;"></span> 100%
                    </span>
                </div>
            </div>

            {{-- SVG Chart Anggaran --}}
            <svg viewBox="0 0 530 205" style="width: 100%; height: 200px; overflow: visible;">
                <!-- Grid Lines Horizontal -->
                <line x1="45" y1="30" x2="510" y2="30" stroke="#fecdd3" stroke-width="1.5" stroke-dasharray="5,4" />
                <text x="38" y="34" font-size="10" fill="#f43f5e" text-anchor="end" font-weight="600">100%</text>

                <line x1="45" y1="65" x2="510" y2="65" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="69" font-size="10" fill="#94a3b8" text-anchor="end">75%</text>

                <line x1="45" y1="100" x2="510" y2="100" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="104" font-size="10" fill="#94a3b8" text-anchor="end">50%</text>

                <line x1="45" y1="135" x2="510" y2="135" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3,3" />
                <text x="38" y="139" font-size="10" fill="#94a3b8" text-anchor="end">25%</text>

                <line x1="45" y1="170" x2="510" y2="170" stroke="#e2e8f0" stroke-width="1.5" />
                <text x="38" y="174" font-size="10" fill="#94a3b8" text-anchor="end">0%</text>

                <!-- Batang Anggaran Kumulatif -->
                @foreach($dataDetail['anggaran_points'] as $pt)
                    <rect x="{{ $pt['x'] - 11 }}" y="{{ $pt['y'] }}" width="22" height="{{ $pt['bar_height'] }}" rx="4" fill="rgba(99, 102, 241, 0.35)" stroke="#6366f1" stroke-width="1.5" />
                @endforeach

                <!-- Garis Kurva S Menghubungkan Ujung Batang -->
                @if(!empty($dataDetail['anggaran_polyline']))
                    <polyline points="{{ $dataDetail['anggaran_polyline'] }}" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                @endif

                <!-- Titik di Ujung Batang & Label Nilai -->
                @foreach($dataDetail['anggaran_points'] as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />
                    <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" font-size="10" font-weight="700" fill="#312e81" text-anchor="middle">
                        {{ $pt['val'] }}%
                    </text>
                @endforeach

                <!-- Sumbu X: Nama Bulan -->
                @foreach($dataDetail['short_months'] as $idx => $m)
                    @php $posX = $dataDetail['plot_x_start'] + ($idx * ($dataDetail['plot_x_width'] / 11)); @endphp
                    <text x="{{ $posX }}" y="190" font-size="11" fill="{{ ($idx + 1) <= $dataDetail['max_bulan'] ? '#334155' : '#94a3b8' }}" text-anchor="middle" font-weight="{{ ($idx + 1) <= $dataDetail['max_bulan'] ? '600' : '400' }}">
                        {{ $m }}
                    </text>
                @endforeach
            </svg>
        </div>
    </div>

    {{-- 3. Tabel Rincian 12 Bulan yang Rapi & Terstruktur --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="padding: 12px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.5px;">
                Tabel Rincian Kinerja & Anggaran Bulanan (TA {{ $tahun }})
            </span>
            <span style="font-size: 11px; font-weight: 500; color: #64748b; background: #ffffff; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 20px;">
                Posisi Data Final: s.d. Bulan {{ $dataDetail['max_bulan'] }}
            </span>
        </div>

        <div style="overflow-x: auto; max-height: 280px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px;">
                <thead style="background: #f1f5f9; color: #475569; position: sticky; top: 0; z-index: 10; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                    <tr>
                        <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">Bulan</th>
                        <th style="padding: 10px 14px; text-align: center; border-bottom: 1px solid #e2e8f0;">Output Realisasi</th>
                        <th style="padding: 10px 14px; text-align: center; border-bottom: 1px solid #e2e8f0;">Capaian (%)</th>
                        <th style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #e2e8f0;">Realisasi Anggaran</th>
                        <th style="padding: 10px 14px; text-align: center; border-bottom: 1px solid #e2e8f0;">Penyerapan (%)</th>
                        <th style="padding: 10px 14px; text-align: center; border-bottom: 1px solid #e2e8f0;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataDetail['tabel_bulanan'] as $row)
                        @php $isAktif = $row['bulan'] <= $dataDetail['max_bulan']; @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $isAktif ? 'background: #ffffff;' : 'background: #fafafa; opacity: 0.55;' }}">
                            <td style="padding: 10px 14px; font-weight: 600; color: #0f172a;">
                                {{ $row['nama_bulan'] }}
                            </td>
                            <td style="padding: 10px 14px; text-align: center; font-weight: 500;">
                                @if($row['realisasi_kinerja'] !== null)
                                    <span style="font-weight: 600; color: #1e293b;">{{ number_format($row['realisasi_kinerja'], 2, ',', '.') }}</span>
                                    <span style="font-size: 10px; color: #64748b;">{{ $dataDetail['satuan'] }}</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="padding: 10px 14px; text-align: center;">
                                @if($row['capaian_kinerja'] !== null)
                                    @php
                                        $cVal = $row['capaian_kinerja'];
                                        $bgC = $cVal >= 100 ? '#dcfce7' : ($cVal >= 70 ? '#dbeafe' : '#fee2e2');
                                        $txC = $cVal >= 100 ? '#15803d' : ($cVal >= 70 ? '#1d4ed8' : '#b91c1c');
                                    @endphp
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: {{ $bgC }}; color: {{ $txC }};">
                                        {{ number_format($cVal, 1, ',', '.') }}%
                                    </span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="padding: 10px 14px; text-align: right; font-weight: 500; color: #0f172a;">
                                @if($row['realisasi_anggaran'] !== null)
                                    Rp {{ number_format($row['realisasi_anggaran'], 0, ',', '.') }}
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="padding: 10px 14px; text-align: center; font-weight: 700; color: #059669;">
                                @if($row['persen_anggaran'] !== null)
                                    {{ number_format($row['persen_anggaran'], 1, ',', '.') }}%
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="padding: 10px 14px; text-align: center;">
                                @if($row['status_laporan'] === 'final')
                                    <span style="display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #dcfce7; color: #15803d;">
                                        FINAL
                                    </span>
                                @elseif($row['status_laporan'] === 'belum_ada')
                                    <span style="font-size: 10px; color: #94a3b8;">Belum Ada</span>
                                @else
                                    <span style="display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #fef3c7; color: #b45309;">
                                        {{ strtoupper($row['status_laporan']) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
