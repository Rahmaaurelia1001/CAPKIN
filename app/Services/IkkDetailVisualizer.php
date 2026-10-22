<?php

namespace App\Services;

use App\Models\AnggaranBulanan;
use App\Models\Ikk;
use App\Models\LaporanBulanan;
use App\Models\PaguAnggaran;
use App\Models\TargetIkk;

class IkkDetailVisualizer
{
    public static function ambilDataBulanan(Ikk $ikk, int $tahun, int $bulanAktif = 7): array
    {
        $kinerjaCalc = app(DashboardKinerjaCalculator::class);

        // Satuan yang sesuai standar definisi indikator
        $satuanTampil = match ($ikk->kode_ikk) {
            'IKK 20' => 'Orang',
            'IKK 21' => 'Dokumen',
            'IKK 22' => 'Indeks',
            'IKK 24' => '%',
            'IKK 25' => 'Poin',
            'IKK 26' => 'Poin',
            'IKK 28' => '%',
            default  => $ikk->satuan ?? '',
        };

        // Target IKK
        $targetRecord = TargetIkk::where('tahun', $tahun)
            ->whereHas('ikkPeriode', fn ($q) => $q->where('ikk_id', $ikk->id))
            ->first();
        $nilaiTarget = $targetRecord ? (float) $targetRecord->nilai_target : (float) $ikk->target_tahunan;

        // Pagu Anggaran
        $paguAwal = PaguAnggaran::where('ikk_id', $ikk->id)->where('tahun', $tahun)->orderBy('nomor_revisi')->first();
        $paguAkhir = PaguAnggaran::where('ikk_id', $ikk->id)->where('tahun', $tahun)->orderByDesc('nomor_revisi')->first();
        $nilaiPagu = $paguAkhir ? (float) $paguAkhir->nilai_pagu : ($paguAwal ? (float) $paguAwal->nilai_pagu : 0);

        // Batas bulan yang memiliki data final
        $maxBulanFinal = (int) (LaporanBulanan::where('ikk_id', $ikk->id)
            ->where('tahun', $tahun)
            ->where('status', 'final')
            ->max('bulan') ?? 7);

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $tabelBulanan = [];
        $kinerjaPoints = [];
        $anggaranPoints = [];
        $kinerjaPolyline = [];
        $anggaranPolyline = [];

        $prevAnggaran = 0;

        // Dimensi plotting SVG (viewBox 0 0 540 210)
        // Plot Area: X dari 45 sampai 505 (width: 460), Y dari 30 (100%) sampai 170 (0%) (height: 140)
        $plotXStart = 48;
        $plotXWidth = 450;
        $plotYTop = 30;
        $plotYBottom = 170;
        $plotYHeight = $plotYBottom - $plotYTop; // 140

        for ($b = 1; $b <= 12; $b++) {
            $x = $plotXStart + (($b - 1) * ($plotXWidth / 11));

            $laporan = LaporanBulanan::where('ikk_id', $ikk->id)
                ->where('tahun', $tahun)
                ->where('bulan', $b)
                ->first();

            $anggaranRow = AnggaranBulanan::where('ikk_id', $ikk->id)
                ->where('tahun', $tahun)
                ->where('bulan', $b)
                ->first();

            if ($b <= $maxBulanFinal) {
                // Kinerja
                $hasilKinerja = $kinerjaCalc->hitung($ikk, $b, $tahun);
                $realisasiKinerja = (float) $hasilKinerja['realisasi_kumulatif'];
                $capaianKinerja = (float) $hasilKinerja['capaian_kumulatif'];

                // Y Kinerja (clamped max 115% for visual)
                $clampedKinerja = min(120, max(0, $capaianKinerja));
                $yKinerja = $plotYBottom - (($clampedKinerja / 100) * $plotYHeight);

                $kinerjaPoints[] = [
                    'b'     => $b,
                    'label' => $shortMonths[$b - 1],
                    'x'     => round($x, 1),
                    'y'     => round($yKinerja, 1),
                    'val'   => round($capaianKinerja, 1),
                ];
                $kinerjaPolyline[] = round($x, 1) . ',' . round($yKinerja, 1);

                // Anggaran
                $realisasiAnggKum = $anggaranRow && $anggaranRow->realisasi_anggaran !== null
                    ? (float) $anggaranRow->realisasi_anggaran
                    : null;

                $realisasiAnggBln = $realisasiAnggKum !== null
                    ? max(0, $realisasiAnggKum - $prevAnggaran)
                    : 0;

                if ($realisasiAnggKum !== null) {
                    $prevAnggaran = $realisasiAnggKum;
                }

                $persenAngg = ($nilaiPagu > 0 && $realisasiAnggKum !== null)
                    ? ($realisasiAnggKum / $nilaiPagu) * 100
                    : 0;

                // Y Anggaran
                $clampedAngg = min(100, max(0, $persenAngg));
                $yAngg = $plotYBottom - (($clampedAngg / 100) * $plotYHeight);

                $anggaranPoints[] = [
                    'b'          => $b,
                    'label'      => $shortMonths[$b - 1],
                    'x'          => round($x, 1),
                    'y'          => round($yAngg, 1),
                    'bar_height' => round($plotYBottom - $yAngg, 1),
                    'val'        => round($persenAngg, 1),
                ];
                $anggaranPolyline[] = round($x, 1) . ',' . round($yAngg, 1);

                $tabelBulanan[] = [
                    'bulan'             => $b,
                    'nama_bulan'        => $namaBulan[$b],
                    'realisasi_kinerja' => $realisasiKinerja,
                    'capaian_kinerja'   => $capaianKinerja,
                    'realisasi_anggaran'=> $realisasiAnggKum,
                    'anggaran_bulanan'  => $realisasiAnggBln,
                    'persen_anggaran'   => $persenAngg,
                    'status_laporan'    => $laporan?->status ?? 'belum_ada',
                    'narasi'            => $laporan?->narasi_capaian ?? '-',
                ];
            } else {
                $tabelBulanan[] = [
                    'bulan'             => $b,
                    'nama_bulan'        => $namaBulan[$b],
                    'realisasi_kinerja' => null,
                    'capaian_kinerja'   => null,
                    'realisasi_anggaran'=> null,
                    'anggaran_bulanan'  => null,
                    'persen_anggaran'   => null,
                    'status_laporan'    => 'belum_ada',
                    'narasi'            => 'Belum ada laporan',
                ];
            }
        }

        // Posisi capaian terakhir s.d. bulan aktif
        $posisiTerakhir = $kinerjaCalc->hitung($ikk, min($bulanAktif, $maxBulanFinal), $tahun);
        $anggaranTerakhir = AnggaranBulanan::where('ikk_id', $ikk->id)
            ->where('tahun', $tahun)
            ->where('bulan', '<=', min($bulanAktif, $maxBulanFinal))
            ->whereNotNull('realisasi_anggaran')
            ->orderByDesc('bulan')
            ->first();

        $realisasiAnggAkhir = $anggaranTerakhir ? (float) $anggaranTerakhir->realisasi_anggaran : 0;
        $persenAnggAkhir = $nilaiPagu > 0 ? ($realisasiAnggAkhir / $nilaiPagu) * 100 : 0;

        return [
            'target'              => $nilaiTarget,
            'satuan'              => $satuanTampil,
            'pagu'                => $nilaiPagu,
            'realisasi_kinerja'   => (float) $posisiTerakhir['realisasi_kumulatif'],
            'capaian_kinerja'     => (float) $posisiTerakhir['capaian_kumulatif'],
            'realisasi_anggaran'  => $realisasiAnggAkhir,
            'persen_anggaran'     => $persenAnggAkhir,
            'tabel_bulanan'       => $tabelBulanan,
            'max_bulan'           => $maxBulanFinal,
            'short_months'        => $shortMonths,
            'kinerja_points'      => $kinerjaPoints,
            'kinerja_polyline'    => implode(' ', $kinerjaPolyline),
            'anggaran_points'     => $anggaranPoints,
            'anggaran_polyline'   => implode(' ', $anggaranPolyline),
            'plot_x_start'        => $plotXStart,
            'plot_x_width'        => $plotXWidth,
            'plot_y_top'          => $plotYTop,
            'plot_y_bottom'       => $plotYBottom,
            'plot_y_height'       => $plotYHeight,
        ];
    }
}
