<?php

namespace App\Filament\Resources\Ikks\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $title = 'Unit Kerja Penanggung Jawab';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Unit Kerja')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Jumlah Pegawai')
                    ->badge()
                    ->color('gray'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tautkan Unit Kerja')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['nama']),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Lepaskan Unit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Lepaskan Unit Terpilih'),
                ]),
            ]);
    }
}
