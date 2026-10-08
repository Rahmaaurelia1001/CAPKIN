<?php

namespace App\Filament\Resources\SasaranKegiatans;

use App\Filament\Resources\SasaranKegiatans\Pages\CreateSasaranKegiatan;
use App\Filament\Resources\SasaranKegiatans\Pages\EditSasaranKegiatan;
use App\Filament\Resources\SasaranKegiatans\Pages\ListSasaranKegiatans;
use App\Filament\Resources\SasaranKegiatans\Schemas\SasaranKegiatanForm;
use App\Filament\Resources\SasaranKegiatans\Tables\SasaranKegiatansTable;
use App\Models\SasaranKegiatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SasaranKegiatanResource extends Resource
{
    protected static ?string $model = SasaranKegiatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';
    
    protected static ?string $navigationLabel = 'Sasaran Kegiatan';

    protected static ?string $modelLabel = 'Sasaran Kegiatan';

    protected static ?string $pluralModelLabel = 'Sasaran Kegiatan';

    public static function form(Schema $schema): Schema
    {
        return SasaranKegiatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SasaranKegiatansTable::configure($table);
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
            'index' => ListSasaranKegiatans::route('/'),
            'create' => CreateSasaranKegiatan::route('/create'),
            'edit' => EditSasaranKegiatan::route('/{record}/edit'),
        ];
    }
}
