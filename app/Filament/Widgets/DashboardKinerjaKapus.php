<?php

namespace App\Filament\Widgets;

use App\Models\Ikk;
use App\Services\DashboardKinerjaCalculator;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class DashboardKinerjaKapus extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): string|Htmlable|null
    {
        return new HtmlString('
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span style="font-weight: 700;">Tren Capaian Kinerja (Rata-rata)</span>
                <span style="font-size: 0.78rem; font-weight: 700; color: #dc2626;">Target 100%</span>
            </div>
        ');
    }

    protected ?string $description = 'Perkembangan rata-rata capaian kinerja kumulatif terhadap target tahunan 100%.';

    protected static ?int $sort = 4;

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

        $kinerjaCalc = app(DashboardKinerjaCalculator::class);
        $ikks        = Ikk::where('is_active', true)->get();

        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $labels          = [];
        $dataCapaian     = [];
        $dataTarget      = [];

        for ($b = 1; $b <= 12; $b++) {
            $labels[]     = $shortMonths[$b - 1];
            $dataTarget[] = 100;

            if ($b <= $bulanFilter) {
                $totalCapaian   = 0;
                $jumlahDihitung = 0;

                foreach ($ikks as $ikk) {
                    $hasil = $kinerjaCalc->hitung($ikk, $b, $tahun);
                    if ($hasil['ada_laporan_final'] ?? false) {
                        $totalCapaian += (float) $hasil['capaian_kumulatif'];
                        $jumlahDihitung++;
                    }
                }

                $avg = $jumlahDihitung > 0 ? round($totalCapaian / $jumlahDihitung, 1) : null;
                $dataCapaian[] = $avg;
            } else {
                $dataCapaian[] = null;
            }
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Realisasi Kinerja',
                    'data'                 => $dataCapaian,
                    'type'                 => 'line',
                    'fill'                 => false,
                    'borderColor'          => '#2563eb',
                    'backgroundColor'      => '#2563eb',
                    'borderWidth'          => 2.5,
                    'tension'              => 0.35,
                    'pointBackgroundColor' => '#2563eb',
                    'pointBorderColor'     => '#ffffff',
                    'pointHoverRadius'     => 7,
                    'pointRadius'          => 5,
                    'pointBorderWidth'     => 2,
                    'spanGaps'             => false,
                ],
                [
                    'label'                => 'Target 100%',
                    'data'                 => $dataTarget,
                    'type'                 => 'line',
                    'fill'                 => false,
                    'borderColor'          => '#ef4444',
                    'borderWidth'          => 1.5,
                    'borderDash'           => [5, 5],
                    'pointRadius'          => 0,
                    'pointHoverRadius'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                    bodyColor: '#2563eb',
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
                            return 'Realisasi : ' + Number(val).toFixed(2) + '%';
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
