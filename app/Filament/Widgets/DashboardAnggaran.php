<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use App\Services\TargetPenarikanService;
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
                <span style="font-size: 0.78rem; font-weight: 700; color: #dc2626;">Target Akhir 100%</span>
            </div>
        ');
    }

    protected ?string $description = 'Perbandingan kurva realisasi penyerapan anggaran kumulatif terhadap target penarikan dana (RPD).';

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

        $maxBulanData = (int) (\App\Models\AnggaranBulanan::where('tahun', $tahun)
            ->whereNotNull('realisasi_anggaran')
            ->max('bulan') ?? 7);

        $labels             = [];
        $dataPenyerapanKum  = [];
        $dataTarget         = [];

        // Fetch target penarikan dana percentages for the selected year
        $targetService = app(TargetPenarikanService::class);
        $targetPerMonth = $targetService->getTargetByYear($tahun); // [1=>8.28, 2=>16.55, ...]

        for ($b = 1; $b <= 12; $b++) {
            $labels[] = $shortMonths[$b - 1];

            // Use target percentage if defined, otherwise null so it doesn't plunge to 0
            $dataTarget[] = isset($targetPerMonth[$b]) ? (float) $targetPerMonth[$b] : null;

            if ($b <= min($bulanFilter, $maxBulanData)) {
                $kumulatif = $tren['persentase_penyerapan'][$b - 1] ?? 0;
                $dataPenyerapanKum[] = round($kumulatif, 2);
            } else {
                $dataPenyerapanKum[] = null;
            }
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Realisasi Kumulatif (%)',
                    'data'                 => $dataPenyerapanKum,
                    'type'                 => 'line',
                    'fill'                 => false,
                    'borderColor'          => '#2563eb', // Vibrant Royal Blue
                    'backgroundColor'      => '#2563eb',
                    'borderWidth'          => 3,
                    'tension'              => 0.25,
                    'pointStyle'           => 'circle',
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 7,
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderColor'     => '#2563eb',
                    'pointBorderWidth'     => 2.5,
                    'order'                => 1,
                    'spanGaps'             => false,
                ],
                [
                    'label'                => 'Target Penarikan Dana (%)',
                    'data'                 => $dataTarget,
                    'type'                 => 'line',
                    'fill'                 => false,
                    'borderColor'          => '#059669', // Emerald Green yang segar, kontras, dan solid
                    'backgroundColor'      => '#059669',
                    'borderWidth'          => 2.5,
                    'tension'              => 0.25,
                    'pointStyle'           => 'triangle', // Titik Segitiga agar tetap berbeda dengan lingkaran
                    'pointRadius'          => 6,
                    'pointHoverRadius'     => 8,
                    'pointBackgroundColor' => '#059669',
                    'pointBorderColor'     => '#ffffff',
                    'pointBorderWidth'     => 2,
                    'order'                => 2,
                    'spanGaps'             => true,
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
                    bodyColor: '#334155',
                    footerColor: '#0f172a',
                    borderColor: '#cbd5e1',
                    borderWidth: 1,
                    padding: { top: 10, bottom: 10, left: 14, right: 14 },
                    cornerRadius: 10,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    titleFont: { size: 13, weight: 'bold', family: 'Plus Jakarta Sans' },
                    bodyFont: { size: 12, weight: '500', family: 'Plus Jakarta Sans' },
                    footerFont: { size: 12, weight: '700', family: 'Plus Jakarta Sans' },
                    boxPadding: 6,
                    callbacks: {
                        title: function(items) {
                            return 'Bulan: ' + items[0].label;
                        },
                        label: function(item) {
                            var val = item.parsed.y;
                            if (val === null || val === undefined) return null;
                            return ' ' + item.dataset.label + ': ' + Number(val).toFixed(2) + '%';
                        },
                        footer: function(items) {
                            var realisasi = null;
                            var target = null;
                            for (var i = 0; i < items.length; i++) {
                                if (items[i].dataset.label.indexOf('Realisasi') !== -1) {
                                    realisasi = items[i].parsed.y;
                                }
                                if (items[i].dataset.label.indexOf('Target Penarikan') !== -1) {
                                    target = items[i].parsed.y;
                                }
                            }
                            if (realisasi !== null && target !== null && target > 0) {
                                var diff = realisasi - target;
                                var sign = diff >= 0 ? '+' : '';
                                var status = diff >= 0 ? ' (Sesuai / Melampaui)' : ' (Di Bawah Target)';
                                return 'Deviasi: ' + sign + diff.toFixed(2) + '%' + status;
                            }
                            return '';
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
