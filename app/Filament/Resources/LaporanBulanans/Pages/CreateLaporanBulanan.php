<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\AngkaKinerja;
use App\Models\IkkKomponen;
use App\Models\RincianKerjasama;
use App\Models\RincianNSPK;
use App\Models\RincianPelatihan;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanBulanan extends CreateRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Semua laporan baru dimulai dari draft.
        $data['status'] = 'draft';

        // Tetapkan PIC dan unit dari akun login.
        if (auth()->user()?->role === 'pic') {
            $data['pic_id'] = auth()->id();
            $data['unit_id'] = auth()->user()->unit_id;
        }

        // Detail disimpan pada tabel masing-masing.
        unset($data['detail_data']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $kodeIkk = strtoupper(
            str_replace(
                [' ', '_'],
                '',
                (string) $this->record->ikk?->kode_ikk
            )
        );

        $detail = $this->data['detail_data'] ?? [];

        /*
         * IKK 20 — Detail Pelatihan
         */
        if ($kodeIkk === 'IKK20') {
            $pelatihan = $detail['pelatihan'] ?? [];

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
         * IKK 21 — Detail NSPK
         */
        if ($kodeIkk === 'IKK21') {
            $nspk = $detail['nspk'] ?? [];

            foreach ($nspk as $item) {
                RincianNSPK::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'jenis_nspk' => $item['jenis_nspk'],
                    'nama_nspk' => $item['nama_nspk'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        }

        /*
         * IKK 24 — Komponen A dan B
         */
        if ($kodeIkk === 'IKK24') {
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

                /*
         * IKK 28 — Daftar Judul Kerja Sama dan Komponen A/B
         */
        if ($kodeIkk === 'IKK28') {
            // Simpan daftar judul kerja sama.
            $kerjasama = $detail['kerjasama'] ?? [];

            foreach ($kerjasama as $item) {
                RincianKerjasama::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'judul_kerjasama' => $item['judul_kerjasama'],
                ]);
            }

            // Ambil konfigurasi komponen dari database.
            $komponenA = IkkKomponen::query()
                ->where('ikk_id', $this->record->ikk_id)
                ->where('kode_komponen', 'A')
                ->first();

            $komponenB = IkkKomponen::query()
                ->where('ikk_id', $this->record->ikk_id)
                ->where('kode_komponen', 'B')
                ->first();

            // Simpan angka Komponen A.
            if ($komponenA) {
                AngkaKinerja::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'ikk_komponen_id' => $komponenA->id,
                    'pembilang' => $detail['komponen_a_pembilang'] ?? 0,
                    'penyebut' => $detail['komponen_a_penyebut'] ?? 0,
                ]);
            }

            // Simpan angka Komponen B.
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