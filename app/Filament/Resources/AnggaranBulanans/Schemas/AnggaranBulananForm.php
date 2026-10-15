<?php

namespace App\Filament\Resources\AnggaranBulanans\Schemas;

use App\Models\AnggaranBulanan;
use App\Models\Ikk;
use App\Models\PaguAnggaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AnggaranBulananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ikk_id')
                    ->label('IKK')
                    ->options(fn () => Ikk::query()
                        ->orderBy('kode_ikk')
                        ->get()
                        ->mapWithKeys(fn ($ikk) => [
                            $ikk->id => $ikk->kode_ikk . ' - ' . $ikk->nama_ikk,
                        ])
                        ->all())
                    ->searchable()
                    ->live()
                    ->required(),

                Select::make('bulan')
                    ->label('Bulan')
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
                    ->live()
                    ->required(),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->live()
                    ->required(),

                Select::make('pagu_anggaran_id')
                    ->label('Pagu yang Berlaku')
                    ->options(function (callable $get, ?AnggaranBulanan $record) {
                        $ikkId = $get('ikk_id');
                        $bulan = $get('bulan');
                        $tahun = $get('tahun');

                        if (! $ikkId || ! $bulan || ! $tahun) {
                            return $record?->pagu_anggaran_id
                                ? PaguAnggaran::query()
                                    ->whereKey($record->pagu_anggaran_id)
                                    ->pluck('nomor_revisi', 'id')
                                    ->all()
                                : [];
                        }

                        return PaguAnggaran::query()
                            ->where('ikk_id', $ikkId)
                            ->where('tahun', $tahun)
                            ->where('berlaku_mulai_bulan', '<=', $bulan)
                            ->orderByDesc('berlaku_mulai_bulan')
                            ->orderByDesc('nomor_revisi')
                            ->get()
                            ->unique('berlaku_mulai_bulan')
                            ->mapWithKeys(fn ($pagu) => [
                                $pagu->id => (
                                    ((int) $pagu->nomor_revisi === 0
                                        ? 'Pagu Awal'
                                        : 'Revisi ' . $pagu->nomor_revisi)
                                    . ' — Rp '
                                    . number_format((float) $pagu->nilai_pagu, 2, ',', '.')
                                    . ' (mulai '
                                    . [
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
                                    ][$pagu->berlaku_mulai_bulan]
                                    . ')'
                                ),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->live()
                    ->required(),

                TextInput::make('realisasi_anggaran')
                    ->label('Realisasi Anggaran (Rp)')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Catatan realisasi anggaran')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
