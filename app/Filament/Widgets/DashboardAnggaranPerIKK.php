<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
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
        $dataPagu      = [];
        $dataRealisasi = [];
        $warnaRealisi  = [];

        foreach ($items as $item) {
            $labels[]        = $item['kode_ikk'];
            $dataPagu[]      = $item['pagu'];
            $dataRealisasi[] = $item['realisasi'];
            $warnaRealisi[]  = $item['lebihi_pagu']
                ? 'rgba(244, 63, 94, 0.8)'  // rose-500
                : 'rgba(2, 132, 199, 0.8)'; // sky-600
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pagu (Rp)',
                    'data'            => $dataPagu,
                    'backgroundColor' => 'rgba(203, 213, 225, 0.6)',
                    'borderColor'     => '#94a3b8',
                    'borderWidth'     => 1,
                    'borderRadius'    => 5,
                    'borderSkipped'   => false,
                ],
                [
                    'label'           => 'Realisasi (Rp)',
                    'data'            => $dataRealisasi,
                    'backgroundColor' => $warnaRealisi,
                    'borderWidth'     => 0,
                    'borderRadius'    => 5,
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

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'top',
                    'align'    => 'end',
                    'labels'   => [
                        'usePointStyle' => true,
                        'pointStyle'    => 'circle',
                        'boxWidth'      => 8,
                        'padding'       => 16,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(ctx) {
                            var val = new Intl.NumberFormat("id-ID",{maximumFractionDigits:0}).format(ctx.raw);
                            return ctx.dataset.label + ": Rp " + val;
                        }',
                        'afterBody' => 'function(ctx) {
                            if (ctx[1]) {
                                var pagu = ctx[0].raw;
                                var real = ctx[1].raw;
                                if (pagu > 0) {
                                    var pct = Math.round((real / pagu) * 100);
                                    return ["Penyerapan: " + pct + "%"];
                                }
                            }
                            return [];
                        }',
                    ],
                    'mode'            => 'index',
                    'intersect'       => false,
                    'backgroundColor' => 'rgba(15, 23, 42, 0.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => ['font' => ['size' => 11, 'weight' => '600']],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid'        => ['color' => 'rgba(226, 232, 240, 0.5)'],
                    'ticks'       => [
                        'callback' => 'function(v) {
                            return "Rp " + new Intl.NumberFormat("id-ID",{notation:"compact",maximumFractionDigits:1}).format(v);
                        }',
                    ],
                ],
            ],
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'animation'           => ['duration' => 800, 'easing' => 'easeInOutQuart'],
            'interaction'         => ['mode' => 'index', 'intersect' => false],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '380px';
    }
}
