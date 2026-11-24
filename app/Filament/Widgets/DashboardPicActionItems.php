<?php

namespace App\Filament\Widgets;

use App\Models\LaporanBulanan;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class DashboardPicActionItems extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    protected string $view = 'filament.widgets.dashboard-pic-action-items';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'pic';
    }

    public function getViewData(): array
    {
        $user = auth()->user();
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);

        if (! $user || $user->role !== 'pic') {
            return [
                'revisiLaporans'  => collect(),
                'bulanBelumLapor' => null,
                'tahun'           => $tahun,
            ];
        }

        // 1. Ambil laporan berstatus perlu_revisi milik PIC untuk tahun terpilih
        $revisiLaporans = LaporanBulanan::with([
            'ikk',
            'persetujuan' => fn ($q) => $q->where('keputusan', 'dikembalikan')->latest('id')->with('user'),
        ])
            ->where('pic_id', $user->id)
            ->where('tahun', $tahun)
            ->where('status', 'perlu_revisi')
            ->get();

        // 2. Cek bulan berjalan apakah sudah dibuat laporannya
        $currentMonth = (int) now()->month;
        $currentYear  = (int) now()->year;
        $bulanBelumLapor = null;

        if ($tahun === $currentYear) {
            $adaLaporanBulanIni = LaporanBulanan::where('pic_id', $user->id)
                ->where('tahun', $tahun)
                ->where('bulan', $currentMonth)
                ->exists();

            if (! $adaLaporanBulanIni) {
                $bulanBelumLapor = $currentMonth;
            }
        }

        return [
            'revisiLaporans'  => $revisiLaporans,
            'bulanBelumLapor' => $bulanBelumLapor,
            'tahun'           => $tahun,
        ];
    }
}
