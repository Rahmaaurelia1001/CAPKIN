<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardAnggaran;
use App\Filament\Widgets\DashboardAnggaranPerIKK;
use App\Filament\Widgets\DashboardAnggaranPie;
use App\Filament\Widgets\DashboardCapaianIKK;
use App\Filament\Widgets\DashboardKinerjaKapus;
use App\Filament\Widgets\DashboardJadwalDeadline;
use App\Filament\Widgets\DashboardRingkasan;
use App\Filament\Widgets\DashboardPicActionItems;
use App\Filament\Widgets\DashboardPicLaporanTable;
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
            ? 'Dashboard Pimpinan — SIMONIKA'
            : 'Dashboard SIMONIKA';
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
            return;
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
                    ->live()
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
                    ->options(function ($get) {
                        $tahun = (int) ($get('tahun') ?? now()->year);

                        // Ambil batas bulan maksimum yang ada datanya
                        $maxBulanLaporan = (int) \App\Models\LaporanBulanan::where('tahun', $tahun)->max('bulan');
                        $maxBulanAnggaran = (int) \App\Models\AnggaranBulanan::where('tahun', $tahun)
                            ->whereNotNull('realisasi_anggaran')
                            ->where('realisasi_anggaran', '>', 0)
                            ->max('bulan');

                        $maxBulanData = max($maxBulanLaporan, $maxBulanAnggaran);

                        // Batasi sampai bulan berjalan (now()->month) dan yang ada datanya
                        if ($tahun === (int) now()->year) {
                            $maxLimit = $maxBulanData > 0 
                                ? min((int) now()->month, $maxBulanData)
                                : (int) now()->month;
                        } else {
                            $maxLimit = $maxBulanData > 0 ? min(12, $maxBulanData) : 12;
                        }

                        $maxLimit = max(1, $maxLimit);

                        $namaBulan = [
                            1  => 's.d. Januari',
                            2  => 's.d. Februari',
                            3  => 's.d. Maret',
                            4  => 's.d. April',
                            5  => 's.d. Mei',
                            6  => 's.d. Juni',
                            7  => 's.d. Juli',
                            8  => 's.d. Agustus',
                            9  => 's.d. September',
                            10 => 's.d. Oktober',
                            11 => 's.d. November',
                            12 => 's.d. Desember',
                        ];

                        $options = [];
                        for ($m = 1; $m <= $maxLimit; $m++) {
                            $options[$m] = $namaBulan[$m];
                        }

                        return $options;
                    })
                    ->default(function ($get) {
                        $tahun = (int) ($get('tahun') ?? now()->year);
                        $maxBulanFinal = (int) \App\Models\LaporanBulanan::where('tahun', $tahun)
                            ->where('status', 'final')
                            ->max('bulan');

                        if ($maxBulanFinal > 0) {
                            return ($tahun === (int) now()->year) ? min((int) now()->month, $maxBulanFinal) : $maxBulanFinal;
                        }

                        $maxBulanData = (int) \App\Models\LaporanBulanan::where('tahun', $tahun)->max('bulan');
                        if ($maxBulanData > 0) {
                            return ($tahun === (int) now()->year) ? min((int) now()->month, $maxBulanData) : $maxBulanData;
                        }

                        return ($tahun === (int) now()->year) ? (int) now()->month : 7;
                    }),
            ]);
    }

    public function getWidgets(): array
    {
        $role = auth()->user()?->role;

        return match ($role) {
            'kapus' => [
                DashboardJadwalDeadline::class,
                DashboardRingkasan::class,
                DashboardAnggaranPie::class,
                DashboardAnggaran::class,
                DashboardCapaianIKK::class,
                DashboardAnggaranPerIKK::class,
            ],

            'katim_umper', 'kabag_umum' => [
                DashboardJadwalDeadline::class,
                DashboardRingkasan::class,
                DashboardAnggaranPie::class,
                DashboardAnggaran::class,
                DashboardCapaianIKK::class,
                DashboardAnggaranPerIKK::class,
            ],

            'pic' => [
                DashboardJadwalDeadline::class,
                DashboardRingkasan::class,
                DashboardPicActionItems::class,
                DashboardPicLaporanTable::class,
            ],

            'kabid_kabag' => [
                DashboardJadwalDeadline::class,
                DashboardRingkasan::class,
                DashboardCapaianIKK::class,
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
