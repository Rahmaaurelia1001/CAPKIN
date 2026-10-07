<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'pic' => 'PIC',
                        'kabid_kabag' => 'Kabid / Kabag',
                        'katim_umper' => 'Katim Umper',
                        'kabag_umum' => 'Kabag Umum',
                        'kapus' => 'Kapus',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('unit.nama')
                    ->label('Unit Kerja')
                    ->placeholder('-')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'admin' => 'Admin',
                        'pic' => 'PIC',
                        'kabid_kabag' => 'Kabid / Kabag',
                        'katim_umper' => 'Katim Umper',
                        'kabag_umum' => 'Kabag Umum',
                        'kapus' => 'Kapus',
                    ]),
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