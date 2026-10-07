<?php

namespace Database\Seeders;

use App\Models\Ikk;
use App\Models\SasaranKegiatan;
use Illuminate\Database\Seeder;

class SasaranKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $sasaranKegiatans = [
            'SK 8' => 'Meningkatnya Pengembangan Sumber Daya Manusia Aparatur Perhubungan',
            'SK 9' => 'Terwujudnya Organisasi yang Agile dan SDM Unggul',
            'SK 10' => 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima',
            'SK 12' => 'Meningkatnya Kapabilitas Kerjasama dan Kemitraan BPSDM Perhubungan',
        ];

        foreach ($sasaranKegiatans as $kode => $nama) {
            SasaranKegiatan::updateOrCreate(
                ['kode' => $kode],
                [
                    'nama' => $nama,
                    'is_active' => true,
                ]
            );
        }

        $mappingIkk = [
            'IKK 20' => [
                'sk' => 'SK 8',
                'jenis' => 'tunggal',
            ],
            'IKK 21' => [
                'sk' => 'SK 8',
                'jenis' => 'tunggal',
            ],
            'IKK 22' => [
                'sk' => 'SK 9',
                'jenis' => 'nilai',
            ],
            'IKK 24' => [
                'sk' => 'SK 10',
                'jenis' => 'gabungan',
            ],
            'IKK 25' => [
                'sk' => 'SK 12',
                'jenis' => 'nilai',
            ],
            'IKK 26' => [
                'sk' => 'SK 12',
                'jenis' => 'nilai',
            ],
            'IKK 28' => [
                'sk' => 'SK 12',
                'jenis' => 'gabungan',
            ],
        ];

        foreach ($mappingIkk as $kodeIkk => $data) {
            $sk = SasaranKegiatan::where('kode', $data['sk'])->firstOrFail();

            Ikk::where('kode_ikk', $kodeIkk)->update([
                'sasaran_kegiatan_id' => $sk->id,
                'jenis_perhitungan' => $data['jenis'],
            ]);
        }
    }
}