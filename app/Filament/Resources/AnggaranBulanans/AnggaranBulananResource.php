<?php

namespace App\Filament\Resources\AnggaranBulanans;

use App\Filament\Resources\AnggaranBulanans\Pages\CreateAnggaranBulanan;
use App\Filament\Resources\AnggaranBulanans\Pages\EditAnggaranBulanan;
use App\Filament\Resources\AnggaranBulanans\Pages\ListAnggaranBulanans;
use App\Filament\Resources\AnggaranBulanans\Schemas\AnggaranBulananForm;
use App\Filament\Resources\AnggaranBulanans\Tables\AnggaranBulanansTable;
use App\Models\AnggaranBulanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnggaranBulananResource extends Resource
{
    protected static ?string $model = AnggaranBulanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Realisasi Anggaran';

    protected static ?string $modelLabel = 'Anggaran Bulanan';

    protected static ?string $pluralModelLabel = 'Anggaran Bulanan';

    protected static ?string $recordTitleAttribute = 'ikk_id';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengelolaan Anggaran';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AnggaranBulananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnggaranBulanansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAnggaranBulanans::route('/'),
            'create' => CreateAnggaranBulanan::route('/create'),
            'edit'   => EditAnggaranBulanan::route('/{record}/edit'),
        ];
    }
}
