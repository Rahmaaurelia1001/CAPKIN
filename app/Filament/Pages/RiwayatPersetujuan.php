<?php

namespace App\Filament\Pages;

use App\Models\Ikk;
use App\Models\LaporanBulanan;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RiwayatPersetujuan extends Page
{
    protected string $view = 'filament.pages.riwayat-persetujuan';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Riwayat';

    protected static ?string $title = 'Riwayat Pelaporan & Persetujuan';

    protected static ?string $slug = 'riwayat-persetujuan';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    public ?string $selectedBulan = null;

    public ?string $selectedIkk = null;

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

    public function resetFilters(): void
    {
        $this->selectedBulan = null;
        $this->selectedIkk = null;
    }

    public function getIkkOptions(): Collection
    {
        $user = auth()->user();
        $query = Ikk::query()->orderBy('kode_ikk');

        if ($user && $user->role === 'pic') {
            $ikkIds = LaporanBulanan::where('pic_id', $user->id)->distinct()->pluck('ikk_id');
            $query->whereIn('id', $ikkIds);
        } elseif ($user && $user->role === 'kabid_kabag') {
            $ikkIds = LaporanBulanan::where('unit_id', $user->unit_id)->distinct()->pluck('ikk_id');
            $query->whereIn('id', $ikkIds);
        }

        return $query->get(['id', 'kode_ikk', 'nama_ikk']);
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
            $query->where('pic_id', $user->id);
        } elseif ($user->role === 'kabid_kabag') {
            $query->where('unit_id', $user->unit_id);
        } else {
            $query->whereHas('persetujuan', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        if ($this->selectedBulan) {
            $query->where('bulan', (int) $this->selectedBulan);
        }

        if ($this->selectedIkk) {
            $query->where('ikk_id', (int) $this->selectedIkk);
        }

        return $query;
    }
}
