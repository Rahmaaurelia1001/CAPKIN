<?php

namespace App\Filament\Resources\AnggaranBulanans\Tables;

use App\Models\DataDukung;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AnggaranBulanansTable
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
                    ][$state] ?? '-')
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('paguAnggaran.nomor_revisi')
                    ->label('Revisi Pagu')
                    ->formatStateUsing(
                        fn ($state) => (int) $state === 0
                            ? 'Pagu Awal'
                            : 'Revisi ' . $state
                    ),

                TextColumn::make('paguAnggaran.nilai_pagu')
                    ->label('Nilai Pagu')
                    ->formatStateUsing(
                        fn ($state) => $state === null
                            ? '-'
                            : 'Rp ' . number_format(
                                (float) $state,
                                2,
                                ',',
                                '.'
                            )
                    ),

                TextColumn::make('realisasi_anggaran')
                    ->label('Realisasi Anggaran')
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format(
                            (float) $state,
                            2,
                            ',',
                            '.'
                        )
                    )
                    ->sortable(),

                TextColumn::make('dataDukung.nama_file')
                    ->label('Bukti Dukung')
                    ->placeholder('Belum diunggah')
                    ->limit(30),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tahun', 'desc')
            ->recordActions([
                Action::make('unggahBukti')
                    ->label('Unggah/Ganti Bukti')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(
                        fn () => auth()->user()?->role === 'katim_umper'
                    )
                    ->form([
                        FileUpload::make('file')
                            ->label('File Bukti Dukung')
                            ->disk('public')
                            ->directory('bukti-anggaran')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        abort_unless(
                            auth()->user()?->role === 'katim_umper',
                            403
                        );

                        $pathBaru = $data['file'];

                        $buktiLama = DataDukung::query()
                            ->where('anggaran_bulanan_id', $record->id)
                            ->first();

                        $pathLama = $buktiLama?->path_file;

                        DataDukung::updateOrCreate(
                            [
                                'anggaran_bulanan_id' => $record->id,
                            ],
                            [
                                'laporan_bulanan_id' => null,
                                'bulan' => $record->bulan,
                                'tahun' => $record->tahun,
                                'nama_file' => basename($pathBaru),
                                'path_file' => $pathBaru,
                            ]
                        );

                        if ($pathLama && $pathLama !== $pathBaru) {
                            Storage::disk('public')->delete($pathLama);
                        }
                    })
                    ->successNotificationTitle(
                        'Bukti dukung berhasil disimpan.'
                    ),

                Action::make('lihatBukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->visible(
                        fn ($record) => filled(
                            $record->dataDukung?->path_file
                        )
                    )
                    ->url(
                        fn ($record) => Storage::disk('public')->url(
                            $record->dataDukung->path_file
                        )
                    )
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->label('Ubah'),
            ]);
    }
}
