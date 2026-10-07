<?php

namespace Database\Seeders;

use App\Models\Ikk;
use App\Models\IkkKomponen;
use Illuminate\Database\Seeder;

class IkkKomponenSeeder extends Seeder
{
    public function run(): void
    {
        $komponen = [
            'IKK 24' => [
                [
                    'kode_komponen' => 'A',
                    'nama_komponen' => 'Komponen A',
                    'bobot' => 50,
                    'sumber_pembilang' => 'jumlah_unit_yang_ditetapkan',
                    'sumber_penyebut' => 'target_unit',
                ],
                [
                    'kode_komponen' => 'B',
                    'nama_komponen' => 'Komponen B',
                    'bobot' => 50,
                    'sumber_pembilang' => 'jumlah_layanan_yang_dilaksanakan',
                    'sumber_penyebut' => 'target_layanan',
                ],
            ],

            'IKK 28' => [
                [
                    'kode_komponen' => 'A',
                    'nama_komponen' => 'Kerja sama yang ditindaklanjuti',
                    'bobot' => 40,
                    'sumber_pembilang' => 'a2_jumlah_kerja_sama_ditindaklanjuti',
                    'sumber_penyebut' => 'a3_jumlah_seluruh_kerja_sama_berlaku',
                ],
                [
                    'kode_komponen' => 'B',
                    'nama_komponen' => 'Kerja sama yang disusun',
                    'bobot' => 60,
                    'sumber_pembilang' => 'b2_jumlah_kerja_sama_disusun',
                    'sumber_penyebut' => 'b3_target_kerja_sama_disusun',
                ],
            ],
        ];

        foreach ($komponen as $kodeIkk => $items) {
            $ikk = Ikk::where('kode_ikk', $kodeIkk)->firstOrFail();

            foreach ($items as $item) {
                IkkKomponen::updateOrCreate(
                    [
                        'ikk_id' => $ikk->id,
                        'kode_komponen' => $item['kode_komponen'],
                    ],
                    [
                        'nama_komponen' => $item['nama_komponen'],
                        'bobot' => $item['bobot'],
                        'sumber_pembilang' => $item['sumber_pembilang'],
                        'sumber_penyebut' => $item['sumber_penyebut'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}