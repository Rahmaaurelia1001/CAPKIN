<?php

namespace App\Filament\Resources\TargetIkks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TargetIkksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ikkPeriode.ikk.kode_ikk')
                    ->label('Kode IKK')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ikkPeriode.ikk.nama_ikk')
                    ->label('Nama Indikator Kinerja')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('nilai_target')
                    ->label('Nilai Target')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}