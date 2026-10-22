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
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ikk.nama_ikk')
                    ->label('Indikator Kinerja')
                    ->description(fn (LaporanBulanan $record): string => 
                        ($record->unit?->nama ?? 'Unit') . ' · PIC: ' . ($record->pic?->name ?? '-')
                    )
                    ->wrap()
                    ->searchable(),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->state(fn (LaporanBulanan $record): string => [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ][$record->bulan] . ' ' . $record->tahun)
                    ->icon('heroicon-o-calendar')
                    ->sortable(query: fn ($query, string $direction) => 
                        $query->orderBy('tahun', $direction)->orderBy('bulan', $direction)
                    ),

                TextColumn::make('realisasi')
                    ->label('Realisasi Kinerja')
                    ->state(function (LaporanBulanan $record): string {
                        $hasil = app(KinerjaCalculator::class)->hitung(
                            $record->ikk_id,
                            $record->bulan,
                            $record->tahun
                        );
                        $satuan = $record->ikk?->satuan ?? '';
                        $nilai = number_format($hasil['realisasi_bulanan'] ?? 0, 2, ',', '.');
                        
                        return "{$nilai} {$satuan}";
                    })
                    ->description(function (LaporanBulanan $record): string {
                        $hasil = app(KinerjaCalculator::class)->hitung(
                            $record->ikk_id,
                            $record->bulan,
                            $record->tahun
                        );
                        $capaian = number_format($hasil['capaian_kumulatif'] ?? 0, 1, ',', '.');
                        return "Capaian: {$capaian}%";
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function (LaporanBulanan $record): string {
                        $isUmper = (int) $record->unit_id === 6;
                        return match ($record->status) {
                            'final' => 'Final',
                            'perlu_revisi' => 'Perlu Revisi',
                            'draft' => 'Draf',
                            'diajukan' => $isUmper ? 'Menunggu Katim UMPer' : 'Menunggu Kabid',
                            'disetujui_kabid' => 'Menunggu Katim UMPer',
                            'disetujui_katim_umper' => 'Menunggu Kabag Umum',
                            'disetujui_kabag_umum' => 'Menunggu Kapus',
                            default => ucwords(str_replace('_', ' ', $record->status)),
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'final' => 'success',
                        'perlu_revisi' => 'danger',
                        'draft' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('bukti_dukung')
                    ->label('Bukti Dukung')
                    ->formatStateUsing(fn ($state) => $state ? 'Ada Dokumen' : 'Belum Ada')
                    ->icon(fn ($state) => $state ? 'heroicon-o-paper-clip' : 'heroicon-o-minus')
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->url(fn (LaporanBulanan $record) => $record->bukti_dukung ? asset('storage/' . $record->bukti_dukung) : null)
                    ->openUrlInNewTab(),

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
                    ->modalHeading('Persetujuan Final oleh Kepala Pusat')
                    ->modalDescription('Laporan ini akan disahkan sebagai final dan dikunci secara permanen.')
                    ->schema([
                        Textarea::make('catatan_kapus')
                            ->label('Catatan / Arahan Kapus untuk Kabid/Kabag (Opsional)')
                            ->placeholder('Tuliskan arahan, masukan, atau apresiasi untuk bidang terkait (opsional)...')
                            ->rows(3),
                    ])
                    ->visible(fn (LaporanBulanan $record): bool =>
                        auth()->user()?->role === 'kapus'
                        && $record->status === 'disetujui_kabag_umum'
                    )
                    ->action(function (LaporanBulanan $record, array $data): void {
                        app(PersetujuanLaporanService::class)->setujui(
                            $record, 5, $data['catatan_kapus'] ?? null
                        );

                        Notification::make()
                            ->title('Laporan berhasil disahkan sebagai Final')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
