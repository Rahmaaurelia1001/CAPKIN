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
    protected static ?string $modelLabel = 'Laporan Bulanan';
    protected static ?string $pluralModelLabel = 'Laporan Bulanan';

    protected static ?string $recordTitleAttribute = 'bulan';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
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

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        $role = auth()->user()?->role;

        if ($role === 'pic') {
            return Heroicon::OutlinedDocumentText;
        }

        if (in_array($role, ['kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true)) {
            return Heroicon::OutlinedInboxArrowDown;
        }

        return Heroicon::OutlinedDocumentText;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if (in_array($user->role, ['kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true)) {
            $query = LaporanBulanan::query();
            $filtered = match ($user->role) {
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

            $count = $filtered->count();

            return $count > 0 ? (string) $count : null;
        }

        if ($user->role === 'pic') {
            $countRevisi = LaporanBulanan::where('pic_id', $user->id)
                ->where('status', 'perlu_revisi')
                ->count();

            return $countRevisi > 0 ? (string) $countRevisi : null;
        }

        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return auth()->user()?->role === 'pic' ? 'danger' : 'warning';
    }

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
            'index'  => ListLaporanBulanans::route('/'),
            'create' => CreateLaporanBulanan::route('/create'),
            'edit'   => EditLaporanBulanan::route('/{record}/edit'),
            'view'   => ViewLaporanBulanan::route('/{record}'),
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

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pic') {
            return (int) $record->pic_id === (int) $user->id;
        }

        if ($user->role === 'kabid_kabag') {
            return (int) $record->unit_id === (int) $user->unit_id;
        }

        // katim_umper, kabag_umum, kapus boleh melihat semua laporan yang sudah diajukan
        return in_array($user->role, ['katim_umper', 'kabag_umum', 'kapus'], true);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'pic') {
            return false;
        }

        // PIC hanya bisa edit laporan miliknya sendiri yang masih draft atau perlu revisi
        return (int) $record->pic_id === (int) $user->id
            && in_array($record->status, ['draft', 'perlu_revisi'], true);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if (! $user) {
            return $query->whereRaw('1=0');
        }

        // PIC hanya melihat laporan milik sendiri
        if ($user->role === 'pic') {
            return $query->where('pic_id', $user->id);
        }

        // Kabid hanya melihat laporan unit kerjanya
        if ($user->role === 'kabid_kabag') {
            return $query->where('unit_id', $user->unit_id);
        }

        // katim_umper, kabag_umum, kapus, admin melihat semua
        return $query;
    }
}
