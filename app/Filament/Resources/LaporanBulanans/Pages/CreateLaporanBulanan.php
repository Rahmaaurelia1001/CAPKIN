<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\RincianPelatihan;
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
        $pelatihan = $this->data['detail_data']['pelatihan'] ?? [];

        if (! $this->record->ikk || $this->record->ikk->kode_ikk !== 'IKK20') {
            return;
        }

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
}