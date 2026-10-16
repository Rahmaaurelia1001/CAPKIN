<?php

namespace App\Filament\Widgets;

use App\Models\Ikk;
use App\Services\DashboardKinerjaCalculator;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DashboardKinerjaKapus extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Capaian Kinerja per IKK';

    protected ?string $description = 'Perbandingan capaian kumulatif terhadap target tahunan masing-masing indikator kinerja.';

    protected static ?int $sort = 4;

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

        $kinerjaCalc = app(DashboardKinerjaCalculator::class);
        $ikks        = Ikk::where('is_active', true)->orderBy('kode_ikk')->get();

        $labels          = [];
        $dataCapaian     = [];
        $dataTarget      = [];
        $warnaCapaian    = [];

        foreach ($ikks as $ikk) {
            $hasil = $kinerjaCalc->hitung($ikk, $bulan, $tahun);

            $labels[] = $ikk->kode_ikk;

            // Capaian kumulatif (0 jika belum ada)
            $capaian = $hasil['ada_laporan_final'] ? (float) $hasil['capaian_kumulatif'] : 0;
            $dataCapaian[] = round($capaian, 1);

            // Target selalu 100% (sudah dalam persen capaian)
            $dataTarget[] = 100;

            // Warna berdasarkan ketercapaian
            $warnaCapaian[] = match (true) {
                !$hasil['ada_laporan_final'] => 'rgba(148, 163, 184, 0.6)', // Abu-abu — belum ada data
                $capaian >= 100             => 'rgba(34, 197, 94, 0.8)',    // Hijau — tercapai
                $capaian >= 70              => 'rgba(14, 165, 233, 0.8)',   // Biru — on track
                default                     => 'rgba(251, 191, 36, 0.8)',   // Kuning — perlu perhatian
            };
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Target (%)',
                    'data'            => $dataTarget,
                    'backgroundColor' => 'rgba(226, 232, 240, 0.4)',
                    'borderColor'     => '#cbd5e1',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                    'borderSkipped'   => false,
                ],
                [
                    'label'           => 'Capaian (%)',
                    'data'            => $dataCapaian,
                    'backgroundColor' => $warnaCapaian,
                    'borderColor'     => array_map(
                        fn ($c) => str_replace('0.8', '1', str_replace('0.6', '1', $c)),
                        $warnaCapaian
                    ),
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
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
                        'label' => 'function(context) {
                            var val = context.raw;
                            return context.dataset.label + ": " + val + "%";
                        }',
                        'afterBody' => 'function(context) {
                            if (context[0] && context[0].datasetIndex === 1) {
                                var val = context[0].raw;
                                if (val === 0) return ["Status: Belum ada laporan final"];
                                if (val >= 100) return ["Status: ✅ Target Tercapai"];
                                if (val >= 70) return ["Status: 🔵 On Track"];
                                return ["Status: ⚠️ Perlu Perhatian"];
                            }
                            return [];
                        }',
                    ],
                    'backgroundColor' => 'rgba(15, 23, 42, 0.9)',
                    'padding'         => 12,
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'font' => ['size' => 11, 'weight' => '600'],
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'max'         => 110,
                    'grid'        => ['color' => 'rgba(226, 232, 240, 0.5)'],
                    'ticks'       => [
                        'callback' => 'function(value) { return value + "%"; }',
                        'stepSize' => 20,
                    ],
                ],
            ],
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'animation'           => [
                'duration' => 800,
                'easing'   => 'easeInOutQuart',
            ],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '380px';
    }
}
