<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DashboardAnggaranPerIKK extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Pagu vs Realisasi Anggaran per IKK';

    protected ?string $description = 'Perbandingan pagu berlaku dengan realisasi anggaran kumulatif. Batang merah menandakan realisasi melebihi pagu.';

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

        $labels        = [];
        $namaIkkList   = [];
        $dataPagu      = [];
        $dataRealisasi = [];
        $warnaRealisi  = [];

        foreach ($items as $item) {
            $labels[]        = $item['kode_ikk'];
            $namaIkkList[]   = $item['nama_ikk'];
            $dataPagu[]      = $item['pagu'];
            $dataRealisasi[] = $item['realisasi'];
            $warnaRealisi[]  = $item['lebihi_pagu']
                ? 'rgba(239, 68, 68, 0.9)'  // red-500
                : 'rgba(79, 70, 229, 0.85)'; // indigo-600
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pagu',
                    'data'            => $dataPagu,
                    'namaIkk'         => $namaIkkList,
                    'backgroundColor' => 'rgba(203, 213, 225, 0.7)',
                    'borderColor'     => '#94a3b8',
                    'borderWidth'     => 1,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                ],
                [
                    'label'           => 'Realisasi',
                    'data'            => $dataRealisasi,
                    'namaIkk'         => $namaIkkList,
                    'backgroundColor' => $warnaRealisi,
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
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
                        padding: 16,
                        font: { size: 11, weight: '600', family: 'Plus Jakarta Sans' },
                        color: '#475569'
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#334155',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: { top: 12, bottom: 12, left: 14, right: 14 },
                    cornerRadius: 10,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                    bodyFont: { size: 12, weight: '500', family: 'Plus Jakarta Sans' },
                    boxPadding: 4,
                    callbacks: {
                        title: function(items) {
                            var item = items[0];
                            var kode = item.label;
                            var namaList = item.dataset.namaIkk || [];
                            var nama = namaList[item.dataIndex] || '';
                            if (nama.length > 45) {
                                nama = nama.substring(0, 42) + '...';
                            }
                            return kode + (nama ? ' (' + nama + ')' : '');
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
                                if (pagu > 0) {
                                    var pct = ((real / pagu) * 100).toFixed(1);
                                    var note = real > pagu ? ' (Melebihi Pagu)' : '';
                                    return ['Penyerapan : ' + pct + '%' + note];
                                } else if (real > 0) {
                                    return ['Penyerapan : >100% (Pagu Rp 0)'];
                                }
                            }
                            return [];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, weight: '600', family: 'Plus Jakarta Sans' },
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        borderDash: [4, 4]
                    },
                    ticks: {
                        font: { size: 11, family: 'Plus Jakarta Sans' },
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
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 800,
                easing: 'easeInOutQuart'
            }
        }
        JS);
    }

    protected function getMaxHeight(): ?string
    {
        return '380px';
    }
}
