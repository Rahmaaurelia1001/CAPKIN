<?php

namespace App\Filament\Resources\PaguAnggarans;

use App\Filament\Resources\PaguAnggarans\Pages\CreatePaguAnggaran;
use App\Filament\Resources\PaguAnggarans\Pages\EditPaguAnggaran;
use App\Filament\Resources\PaguAnggarans\Pages\ListPaguAnggarans;
use App\Filament\Resources\PaguAnggarans\Schemas\PaguAnggaranForm;
use App\Filament\Resources\PaguAnggarans\Tables\PaguAnggaransTable;
use App\Models\PaguAnggaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaguAnggaranResource extends Resource
{
    protected static ?string $model = PaguAnggaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Pagu Anggaran';

    protected static ?string $modelLabel = 'Pagu Anggaran';

    protected static ?string $pluralModelLabel = 'Pagu Anggaran';

    protected static ?string $recordTitleAttribute = 'nomor_revisi';

    public static function canViewAny(): bool
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
        return PaguAnggaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaguAnggaransTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaguAnggarans::route('/'),
            'create' => CreatePaguAnggaran::route('/create'),
            'edit' => EditPaguAnggaran::route('/{record}/edit'),
        ];
    }
}