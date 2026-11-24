<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class DashboardAnggaranPerIKK extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): string|Htmlable|null
    {
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan = (int) ($this->filters['bulan'] ?? now()->month);

        $items = app(AnggaranCalculator::class)->ambilPerbandinganPerIkk($tahun, $bulan);

        $itemsCol  = collect($items);
        $totalPagu = (float) $itemsCol->sum('pagu');
        $totalReal = (float) $itemsCol->sum('realisasi');
        $persen    = $totalPagu > 0 ? round(($totalReal / $totalPagu) * 100, 1) : 0;

        $paguFormatted = 'Rp ' . number_format($totalPagu / 1000000000, 2, ',', '.') . ' M';
        $realFormatted = 'Rp ' . number_format($totalReal / 1000000000, 2, ',', '.') . ' M';

        return new HtmlString('
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-weight: 800; font-size: 1.05rem; letter-spacing: -0.01em;">Pagu vs Realisasi Anggaran per IKK</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 0.74rem; font-weight: 800; background: rgba(52, 152, 219, 0.12); color: #0284c7; border: 1px solid rgba(52, 152, 219, 0.4); padding: 4px 12px; border-radius: 9999px;">
                        Pagu Total: ' . $paguFormatted . '
                    </span>
                    <span style="font-size: 0.74rem; font-weight: 800; background: rgba(155, 89, 182, 0.12); color: #8e44ad; border: 1px solid rgba(155, 89, 182, 0.4); padding: 4px 12px; border-radius: 9999px;">
                        Realisasi Total: ' . $realFormatted . ' (' . $persen . '%)
                    </span>
                </div>
            </div>
        ');
    }

    protected ?string $description = 'Perbandingan visual pagu DIPA terhadap realisasi anggaran kumulatif. Batang merah menandakan realisasi melebihi alokasi pagu.';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, [
            'kapus',
            'katim_umper',
            'kabag_umum',
        ], true);
    }

    protected function getData(): array
    {
        $tahun  = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan  = (int) ($this->filters['bulan'] ?? now()->month);

        $items = app(AnggaranCalculator::class)
            ->ambilPerbandinganPerIkk($tahun, $bulan);

        $labels          = [];
        $namaIkkList     = [];
        $dataPagu        = [];
        $dataRealisasi   = [];
        $warnaRealisasi  = [];
        $borderRealisasi = [];

        foreach ($items as $item) {
            $labels[]        = $item['kode_ikk'];
            $namaIkkList[]   = $item['nama_ikk'];
            $dataPagu[]      = $item['pagu'];
            $dataRealisasi[] = $item['realisasi'];

            if ($item['lebihi_pagu']) {
                $warnaRealisasi[]  = 'rgba(244, 63, 94, 0.88)';  // Vibrant Rose Red (melebihi pagu)
                $borderRealisasi[] = '#e11d48';
            } else {
                $warnaRealisasi[]  = 'rgba(155, 89, 182, 0.9)';  // Vibrant Amethyst Purple (serasi dengan donut chart!)
                $borderRealisasi[] = '#8e44ad';
            }
        }

        return [
            'datasets' => [
                [
                    'label'              => 'Pagu DIPA',
                    'data'               => $dataPagu,
                    'namaIkk'            => $namaIkkList,
                    'backgroundColor'    => 'rgba(52, 152, 219, 0.22)', // Soft Sky Blue container (serasi dengan donut chart!)
                    'borderColor'        => '#0284c7',
                    'borderWidth'        => 1.5,
                    'borderRadius'       => 6,
                    'borderSkipped'      => false,
                    'barPercentage'      => 0.78,
                    'categoryPercentage' => 0.7,
                ],
                [
                    'label'              => 'Realisasi Terserap',
                    'data'               => $dataRealisasi,
                    'namaIkk'            => $namaIkkList,
                    'backgroundColor'    => $warnaRealisasi,
                    'borderColor'        => $borderRealisasi,
                    'borderWidth'        => 1.5,
                    'borderRadius'       => 6,
                    'borderSkipped'      => false,
                    'barPercentage'      => 0.78,
                    'categoryPercentage' => 0.7,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            indexAxis: 'y',
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 18,
                        font: { size: 12, weight: '700', family: 'Plus Jakarta Sans' },
                        color: '#475569'
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(51, 65, 85, 0.8)',
                    borderWidth: 1,
                    padding: 14,
                    cornerRadius: 10,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    titleFont: { size: 13, weight: '800', family: 'Plus Jakarta Sans' },
                    bodyFont: { size: 12, weight: '600', family: 'Plus Jakarta Sans' },
                    boxPadding: 6,
                    callbacks: {
                        title: function(items) {
                            var item = items[0];
                            var kode = item.label;
                            var namaList = item.dataset.namaIkk || [];
                            var nama = namaList[item.dataIndex] || '';
                            return kode + (nama ? ' — ' + nama : '');
                        },
                        label: function(ctx) {
                            var val = ctx.raw || 0;
                            var formatted = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                maximumFractionDigits: 0
                            }).format(val);
                            return ' ' + ctx.dataset.label + ' : ' + formatted;
                        },
                        afterBody: function(items) {
                            if (items.length >= 2) {
                                var pagu = items[0].raw || 0;
                                var real = items[1].raw || 0;
                                var sisa = pagu - real;
                                var sisaFormatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(Math.abs(sisa));

                                if (pagu > 0) {
                                    var pct = ((real / pagu) * 100).toFixed(1);
                                    var note = real > pagu ? ' ⚠️ Melebihi Pagu (' + sisaFormatted + ')' : ' • Sisa: ' + sisaFormatted;
                                    return ['', '📊 Penyerapan : ' + pct + '%' + note];
                                } else if (real > 0) {
                                    return ['', '📊 Penyerapan : >100% (Pagu Rp 0)'];
                                }
                            }
                            return [];
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(226, 232, 240, 0.6)',
                        borderDash: [4, 4]
                    },
                    ticks: {
                        font: { size: 11, weight: '600', family: 'Plus Jakarta Sans' },
                        color: '#64748b',
                        callback: function(v) {
                            if (v >= 1000000000) {
                                return 'Rp ' + (v / 1000000000).toFixed(1) + ' M';
                            } else if (v >= 1000000) {
                                return 'Rp ' + (v / 1000000).toFixed(0) + ' Jt';
                            }
                            return 'Rp ' + v;
                        }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, weight: '800', family: 'Plus Jakarta Sans' },
                        color: '#334155'
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 900,
                easing: 'easeInOutQuart'
            }
        }
        JS);
    }

    protected function getMaxHeight(): ?string
    {
        return '450px';
    }
}
