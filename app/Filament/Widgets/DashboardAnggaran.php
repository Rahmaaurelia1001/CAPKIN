<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class DashboardAnggaran extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): string|Htmlable|null
    {
        return new HtmlString('
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span style="font-weight: 700;">Penyerapan Anggaran per Bulan</span>
                <span style="font-size: 0.78rem; font-weight: 700; color: #dc2626;">Target 100%</span>
            </div>
        ');
    }

    protected ?string $description = 'Realisasi penyerapan anggaran kumulatif terhadap target 100%.';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md'      => 1,
        'xl'      => 1,
    ];

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
        $tahun       = (int) ($this->filters['tahun'] ?? now()->year);
        $bulanFilter = (int) ($this->filters['bulan'] ?? now()->month);

        $calc = app(AnggaranCalculator::class);
        $tren = $calc->ambilTrenBulanan($tahun);

        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $labels         = [];
        $dataPenyerapan = [];
        $dataTarget     = [];

        for ($b = 1; $b <= 12; $b++) {
            $labels[]     = $shortMonths[$b - 1];
            $dataTarget[] = 100;

            if ($b <= $bulanFilter) {
                $persen = $tren['persentase_penyerapan'][$b - 1] ?? 0;
                $dataPenyerapan[] = round($persen, 1);
            } else {
                $dataPenyerapan[] = null;
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Penyerapan (%)',
                    'data'            => $dataPenyerapan,
                    'type'            => 'bar',
                    'backgroundColor' => '#8b5cf6',
                    'hoverBackgroundColor' => '#7c3aed',
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                    'barPercentage'   => 0.65,
                ],
                [
                    'label'            => 'Target 100%',
                    'data'             => $dataTarget,
                    'type'             => 'line',
                    'fill'             => false,
                    'borderColor'      => '#ef4444',
                    'borderWidth'      => 1.5,
                    'borderDash'       => [5, 5],
                    'pointRadius'      => 0,
                    'pointHoverRadius' => 0,
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
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 16,
                        font: { size: 11, weight: '600', family: 'Plus Jakarta Sans' }
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#8b5cf6',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: { top: 10, bottom: 10, left: 14, right: 14 },
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { size: 13, weight: 'bold', family: 'Plus Jakarta Sans' },
                    bodyFont: { size: 12, weight: '600', family: 'Plus Jakarta Sans' },
                    boxPadding: 4,
                    callbacks: {
                        title: function(items) {
                            return items[0].label;
                        },
                        label: function(item) {
                            if (item.dataset.label === 'Target 100%') {
                                return null;
                            }
                            var val = item.parsed.y;
                            if (val === null || val === undefined) return null;
                            return 'Penyerapan : ' + Number(val).toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, family: 'Plus Jakarta Sans' },
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    grid: {
                        color: '#f1f5f9',
                        borderDash: [4, 4]
                    },
                    ticks: {
                        stepSize: 25,
                        callback: function(v) { return v + '%'; },
                        font: { size: 11, family: 'Plus Jakarta Sans' },
                        color: '#64748b'
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        JS);
    }

    protected function getMaxHeight(): ?string
    {
        return '320px';
    }
}
