<?php

namespace App\Filament\Resources\Ikks;

use App\Filament\Resources\Ikks\Pages\CreateIkk;
use App\Filament\Resources\Ikks\Pages\EditIkk;
use App\Filament\Resources\Ikks\Pages\ListIkks;
use App\Filament\Resources\Ikks\Schemas\IkkForm;
use App\Filament\Resources\Ikks\Tables\IkksTable;
use App\Filament\Resources\Ikks\RelationManagers\PicsRelationManager;
use App\Models\Ikk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IkkResource extends Resource
{
    protected static ?string $model = Ikk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_ikk';

    public static function form(Schema $schema): Schema
    {
        return IkkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IkksTable::configure($table);
    }

    public static function getRelations(): array
    {
    return [
        PicsRelationManager::class,
    ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIkks::route('/'),
            'create' => CreateIkk::route('/create'),
            'edit' => EditIkk::route('/{record}/edit'),
        ];
    }
}
