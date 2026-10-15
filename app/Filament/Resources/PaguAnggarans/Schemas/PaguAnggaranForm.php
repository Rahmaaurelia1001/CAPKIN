<?php

namespace App\Filament\Resources\PaguAnggarans\Schemas;

use App\Models\Ikk;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaguAnggaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ikk_id')
                    ->label('IKK')
                    ->options(fn () => Ikk::query()
                        ->orderBy('kode_ikk')
                        ->get()
                        ->mapWithKeys(fn ($ikk) => [
                            $ikk->id => $ikk->kode_ikk . ' - ' . $ikk->nama_ikk,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(2020)
                    ->maxValue(2100),

                TextInput::make('nomor_revisi')
                    ->label('Nomor Revisi')
                    ->helperText('Gunakan 0 untuk pagu awal.')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0),

                TextInput::make('nilai_pagu')
                    ->label('Nilai Pagu (Rp)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp'),

                Select::make('berlaku_mulai_bulan')
                    ->label('Berlaku Mulai Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Catatan pagu atau revisi anggaran')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}