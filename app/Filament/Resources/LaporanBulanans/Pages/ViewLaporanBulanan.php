<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use App\Services\KinerjaCalculator;

class ViewLaporanBulanan extends ViewRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected static ?string $title = 'Detail Laporan';

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Laporan')
                ->columns(2)
                ->schema([
                    TextEntry::make('ikk.kode_ikk')
                        ->label('Kode IKK'),

                    TextEntry::make('ikk.nama_ikk')
                        ->label('Indikator Kinerja'),

                    TextEntry::make('unit.nama')
                        ->label('Unit Kerja'),

                    TextEntry::make('pic.name')
                        ->label('PIC'),

                    TextEntry::make('bulan')
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
                        ][$state] ?? $state),

                    TextEntry::make('tahun')
                        ->label('Tahun'),

                   TextEntry::make('realisasi')
                        ->label('Realisasi Bulanan')
                        ->state(function ($record): float {
                            return app(KinerjaCalculator::class)
                                ->hitungRealisasiBulanan(
                                    (int) $record->ikk_id,
                                    (int) $record->bulan,
                                    (int) $record->tahun,
                                );
                        })
                        ->numeric(decimalPlaces: 2),

                    TextEntry::make('catatan')
                        ->label('Catatan PIC')
                        ->placeholder('Tidak ada catatan')
                        ->columnSpanFull(),

                    TextEntry::make('narasi_capaian')
                        ->label('Narasi Capaian')
                        ->placeholder('Belum diisi')
                        ->columnSpanFull(),

                    TextEntry::make('kendala')
                        ->label('Kendala')
                        ->placeholder('Tidak ada kendala')
                        ->columnSpanFull(),

                    TextEntry::make('tindak_lanjut')
                        ->label('Tindak Lanjut')
                        ->placeholder('Belum diisi')
                        ->columnSpanFull(),

                    TextEntry::make('realisasi_anggaran')
                        ->label('Realisasi Anggaran')
                        ->placeholder('Belum diisi'),

                    TextEntry::make('status')
                        ->label('Status Laporan')
                        ->badge(),

                    TextEntry::make('bukti_dukung')
                    ->label('Bukti Pendukung')
                    ->formatStateUsing(fn ($state) =>
                        $state ? basename($state) : 'Belum ada bukti'
                    )
                    ->url(fn ($record) =>
                        $record->bukti_dukung
                            && Storage::disk('public')->exists($record->bukti_dukung)
                                ? Storage::disk('public')->url($record->bukti_dukung)
                                : null
                    )
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->columnSpanFull(),
                     ]),

            Section::make('Detail Pelatihan — IKK 20')
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_'], '', (string) $record->ikk?->kode_ikk)) === 'IKK20'
                )
                ->schema([
                    RepeatableEntry::make('rincianPelatihans')
                        ->label('')
                        ->schema([
                            TextEntry::make('nama_pelatihan')->label('Nama Pelatihan'),
                            TextEntry::make('sumber_dana')->label('Sumber Dana'),
                            TextEntry::make('jumlah_peserta')->label('Jumlah Peserta'),
                            TextEntry::make('jumlah_lulus')->label('Jumlah Lulus'),
                            TextEntry::make('jumlah_laki_laki')->label('Laki-laki'),
                            TextEntry::make('jumlah_perempuan')->label('Perempuan'),
                        ])
                        ->columns(2),
                ]),

            Section::make('Detail NSPK — IKK 21')
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_'], '', (string) $record->ikk?->kode_ikk)) === 'IKK21'
                )
                ->schema([
                    RepeatableEntry::make('rincianNSPKs')
                        ->label('')
                        ->schema([
                            TextEntry::make('jenis_nspk')->label('Jenis NSPK'),
                            TextEntry::make('nama_nspk')->label('Nama NSPK'),
                            TextEntry::make('keterangan')->label('Keterangan'),
                        ])
                        ->columns(2),
                ]),

            Section::make('Detail Kerja Sama — IKK 28')
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_'], '', (string) $record->ikk?->kode_ikk)) === 'IKK28'
                )
                ->schema([
                    RepeatableEntry::make('rincianKerjasamas')
                        ->label('')
                        ->schema([
                            TextEntry::make('judul_kerjasama')->label('Judul Kerja Sama'),
                        ])
                        ->columns(1),
                ]),

            Section::make('Riwayat Persetujuan')
                    ->schema([
                        RepeatableEntry::make('persetujuan')
                            ->label('')
                            ->schema([
                                TextEntry::make('tahap')
                                    ->label('Tahap'),

                                TextEntry::make('user.name')
                                    ->label('Pejabat'),

                                TextEntry::make('keputusan')
                                    ->label('Keputusan')
                                    ->badge(),

                                TextEntry::make('catatan')
                                    ->label('Catatan Pejabat')
                                    ->placeholder('Tidak ada catatan'),

                                TextEntry::make('diproses_pada')
                                    ->label('Waktu')
                                    ->dateTime('d/m/Y H:i'),
                            ])
                            ->columns(2),
                    ]),
        ]);
    }
}
