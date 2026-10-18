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

    public function getTitle(): string
    {
        return auth()->user()?->role === 'kapus'
            ? 'Dashboard Pimpinan'
            : 'Dashboard Monitoring Kinerja & Anggaran';
    }

    public function getSubheading(): ?string
    {
        $tahun = $this->filters['tahun'] ?? now()->year;
        return "Monitoring Capaian Kinerja dan Realisasi Anggaran — Tahun Anggaran {$tahun}";
    }

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
            ->columns(2)
            ->components([
                Select::make('tahun')
                    ->label('Tahun Anggaran')
                    ->options(function () {
                        $currentYear = (int) now()->year;
                        $years = \App\Models\TargetIkk::distinct()->pluck('tahun')
                            ->merge(\App\Models\PaguAnggaran::distinct()->pluck('tahun'))
                            ->merge(\App\Models\LaporanBulanan::distinct()->pluck('tahun'))
                            ->push($currentYear)
                            ->unique()
                            ->sortDesc()
                            ->values();

                        return $years->mapWithKeys(fn ($y) => [$y => "Tahun {$y}"])->all();
                    })
                    ->default((int) now()->year),

                Select::make('bulan')
                    ->label('Periode')
                    ->options([
                        1 => 's.d. Januari',
                        2 => 's.d. Februari',
                        3 => 's.d. Maret',
                        4 => 's.d. April',
                        5 => 's.d. Mei',
                        6 => 's.d. Juni',
                        7 => 's.d. Juli',
                        8 => 's.d. Agustus',
                        9 => 's.d. September',
                        10 => 's.d. Oktober',
                        11 => 's.d. November',
                        12 => 's.d. Desember',
                    ])
                    ->default((int) now()->month),
            ]);
    }

    public function getWidgets(): array
    {
        $role = auth()->user()?->role;

        return match ($role) {
            'kapus' => [
                DashboardRingkasan::class,      // sort 1: 4 stats cards (Total Indikator, Realisasi Kinerja, Realisasi Anggaran, Penyerapan)
                DashboardKinerjaKapus::class,   // sort 2 (left): Tren Capaian Kinerja (Rata-rata) Line Chart
                DashboardAnggaran::class,       // sort 3 (right): Penyerapan Anggaran per Bulan Bar Chart
                DashboardCapaianIKK::class,     // sort 4: Daftar Indikator Kinerja (7 IKK) Table
                DashboardAnggaranPie::class,    // sort 5: Ringkasan Anggaran Donut
                DashboardAnggaranPerIKK::class, // sort 6: Pagu vs Realisasi per IKK
            ],

            'katim_umper', 'kabag_umum' => [
                DashboardRingkasan::class,
                DashboardKinerjaKapus::class,
                DashboardAnggaran::class,
                DashboardCapaianIKK::class,
                DashboardAnggaranPie::class,
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
        ];
    }
}
