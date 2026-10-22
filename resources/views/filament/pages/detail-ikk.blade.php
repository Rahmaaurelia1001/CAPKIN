<x-filament-panels::page>
    {{-- Memastikan Chart.js termuat dengan andal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Bar Navigasi Atas & Pemilih IKK Cepat --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; background: #ffffff; padding: 14px 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <a href="{{ \App\Filament\Pages\Dashboard::getUrl() }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #4f46e5; text-decoration: none; padding: 7px 14px; border-radius: 8px; background: #eef2ff; transition: all 0.2s;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard Utama
            </a>

            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b;">Pilih Indikator:</span>
                <select wire:model.live="record" style="font-size: 13px; font-weight: 600; color: #0f172a; padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer; outline: none;">
                    @foreach(\App\Models\Ikk::where('is_active', true)->orderBy('kode_ikk')->get() as $item)
                        <option value="{{ $item->id }}">{{ $item->kode_ikk }} — {{ $item->nama_ikk }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 1. Kartu Ringkasan 4 Metrik Eksekutif --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Target Tahunan</div>
                <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                    {{ number_format($dataDetail['target'], 2, ',', '.') }}
                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">{{ $dataDetail['satuan'] }}</span>
                </div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Target DIPA TA {{ $tahun }}</div>
            </div>

            <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="font-size: 11px; font-weight: 700; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">Realisasi Kinerja</div>
                <div style="font-size: 24px; font-weight: 800; color: #312e81; margin-top: 4px;">
                    {{ number_format($dataDetail['realisasi_kinerja'], 2, ',', '.') }}
                    <span style="font-size: 13px; font-weight: 600; color: #6366f1;">{{ $dataDetail['satuan'] }}</span>
                </div>
                <div style="font-size: 12px; font-weight: 700; color: {{ $dataDetail['capaian_kinerja'] >= 100 ? '#15803d' : ($dataDetail['capaian_kinerja'] >= 70 ? '#2563eb' : '#dc2626') }}; margin-top: 4px;">
                    Capaian: {{ number_format($dataDetail['capaian_kinerja'], 2, ',', '.') }}%
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Pagu Anggaran</div>
                <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                    Rp {{ number_format($dataDetail['pagu'], 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Alokasi Anggaran TA {{ $tahun }}</div>
            </div>

            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="font-size: 11px; font-weight: 700; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px;">Realisasi Anggaran</div>
                <div style="font-size: 20px; font-weight: 800; color: #064e3b; margin-top: 4px;">
                    Rp {{ number_format($dataDetail['realisasi_anggaran'], 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; font-weight: 700; color: #059669; margin-top: 4px;">
                    Penyerapan: {{ number_format($dataDetail['persen_anggaran'], 2, ',', '.') }}%
                </div>
            </div>
        </div>

        {{-- 2. Dua Grafik Chart.js Interaktif Lengkap dengan TOOLTIP --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 18px;">

            {{-- Grafik 1: Tren Capaian Kinerja (Garis Tegas) --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: #0f172a;">Tren Capaian Kinerja Bulanan (%)</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Garis lurus tegas capaian kumulatif vs Target 100%</div>
                    </div>
                    <span style="font-size: 11px; font-weight: 700; color: #4f46e5; background: #eef2ff; border: 1px solid #c7d2fe; padding: 3px 10px; border-radius: 20px;">
                        Interactive Tooltip Aktif
                    </span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="chartDetailKinerja"></canvas>
                </div>
            </div>

            {{-- Grafik 2: Penyerapan Anggaran (Kurva S + Barchart Kumulatif) --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: #0f172a;">Kurva S Penyerapan Anggaran (%)</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Batang realisasi kumulatif dengan garis Kurva S di ujung atasnya</div>
                    </div>
                    <span style="font-size: 11px; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 20px;">
                        Kurva S & Bar Aktif
                    </span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="chartDetailAnggaran"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Tabel Rincian 12 Bulan Lengkap --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
            <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 14px; font-weight: 800; color: #0f172a;">
                        Tabel Rincian Bulanan: {{ $ikk->kode_ikk }}
                    </h3>
                    <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Detail output realisasi kinerja, penyerapan anggaran, dan status pemeriksaan</p>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #475569; background: #ffffff; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 20px;">
                    Posisi Laporan Final: s.d. Bulan {{ $dataDetail['max_bulan'] }}
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px;">
                    <thead style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px 16px;">Bulan</th>
                            <th style="padding: 12px 16px; text-align: center;">Output Realisasi</th>
                            <th style="padding: 12px 16px; text-align: center;">Capaian Kinerja (%)</th>
                            <th style="padding: 12px 16px; text-align: right;">Realisasi Anggaran Kumulatif</th>
                            <th style="padding: 12px 16px; text-align: right;">Pengeluaran Bulanan</th>
                            <th style="padding: 12px 16px; text-align: center;">Penyerapan (%)</th>
                            <th style="padding: 12px 16px; text-align: center;">Status</th>
                            <th style="padding: 12px 16px;">Catatan Narasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataDetail['tabel_bulanan'] as $row)
                            @php $isAktif = $row['bulan'] <= $dataDetail['max_bulan']; @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9; {{ $isAktif ? 'background: #ffffff;' : 'background: #fafafa; opacity: 0.55;' }}">
                                <td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">
                                    {{ $row['nama_bulan'] }}
                                </td>
                                <td style="padding: 12px 16px; text-align: center; font-weight: 600;">
                                    @if($row['realisasi_kinerja'] !== null)
                                        {{ number_format($row['realisasi_kinerja'], 2, ',', '.') }}
                                        <span style="font-size: 11px; color: #64748b; font-weight: 400;">{{ $dataDetail['satuan'] }}</span>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    @if($row['capaian_kinerja'] !== null)
                                        @php
                                            $cVal = $row['capaian_kinerja'];
                                            $bgC = $cVal >= 100 ? '#dcfce7' : ($cVal >= 70 ? '#dbeafe' : '#fee2e2');
                                            $txC = $cVal >= 100 ? '#15803d' : ($cVal >= 70 ? '#1d4ed8' : '#b91c1c');
                                        @endphp
                                        <span style="display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background: {{ $bgC }}; color: {{ $txC }};">
                                            {{ number_format($cVal, 2, ',', '.') }}%
                                        </span>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 700; color: #0f172a;">
                                    @if($row['realisasi_anggaran'] !== null)
                                        Rp {{ number_format($row['realisasi_anggaran'], 0, ',', '.') }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 500; color: #475569;">
                                    @if($row['anggaran_bulanan'] !== null && $row['anggaran_bulanan'] > 0)
                                        Rp {{ number_format($row['anggaran_bulanan'], 0, ',', '.') }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: center; font-weight: 800; color: #059669;">
                                    @if($row['persen_anggaran'] !== null)
                                        {{ number_format($row['persen_anggaran'], 2, ',', '.') }}%
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    @if($row['status_laporan'] === 'final')
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; background: #dcfce7; color: #15803d; letter-spacing: 0.5px;">
                                            FINAL
                                        </span>
                                    @elseif($row['status_laporan'] === 'belum_ada')
                                        <span style="font-size: 11px; color: #94a3b8;">Belum Ada</span>
                                    @else
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; background: #fef3c7; color: #b45309; letter-spacing: 0.5px;">
                                            {{ strtoupper($row['status_laporan']) }}
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; font-size: 11px; color: #475569; max-width: 250px;">
                                    {{ $row['narasi'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

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
                            borderColor: '#4f46e5',
                            backgroundColor: '#4f46e5',
                            borderWidth: 3,
                            tension: 0, // Tegas & lurus
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
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
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
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
                        y: { beginAtZero: true, max: 120, ticks: { callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 2. Chart Anggaran
        var ctxA = document.getElementById('chartDetailAnggaran');
        if (ctxA) {
            if (window.myChartAnggaran) window.myChartAnggaran.destroy();
            window.myChartAnggaran = new Chart(ctxA, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Kurva S (%)',
                            data: anggaranData,
                            type: 'line',
                            borderColor: '#4f46e5',
                            backgroundColor: '#4f46e5',
                            borderWidth: 3,
                            tension: 0.25, // Kurva S
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointRadius: 6,
                            pointHoverRadius: 9,
                            pointBorderWidth: 2.5,
                            order: 1,
                        },
                        {
                            label: 'Realisasi Kumulatif (%)',
                            data: anggaranData,
                            type: 'bar',
                            backgroundColor: 'rgba(99, 102, 241, 0.35)',
                            hoverBackgroundColor: 'rgba(99, 102, 241, 0.65)',
                            borderColor: '#6366f1',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.65,
                            order: 2,
                        },
                        {
                            label: 'Target 100%',
                            data: targetData,
                            type: 'line',
                            borderColor: '#f43f5e',
                            borderWidth: 1.5,
                            borderDash: [6, 6],
                            pointRadius: 0,
                            pointHoverRadius: 0,
                            order: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
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
                        y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }

    // Panggil ulang setelah Livewire render
    if (window.Livewire) {
        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                setTimeout(initDetailCharts, 100);
            });
        });
    }
    </script>
</x-filament-panels::page>
