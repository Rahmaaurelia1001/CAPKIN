<?php

namespace App\Filament\Resources\Ikks\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PicsRelationManager extends RelationManager
{
    protected static string $relationship = 'picUsers';

    protected static ?string $title = 'PIC Penanggung Jawab';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama PIC')
                    ->searchable(),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambahkan PIC')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email', 'nip']),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Lepaskan PIC'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Lepaskan PIC terpilih'),
                ]),
            ]);
    }
}