<?php

namespace App\Filament\Resources\Ikks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class IkksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_ikk')
                    ->label('Kode IKK')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_ikk')
                    ->label('Nama Indikator Kinerja')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->placeholder('-'),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status IKK')
                    ->placeholder('Semua IKK')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
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