<?php

namespace App\Filament\Resources\Ikks\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KomponensRelationManager extends RelationManager
{
    protected static string $relationship = 'komponens';

    protected static ?string $title = 'Komponen IKK Gabungan';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->jenis_perhitungan === 'gabungan'
            || in_array(strtoupper(str_replace([' ', '_', '-'], '', (string) $ownerRecord->kode_ikk)), ['IKK24', 'IKK28']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_komponen')
                    ->label('Kode Komponen')
                    ->placeholder('Contoh: A, B')
                    ->required()
                    ->maxLength(50),

                TextInput::make('nama_komponen')
                    ->label('Nama Komponen')
                    ->placeholder('Contoh: Jumlah Unit yang Ditetapkan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('bobot')
                    ->label('Bobot (%)')
                    ->numeric()
                    ->suffix('%')
                    ->required(),

                TextInput::make('sumber_pembilang')
                    ->label('Cara Perhitungan Pembilang (Realisasi)')
                    ->placeholder('Contoh: Jumlah unit kerja yang telah ditetapkan...')
                    ->maxLength(255),

                TextInput::make('sumber_penyebut')
                    ->label('Cara Dapat Penyebut (Target)')
                    ->placeholder('Contoh: Total seluruh target unit kerja...')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_komponen')
            ->columns([
                TextColumn::make('kode_komponen')
                    ->label('Kode')
                    ->sortable()
                    ->badge(),

                TextColumn::make('nama_komponen')
                    ->label('Nama Komponen')
                    ->searchable(),

                TextColumn::make('bobot')
                    ->label('Bobot')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable(),

                TextColumn::make('sumber_penyebut')
                    ->label('Cara Dapat Penyebut')
                    ->limit(35),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Komponen'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ]);
    }
}
