<?php

namespace App\Filament\Resources\Ikks\Schemas;

use App\Models\SasaranKegiatan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IkkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sasaran_kegiatan_id')
                    ->label('Sasaran Kegiatan')
                    ->options(
                        SasaranKegiatan::query()
                            ->where('is_active', true)
                            ->orderBy('kode')
                            ->pluck('nama', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

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

                Select::make('jenis_perhitungan')
                    ->label('Jenis Perhitungan')
                    ->options([
                        'tunggal' => 'Tunggal',
                        'nilai' => 'Nilai',
                        'gabungan' => 'Gabungan',
                    ])
                    ->required(),

                TextInput::make('satuan')
                    ->label('Satuan')
                    ->placeholder('Contoh: Persentase, Nilai')
                    ->maxLength(100),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}