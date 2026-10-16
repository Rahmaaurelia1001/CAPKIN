<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DashboardAnggaran extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Tren Penyerapan Anggaran Kumulatif';

    protected ?string $description = 'Perkembangan realisasi dan penyerapan anggaran sepanjang tahun berjalan.';

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

        $labels            = [];
        $dataPenyerapan    = [];
        $dataRealisasiRp   = [];
        $shortMonths       = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                              'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        foreach ($tren['labels'] as $idx => $label) {
            $bulanNum = $idx + 1;
            if ($bulanNum <= $bulanFilter) {
                $labels[]          = $shortMonths[$idx] ?? $label;
                $dataPenyerapan[]  = round($tren['persentase_penyerapan'][$idx] ?? 0, 1);
                $dataRealisasiRp[] = round($tren['realisasi_nominal'][$idx] ?? 0, 0);
            }
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Penyerapan (%)',
                    'data'                 => $dataPenyerapan,
                    'type'                 => 'line',
                    'fill'                 => true,
                    'borderColor'          => '#0284c7',
                    'backgroundColor'      => 'rgba(14, 165, 233, 0.12)',
                    'tension'              => 0.4,
                    'pointBackgroundColor' => '#0284c7',
                    'pointBorderColor'     => '#ffffff',
                    'pointHoverRadius'     => 7,
                    'pointRadius'          => 5,
                    'pointBorderWidth'     => 2,
                    'yAxisID'              => 'y',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'top',
                    'align'    => 'end',
                ],
                'tooltip' => [
                    'mode'         => 'index',
                    'intersect'    => false,
                    'callbacks'    => [
                        'label' => 'function(context) {
                            return " Penyerapan: " + context.raw + "%";
                        }',
                    ],
                    'backgroundColor' => 'rgba(15, 23, 42, 0.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => ['font' => ['size' => 11]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'max'         => 100,
                    'grid'        => ['color' => 'rgba(226, 232, 240, 0.5)'],
                    'ticks'       => [
                        'callback' => 'function(v) { return v + "%"; }',
                        'stepSize' => 25,
                    ],
                    'position' => 'left',
                ],
            ],
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'animation'           => ['duration' => 800, 'easing' => 'easeInOutQuart'],
            'interaction'         => ['mode' => 'nearest', 'axis' => 'x', 'intersect' => false],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '300px';
    }
}
