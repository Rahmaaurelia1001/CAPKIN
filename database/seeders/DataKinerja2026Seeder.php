<?php

namespace Database\Seeders;

use App\Models\AnggaranBulanan;
use App\Models\AngkaKinerja;
use App\Models\Ikk;
use App\Models\IkkKomponen;
use App\Models\IkkPeriode;
use App\Models\LaporanBulanan;
use App\Models\PaguAnggaran;
use App\Models\PersetujuanLaporan;
use App\Models\RincianKerjasama;
use App\Models\RincianNSPK;
use App\Models\RincianPelatihan;
use App\Models\TargetIkk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataKinerja2026Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $tahun = 2026;

            // -------------------------------------------------------
            // 1. Ambil User berdasarkan peran
            // -------------------------------------------------------
            $kapus      = User::where('role', 'kapus')->first();
            $kabagUmum  = User::where('role', 'kabag_umum')->first();
            $katimUmper = User::where('role', 'katim_umper')->first();

            $picAlvian   = User::where('name', 'ALVIAN GUNTUR')->first();
            $picArdian   = User::where('name', 'ARDIAN HIDAYAT')->first();
            $picIndah    = User::where('name', 'INDAH IMAYANTI')->first();
            $picSuwarman = User::where('name', 'SUWARMAN')->first();
            $picLusi     = User::where('name', 'LUSI FIBRIANI MANIK')->first();

            $kabidUnit1 = User::where('role', 'kabid_kabag')->where('unit_id', 1)->first();

            // -------------------------------------------------------
            // 2. Hubungkan IKK ke Unit Kerja (pivot ikk_unit)
            // -------------------------------------------------------
            $ikk20 = Ikk::where('kode_ikk', 'IKK 20')->first();
            $ikk21 = Ikk::where('kode_ikk', 'IKK 21')->first();
            $ikk22 = Ikk::where('kode_ikk', 'IKK 22')->first();
            $ikk24 = Ikk::where('kode_ikk', 'IKK 24')->first();
            $ikk25 = Ikk::where('kode_ikk', 'IKK 25')->first();
            $ikk26 = Ikk::where('kode_ikk', 'IKK 26')->first();
            $ikk28 = Ikk::where('kode_ikk', 'IKK 28')->first();

            if ($ikk20) $ikk20->units()->syncWithoutDetaching([1, 2, 3]);
            if ($ikk21) $ikk21->units()->syncWithoutDetaching([1, 2, 3]);
            if ($ikk22) $ikk22->units()->syncWithoutDetaching([4]);
            if ($ikk24) $ikk24->units()->syncWithoutDetaching([4]);
            if ($ikk25) $ikk25->units()->syncWithoutDetaching([5]);
            if ($ikk26) $ikk26->units()->syncWithoutDetaching([6]);
            if ($ikk28) $ikk28->units()->syncWithoutDetaching([6]);

            // -------------------------------------------------------
            // 3. Target Tahunan 2026 (Sesuai Spreadsheet)
            // -------------------------------------------------------
            $targets = [
                'IKK 20' => 7050.00,
                'IKK 21' => 9.00,
                'IKK 22' => 77.00,
                'IKK 24' => 100.00,
                'IKK 25' => 90.50,
                'IKK 26' => 78.50,
                'IKK 28' => 100.00,
            ];

            foreach ($targets as $kode => $nilai) {
                $ikk = Ikk::where('kode_ikk', $kode)->first();
                if (! $ikk) continue;

                $periode = IkkPeriode::firstOrCreate([
                    'ikk_id'      => $ikk->id,
                    'tahun_mulai' => 2025,
                ]);

                TargetIkk::updateOrCreate(
                    ['ikk_periode_id' => $periode->id, 'tahun' => $tahun],
                    ['nilai_target'   => $nilai]
                );
            }

            // -------------------------------------------------------
            // 4. Pagu Anggaran 2026 per IKK (Sesuai Spreadsheet)
            // -------------------------------------------------------
            $paguData = [
                'IKK 20' => ['pagu' => 21_456_042_000, 'revisi_bulan' => 7, 'pagu_revisi' => 21_456_042_000],
                'IKK 21' => ['pagu' =>    587_762_000, 'revisi_bulan' => null],
                'IKK 22' => ['pagu' => 12_250_911_000, 'revisi_bulan' => null],
                'IKK 24' => ['pagu' => 39_585_150_000, 'revisi_bulan' => null],
                'IKK 25' => ['pagu' =>  1_555_936_000, 'revisi_bulan' => null],
                'IKK 26' => ['pagu' =>  1_555_936_000, 'revisi_bulan' => null],
                'IKK 28' => ['pagu' =>    102_498_000, 'revisi_bulan' => null],
            ];

            $paguRefs = [];
            foreach ($paguData as $kode => $item) {
                $ikk = Ikk::where('kode_ikk', $kode)->first();
                if (! $ikk) continue;

                $paguAwal = PaguAnggaran::updateOrCreate(
                    ['ikk_id' => $ikk->id, 'tahun' => $tahun, 'nomor_revisi' => 0],
                    [
                        'nilai_pagu'          => $item['pagu'],
                        'berlaku_mulai_bulan' => 1,
                        'keterangan'          => 'Pagu DIPA 2026',
                    ]
                );
                $paguRefs[$kode]['awal'] = $paguAwal;

                if (! empty($item['revisi_bulan'])) {
                    $paguRevisi = PaguAnggaran::updateOrCreate(
                        ['ikk_id' => $ikk->id, 'tahun' => $tahun, 'nomor_revisi' => 1],
                        [
                            'nilai_pagu'          => $item['pagu_revisi'],
                            'berlaku_mulai_bulan' => $item['revisi_bulan'],
                            'keterangan'          => 'Revisi Pagu Anggaran Semester II',
                        ]
                    );
                    $paguRefs[$kode]['revisi'] = $paguRevisi;
                }
            }

            // -------------------------------------------------------
            // 5. Realisasi Anggaran Bulanan Jan s.d. Jul 2026 (Kumulatif)
            // -------------------------------------------------------
            // Hapus data anggaran tahun 2026 lama agar bersih
            AnggaranBulanan::where('tahun', $tahun)->delete();

            $realisasiBulananSemuaIKK = [
                // IKK 20
                'IKK 20' => [
                    1 => ['realisasi' => 0,             'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 510_150_000,   'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 1_658_742_245, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 5_070_024_890, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 6_567_500_000, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 7_718_000_750, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 8_758_342_645, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
                // IKK 21 (Bulan 1 belum ada nilai [-])
                'IKK 21' => [
                    1 => ['realisasi' => null,          'ket' => 'Belum ada nilai (-)'],
                    2 => ['realisasi' => 14_230_000,    'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 155_940_365,   'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 155_940_365,   'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 288_423_365,   'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 432_956_465,   'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 441_056_465,   'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
                // IKK 22
                'IKK 22' => [
                    1 => ['realisasi' => 1_257_097_921, 'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 1_499_410_240, 'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 2_747_078_253, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 4_177_855_457, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 4_293_571_214, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 6_585_135_957, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 6_145_204_190, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026 (koreksi/revisi)'],
                ],
                // IKK 24
                'IKK 24' => [
                    1 => ['realisasi' => 3_147_570_379,  'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 11_505_726_984, 'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 18_753_051_513, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 21_918_338_609, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 26_259_267_204, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 26_259_267_204, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 30_685_363_568, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
                // IKK 25
                'IKK 25' => [
                    1 => ['realisasi' => 0,           'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 177_486_572, 'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 428_926_799, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 643_954_103, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 643_954_103, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 664_872_463, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 746_546_467, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
                // IKK 26
                'IKK 26' => [
                    1 => ['realisasi' => 0,           'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 177_486_572, 'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 428_926_799, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 643_954_103, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 643_954_103, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 664_872_463, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 746_546_467, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
                // IKK 28
                'IKK 28' => [
                    1 => ['realisasi' => 0,          'ket' => 'Realisasi anggaran s.d. bulan 1 tahun 2026'],
                    2 => ['realisasi' => 0,          'ket' => 'Realisasi anggaran s.d. bulan 2 tahun 2026'],
                    3 => ['realisasi' => 62_090_427, 'ket' => 'Realisasi anggaran s.d. bulan 3 tahun 2026'],
                    4 => ['realisasi' => 64_417_264, 'ket' => 'Realisasi anggaran s.d. bulan 4 tahun 2026'],
                    5 => ['realisasi' => 64_417_264, 'ket' => 'Realisasi anggaran s.d. bulan 5 tahun 2026'],
                    6 => ['realisasi' => 64_417_264, 'ket' => 'Realisasi anggaran s.d. bulan 6 tahun 2026'],
                    7 => ['realisasi' => 64_417_264, 'ket' => 'Realisasi anggaran s.d. bulan 7 tahun 2026'],
                ],
            ];

            foreach ($realisasiBulananSemuaIKK as $kodeIkk => $bulanData) {
                $ikk = Ikk::where('kode_ikk', $kodeIkk)->first();
                if (! $ikk || ! isset($paguRefs[$kodeIkk])) continue;

                $paguAwal   = $paguRefs[$kodeIkk]['awal'];
                $paguRevisi = $paguRefs[$kodeIkk]['revisi'] ?? $paguAwal;

                foreach ($bulanData as $bln => $d) {
                    $paguId = ($bln >= 7 && isset($paguRefs[$kodeIkk]['revisi'])) ? $paguRevisi->id : $paguAwal->id;
                    AnggaranBulanan::create([
                        'ikk_id'             => $ikk->id,
                        'bulan'              => $bln,
                        'tahun'              => $tahun,
                        'pagu_anggaran_id'   => $paguId,
                        'realisasi_anggaran' => $d['realisasi'],
                        'keterangan'         => $d['ket'],
                    ]);
                }
            }

            // -------------------------------------------------------
            // 6. Laporan Kinerja Bulanan (Januari s.d. Juli 2026)
            // -------------------------------------------------------

            /*
             * 6.1 IKK 20 — Tunggal (Sum dari rincian_pelatihans.jumlah_peserta)
             * Target: 7.050 Orang | Realisasi s.d. Juli: 5.308 Orang (75,29%)
             */
            if ($ikk20 && $picAlvian) {
                LaporanBulanan::where(['ikk_id' => $ikk20->id, 'tahun' => $tahun])
                    ->each(function ($l) {
                        $l->rincianPelatihans()->delete();
                        $l->persetujuan()->delete();
                        $l->delete();
                    });

                $pelatihanBulanan = [
                    1 => [
                        ['nama' => 'Diklat Kepemimpinan Pengawas Angk. I', 'dana' => 'PNBP', 'peserta' => 500, 'lulus' => 498, 'lk' => 300, 'pr' => 200],
                    ],
                    2 => [
                        ['nama' => 'Pelatihan Dasar CPNS Gelombang I', 'dana' => 'PNBP', 'peserta' => 700, 'lulus' => 695, 'lk' => 420, 'pr' => 280],
                    ],
                    3 => [
                        ['nama' => 'Pelatihan Teknis Perhubungan Darat', 'dana' => 'PNBP', 'peserta' => 500, 'lulus' => 498, 'lk' => 300, 'pr' => 200],
                        ['nama' => 'Pelatihan Teknis Perhubungan Udara',  'dana' => 'RM',   'peserta' => 300, 'lulus' => 298, 'lk' => 180, 'pr' => 120],
                    ],
                    4 => [
                        ['nama' => 'Diklat Kepemimpinan Administrator Angk. II', 'dana' => 'PNBP', 'peserta' => 1100, 'lulus' => 1090, 'lk' => 660, 'pr' => 440],
                    ],
                    5 => [
                        ['nama' => 'Pelatihan Manajemen Transportasi Multimoda', 'dana' => 'PNBP', 'peserta' => 1200, 'lulus' => 1190, 'lk' => 720, 'pr' => 480],
                    ],
                    6 => [
                        ['nama' => 'Pelatihan Teknis Keselamatan Transportasi', 'dana' => 'PNBP', 'peserta' => 500, 'lulus' => 498, 'lk' => 300, 'pr' => 200],
                    ],
                    7 => [
                        ['nama' => 'Pelatihan Digitalisasi Layanan Transportasi', 'dana' => 'RM',   'peserta' => 258, 'lulus' => 255, 'lk' => 155, 'pr' => 103],
                        ['nama' => 'Pelatihan Reformasi Birokrasi Angk. III',     'dana' => 'PNBP', 'peserta' => 250, 'lulus' => 248, 'lk' => 150, 'pr' => 100],
                    ],
                ];

                foreach ($pelatihanBulanan as $bulan => $daftarPelatihan) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk20->id,
                        'unit_id'        => $picAlvian->unit_id,
                        'pic_id'         => $picAlvian->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => null,  // ← dihitung otomatis oleh sistem
                        'status'         => 'final',
                        'narasi_capaian' => "Penyelenggaraan pelatihan bulan ke-{$bulan} berjalan lancar dan mencapai target peserta.",
                        'kendala'        => 'Tidak ada kendala berarti pada periode ini.',
                        'tindak_lanjut'  => 'Melanjutkan pelaksanaan batch pelatihan periode berikutnya.',
                    ]);

                    foreach ($daftarPelatihan as $p) {
                        RincianPelatihan::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'nama_pelatihan'     => $p['nama'],
                            'sumber_dana'        => $p['dana'],
                            'jumlah_peserta'     => $p['peserta'],
                            'jumlah_lulus'       => $p['lulus'],
                            'jumlah_laki_laki'   => $p['lk'],
                            'jumlah_perempuan'   => $p['pr'],
                        ]);
                    }

                    $this->buatJejakPersetujuan($laporan, $picAlvian, $kabidUnit1, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.2 IKK 21 — Tunggal (Count dari rincian_n_s_p_k_s)
             * Target: 9 Dokumen
             * Jan-Mei: 0 Dokumen (0%)
             * Juni: 3 Dokumen (33,3%)
             * Juli: 3 Dokumen kumulatif (33,3%)
             */
            if ($ikk21 && $picArdian) {
                LaporanBulanan::where(['ikk_id' => $ikk21->id, 'tahun' => $tahun])
                    ->each(function ($l) {
                        $l->rincianNSPKs()->delete();
                        $l->persetujuan()->delete();
                        $l->delete();
                    });

                $nspkBulanan = [
                    1 => [],
                    2 => [],
                    3 => [],
                    4 => [],
                    5 => [],
                    6 => [
                        ['jenis' => 'Pedoman',  'nama' => 'Pedoman Teknis Pengembangan Kompetensi SDM',           'ket' => 'Selesai disusun dan divalidasi'],
                        ['jenis' => 'Standar',  'nama' => 'Standar Operasional Prosedur Diklat Teknis',           'ket' => 'Selesai disusun dan divalidasi'],
                        ['jenis' => 'Norma',    'nama' => 'Norma Pelaksanaan Uji Kompetensi Aparatur',            'ket' => 'Selesai disusun dan divalidasi'],
                    ],
                    7 => [], // Tidak ada dokumen baru di Juli, kumulatif tetap 3 dokumen
                ];

                foreach ($nspkBulanan as $bulan => $daftarNspk) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk21->id,
                        'unit_id'        => $picArdian->unit_id,
                        'pic_id'         => $picArdian->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => null,  // ← dihitung otomatis oleh sistem dari count dokumen
                        'status'         => 'final',
                        'narasi_capaian' => count($daftarNspk) > 0
                            ? "Penyusunan 3 dokumen NSPK pada bulan ke-{$bulan} telah selesai dan divalidasi."
                            : "Proses koordinasi dan perumusan dokumen NSPK pada bulan ke-{$bulan}.",
                        'kendala'        => 'Tahapan harmonisasi regulasi memerlukan sinkronisasi lintas unit.',
                        'tindak_lanjut'  => 'Mempercepat asistensi perumusan dokumen NSPK tahap berikutnya.',
                    ]);

                    foreach ($daftarNspk as $n) {
                        RincianNSPK::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'jenis_nspk'         => $n['jenis'],
                            'nama_nspk'          => $n['nama'],
                            'keterangan'         => $n['ket'],
                        ]);
                    }

                    $this->buatJejakPersetujuan($laporan, $picArdian, $kabidUnit1, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.3 IKK 22 — Nilai Indeks Profesionalitas ASN (Field realisasi)
             * Target: 77.00
             * Jan: 0.00
             * Feb s.d. Jul: 85.53 (Capaian: 111,08%)
             */
            if ($ikk22 && $picIndah) {
                LaporanBulanan::where(['ikk_id' => $ikk22->id, 'tahun' => $tahun])
                    ->each(fn ($l) => $l->persetujuan()->delete() | $l->delete());

                $nilaiIkk22 = [
                    1 => 0.00,
                    2 => 85.53,
                    3 => 85.53,
                    4 => 85.53,
                    5 => 85.53,
                    6 => 85.53,
                    7 => 85.53,
                ];

                foreach ($nilaiIkk22 as $bulan => $nilai) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk22->id,
                        'unit_id'        => $picIndah->unit_id,
                        'pic_id'         => $picIndah->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => $nilai,
                        'status'         => 'final',
                        'narasi_capaian' => $nilai > 0
                            ? "Indeks Profesionalisme ASN bulan {$bulan} tercatat {$nilai} — melampaui target 77,00."
                            : "Pengumpulan dan verifikasi data Indeks Profesionalitas ASN periode awal tahun.",
                        'kendala'        => 'Masih terdapat beberapa ASN yang belum memperbarui riwayat kompetensi mandiri.',
                        'tindak_lanjut'  => 'Sosialisasi pengisian mandiri e-Kinerja dan SIMPEG secara berkala.',
                    ]);

                    $this->buatJejakPersetujuan($laporan, $picIndah, null, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.4 IKK 24 — Gabungan Akuntabilitas (Bobot A: 50%, Bobot B: 50%)
             * Target: 100%
             * Jan - Mei: Unit: 0/13, Layanan: 0/13 -> 0%
             * Jun - Jul: Unit: 7/13 (26,92%), Layanan: 7/13 (26,92%) -> Total: 53,85%
             */
            if ($ikk24 && $picIndah) {
                $kompA24 = IkkKomponen::where('ikk_id', $ikk24->id)->where('kode_komponen', 'A')->first();
                $kompB24 = IkkKomponen::where('ikk_id', $ikk24->id)->where('kode_komponen', 'B')->first();

                LaporanBulanan::where(['ikk_id' => $ikk24->id, 'tahun' => $tahun])
                    ->each(fn ($l) => $l->angkaKinerjas()->delete() | $l->persetujuan()->delete() | $l->delete());

                $komponenIkk24Bulanan = [
                    1 => ['a_pem' => 0, 'a_peny' => 13, 'b_pem' => 0, 'b_peny' => 13],
                    2 => ['a_pem' => 0, 'a_peny' => 13, 'b_pem' => 0, 'b_peny' => 13],
                    3 => ['a_pem' => 0, 'a_peny' => 13, 'b_pem' => 0, 'b_peny' => 13],
                    4 => ['a_pem' => 0, 'a_peny' => 13, 'b_pem' => 0, 'b_peny' => 13],
                    5 => ['a_pem' => 0, 'a_peny' => 13, 'b_pem' => 0, 'b_peny' => 13],
                    6 => ['a_pem' => 7, 'a_peny' => 13, 'b_pem' => 7, 'b_peny' => 13],
                    7 => ['a_pem' => 7, 'a_peny' => 13, 'b_pem' => 7, 'b_peny' => 13],
                ];

                foreach ($komponenIkk24Bulanan as $bulan => $k) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk24->id,
                        'unit_id'        => $picIndah->unit_id,
                        'pic_id'         => $picIndah->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => null,  // ← dihitung otomatis oleh sistem dari AngkaKinerja berbobot
                        'status'         => 'final',
                        'narasi_capaian' => $k['a_pem'] > 0
                            ? "Pemenuhan akuntabilitas: {$k['a_pem']}/{$k['a_peny']} unit dan {$k['b_pem']}/{$k['b_peny']} layanan terlaksana (Capaian 53,85%)."
                            : "Proses evaluasi pemenuhan standar akuntabilitas pada unit kerja.",
                        'kendala'        => 'Sebagian unit masih merampungkan kelengkapan dokumen pendukung akuntabilitas.',
                        'tindak_lanjut'  => 'Pendampingan langsung ke unit yang belum memenuhi target standar layanan.',
                    ]);

                    if ($kompA24) {
                        AngkaKinerja::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'ikk_komponen_id'    => $kompA24->id,
                            'pembilang'          => $k['a_pem'],
                            'penyebut'           => $k['a_peny'],
                        ]);
                    }
                    if ($kompB24) {
                        AngkaKinerja::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'ikk_komponen_id'    => $kompB24->id,
                            'pembilang'          => $k['b_pem'],
                            'penyebut'           => $k['b_peny'],
                        ]);
                    }

                    $this->buatJejakPersetujuan($laporan, $picIndah, null, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.5 IKK 25 — Nilai IKPA (Field realisasi)
             * Target: 90.50
             * Jan - Mar: 0.00
             * Apr - Jun: 96.80
             * Jul: 95.71
             */
            if ($ikk25 && $picSuwarman) {
                LaporanBulanan::where(['ikk_id' => $ikk25->id, 'tahun' => $tahun])
                    ->each(fn ($l) => $l->persetujuan()->delete() | $l->delete());

                $nilaiIkk25 = [
                    1 => 0.00,
                    2 => 0.00,
                    3 => 0.00,
                    4 => 96.80,
                    5 => 96.80,
                    6 => 96.80,
                    7 => 95.71,
                ];

                foreach ($nilaiIkk25 as $bulan => $nilai) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk25->id,
                        'unit_id'        => $picSuwarman->unit_id,
                        'pic_id'         => $picSuwarman->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => $nilai,
                        'status'         => 'final',
                        'narasi_capaian' => $nilai > 0
                            ? "Nilai IKPA periode bulan {$bulan} mencapai {$nilai} — melampaui target 90,50."
                            : "Pencatatan awal administrasi dan pelaksanaan anggaran triwulan I.",
                        'kendala'        => 'Deviasi halaman III DIPA perlu terus dijaga agar tidak menurunkan nilai IKPA.',
                        'tindak_lanjut'  => 'Monitoring penarikan dana bulanan agar tetap presisi sesuai RPD.',
                    ]);

                    $this->buatJejakPersetujuan($laporan, $picSuwarman, null, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.6 IKK 26 — Nilai SAKIP BPSDMP (Field realisasi)
             * Target: 78.50
             * Jan - Mar: null (tanda - / belum ada nilai)
             * Apr - Jun: 84.66
             * Jul: 85.40
             */
            if ($ikk26 && $picLusi) {
                LaporanBulanan::where(['ikk_id' => $ikk26->id, 'tahun' => $tahun])
                    ->each(fn ($l) => $l->persetujuan()->delete() | $l->delete());

                $nilaiIkk26 = [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => 84.66,
                    5 => 84.66,
                    6 => 84.66,
                    7 => 85.40,
                ];

                foreach ($nilaiIkk26 as $bulan => $nilai) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk26->id,
                        'unit_id'        => $picLusi->unit_id,
                        'pic_id'         => $picLusi->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => $nilai,
                        'status'         => 'final',
                        'narasi_capaian' => $nilai !== null
                            ? "Nilai SAKIP periode bulan {$bulan} mencapai {$nilai} — melampaui target 78,50."
                            : "Proses kompilasi dokumen penilaian SAKIP tahun berjalan.",
                        'kendala'        => 'Sinkronisasi eviden kinerja dari beberapa unit pendukung.',
                        'tindak_lanjut'  => 'Kompilasi berkas eviden pendukung evaluasi SAKIP tingkat Kementerian.',
                    ]);

                    $this->buatJejakPersetujuan($laporan, $picLusi, null, $katimUmper, $kabagUmum, $kapus);
                }
            }

            /*
             * 6.7 IKK 28 — Gabungan Kerja Sama dan Kemitraan (Bobot A: 40%, Bobot B: 60%)
             * Target: 100%
             * Jan - Mei: A: 0/2, B: 0/2 -> 0%
             * Jun - Jul: A: 1/2 (20%), B: 1/2 (30%) -> Total: 50%
             */
            if ($ikk28 && $picLusi) {
                $kompA28 = IkkKomponen::where('ikk_id', $ikk28->id)->where('kode_komponen', 'A')->first();
                $kompB28 = IkkKomponen::where('ikk_id', $ikk28->id)->where('kode_komponen', 'B')->first();

                LaporanBulanan::where(['ikk_id' => $ikk28->id, 'tahun' => $tahun])
                    ->each(function ($l) {
                        $l->angkaKinerjas()->delete();
                        $l->rincianKerjasamas()->delete();
                        $l->persetujuan()->delete();
                        $l->delete();
                    });

                $komponenIkk28Bulanan = [
                    1 => ['a_pem' => 0, 'a_peny' => 2, 'b_pem' => 0, 'b_peny' => 2],
                    2 => ['a_pem' => 0, 'a_peny' => 2, 'b_pem' => 0, 'b_peny' => 2],
                    3 => ['a_pem' => 0, 'a_peny' => 2, 'b_pem' => 0, 'b_peny' => 2],
                    4 => ['a_pem' => 0, 'a_peny' => 2, 'b_pem' => 0, 'b_peny' => 2],
                    5 => ['a_pem' => 0, 'a_peny' => 2, 'b_pem' => 0, 'b_peny' => 2],
                    6 => ['a_pem' => 1, 'a_peny' => 2, 'b_pem' => 1, 'b_peny' => 2],
                    7 => ['a_pem' => 1, 'a_peny' => 2, 'b_pem' => 1, 'b_peny' => 2],
                ];

                foreach ($komponenIkk28Bulanan as $bulan => $k) {
                    $laporan = LaporanBulanan::create([
                        'ikk_id'         => $ikk28->id,
                        'unit_id'        => $picLusi->unit_id,
                        'pic_id'         => $picLusi->id,
                        'bulan'          => $bulan,
                        'tahun'          => $tahun,
                        'realisasi'      => null,  // ← dihitung otomatis oleh sistem dari AngkaKinerja berbobot
                        'status'         => 'final',
                        'narasi_capaian' => $k['a_pem'] > 0
                            ? "Pelaksanaan kerja sama: {$k['a_pem']}/{$k['a_peny']} ditindaklanjuti dan {$k['b_pem']}/{$k['b_peny']} disusun (Capaian 50%)."
                            : "Penjajakan dan koordinasi draft naskah kerja sama dengan mitra kerja.",
                        'kendala'        => 'Proses sinkronisasi klausul kerja sama dengan mitra eksternal.',
                        'tindak_lanjut'  => 'Finalisasi naskah PKS dan percepatan penandatanganan di semester II.',
                    ]);

                    if ($k['a_pem'] > 0) {
                        RincianKerjasama::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'judul_kerjasama'    => 'PKS Pelatihan SDM Transportasi dengan PT Kereta Api Indonesia (Persero)',
                        ]);
                        RincianKerjasama::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'judul_kerjasama'    => 'MoU Riset dan Pengabdian Masyarakat SDM Maritim dengan Institut Teknologi Bandung',
                        ]);
                    }

                    if ($kompA28) {
                        AngkaKinerja::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'ikk_komponen_id'    => $kompA28->id,
                            'pembilang'          => $k['a_pem'],
                            'penyebut'           => $k['a_peny'],
                        ]);
                    }
                    if ($kompB28) {
                        AngkaKinerja::create([
                            'laporan_bulanan_id' => $laporan->id,
                            'ikk_komponen_id'    => $kompB28->id,
                            'pembilang'          => $k['b_pem'],
                            'penyebut'           => $k['b_peny'],
                        ]);
                    }

                    $this->buatJejakPersetujuan($laporan, $picLusi, null, $katimUmper, $kabagUmum, $kapus);
                }
            }
        });
    }

    // -------------------------------------------------------
    // Helper: Buat Jejak Persetujuan Berjenjang 5 Tahap
    // -------------------------------------------------------
    private function buatJejakPersetujuan(
        LaporanBulanan $laporan,
        User $pic,
        ?User $kabid,
        ?User $katimUmper,
        ?User $kabagUmum,
        ?User $kapus
    ): void {
        $laporan->persetujuan()->delete();

        // Tahap 1: Pengajuan PIC
        PersetujuanLaporan::create([
            'laporan_bulanan_id' => $laporan->id,
            'tahap'              => 1,
            'user_id'            => $pic->id,
            'keputusan'          => 'diajukan',
            'catatan'            => 'Laporan bulanan telah diisi dan diajukan untuk diverifikasi.',
            'diproses_pada'      => now()->subDays(5),
        ]);

        // Tahap 2: Kabid (hanya unit 1–3, bukan unit 4, 5, 6)
        if ($kabid && in_array((int) $laporan->unit_id, [1, 2, 3], true)) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap'              => 2,
                'user_id'            => $kabid->id,
                'keputusan'          => 'disetujui',
                'catatan'            => 'Data kinerja unit telah diverifikasi dan disetujui Kepala Bidang.',
                'diproses_pada'      => now()->subDays(4),
            ]);
        }

        // Tahap 3: Katim Umper
        if ($katimUmper) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap'              => 3,
                'user_id'            => $katimUmper->id,
                'keputusan'          => 'disetujui',
                'catatan'            => 'Pemeriksaan substansi perencanaan dan kinerja selesai.',
                'diproses_pada'      => now()->subDays(3),
            ]);
        }

        // Tahap 4: Kabag Umum
        if ($kabagUmum) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap'              => 4,
                'user_id'            => $kabagUmum->id,
                'keputusan'          => 'disetujui',
                'catatan'            => 'Validasi administrasi disetujui dan diteruskan ke Kepala Pusat.',
                'diproses_pada'      => now()->subDays(2),
            ]);
        }

        // Tahap 5: Kapus (Final)
        if ($kapus) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap'              => 5,
                'user_id'            => $kapus->id,
                'keputusan'          => 'disetujui',
                'catatan'            => 'Laporan capaian kinerja disahkan sebagai laporan final.',
                'diproses_pada'      => now()->subDay(),
            ]);
        }
    }
}
