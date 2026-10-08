<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\RincianNSPK;
use App\Models\RincianPelatihan;
use Filament\Resources\Pages\EditRecord;

class EditLaporanBulanan extends EditRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $this->record->ikk?->kode_ikk)
        );

        /*
         * IKK 20
         */
        if ($kodeIkk === 'IKK20') {
            $data['detail_data'] = [
                'pelatihan' => $this->record
                    ->rincianPelatihans()
                    ->get()
                    ->map(fn (RincianPelatihan $item) => [
                        'nama_pelatihan' => $item->nama_pelatihan,
                        'sumber_dana' => $item->sumber_dana,
                        'jumlah_peserta' => $item->jumlah_peserta,
                        'jumlah_lulus' => $item->jumlah_lulus,
                        'jumlah_laki_laki' => $item->jumlah_laki_laki,
                        'jumlah_perempuan' => $item->jumlah_perempuan,
                    ])
                    ->toArray(),
            ];
        }

        /*
         * IKK 21
         */
        if ($kodeIkk === 'IKK21') {
            $data['detail_data'] = [
                'nspk' => $this->record
                    ->rincianNSPKs()
                    ->get()
                    ->map(fn (RincianNSPK $item) => [
                        'jenis_nspk' => $item->jenis_nspk,
                        'nama_nspk' => $item->nama_nspk,
                        'keterangan' => $item->keterangan,
                    ])
                    ->toArray(),
            ];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $this->record->ikk?->kode_ikk)
        );

        /*
         * IKK 20
         */
        if ($kodeIkk === 'IKK20') {
            $pelatihan = $this->data['detail_data']['pelatihan'] ?? [];

            $this->record->rincianPelatihans()->delete();

            foreach ($pelatihan as $item) {
                RincianPelatihan::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'nama_pelatihan' => $item['nama_pelatihan'] ?? null,
                    'sumber_dana' => $item['sumber_dana'] ?? null,
                    'jumlah_peserta' => $item['jumlah_peserta'] ?? 0,
                    'jumlah_lulus' => $item['jumlah_lulus'] ?? 0,
                    'jumlah_laki_laki' => $item['jumlah_laki_laki'] ?? 0,
                    'jumlah_perempuan' => $item['jumlah_perempuan'] ?? 0,
                ]);
            }

            return;
        }

        /*
         * IKK 21
         */
        if ($kodeIkk === 'IKK21') {
            $nspk = $this->data['detail_data']['nspk'] ?? [];

            $this->record->rincianNSPKs()->delete();

            foreach ($nspk as $item) {
                RincianNSPK::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'jenis_nspk' => $item['jenis_nspk'] ?? null,
                    'nama_nspk' => $item['nama_nspk'] ?? null,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        }
    }
}