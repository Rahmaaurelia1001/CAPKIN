<?php

namespace App\Filament\Resources\Ikks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IkkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_ikk')
                    ->label('Kode IKK')
                    ->placeholder('Contoh: IKK 20')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('nama_ikk')
                    ->label('Nama Indikator Kinerja')
                    ->required()
                    ->maxLength(255),

                TextInput::make('satuan')
                    ->label('Satuan')
                    ->placeholder('Contoh: orang, dokumen, kegiatan')
                    ->maxLength(100),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}