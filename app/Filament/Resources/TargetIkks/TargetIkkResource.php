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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTargetIkks::route('/'),
            'create' => CreateTargetIkk::route('/create'),
            'edit' => EditTargetIkk::route('/{record}/edit'),
        ];
    }
}
