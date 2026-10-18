<?php

namespace App\Filament\Pages;

use App\Models\LaporanBulanan;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class RiwayatPersetujuan extends Page
{
    protected string $view = 'filament.pages.riwayat-persetujuan';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Riwayat';

    protected static ?string $title = 'Riwayat Pelaporan & Persetujuan';

    protected static ?string $slug = 'riwayat-persetujuan';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, [
            'pic',
            'kabid_kabag',
            'katim_umper',
        ], true);
    }

    public function getLaporanQuery(): Builder
    {
        $user = auth()->user();

        $query = LaporanBulanan::query()
            ->with([
                'persetujuan.user',
                'ikk',
                'unit',
                'pic',
                'rincianPelatihans',
                'rincianNSPKs',
                'rincianKerjasamas',
            ]);

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === 'pic') {
            return $query->where('pic_id', $user->id);
        }

        if ($user->role === 'kabid_kabag') {
            return $query->where('unit_id', $user->unit_id);
        }

        return $query->whereHas('persetujuan', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }
}
