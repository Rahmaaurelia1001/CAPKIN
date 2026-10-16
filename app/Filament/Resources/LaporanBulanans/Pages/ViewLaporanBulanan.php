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
                        ->label('Realisasi Laporan')
                        ->state(function ($record): string {
                            $ikk = $record->ikk;
                            if (! $ikk) return '0';

                            $kodeIkk = strtoupper(str_replace([' ', '_', '-'], '', (string) $ikk->kode_ikk));

                            if ($kodeIkk === 'IKK20') {
                                $totalPeserta = (int) $record->rincianPelatihans()->sum('jumlah_peserta');
                                return number_format($totalPeserta, 0, ',', '.') . ' Orang';
                            }

                            if ($kodeIkk === 'IKK21') {
                                $totalNspk = (int) $record->rincianNSPKs()->count();
                                return number_format($totalNspk, 0, ',', '.') . ' Dokumen';
                            }

                            if ($ikk->jenis_perhitungan === 'gabungan') {
                                $angka = $record->angkaKinerjas()->with('ikkKomponen')->get();
                                $capaian = 0;
                                foreach ($angka as $item) {
                                    if ($item->penyebut > 0 && $item->ikkKomponen) {
                                        $capaian += ((float) $item->pembilang / (float) $item->penyebut) * 100 * ((float) $item->ikkKomponen->bobot / 100);
                                    }
                                }
                                return number_format($capaian, 2, ',', '.') . '%';
                            }

                            $satuan = $ikk->satuan ?: '';
                            return number_format((float) $record->realisasi, 2, ',', '.') . ($satuan ? " {$satuan}" : '');
                        }),

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
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK20'
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
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK21'
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

            Section::make('Detail Komponen Akuntabilitas — IKK 24')
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK24'
                )
                ->schema([
                    RepeatableEntry::make('angkaKinerjas')
                        ->label('')
                        ->schema([
                            TextEntry::make('ikkKomponen.nama_komponen')->label('Komponen'),
                            TextEntry::make('ikkKomponen.bobot')->label('Bobot')->formatStateUsing(fn ($state) => "{$state}%"),
                            TextEntry::make('pembilang')->label('Realisasi (Pembilang)'),
                            TextEntry::make('penyebut')->label('Target (Penyebut)'),
                            TextEntry::make('capaian_komponen')
                                ->label('Capaian Komponen')
                                ->state(fn ($record) => (float) $record->penyebut > 0
                                    ? number_format(((float) $record->pembilang / (float) $record->penyebut) * 100, 2, ',', '.') . '%'
                                    : '0%'),
                        ])
                        ->columns(3),
                ]),

            Section::make('Detail Kerja Sama — IKK 28')
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK28'
                )
                ->schema([
                    RepeatableEntry::make('angkaKinerjas')
                        ->label('Komponen Berbobot')
                        ->schema([
                            TextEntry::make('ikkKomponen.nama_komponen')->label('Komponen'),
                            TextEntry::make('ikkKomponen.bobot')->label('Bobot')->formatStateUsing(fn ($state) => "{$state}%"),
                            TextEntry::make('pembilang')->label('Realisasi (Pembilang)'),
                            TextEntry::make('penyebut')->label('Target (Penyebut)'),
                            TextEntry::make('capaian_komponen')
                                ->label('Capaian Komponen')
                                ->state(fn ($record) => (float) $record->penyebut > 0
                                    ? number_format(((float) $record->pembilang / (float) $record->penyebut) * 100, 2, ',', '.') . '%'
                                    : '0%'),
                        ])
                        ->columns(3),

                    RepeatableEntry::make('rincianKerjasamas')
                        ->label('Daftar Judul Kerja Sama')
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

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(LaporanBulananResource::getUrl('index')),

            \Filament\Actions\Action::make('ajukan')
                ->label('Ajukan Laporan')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Laporan')
                ->modalDescription('Setelah diajukan, laporan tidak dapat diedit lagi oleh PIC.')
                ->visible(fn (): bool =>
                    auth()->user()?->role === 'pic'
                    && (int) $this->record->pic_id === (int) auth()->id()
                    && in_array($this->record->status, ['draft', 'perlu_revisi'], true)
                )
                ->action(function (): void {
                    app(\App\Services\PersetujuanLaporanService::class)->ajukan($this->record);
                    \Filament\Notifications\Notification::make()->title('Laporan berhasil diajukan')->success()->send();
                }),

            \Filament\Actions\Action::make('setujui')
                ->label(fn (): string => auth()->user()?->role === 'kapus' ? 'Setujui (Final)' : 'Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->canUserApprove())
                ->action(function (): void {
                    $tahap = match (auth()->user()?->role) {
                        'kabid_kabag' => 2,
                        'katim_umper' => 3,
                        'kabag_umum' => 4,
                        'kapus' => 5,
                        default => null,
                    };
                    if ($tahap) {
                        app(\App\Services\PersetujuanLaporanService::class)->setujui($this->record, $tahap);
                        \Filament\Notifications\Notification::make()->title('Laporan berhasil disetujui')->success()->send();
                    }
                }),

            \Filament\Actions\Action::make('kembalikan')
                ->label('Kembalikan untuk Revisi')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Revisi')
                        ->required()
                        ->maxLength(2000),
                ])
                ->visible(fn (): bool => $this->canUserReturn())
                ->action(function (array $data): void {
                    $tahap = match (auth()->user()?->role) {
                        'kabid_kabag' => 2,
                        'katim_umper' => 3,
                        'kabag_umum' => 4,
                        default => null,
                    };
                    if ($tahap) {
                        app(\App\Services\PersetujuanLaporanService::class)->kembalikan($this->record, $tahap, $data['catatan']);
                        \Filament\Notifications\Notification::make()->title('Laporan dikembalikan untuk revisi')->warning()->send();
                    }
                }),
        ];
    }

    private function canUserApprove(): bool
    {
        $user = auth()->user();
        if (! $user) return false;

        $r = $this->record;

        if ($user->role === 'kabid_kabag') {
            return (int) $r->unit_id === (int) $user->unit_id && (int) $r->unit_id !== 6 && $r->status === 'diajukan';
        }

        if ($user->role === 'katim_umper') {
            return ((int) $r->unit_id === 6 && $r->status === 'diajukan') || ((int) $r->unit_id !== 6 && $r->status === 'disetujui_kabid');
        }

        if ($user->role === 'kabag_umum') {
            return $r->status === 'disetujui_katim_umper';
        }

        if ($user->role === 'kapus') {
            return $r->status === 'disetujui_kabag_umum';
        }

        return false;
    }

    private function canUserReturn(): bool
    {
        $user = auth()->user();
        if (! $user || $user->role === 'kapus') return false;

        return $this->canUserApprove();
    }
}

