<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardAnggaran;
use App\Filament\Widgets\DashboardAnggaranPerIKK;
use App\Filament\Widgets\DashboardAnggaranPie;
use App\Filament\Widgets\DashboardCapaianIKK;
use App\Filament\Widgets\DashboardKinerjaKapus;
use App\Filament\Widgets\DashboardRingkasan;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard Monitoring Kinerja & Anggaran';

    public function mount(): void
    {
        if (auth()->user()?->role === 'admin') {
            redirect(\App\Filament\Resources\Users\UserResource::getUrl('index'));
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role !== 'admin';
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tahun')
                    ->label('Filter Periode Tahun')
                    ->options([
                        2025 => 'Tahun 2025',
                        2026 => 'Tahun 2026',
                    ])
                    ->default(2026),

                Select::make('bulan')
                    ->label('Filter Sampai Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->default((int) now()->month),
            ]);
    }

    public function getWidgets(): array
    {
        $role = auth()->user()?->role;

        return match ($role) {
            'kapus' => [
                DashboardRingkasan::class,      // sort 1: stats cards
                DashboardAnggaranPie::class,    // sort 3: donut chart
                DashboardAnggaran::class,       // sort 5: tren line chart
                DashboardKinerjaKapus::class,   // sort 4: capaian bar chart per IKK
                DashboardAnggaranPerIKK::class, // sort 7: pagu vs realisasi per IKK
                DashboardCapaianIKK::class,     // sort 6: rekap tabel IKK
            ],

            'katim_umper', 'kabag_umum' => [
                DashboardRingkasan::class,
                DashboardAnggaranPie::class,
                DashboardAnggaran::class,
                DashboardKinerjaKapus::class,
                DashboardAnggaranPerIKK::class,
            ],

            'pic', 'kabid_kabag' => [
                DashboardRingkasan::class,
            ],

            default => [],
        };
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md'      => 2,
            'xl'      => 2,
            '2xl'     => 3,
        ];
    }
}
