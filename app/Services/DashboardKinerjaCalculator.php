<?php

namespace App\Services;

use App\Models\AngkaKinerja;
use App\Models\Ikk;
use App\Models\IkkSumberRealisasi;
use App\Models\LaporanBulanan;
use App\Models\TargetIkk;
use Illuminate\Support\Facades\DB;

class DashboardKinerjaCalculator
{
    private static array $cache = [];
    private static ?\Illuminate\Support\Collection $sumberRealisasiCache = null;
    private static array $targetCache = [];

    public static function clearCache(): void
    {
        self::$cache = [];
        self::$sumberRealisasiCache = null;
        self::$targetCache = [];
    }
   
    public function hitung(
        Ikk $ikk,
        int $bulan,
        int $tahun
    ): array {
        $cacheKey = "{$ikk->id}_{$bulan}_{$tahun}";
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $targetKey = "{$ikk->id}_{$tahun}";
        if (! array_key_exists($targetKey, self::$targetCache)) {
            self::$targetCache[$targetKey] = (float) TargetIkk::query()
                ->where('tahun', $tahun)
                ->whereHas(
                    'ikkPeriode',
                    fn ($query) => $query->where('ikk_id', $ikk->id)
                )
                ->value('nilai_target');
        }
        $target = self::$targetCache[$targetKey];

        $adaLaporanFinal = $this->laporanFinal($bulan, $tahun)
            ->where('ikk_id', $ikk->id)
            ->exists();

        if ($ikk->jenis_perhitungan === 'gabungan') {
            $capaian = $this->hitungGabungan(
                (int) $ikk->id,
                $bulan,
                $tahun
            );

            return self::$cache[$cacheKey] = [
                'target' => $target,
                'realisasi_kumulatif' => $capaian,
                'capaian_kumulatif' => $capaian,
                'ada_laporan_final' => $adaLaporanFinal,
            ];
        }

        $realisasi = $this->hitungRealisasi(
            (int) $ikk->id,
            $bulan,
            $tahun
        );

        return self::$cache[$cacheKey] = [
            'target' => $target,
            'realisasi_kumulatif' => $realisasi,
            'capaian_kumulatif' => $target > 0
                ? ($realisasi / $target) * 100
                : 0,
            'ada_laporan_final' => $adaLaporanFinal,
        ];
    }

    private function laporanFinal(int $bulan, int $tahun)
    {
        return LaporanBulanan::query()
            ->where('status', 'final')
            ->where('tahun', $tahun)
            ->where('bulan', '<=', $bulan);
    }

   
    private function hitungRealisasi(int $ikkId, int $bulan, int $tahun): float
    {
        if (self::$sumberRealisasiCache === null) {
            self::$sumberRealisasiCache = IkkSumberRealisasi::all()->keyBy('ikk_id');
        }
        $sumber = self::$sumberRealisasiCache->get($ikkId);

        if (! $sumber) {
            return 0;
        }

        $laporanIds = $this->laporanFinal($bulan, $tahun)
            ->where('ikk_id', $ikkId)
            ->pluck('id');

        if ($laporanIds->isEmpty()) {
            return 0;
        }

        // Tipe: SUM (contoh: IKK 20 - Pelatihan, jumlah peserta)
        // Menjumlahkan rincian dari seluruh laporan final s.d. bulan berjalan (multi-PIC teragregasi)
        if ($sumber->sumber_tipe === 'sum') {
            return (float) DB::table($sumber->sumber_tabel)
                ->whereIn('laporan_bulanan_id', $laporanIds)
                ->sum($sumber->sumber_field);
        }

        // Tipe: COUNT (contoh: IKK 21 - NSPK, jumlah dokumen)
        // Menghitung rincian dokumen dari seluruh laporan final s.d. bulan berjalan
        if ($sumber->sumber_tipe === 'count') {
            return (float) DB::table($sumber->sumber_tabel)
                ->whereIn('laporan_bulanan_id', $laporanIds)
                ->count($sumber->sumber_field);
        }

        // Tipe: FIELD (contoh: IKK 22, 25, 26 - Nilai Indeks / Realisasi Langsung)
        // Mengambil skor indeks dari laporan final terbaru pada atau sebelum bulan berjalan
        if ($sumber->sumber_tipe === 'field') {
            $laporanTerbaru = $this->laporanFinal($bulan, $tahun)
                ->where('ikk_id', $ikkId)
                ->orderByDesc('bulan')
                ->orderByDesc('id')
                ->first();

            return (float) ($laporanTerbaru?->{$sumber->sumber_field} ?? 0);
        }

        return 0;
    }

    private function hitungGabungan(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $komponen = \App\Models\IkkKomponen::query()
            ->where('ikk_id', $ikkId)
            ->where('is_active', true)
            ->get();

        $hasil = 0;

        foreach ($komponen as $item) {
            // Ambil angka kinerja dari laporan final terbaru s.d. periode bulan berjalan
            // Mencegah penggandaan penyebut (target unit / target layanan) akibat sum berulang
            $angkaTerbaru = AngkaKinerja::query()
                ->where('ikk_komponen_id', $item->id)
                ->whereHas('laporanBulanan', fn ($query) =>
                    $query->where('status', 'final')
                        ->where('tahun', $tahun)
                        ->where('bulan', '<=', $bulan)
                )
                ->join('laporan_bulanans', 'laporan_bulanans.id', '=', 'angka_kinerjas.laporan_bulanan_id')
                ->orderByDesc('laporan_bulanans.bulan')
                ->orderByDesc('angka_kinerjas.id')
                ->select('angka_kinerjas.*')
                ->first();

            if (! $angkaTerbaru || (float) $angkaTerbaru->penyebut <= 0) {
                continue;
            }

            $pembilang = (float) $angkaTerbaru->pembilang;
            $penyebut = (float) $angkaTerbaru->penyebut;

            $persentase = ($pembilang / $penyebut) * 100;

            $hasil += $persentase * ((float) $item->bobot / 100);
        }

        return $hasil;
    }
}
