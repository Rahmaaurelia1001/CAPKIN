<?php

namespace App\Filament\Resources\TargetIkks\Schemas;

use App\Models\IkkPeriode;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TargetIkkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ikk_periode_id')
                    ->label('IKK')
                    ->options(
                        IkkPeriode::with('ikk')
                            ->orderBy('id')
                            ->get()
                            ->mapWithKeys(fn ($periode) => [
                                $periode->id =>
                                    $periode->ikk->kode_ikk
                                    . ' - '
                                    . $periode->ikk->nama_ikk,
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->required(),

                TextInput::make('nilai_target')
                    ->label('Nilai Target')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }
}