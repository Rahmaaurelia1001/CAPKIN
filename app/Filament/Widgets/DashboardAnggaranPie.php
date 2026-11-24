<?php

namespace App\Filament\Widgets;

use App\Services\AnggaranCalculator;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DashboardAnggaranPie extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Ringkasan Anggaran';

    protected ?string $description = 'Proporsi realisasi anggaran terserap vs sisa pagu tahun berjalan.';

    protected static ?int $sort = 7;

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

        $calc           = app(AnggaranCalculator::class);
        $totalPagu      = $calc->hitungTotalPagu($tahun, $bulan);
        $totalRealisasi = $calc->hitungTotalRealisasi($tahun, $bulan);
        $sisaAnggaran   = max(0, $totalPagu - $totalRealisasi);

        $persenTerserap = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 1) : 0;
        $persenSisa     = $totalPagu > 0 ? round(($sisaAnggaran / $totalPagu) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label'                => 'Anggaran (Rp)',
                    'data'                 => [
                        round($totalRealisasi, 0),
                        round($sisaAnggaran, 0),
                    ],
                    // Warna favorit: Ungu Amethyst (Terserap) & Biru Sky (Sisa)
                    'backgroundColor'      => ['#9b59b6', '#3498db'],
                    'hoverBackgroundColor' => ['#8e44ad', '#2563eb'],
                    'borderWidth'          => 2,
                    'borderColor'          => '#ffffff',
                    'hoverBorderWidth'     => 3,
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

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            cutout: '68%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 16,
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
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    titleFont: { size: 13, weight: '800', family: 'Plus Jakarta Sans' },
                    bodyFont: { size: 12, weight: '600', family: 'Plus Jakarta Sans' },
                    boxPadding: 4,
                    callbacks: {
                        title: function(items) {
                            return items[0].label;
                        },
                        label: function(ctx) {
                            var val = ctx.raw || 0;
                            var formatted = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                maximumFractionDigits: 0
                            }).format(val);
                            return ' Nominal: ' + formatted;
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            animation: { animateRotate: true, duration: 800 }
        }
        JS);
    }

    protected function getMaxHeight(): ?string
    {
        return '300px';
    }
}
