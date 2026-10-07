<?php

namespace App\Filament\Resources\LaporanBulanans\Tables;

use App\Models\LaporanBulanan;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LaporanBulanansTable
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
                    ->label('Indikator Kinerja')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('unit.nama')
                    ->label('Unit Kerja')
                    ->searchable(),

                TextColumn::make('pic.name')
                    ->label('PIC')
                    ->searchable(),

                TextColumn::make('bulan')
                    ->label('Bulan')
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
                    ][$state] ?? $state)
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('realisasi')
                    ->label('Realisasi')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('bukti_dukung')
                    ->label('Bukti Dukung')
                    ->placeholder('Belum diunggah'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'pic'
                        && $record->pic_id === auth()->id()
                        && $record->status === 'draft'
                    ),

                Action::make('ajukan')
                    ->label('Ajukan Laporan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Laporan')
                    ->modalDescription('Setelah diajukan, laporan tidak dapat diedit oleh PIC.')
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'pic'
                        && $record->pic_id === auth()->id()
                        && $record->status === 'draft'
                    )
                    ->action(function (LaporanBulanan $record): void {
                        $record->update([
                            'status' => 'diajukan',
                        ]);

                        Notification::make()
                            ->title('Laporan berhasil diajukan')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}