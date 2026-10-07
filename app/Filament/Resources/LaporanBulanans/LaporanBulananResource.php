<?php

namespace App\Filament\Resources\LaporanBulanans;

use App\Filament\Resources\LaporanBulanans\Pages\CreateLaporanBulanan;
use App\Filament\Resources\LaporanBulanans\Pages\EditLaporanBulanan;
use App\Filament\Resources\LaporanBulanans\Pages\ListLaporanBulanans;
use App\Filament\Resources\LaporanBulanans\Schemas\LaporanBulananForm;
use App\Filament\Resources\LaporanBulanans\Tables\LaporanBulanansTable;
use App\Models\LaporanBulanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporanBulananResource extends Resource
{
    protected static ?string $model = LaporanBulanan::class;

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaporanBulanans::route('/'),
            'create' => CreateLaporanBulanan::route('/create'),
            'edit' => EditLaporanBulanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
    $query = parent::getEloquentQuery();

    if (auth()->user()?->role === 'pic') {
        $query->where('pic_id', auth()->id());
    }

    return $query;
    }
}
