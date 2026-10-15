<?php

namespace App\Filament\Pages;

use App\Models\LaporanBulanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class RiwayatPersetujuan extends Page
{
    protected string $view = 'filament.pages.riwayat-persetujuan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Riwayat Persetujuan';

    protected static ?string $title = 'Riwayat Persetujuan';

    protected static ?string $slug = 'riwayat-persetujuan';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, [
            'pic',
            'kabid_kabag',
            'katim_umper',
            'kabag_umum',
            'kapus',
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

        return $query->whereHas('persetujuan', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }
}
