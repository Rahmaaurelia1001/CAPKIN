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
                    ->live()
                    ->required(),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->integer()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->live()
                    ->required()
                    ->unique(
                        table: 'target_ikks',
                        column: 'tahun',
                        modifyRuleUsing: fn ($rule, callable $get) => $rule
                            ->where('ikk_periode_id', $get('ikk_periode_id')),
                        ignoreRecord: true,
                    )
                    ->validationMessages([
                        'unique' => 'Target IKK untuk periode dan tahun ini sudah pernah dibuat.',
                    ]),

                TextInput::make('nilai_target')
                    ->label('Nilai Target')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }
}