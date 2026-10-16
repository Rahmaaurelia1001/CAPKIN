<?php

namespace Database\Seeders;

use App\Models\AnggaranBulanan;
use App\Models\AngkaKinerja;
use App\Models\DataDukung;
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
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataKinerja2026Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $tahun = 2026;

            // 1. Ambil User Pejabat & PIC
            $admin = User::where('role', 'admin')->first();
            $kapus = User::where('role', 'kapus')->first();
            $kabagUmum = User::where('role', 'kabag_umum')->first();
            $katimUmper = User::where('role', 'katim_umper')->first();

            $picAlvian = User::where('name', 'ALVIAN GUNTUR')->first();
            $picAtar = User::where('name', 'ATAR KOSWARA R')->first();
            $picArdian = User::where('name', 'ARDIAN HIDAYAT')->first();
            $picIndah = User::where('name', 'INDAH IMAYANTI')->first();
            $picSuwarman = User::where('name', 'SUWARMAN')->first();
            $picLusi = User::where('name', 'LUSI FIBRIANI MANIK')->first();

            $kabidUnit1 = User::where('role', 'kabid_kabag')->where('unit_id', 1)->first();
            $kabidUnit2 = User::where('role', 'kabid_kabag')->where('unit_id', 2)->first();
            $kabidUnit3 = User::where('role', 'kabid_kabag')->where('unit_id', 3)->first();

            // 2. Hubungkan IKK ke Unit Kerja (Tabel Pivot ikk_unit)
            $unit1 = Unit::find(1); // Bidang Penyelenggara Diklat Struktural & Fungsional
            $unit2 = Unit::find(2); // Bidang Penyelenggara Diklat Manajerial
            $unit3 = Unit::find(3); // Bidang Perencanaan Pengembangan Kompetensi
            $unit4 = Unit::find(4); // Bagian Umum (Kepegawaian)
            $unit5 = Unit::find(5); // Bagian Umum (Keuangan)
            $unit6 = Unit::find(6); // Bagian Umum (Perencanaan)

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

            // 3. Pastikan Target 2026 terpasang pada TargetIkk
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
                    'ikk_id' => $ikk->id,
                    'tahun_mulai' => 2025,
                ]);

                TargetIkk::updateOrCreate(
                    [
                        'ikk_periode_id' => $periode->id,
                        'tahun' => $tahun,
                    ],
                    [
                        'nilai_target' => $nilai,
                    ]
                );
            }

            // 4. Pagu Anggaran 2026 (Pagu Awal & Revisi 1)
            if ($ikk20) {
                // Pagu Awal (mulai Jan)
                $paguAwal = PaguAnggaran::updateOrCreate(
                    [
                        'ikk_id' => $ikk20->id,
                        'tahun' => $tahun,
                        'nomor_revisi' => 0,
                    ],
                    [
                        'nilai_pagu' => 21456042000.00,
                        'berlaku_mulai_bulan' => 1,
                        'keterangan' => 'Pagu Awal DIPA 2026',
                    ]
                );

                // Pagu Revisi 1 (mulai Juli)
                $paguRevisi = PaguAnggaran::updateOrCreate(
                    [
                        'ikk_id' => $ikk20->id,
                        'tahun' => $tahun,
                        'nomor_revisi' => 1,
                    ],
                    [
                        'nilai_pagu' => 21456042000.00,
                        'berlaku_mulai_bulan' => 7,
                        'keterangan' => 'Revisi Pagu Anggaran Semester II',
                    ]
                );

                // 5. Realisasi Anggaran Bulanan IKK 20 (Januari - Juli 2026)
                // Berdasarkan data Excel penyerapan kumulatif s.d. Juli
                $dataAnggaranBulanan = [
                    1 => ['realisasi' => 0, 'pagu_id' => $paguAwal->id],
                    2 => ['realisasi' => 510150000.00, 'pagu_id' => $paguAwal->id],
                    3 => ['realisasi' => 1658742245.00, 'pagu_id' => $paguAwal->id],
                    4 => ['realisasi' => 5070024890.00, 'pagu_id' => $paguAwal->id],
                    5 => ['realisasi' => 6567500000.00, 'pagu_id' => $paguAwal->id],
                    6 => ['realisasi' => 7718000750.00, 'pagu_id' => $paguAwal->id],
                    7 => ['realisasi' => 8758342645.00, 'pagu_id' => $paguRevisi->id],
                ];

                foreach ($dataAnggaranBulanan as $bulan => $item) {
                    AnggaranBulanan::updateOrCreate(
                        [
                            'ikk_id' => $ikk20->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                        ],
                        [
                            'pagu_anggaran_id' => $item['pagu_id'],
                            'realisasi_anggaran' => $item['realisasi'],
                            'keterangan' => "Realisasi anggaran s.d. bulan {$bulan} tahun {$tahun}",
                        ]
                    );
                }
            }

            // 6. Data Laporan Kinerja Bulanan (Januari s.d. Juli 2026) Berstatus FINAL
            // IKK 20: Peserta Pelatihan (Tambahan bulanan yang jika dijumlahkan tepat 5.308)
            // Jan (500), Feb (700 -> kum 1200), Mar (800 -> kum 2000), Apr (1100 -> kum 3100),
            // Mei (1200 -> kum 4300), Jun (500 -> kum 4800), Jul (508 -> kum 5308)
            $pelatihanBulanan = [
                1 => ['batch' => 'Diklat Kepemimpinan Pengawas Angkatan I', 'peserta' => 500],
                2 => ['batch' => 'Pelatihan Dasar CPNS Gelombang I', 'peserta' => 700],
                3 => ['batch' => 'Pelatihan Teknis Perhubungan Darat & Udara', 'peserta' => 800],
                4 => ['batch' => 'Pelatihan Kepemimpinan Administrator Angkatan II', 'peserta' => 1100],
                5 => ['batch' => 'Pelatihan Manajemen Transportasi Multimoda', 'peserta' => 1200],
                6 => ['batch' => 'Pelatihan Teknis Keselamatan Transportasi', 'peserta' => 500],
                7 => ['batch' => 'Pelatihan Digitalisasi Layanan Transportasi', 'peserta' => 508],
            ];

            if ($ikk20 && $picAlvian) {
                foreach ($pelatihanBulanan as $bulan => $data) {
                    $laporan = LaporanBulanan::updateOrCreate(
                        [
                            'ikk_id' => $ikk20->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'pic_id' => $picAlvian->id,
                        ],
                        [
                            'unit_id' => $picAlvian->unit_id,
                            'realisasi' => $data['peserta'],
                            'status' => 'final',
                            'catatan' => "Laporan penyelenggaraan pelatihan bulan {$bulan}",
                            'narasi_capaian' => "Realisasi peserta pelatihan bulan {$bulan} tercapai {$data['peserta']} orang.",
                            'kendala' => 'Tidak ada kendala berarti.',
                            'tindak_lanjut' => 'Melanjutkan pelaksanaan batch pelatihan periode berikutnya.',
                        ]
                    );

                    // Bersihkan dan simpan rincian pelatihan
                    $laporan->rincianPelatihans()->delete();
                    RincianPelatihan::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'nama_pelatihan' => $data['batch'],
                        'sumber_dana' => 'PNBP',
                        'jumlah_peserta' => $data['peserta'],
                        'jumlah_lulus' => $data['peserta'],
                        'jumlah_laki_laki' => (int) round($data['peserta'] * 0.6),
                        'jumlah_perempuan' => (int) round($data['peserta'] * 0.4),
                    ]);

                    $this->buatJejakPersetujuan($laporan, $picAlvian, $kabidUnit1, $katimUmper, $kabagUmum, $kapus);
                }
            }

            // IKK 21 (NSPK) — Target 9
            // Jan (1), Feb (1), Mar (1), Mei (1), Jun (1), Jul (1) -> Total 6 NSPK
            if ($ikk21 && $picArdian) {
                $nspkBulanan = [
                    1 => 'Pedoman Teknis Pengembangan Kompetensi SDM',
                    2 => 'Standar Operasional Prosedur Diklat Teknis',
                    3 => 'Norma Pelaksanaan Uji Kompetensi Aparatur',
                    5 => 'Kriteria Penilaian Kinerja Lulusan Pelatihan',
                    6 => 'Pedoman Pelaksanaan Pelatihan Jarak Jauh',
                    7 => 'Standar Sarana dan Prasarana Laboratorium Diklat',
                ];

                foreach ($nspkBulanan as $bulan => $namaNspk) {
                    $laporan = LaporanBulanan::updateOrCreate(
                        [
                            'ikk_id' => $ikk21->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'pic_id' => $picArdian->id,
                        ],
                        [
                            'unit_id' => $picArdian->unit_id,
                            'realisasi' => 1,
                            'status' => 'final',
                            'narasi_capaian' => "Penyusunan dokumen NSPK: {$namaNspk}",
                        ]
                    );

                    $laporan->rincianNSPKs()->delete();
                    RincianNSPK::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'jenis_nspk' => 'Pedoman',
                        'nama_nspk' => $namaNspk,
                        'keterangan' => 'Selesai disusun dan divalidasi',
                    ]);

                    $this->buatJejakPersetujuan($laporan, $picArdian, $kabidUnit1, $katimUmper, $kabagUmum, $kapus);
                }
            }

            // IKK 22 (Indeks Profesionalitas ASN) — Target 77.00
            if ($ikk22 && $picIndah) {
                $laporan = LaporanBulanan::updateOrCreate(
                    [
                        'ikk_id' => $ikk22->id,
                        'bulan' => 7,
                        'tahun' => $tahun,
                        'pic_id' => $picIndah->id,
                    ],
                    [
                        'unit_id' => $picIndah->unit_id,
                        'realisasi' => 78.20,
                        'status' => 'final',
                        'narasi_capaian' => 'Pengukuran Indeks Profesionalitas ASN Semester I mencapai nilai 78,20.',
                    ]
                );

                $this->buatJejakPersetujuan($laporan, $picIndah, null, $katimUmper, $kabagUmum, $kapus);
            }

            // IKK 24 (Akuntabilitas BPSDMP — Gabungan 50% + 50%)
            if ($ikk24 && $picIndah) {
                $kompA = IkkKomponen::where('ikk_id', $ikk24->id)->where('kode_komponen', 'A')->first();
                $kompB = IkkKomponen::where('ikk_id', $ikk24->id)->where('kode_komponen', 'B')->first();

                $laporan = LaporanBulanan::updateOrCreate(
                    [
                        'ikk_id' => $ikk24->id,
                        'bulan' => 7,
                        'tahun' => $tahun,
                        'pic_id' => $picIndah->id,
                    ],
                    [
                        'unit_id' => $picIndah->unit_id,
                        'realisasi' => 0,
                        'status' => 'final',
                        'narasi_capaian' => 'Pemenuhan akuntabilitas: Unit ditetapkan 5 dari 5 target, Layanan terlaksana 16 dari 20 target.',
                    ]
                );

                $laporan->angkaKinerjas()->delete();
                if ($kompA) {
                    AngkaKinerja::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'ikk_komponen_id' => $kompA->id,
                        'pembilang' => 5,
                        'penyebut' => 5, // 100% * 50% = 50%
                    ]);
                }
                if ($kompB) {
                    AngkaKinerja::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'ikk_komponen_id' => $kompB->id,
                        'pembilang' => 16,
                        'penyebut' => 20, // 80% * 50% = 40%
                    ]);
                }

                $this->buatJejakPersetujuan($laporan, $picIndah, null, $katimUmper, $kabagUmum, $kapus);
            }

            // IKK 25 (IKPA) — Target 90.50
            if ($ikk25 && $picSuwarman) {
                $laporan = LaporanBulanan::updateOrCreate(
                    [
                        'ikk_id' => $ikk25->id,
                        'bulan' => 7,
                        'tahun' => $tahun,
                        'pic_id' => $picSuwarman->id,
                    ],
                    [
                        'unit_id' => $picSuwarman->unit_id,
                        'realisasi' => 93.45,
                        'status' => 'final',
                        'narasi_capaian' => 'Nilai IKPA per akhir Juli 2026 mencapai 93,45 melampaui target 90,50.',
                    ]
                );

                $this->buatJejakPersetujuan($laporan, $picSuwarman, null, $katimUmper, $kabagUmum, $kapus);
            }

            // IKK 26 (SAKIP BPSDMP) — Target 78.50
            if ($ikk26 && $picLusi) {
                $laporan = LaporanBulanan::updateOrCreate(
                    [
                        'ikk_id' => $ikk26->id,
                        'bulan' => 7,
                        'tahun' => $tahun,
                        'pic_id' => $picLusi->id,
                    ],
                    [
                        'unit_id' => $picLusi->unit_id,
                        'realisasi' => 90.00,
                        'status' => 'final',
                        'narasi_capaian' => 'Nilai evaluasi SAKIP mencapai 90,00 (Kategori A).',
                    ]
                );

                $this->buatJejakPersetujuan($laporan, $picLusi, null, $katimUmper, $kabagUmum, $kapus);
            }

            // IKK 28 (Kerjasama Diklat — Gabungan 40% + 60%)
            if ($ikk28 && $picLusi) {
                $kompA = IkkKomponen::where('ikk_id', $ikk28->id)->where('kode_komponen', 'A')->first();
                $kompB = IkkKomponen::where('ikk_id', $ikk28->id)->where('kode_komponen', 'B')->first();

                $laporan = LaporanBulanan::updateOrCreate(
                    [
                        'ikk_id' => $ikk28->id,
                        'bulan' => 7,
                        'tahun' => $tahun,
                        'pic_id' => $picLusi->id,
                    ],
                    [
                        'unit_id' => $picLusi->unit_id,
                        'realisasi' => 0,
                        'status' => 'final',
                        'narasi_capaian' => 'Kerja sama yang ditindaklanjuti 8 dari 10 (80%), Kerja sama yang disusun 6 dari 8 target (75%).',
                    ]
                );

                $laporan->angkaKinerjas()->delete();
                if ($kompA) {
                    AngkaKinerja::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'ikk_komponen_id' => $kompA->id,
                        'pembilang' => 8,
                        'penyebut' => 10,
                    ]);
                }
                if ($kompB) {
                    AngkaKinerja::create([
                        'laporan_bulanan_id' => $laporan->id,
                        'ikk_komponen_id' => $kompB->id,
                        'pembilang' => 6,
                        'penyebut' => 8,
                    ]);
                }

                $laporan->rincianKerjasamas()->delete();
                RincianKerjasama::create([
                    'laporan_bulanan_id' => $laporan->id,
                    'judul_kerjasama' => 'PKS dengan PT Kereta Api Indonesia (Persero)',
                ]);
                RincianKerjasama::create([
                    'laporan_bulanan_id' => $laporan->id,
                    'judul_kerjasama' => 'MoU Pengembangan Diklat Maritim dengan PT Pelindo',
                ]);

                $this->buatJejakPersetujuan($laporan, $picLusi, null, $katimUmper, $kabagUmum, $kapus);
            }
        });
    }

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
            'tahap' => 1,
            'user_id' => $pic->id,
            'keputusan' => 'diajukan',
            'catatan' => 'Laporan bulanan telah selesai diisi dan diajukan.',
            'diproses_pada' => now()->subDays(5),
        ]);

        // Tahap 2: Kabid (jika bukan Unit 6)
        if ($kabid && (int) $laporan->unit_id !== 6) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => 2,
                'user_id' => $kabid->id,
                'keputusan' => 'disetujui',
                'catatan' => 'Data kinerja unit telah diverifikasi dan disetujui.',
                'diproses_pada' => now()->subDays(4),
            ]);
        }

        // Tahap 3: Katim Umper
        if ($katimUmper) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => 3,
                'user_id' => $katimUmper->id,
                'keputusan' => 'disetujui',
                'catatan' => 'Pemeriksaan substansi perencanaan dan kinerja selesai.',
                'diproses_pada' => now()->subDays(3),
            ]);
        }

        // Tahap 4: Kabag Umum
        if ($kabagUmum) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => 4,
                'user_id' => $kabagUmum->id,
                'keputusan' => 'disetujui',
                'catatan' => 'Validasi administrasi disetujui untuk diteruskan ke Kepala Pusat.',
                'diproses_pada' => now()->subDays(2),
            ]);
        }

        // Tahap 5: Kapus (Final)
        if ($kapus) {
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => 5,
                'user_id' => $kapus->id,
                'keputusan' => 'disetujui',
                'catatan' => 'Laporan capaian kinerja disahkan sebagai laporan final.',
                'diproses_pada' => now()->subDay(),
            ]);
        }
    }
}
