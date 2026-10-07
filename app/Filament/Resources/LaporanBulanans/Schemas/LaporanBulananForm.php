<?php

namespace App\Filament\Resources\LaporanBulanans\Schemas;

use App\Models\Ikk;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;


class LaporanBulananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ikk_id')
                    ->label('Indikator Kinerja (IKK)')
                    ->relationship(
                        name: 'ikk',
                        titleAttribute: 'nama_ikk',
                        modifyQueryUsing: function ($query) {
                            $query->where('is_active', true);

                            if (auth()->user()?->role === 'pic') {
                                $query->whereHas('picUsers', function ($q) {
                                    $q->where('users.id', auth()->id());
                                });
                            }
                        }
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Ikk $record): string =>
                            "{$record->kode_ikk} - {$record->nama_ikk}"
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Select::make('unit_id')
                    ->label('Unit Kerja')
                    ->relationship(
                        name: 'unit',
                        titleAttribute: 'nama',
                        modifyQueryUsing: function ($query) {
                            if (auth()->user()?->role === 'pic') {
                                $query->whereKey(auth()->user()->unit_id);
                            }
                        }
                    )
                    ->default(fn () => auth()->user()?->unit_id)
                    ->disabled(fn () => auth()->user()?->role === 'pic')
                    ->dehydrated()
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('pic_id')
                    ->label('PIC')
                    ->relationship(
                        name: 'pic',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            $query->where('role', 'pic');

                            if (auth()->user()?->role === 'pic') {
                                $query->whereKey(auth()->id());
                            }
                        }
                    )
                    ->default(fn () => auth()->id())
                    ->disabled(fn () => auth()->user()?->role === 'pic')
                    ->dehydrated()
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('bulan')
                    ->label('Periode - Bulan')
                    ->options([
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
                    ])
                    ->required(),

                TextInput::make('tahun')
                    ->label('Periode - Tahun')
                    ->numeric()
                    ->integer()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->required(),

                Repeater::make('detail_data.pelatihan')
                    ->label('Detail Pelatihan IKK 20')
                    ->helperText('Isi data untuk setiap pelatihan, bukan data per orang.')
                    ->schema([
                        TextInput::make('nama_pelatihan')
                            ->label('Nama Pelatihan')
                            ->required()
                            ->maxLength(255),

                        Select::make('sumber_dana')
                            ->label('Sumber Dana')
                            ->options([
                                'PNBP' => 'PNBP',
                                'RM' => 'RM',
                            ])
                            ->required(),

                        TextInput::make('jumlah_peserta')
                            ->label('Jumlah Peserta')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('jumlah_lulus')
                            ->label('Jumlah Lulus')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('jumlah_laki_laki')
                            ->label('Jumlah Laki-laki')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('jumlah_perempuan')
                            ->label('Jumlah Perempuan')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Pelatihan')
                            ->visible(fn (Get $get): bool => self::isIkk20($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk20($get('ikk_id')))
                            ->dehydrated(fn (Get $get): bool => self::isIkk20($get('ikk_id')))
                            ->columnSpanFull(),
                        Textarea::make('detail_data.norma')
                            ->label('Norma')
                            ->placeholder('Masukkan uraian norma')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => self::isIkk21($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk21($get('ikk_id'))),

                        Textarea::make('detail_data.standar')
                            ->label('Standar')
                            ->placeholder('Masukkan uraian standar')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => self::isIkk21($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk21($get('ikk_id'))),

                        Textarea::make('detail_data.pedoman')
                            ->label('Pedoman')
                            ->placeholder('Masukkan uraian pedoman')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => self::isIkk21($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk21($get('ikk_id'))),

                        Textarea::make('detail_data.kriteria')
                            ->label('Kriteria')
                            ->placeholder('Masukkan uraian kriteria')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => self::isIkk21($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk21($get('ikk_id')))
                            ->columnSpanFull(),

                        
                        TextInput::make('detail_data.komponen_a')
                            ->label('Komponen A — Dokumen Rencana Kerja')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (Get $get): bool => self::isIkk24($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk24($get('ikk_id'))),

                        TextInput::make('detail_data.komponen_b')
                            ->label('Komponen B — Dokumen Evaluasi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (Get $get): bool => self::isIkk24($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk24($get('ikk_id'))),

                        TextInput::make('detail_data.komponen_c')
                            ->label('Komponen C — Laporan Realisasi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (Get $get): bool => self::isIkk24($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk24($get('ikk_id'))),

                        TextInput::make('detail_data.judul_kerjasama')
                            ->label('Judul Kerja Sama')
                            ->placeholder('Masukkan judul kerja sama')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => self::isIkk28($get('ikk_id')))
                            ->required(fn (Get $get): bool => self::isIkk28($get('ikk_id')))
                            ->columnSpanFull(),

                TextInput::make('realisasi')
                ->label(fn (Get $get): string => self::realisasiLabel($get('ikk_id')))
                ->numeric()
                ->minValue(0)
                ->maxValue(fn (Get $get): ?float => self::realisasiMaksimum($get('ikk_id')))
                ->suffix(fn (Get $get): ?string => self::realisasiSatuan($get('ikk_id')))
                ->helperText(fn (Get $get): ?string => self::realisasiBantuan($get('ikk_id')))
                ->required(),

                FileUpload::make('bukti_dukung')
                    ->label('Bukti Pendukung')
                    ->helperText('Format PDF atau JPG/PNG. Maksimal 10 MB.')
                    ->disk('public')
                    ->directory('bukti-dukung')
                    ->visibility('private')
                    ->maxSize(10240)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                    ])
                    ->required(),

                Textarea::make('catatan')
                    ->label('Catatan (Opsional)')
                    ->placeholder('Deskripsi singkat pelaksanaan...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    private static function isIkk20(mixed $ikkId): bool
    {
        if (! $ikkId) {
            return false;
        }

        $kodeIkk = Ikk::query()
            ->whereKey($ikkId)
            ->value('kode_ikk');

        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $kodeIkk)
        );

        return in_array($kodeIkk, ['IKK-20', 'IKK20', '20'], true);
    }

    private static function isIkk21(mixed $ikkId): bool
    {
    if (! $ikkId) {
        return false;
    }

    $kodeIkk = Ikk::query()
        ->whereKey($ikkId)
        ->value('kode_ikk');

    $kodeIkk = strtoupper(
        str_replace([' ', '_'], '', (string) $kodeIkk)
    );

    return in_array($kodeIkk, ['IKK-21', 'IKK21', '21'], true);
    }

    private static function realisasiLabel(mixed $ikkId): string
{
    $ikk = $ikkId ? Ikk::find($ikkId) : null;

    $satuan = $ikk?->satuan ?: 'Nilai';

    return "Realisasi Output Fisik ({$satuan})";
}

private static function realisasiMaksimum(mixed $ikkId): ?float
{
    $ikk = $ikkId ? Ikk::find($ikkId) : null;

    if (! $ikk) {
        return null;
    }

    if (str_contains(
        strtolower((string) $ikk->satuan),
        'persen'
    )) {
        return 100;
    }

    return (float) $ikk->target_tahunan;
}

private static function realisasiSatuan(mixed $ikkId): ?string
{
    $ikk = $ikkId ? Ikk::find($ikkId) : null;

    if (! $ikk) {
        return null;
    }

    return str_contains(
        strtolower((string) $ikk->satuan),
        'persen'
    ) ? '%' : null;
}

private static function realisasiBantuan(mixed $ikkId): ?string
{
    $ikk = $ikkId ? Ikk::find($ikkId) : null;

    if (! $ikk) {
        return null;
    }

    return "Nilai maksimal: {$ikk->target_tahunan} {$ikk->satuan}";
}

private static function isIkk24(mixed $ikkId): bool
{
    if (! $ikkId) {
        return false;
    }

    $kodeIkk = Ikk::query()
        ->whereKey($ikkId)
        ->value('kode_ikk');

    $kodeIkk = strtoupper(
        str_replace([' ', '_'], '', (string) $kodeIkk)
    );

    return in_array($kodeIkk, ['IKK-24', 'IKK24', '24'], true);
}

private static function isIkk28(mixed $ikkId): bool
{
    if (! $ikkId) {
        return false;
    }

    $kodeIkk = Ikk::query()
        ->whereKey($ikkId)
        ->value('kode_ikk');

    $kodeIkk = strtoupper(
        str_replace([' ', '_'], '', (string) $kodeIkk)
    );

    return in_array($kodeIkk, ['IKK-28', 'IKK28', '28'], true);
}
}