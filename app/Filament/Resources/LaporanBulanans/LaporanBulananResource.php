<?php

namespace App\Filament\Resources\LaporanBulanans;

use App\Filament\Resources\LaporanBulanans\Pages\CreateLaporanBulanan;
use App\Filament\Resources\LaporanBulanans\Pages\EditLaporanBulanan;
use App\Filament\Resources\LaporanBulanans\Pages\ListLaporanBulanans;
use App\Filament\Resources\LaporanBulanans\Schemas\LaporanBulananForm;
use App\Filament\Resources\LaporanBulanans\Tables\LaporanBulanansTable;
use App\Filament\Resources\LaporanBulanans\Pages\ViewLaporanBulanan;
use App\Models\LaporanBulanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class LaporanBulananResource extends Resource
{
    protected static ?string $model = LaporanBulanan::class;
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static ?string $modelLabel = 'Laporan Bulanan';
    protected static ?string $pluralModelLabel = 'Laporan Bulanan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'bulan';

    public static function form(Schema $schema): Schema
    {
        return LaporanBulananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanBulanansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaporanBulanans::route('/'),
            'create' => CreateLaporanBulanan::route('/create'),
            'edit' => EditLaporanBulanan::route('/{record}/edit'),
            'view' => ViewLaporanBulanan::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, [
            'pic',
            'kabid_kabag',
            'katim_umper',
            'kabag_umum',
            'kapus',
        ], true);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'pic';
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->role === 'pic') {
            return (int) $record->pic_id === (int) $user->id;
        }

        if ($user->role === 'kabid_kabag') {
            return (int) $record->unit_id === (int) $user->unit_id
                && (int) $record->unit_id !== 6
                && $record->status === 'diajukan';
        }

        if ($user->role === 'katim_umper') {
            return (int) $record->unit_id === 6
                ? $record->status === 'diajukan'
                : $record->status === 'disetujui_kabid';
        }

        if ($user->role === 'kabag_umum') {
            return $record->status === 'disetujui_katim_umper';
        }

        if ($user->role === 'kapus') {
            return in_array($record->status, [
                'disetujui_kabag_umum',
                'final',
            ], true);
        }

        return false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->role === 'pic'
            && (int) $record->pic_id === (int) $user->id
            && in_array($record->status, ['draft', 'perlu_revisi'], true);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === 'pic') {
            return $query->where('pic_id', $user->id);
        }

        if ($user->role === 'kabid_kabag') {
            return $query
                ->where('unit_id', $user->unit_id)
                ->where('unit_id', '!=', 6)
                ->where('status', 'diajukan');
        }

        if ($user->role === 'katim_umper') {
            return $query->where(function (Builder $query) {
                $query
                    ->where(function (Builder $query) {
                        $query
                            ->where('unit_id', 6)
                            ->where('status', 'diajukan');
                    })
                    ->orWhere(function (Builder $query) {
                        $query
                            ->where('unit_id', '!=', 6)
                            ->where('status', 'disetujui_kabid');
                    });
            });
        }

        if ($user->role === 'kabag_umum') {
        return $query->where('status', 'disetujui_katim_umper');
        }

        if ($user->role === 'kapus') {
            return $query->whereIn('status', [
                'disetujui_kabag_umum',
                'final',
            ]);
        }

        return $query->whereRaw('1 = 0');
    }
}
