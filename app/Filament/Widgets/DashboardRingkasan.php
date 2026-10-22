<?php

namespace App\Filament\Widgets;

use App\Models\Ikk;
use App\Models\LaporanBulanan;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardRingkasan extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user  = auth()->user();
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan = (int) ($this->filters['bulan'] ?? now()->month);

        if ($user?->role === 'kapus') {
            return $this->getStatsKapus($tahun, $bulan);
        }

        return $this->getStatsUmum($user, $tahun, $bulan);
    }

    private function getStatsUmum($user, int $tahun, int $bulan): array
    {
        $laporan = LaporanBulanan::query()->where('tahun', $tahun);

        if ($user?->role === 'pic') {
            $laporan->where('pic_id', $user->id);
        }
        if ($user?->role === 'kabid_kabag') {
            $laporan->where('unit_id', $user->unit_id);
        }

        $jumlahDraft       = (clone $laporan)->where('status', 'draft')->count();
        $jumlahDalamProses = (clone $laporan)->whereIn('status', [
            'diajukan', 'disetujui_kabid', 'disetujui_katim_umper', 'disetujui_kabag_umum',
        ])->count();
        $jumlahPerluRevisi = (clone $laporan)->where('status', 'perlu_revisi')->count();
        $jumlahFinal       = (clone $laporan)->where('status', 'final')->count();

        $laporanUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('index');

        return [
            Stat::make('Draf', $jumlahDraft . ' Laporan')
                ->description('Laporan belum diajukan')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('gray')
                ->url($laporanUrl),

            Stat::make('Menunggu', $jumlahDalamProses . ' Laporan')
                ->description('Menunggu persetujuan pejabat')
                ->descriptionIcon('heroicon-m-clock')
                ->color($jumlahDalamProses > 0 ? 'warning' : 'gray')
                ->url($laporanUrl),

            Stat::make('Ditolak / Revisi', $jumlahPerluRevisi . ' Laporan')
                ->description($jumlahPerluRevisi > 0 ? 'Perlu perbaikan segera' : 'Tidak ada laporan ditolak')
                ->descriptionIcon($jumlahPerluRevisi > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($jumlahPerluRevisi > 0 ? 'danger' : 'gray')
                ->url($laporanUrl),

            Stat::make('Final', $jumlahFinal . ' Laporan')
                ->description('Telah disetujui Kapus')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url($laporanUrl),
        ];
    }

    private function getStatsKapus(int $tahun, int $bulan): array
    {
        $anggaranCalc = app(\App\Services\AnggaranCalculator::class);
        $kinerjaCalc  = app(\App\Services\DashboardKinerjaCalculator::class);

        $jumlahIkkAktif       = Ikk::where('is_active', true)->count();
        $totalPagu            = $anggaranCalc->hitungTotalPagu($tahun, $bulan);
        $realisasiAnggaran    = $anggaranCalc->hitungTotalRealisasi($tahun, $bulan);
        $persentasePenyerapan = $anggaranCalc->hitungPersentasePenyerapan($tahun, $bulan) ?? 0;

        $jumlahFinal = LaporanBulanan::where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->where('status', 'final')
            ->count();

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$bulan] ?? "Bulan {$bulan}";

        $formatRingkas = function (float $n): string {
            if ($n >= 1_000_000_000) {
                return 'Rp ' . number_format($n / 1_000_000_000, 2, ',', '.') . ' M';
            }
            if ($n >= 1_000_000) {
                return 'Rp ' . number_format($n / 1_000_000, 2, ',', '.') . ' Jt';
            }
            return 'Rp ' . number_format($n, 0, ',', '.');
        };

        $laporanUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('index');

        return [
            Stat::make('Total Indikator', (string) $jumlahIkkAktif . ' IKK')
                ->description('Indikator aktif TA ' . $tahun)
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Laporan Disahkan', (string) $jumlahFinal . ' Laporan')
                ->description("Status final s.d. {$namaBulan}")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url($laporanUrl),

            Stat::make("Realisasi Anggaran s.d. {$namaBulan}", $formatRingkas($realisasiAnggaran))
                ->description('dari pagu ' . $formatRingkas($totalPagu))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Penyerapan Anggaran', number_format($persentasePenyerapan, 2, ',', '.') . '%')
                ->description('dari total pagu TA ' . $tahun)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($persentasePenyerapan >= 50 ? 'success' : 'warning'),
        ];
    }
}
