<?php

namespace App\Filament\Resources\PaguAnggarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaguAnggaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ikk.kode_ikk')
                    ->label('Kode IKK')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ikk.nama_ikk')
                    ->label('Nama IKK')
                    ->searchable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('nomor_revisi')
                    ->label('Nomor Revisi')
                    ->formatStateUsing(fn ($state) => (int) $state === 0
                        ? 'Pagu Awal'
                        : 'Revisi ' . $state)
                    ->sortable(),

                TextColumn::make('nilai_pagu')
                    ->label('Nilai Pagu')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format(
                        (float) $state,
                        2,
                        ',',
                        '.'
                    ))
                    ->sortable(),

                TextColumn::make('berlaku_mulai_bulan')
                    ->label('Mulai Berlaku')
                    ->formatStateUsing(fn ($state) => [
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
                    ][$state] ?? '-')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tahun', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih'),
                ]),
            ]);
    }
}