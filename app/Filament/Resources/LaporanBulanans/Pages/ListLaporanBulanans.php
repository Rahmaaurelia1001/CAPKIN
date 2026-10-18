<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\LaporanBulanan;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLaporanBulanans extends ListRecords
{
    protected static string $resource = LaporanBulananResource::class;

    public function getTitle(): string
    {
        $role = auth()->user()?->role;

        if ($role === 'pic') {
            return 'Laporan Saya';
        }

        if (in_array($role, ['kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true)) {
            return 'Approval Masuk';
        }

        return 'Laporan Bulanan';
    }

    public function getSubheading(): ?string
    {
        $role = auth()->user()?->role;

        if ($role === 'pic') {
            return 'Kelola dan pantau status laporan kinerja bulanan Anda';
        }

        if (in_array($role, ['kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true)) {
            return 'Daftar laporan kinerja bulanan yang memerlukan verifikasi dan persetujuan Anda';
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Isi Laporan Baru')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->visible(fn (): bool => auth()->user()?->role === 'pic'),
        ];
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }

        $tabs = [];

        // Tab "Perlu Persetujuan Saya" untuk para pejabat
        if (in_array($user->role, ['kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true)) {
            $count = $this->hitungPerluPersetujuan($user);
            $tabs['perlu_persetujuan'] = Tab::make('Perlu Persetujuan Saya')
                ->badge($count > 0 ? $count : null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $this->filterPerluPersetujuan($query, $user));
        }

        // Tab "Perlu Revisi" untuk PIC
        if ($user->role === 'pic') {
            $countRevisi = LaporanBulanan::where('pic_id', $user->id)->where('status', 'perlu_revisi')->count();
            if ($countRevisi > 0) {
                $tabs['perlu_revisi'] = Tab::make('Perlu Revisi')
                    ->badge($countRevisi)
                    ->badgeColor('danger')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'perlu_revisi'));
            }
        }

        $tabs['semua'] = Tab::make('Semua Laporan');

        $tabs['dalam_proses'] = Tab::make('Dalam Proses')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                'diajukan',
                'disetujui_kabid',
                'disetujui_katim_umper',
                'disetujui_kabag_umum',
            ]));

        $tabs['final'] = Tab::make('Sudah Final')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'final'));

        return $tabs;
    }

    private function filterPerluPersetujuan(Builder $query, $user): Builder
    {
        return match ($user->role) {
            'kabid_kabag' => $query->where('status', 'diajukan')
                ->where('unit_id', $user->unit_id)
                ->where('unit_id', '!=', 6),

            'katim_umper' => $query->where(function (Builder $q) {
                $q->where(fn (Builder $q2) => $q2->where('status', 'diajukan')->where('unit_id', 6))
                  ->orWhere('status', 'disetujui_kabid');
            }),

            'kabag_umum' => $query->where('status', 'disetujui_katim_umper'),

            'kapus' => $query->where('status', 'disetujui_kabag_umum'),

            default => $query,
        };
    }

    private function hitungPerluPersetujuan($user): int
    {
        $query = LaporanBulanan::query();
        return $this->filterPerluPersetujuan($query, $user)->count();
    }
}