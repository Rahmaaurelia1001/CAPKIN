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

        $jumlahIkkAktif    = Ikk::where('is_active', true)->count();
        $jumlahFinal       = (clone $laporan)->where('status', 'final')->count();
        $jumlahDalamProses = (clone $laporan)->whereIn('status', [
            'diajukan', 'disetujui_kabid', 'disetujui_katim_umper', 'disetujui_kabag_umum',
        ])->count();
        $jumlahPerluRevisi = (clone $laporan)->where('status', 'perlu_revisi')->count();

        $laporanUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('index');

        // Sparkline: laporan final per bulan sepanjang tahun
        $finalPerBulan = LaporanBulanan::query()
            ->where('tahun', $tahun)
            ->where('status', 'final')
            ->when($user?->role === 'pic', fn ($q) => $q->where('pic_id', $user->id))
            ->when($user?->role === 'kabid_kabag', fn ($q) => $q->where('unit_id', $user->unit_id))
            ->selectRaw('bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $sparklineFinal = array_map(
            fn ($b) => (int) ($finalPerBulan[$b] ?? 0),
            range(1, $bulan)
        );

        return [
            Stat::make('Total IKK Aktif', $jumlahIkkAktif . ' Indikator')
                ->description('IKK yang dipantau tahun ' . $tahun)
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('info'),

            Stat::make('Laporan Terverifikasi', $jumlahFinal . ' Laporan')
                ->description('Status final & disetujui Kapus')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($sparklineFinal)
                ->url($laporanUrl),

            Stat::make('Dalam Proses Persetujuan', $jumlahDalamProses . ' Laporan')
                ->description('Menunggu persetujuan berjenjang')
                ->descriptionIcon('heroicon-m-clock')
                ->color($jumlahDalamProses > 0 ? 'warning' : 'gray')
                ->url($laporanUrl),

            Stat::make('Perlu Tindak Lanjut', $jumlahPerluRevisi . ' Laporan')
                ->description($jumlahPerluRevisi > 0 ? 'Ada laporan perlu diperbaiki segera' : 'Semua laporan dalam kondisi baik')
                ->descriptionIcon($jumlahPerluRevisi > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($jumlahPerluRevisi > 0 ? 'danger' : 'gray')
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

        $ikks           = Ikk::where('is_active', true)->get();
        $totalCapaian   = 0;
        $jumlahDihitung = 0;

        foreach ($ikks as $ikk) {
            $hasil = $kinerjaCalc->hitung($ikk, $bulan, $tahun);
            if ($hasil['ada_laporan_final'] ?? false) {
                $totalCapaian += (float) $hasil['capaian_kumulatif'];
                $jumlahDihitung++;
            }
        }

        $rataRataCapaian = $jumlahDihitung > 0 ? round($totalCapaian / $jumlahDihitung, 2) : null;

        // Sparkline tren penyerapan
        $tren               = $anggaranCalc->ambilTrenBulanan($tahun);
        $sparklinePenyerapan = array_slice($tren['persentase_penyerapan'] ?? [], 0, $bulan);

        $formatRupiah = fn (float $nilai): string => 'Rp ' . number_format($nilai, 0, ',', '.');

        $jumlahFinal       = LaporanBulanan::where('tahun', $tahun)->where('status', 'final')->count();
        $jumlahDalamProses = LaporanBulanan::where('tahun', $tahun)
            ->whereIn('status', ['diajukan', 'disetujui_kabid', 'disetujui_katim_umper', 'disetujui_kabag_umum'])
            ->count();

        $laporanUrl = \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl('index');

        return [
            Stat::make('Total IKK Dipantau', $jumlahIkkAktif . ' Indikator')
                ->description('IKK aktif PPSDMAP tahun ' . $tahun)
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('info'),

            Stat::make(
                'Rata-rata Capaian Kinerja',
                $rataRataCapaian !== null
                    ? number_format($rataRataCapaian, 1, ',', '.') . '%'
                    : '—'
            )
                ->description(
                    $rataRataCapaian !== null
                        ? "Dari {$jumlahDihitung} IKK yang sudah final"
                        : 'Menunggu laporan final masuk'
                )
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color(
                    $rataRataCapaian === null ? 'gray' :
                    ($rataRataCapaian >= 90 ? 'success' :
                    ($rataRataCapaian >= 70 ? 'info' : 'warning'))
                )
                ->url($laporanUrl),

            Stat::make('Realisasi Anggaran', $formatRupiah($realisasiAnggaran))
                ->description(
                    number_format($persentasePenyerapan, 1, ',', '.') . '% dari Pagu ' . $formatRupiah($totalPagu)
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($persentasePenyerapan >= 80 ? 'success' : ($persentasePenyerapan >= 50 ? 'info' : 'warning'))
                ->chart($sparklinePenyerapan),

            Stat::make('Status Pelaporan', $jumlahFinal . ' Final / ' . $jumlahDalamProses . ' Proses')
                ->description('Laporan masuk tahun ' . $tahun)
                ->descriptionIcon('heroicon-m-document-check')
                ->color('primary')
                ->url($laporanUrl),
        ];
    }
}
