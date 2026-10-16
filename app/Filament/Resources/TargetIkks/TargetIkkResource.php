<?php

namespace App\Filament\Resources\TargetIkks;

use App\Filament\Resources\TargetIkks\Pages\CreateTargetIkk;
use App\Filament\Resources\TargetIkks\Pages\EditTargetIkk;
use App\Filament\Resources\TargetIkks\Pages\ListTargetIkks;
use App\Filament\Resources\TargetIkks\Schemas\TargetIkkForm;
use App\Filament\Resources\TargetIkks\Tables\TargetIkksTable;
use App\Models\TargetIkk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TargetIkkResource extends Resource
{
    protected static ?string $model = TargetIkk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Target IKK';

    protected static ?string $modelLabel = 'Target IKK';

    protected static ?string $pluralModelLabel = 'Target IKK';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TargetIkkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TargetIkksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTargetIkks::route('/'),
            'create' => CreateTargetIkk::route('/create'),
            'edit'   => EditTargetIkk::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
