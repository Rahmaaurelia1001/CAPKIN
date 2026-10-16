<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\AngkaKinerja;
use App\Models\IkkKomponen;
use App\Models\RincianKerjasama;
use App\Models\RincianNSPK;
use App\Models\RincianPelatihan;
use Filament\Resources\Pages\EditRecord;

class EditLaporanBulanan extends EditRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected static ?string $title = 'Ubah Laporan Bulanan';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(LaporanBulananResource::getUrl('index')),
        ];
    }


    public function mount(int | string $record): void
    {
    parent::mount($record);

    abort_unless(
        auth()->user()?->role !== 'pic'
            || (
                (int) $this->record->pic_id === (int) auth()->id()
                && in_array(
                    $this->record->status,
                    ['draft', 'perlu_revisi'],
                    true
                )
            ),
        403
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $this->record->ikk?->kode_ikk)
        );

        /*
         * IKK 20 — Detail Pelatihan
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
         * IKK 21 — Detail NSPK
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

        /*
         * IKK 24 — Ambil kembali nilai Komponen A dan B
         */
        if ($kodeIkk === 'IKK24') {
            $angkaA = $this->ambilAngkaKomponen('A');
            $angkaB = $this->ambilAngkaKomponen('B');

            $data['detail_data'] = [
                'komponen_a_pembilang' => $angkaA?->pembilang,
                'komponen_a_penyebut' => $angkaA?->penyebut,
                'komponen_b_pembilang' => $angkaB?->pembilang,
                'komponen_b_penyebut' => $angkaB?->penyebut,
            ];
        }

        /*
         * IKK 28 — Judul Kerja Sama dan nilai Komponen A/B
         */
        if ($kodeIkk === 'IKK28') {
            $angkaA = $this->ambilAngkaKomponen('A');
            $angkaB = $this->ambilAngkaKomponen('B');

            $data['detail_data'] = [
                'kerjasama' => $this->record
                    ->rincianKerjasamas()
                    ->get()
                    ->map(fn (RincianKerjasama $item) => [
                        'judul_kerjasama' => $item->judul_kerjasama,
                    ])
                    ->toArray(),

                'komponen_a_pembilang' => $angkaA?->pembilang,
                'komponen_a_penyebut' => $angkaA?->penyebut,
                'komponen_b_pembilang' => $angkaB?->pembilang,
                'komponen_b_penyebut' => $angkaB?->penyebut,
            ];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $kodeIkk = strtoupper(
            str_replace([' ', '_'], '', (string) $this->record->ikk?->kode_ikk)
        );

        $detail = $this->data['detail_data'] ?? [];

        /*
         * IKK 20 — Perbarui Detail Pelatihan
         */
        if ($kodeIkk === 'IKK20') {
            $pelatihan = $detail['pelatihan'] ?? [];

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
         * IKK 21 — Perbarui Detail NSPK
         */
        if ($kodeIkk === 'IKK21') {
            $nspk = $detail['nspk'] ?? [];

            $this->record->rincianNSPKs()->delete();

            foreach ($nspk as $item) {
                RincianNSPK::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'jenis_nspk' => $item['jenis_nspk'] ?? null,
                    'nama_nspk' => $item['nama_nspk'] ?? null,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            return;
        }

        /*
         * IKK 24 — Perbarui Komponen A dan B
         */
        if ($kodeIkk === 'IKK24') {
            $this->record->angkaKinerjas()->delete();

            $this->simpanAngkaKomponen(
                'A',
                $detail['komponen_a_pembilang'] ?? 0,
                $detail['komponen_a_penyebut'] ?? 0,
            );

            $this->simpanAngkaKomponen(
                'B',
                $detail['komponen_b_pembilang'] ?? 0,
                $detail['komponen_b_penyebut'] ?? 0,
            );

            return;
        }

        /*
         * IKK 28 — Perbarui Judul Kerja Sama dan Komponen A/B
         */
        if ($kodeIkk === 'IKK28') {
            $kerjasama = $detail['kerjasama'] ?? [];

            $this->record->rincianKerjasamas()->delete();

            foreach ($kerjasama as $item) {
                RincianKerjasama::create([
                    'laporan_bulanan_id' => $this->record->id,
                    'judul_kerjasama' => $item['judul_kerjasama'] ?? '',
                ]);
            }

            $this->record->angkaKinerjas()->delete();

            $this->simpanAngkaKomponen(
                'A',
                $detail['komponen_a_pembilang'] ?? 0,
                $detail['komponen_a_penyebut'] ?? 0,
            );

            $this->simpanAngkaKomponen(
                'B',
                $detail['komponen_b_pembilang'] ?? 0,
                $detail['komponen_b_penyebut'] ?? 0,
            );
        }
    }

    /**
     * Ambil angka kinerja berdasarkan kode komponen A atau B.
     */
    private function ambilAngkaKomponen(string $kodeKomponen): ?AngkaKinerja
    {
        $komponenId = IkkKomponen::query()
            ->where('ikk_id', $this->record->ikk_id)
            ->where('kode_komponen', $kodeKomponen)
            ->value('id');

        if (! $komponenId) {
            return null;
        }

        return $this->record
            ->angkaKinerjas()
            ->where('ikk_komponen_id', $komponenId)
            ->first();
    }

    /**
     * Simpan angka kinerja ke komponen yang sesuai.
     */
    private function simpanAngkaKomponen(
        string $kodeKomponen,
        mixed $pembilang,
        mixed $penyebut,
    ): void {
        $komponen = IkkKomponen::query()
            ->where('ikk_id', $this->record->ikk_id)
            ->where('kode_komponen', $kodeKomponen)
            ->first();

        if (! $komponen) {
            return;
        }

        AngkaKinerja::create([
            'laporan_bulanan_id' => $this->record->id,
            'ikk_komponen_id' => $komponen->id,
            'pembilang' => $pembilang,
            'penyebut' => $penyebut,
        ]);
    }
}