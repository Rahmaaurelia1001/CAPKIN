<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DashboardAnggaranPie extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Proporsi Penyerapan Anggaran';

    protected ?string $description = 'Perbandingan anggaran terserap vs sisa pagu berlaku tahun berjalan.';

    protected static ?int $sort = 3;

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
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan = (int) ($this->filters['bulan'] ?? now()->month);

        $calc          = app(AnggaranCalculator::class);
        $totalPagu     = $calc->hitungTotalPagu($tahun, $bulan);
        $totalRealisasi = $calc->hitungTotalRealisasi($tahun, $bulan);
        $sisaAnggaran  = max(0, $totalPagu - $totalRealisasi);

        $persenTerserap = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 1) : 0;
        $persenSisa     = $totalPagu > 0 ? round(($sisaAnggaran / $totalPagu) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label'            => 'Anggaran (Rp)',
                    'data'             => [
                        round($totalRealisasi, 0),
                        round($sisaAnggaran, 0),
                    ],
                    'backgroundColor'      => ['#0284c7', '#e2e8f0'],
                    'hoverBackgroundColor' => ['#0369a1', '#cbd5e1'],
                    'borderWidth'          => 3,
                    'borderColor'          => '#ffffff',
                    'hoverBorderWidth'     => 0,
                ],
            ],
            'labels' => [
                "Terserap ({$persenTerserap}%)",
                "Sisa ({$persenSisa}%)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout'  => '72%',
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                    'labels'   => [
                        'usePointStyle' => true,
                        'pointStyle'    => 'circle',
                        'padding'       => 16,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(ctx) {
                            var val = new Intl.NumberFormat("id-ID", {
                                maximumFractionDigits: 0
                            }).format(ctx.raw);
                            return " Rp " + val;
                        }',
                    ],
                    'backgroundColor' => 'rgba(15, 23, 42, 0.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 8,
                ],
            ],
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'animation'           => ['animateRotate' => true, 'duration' => 900],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '300px';
    }
}
