<?php

namespace App\Filament\Resources\LaporanBulanans\Tables;

use App\Models\LaporanBulanan;
use App\Services\KinerjaCalculator;
use App\Services\PersetujuanLaporanService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
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
                    ->state(fn (LaporanBulanan $record): float =>
                        app(KinerjaCalculator::class)->hitung(
                            $record->ikk_id,
                            $record->bulan,
                            $record->tahun
                        )['realisasi_bulanan']
                    )
                    ->numeric(decimalPlaces: 2),

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
                Action::make('lihat_detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (LaporanBulanan $record): string =>
                        \App\Filament\Resources\LaporanBulanans\LaporanBulananResource::getUrl(
                            'view',
                            ['record' => $record]
                        )
                    ),

                EditAction::make()
                    ->label('Ubah')
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'pic'
                        && (int) $record->pic_id === (int) auth()->id()
                        && in_array($record->status, ['draft', 'perlu_revisi'], true)
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
                        && (int) $record->pic_id === (int) auth()->id()
                        && in_array($record->status, ['draft', 'perlu_revisi'], true)
                    )
                    ->action(function (LaporanBulanan $record): void {
                        app(PersetujuanLaporanService::class)->ajukan($record);

                        Notification::make()
                            ->title('Laporan berhasil diajukan')
                            ->success()
                            ->send();
                    }),

                // Tahap 2: Kabid/Kabag, khusus laporan selain unit 6.
                Action::make('setujui_kabid')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kabid_kabag'
                        && (int) $record->unit_id === (int) auth()->user()?->unit_id
                        && (int) $record->unit_id !== 6
                        && $record->status === 'diajukan'
                    )
                    ->action(function (LaporanBulanan $record): void {
                        app(PersetujuanLaporanService::class)->setujui($record, 2);

                        Notification::make()
                            ->title('Laporan berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('revisi_kabid')
                    ->label('Kembalikan untuk Revisi')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Revisi')
                            ->required()
                            ->maxLength(2000),
                    ])
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kabid_kabag'
                        && (int) $record->unit_id === (int) auth()->user()?->unit_id
                        && (int) $record->unit_id !== 6
                        && $record->status === 'diajukan'
                    )
                    ->action(function (LaporanBulanan $record, array $data): void {
                        app(PersetujuanLaporanService::class)->kembalikan(
                            $record, 2, $data['catatan']
                        );

                        Notification::make()
                            ->title('Laporan dikembalikan untuk revisi')
                            ->success()
                            ->send();
                    }),

                // Tahap 3: Katim Umper, hanya jalur reguler.
                Action::make('setujui_katim')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'katim_umper'
                        && (
                            (
                                (int) $record->unit_id === 6
                                && $record->status === 'diajukan'
                            )
                            || (
                                (int) $record->unit_id !== 6
                                && $record->status === 'disetujui_kabid'
                            )
                        )
                    )
                    ->action(function (LaporanBulanan $record): void {
                        app(PersetujuanLaporanService::class)->setujui($record, 3);

                        Notification::make()
                            ->title('Laporan berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('revisi_katim')
                    ->label('Kembalikan untuk Revisi')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Revisi')
                            ->required()
                            ->maxLength(2000),
                    ])
                   ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'katim_umper'
                        && (
                            (
                                (int) $record->unit_id === 6
                                && $record->status === 'diajukan'
                            )
                            || (
                                (int) $record->unit_id !== 6
                                && $record->status === 'disetujui_kabid'
                            )
                        )
                    )
                    ->action(function (LaporanBulanan $record, array $data): void {
                        app(PersetujuanLaporanService::class)->kembalikan(
                            $record, 3, $data['catatan']
                        );

                        Notification::make()
                            ->title('Laporan dikembalikan untuk revisi')
                            ->success()
                            ->send();
                    }),

                // Tahap 4: Kabag Umum menerima jalur reguler dan jalur unit 6.
                Action::make('setujui_kabag_umum')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kabag_umum'
                        && $record->status === 'disetujui_katim_umper'
                    )
                    ->action(function (LaporanBulanan $record): void {
                        app(PersetujuanLaporanService::class)->setujui($record, 4);

                        Notification::make()
                            ->title('Laporan berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('revisi_kabag_umum')
                    ->label('Kembalikan untuk Revisi')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Revisi')
                            ->required()
                            ->maxLength(2000),
                    ])
                   ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kabag_umum'
                        && $record->status === 'disetujui_katim_umper'
                    )

                    ->action(function (LaporanBulanan $record, array $data): void {
                        app(PersetujuanLaporanService::class)->kembalikan(
                            $record, 4, $data['catatan']
                        );

                        Notification::make()
                            ->title('Laporan dikembalikan untuk revisi')
                            ->success()
                            ->send();
                    }),

                // Tahap 5: Kapus menyetujui laporan akhir.
                Action::make('setujui_kapus')
                    ->label('Setujui Final')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kapus'
                        && $record->status === 'disetujui_kabag_umum'
                    )
                    ->action(function (LaporanBulanan $record): void {
                        app(PersetujuanLaporanService::class)->setujui($record, 5);

                        Notification::make()
                            ->title('Laporan ditetapkan sebagai final')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
