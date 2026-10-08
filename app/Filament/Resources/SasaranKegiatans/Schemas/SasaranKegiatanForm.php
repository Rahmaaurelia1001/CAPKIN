<?php

namespace App\Filament\Resources\SasaranKegiatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SasaranKegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nama')
                    ->label('Nama Sasaran Kegiatan')
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}