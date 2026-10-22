<?php

namespace App\Filament\Resources\AnggaranBulanans\Pages;

use App\Filament\Resources\AnggaranBulanans\AnggaranBulananResource;
use App\Models\AnggaranBulanan;
use App\Models\DataDukung;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAnggaranBulanans extends ListRecords
{
    protected static string $resource = AnggaranBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('unggahDokumenBulanan')
                ->label('Unggah 1 Bukti Anggaran (Per Bulan)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Unggah Dokumen Bukti Anggaran (Berlaku Semua IKK)')
                ->modalDescription('Satu dokumen ini akan otomatis ditautkan ke seluruh IKK pada periode bulan dan tahun yang dipilih.')
                ->schema([
                    Select::make('bulan')
                        ->label('Periode Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),

                    TextInput::make('tahun')
                        ->label('Periode Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),

                    FileUpload::make('file')
                        ->label('File Bukti Anggaran (SP2D / LRA / BKU)')
                        ->disk('public')
                        ->directory('bukti-anggaran-bulanan')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxSize(10240)
                        ->helperText('Format PDF, JPG/PNG, atau Excel. Maksimal 10 MB.')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $bulan = (int) $data['bulan'];
                    $tahun = (int) $data['tahun'];
                    $pathBaru = $data['file'];

                    // Tautkan ke seluruh record anggaran bulanan pada periode ini
                    $records = AnggaranBulanan::where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->get();

                    if ($records->isNotEmpty()) {
                        foreach ($records as $rec) {
                            DataDukung::updateOrCreate(
                                ['anggaran_bulanan_id' => $rec->id],
                                [
                                    'bulan' => $bulan,
                                    'tahun' => $tahun,
                                    'nama_file' => basename($pathBaru),
                                    'path_file' => $pathBaru,
                                ]
                            );
                        }
                    }

                    // Simpan juga sebagai dokumen umum periode ini
                    DataDukung::updateOrCreate(
                        ['bulan' => $bulan, 'tahun' => $tahun, 'anggaran_bulanan_id' => null, 'laporan_bulanan_id' => null],
                        [
                            'nama_file' => basename($pathBaru),
                            'path_file' => $pathBaru,
                        ]
                    );

                    Notification::make()
                        ->title("Bukti anggaran periode bulan {$bulan}/{$tahun} berhasil diunggah untuk seluruh IKK.")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Tambah Realisasi IKK'),
        ];
    }
}
