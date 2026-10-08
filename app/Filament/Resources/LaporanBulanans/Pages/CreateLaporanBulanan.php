<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\RincianNSPK;
use App\Models\RincianPelatihan;
use App\Models\AngkaKinerja;
use App\Models\IkkKomponen;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanBulanan extends CreateRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Semua laporan baru dimulai dari draft.
        $data['status'] = 'draft';

        // Jika yang membuat adalah PIC, tetapkan PIC dan unit dari akun login.
        if (auth()->user()?->role === 'pic') {
            $data['pic_id'] = auth()->id();
            $data['unit_id'] = auth()->user()->unit_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $this->record->ikk?->kode_ikk)
        );

        /*
         * IKK 20
         */
        if ($kodeIkk === 'IKK20') {
            $pelatihan = $this->data['detail_data']['pelatihan'] ?? [];

            foreach ($pelatihan as $item) {
                RincianPelatihan::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'nama_pelatihan' => $item['nama_pelatihan'],
                    'sumber_dana' => $item['sumber_dana'],
                    'jumlah_peserta' => $item['jumlah_peserta'],
                    'jumlah_lulus' => $item['jumlah_lulus'],
                    'jumlah_laki_laki' => $item['jumlah_laki_laki'],
                    'jumlah_perempuan' => $item['jumlah_perempuan'],
                ]);
            }
        }

        /*
         * IKK 21
         */
                /*
         * IKK 24
         */
        if ($kodeIkk === 'IKK24') {
            $detail = $this->data['detail_data'] ?? [];

            $komponenA = IkkKomponen::query()
                ->where('ikk_id', $this->record->ikk_id)
                ->where('kode_komponen', 'A')
                ->first();

            $komponenB = IkkKomponen::query()
                ->where('ikk_id', $this->record->ikk_id)
                ->where('kode_komponen', 'B')
                ->first();

            if ($komponenA) {
                AngkaKinerja::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'ikk_komponen_id' => $komponenA->id,
                    'pembilang' => $detail['komponen_a_pembilang'] ?? 0,
                    'penyebut' => $detail['komponen_a_penyebut'] ?? 0,
                ]);
            }

            if ($komponenB) {
                AngkaKinerja::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'ikk_komponen_id' => $komponenB->id,
                    'pembilang' => $detail['komponen_b_pembilang'] ?? 0,
                    'penyebut' => $detail['komponen_b_penyebut'] ?? 0,
                ]);
            }
        }
    }
}