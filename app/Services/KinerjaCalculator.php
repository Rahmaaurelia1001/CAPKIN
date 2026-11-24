<?php

namespace App\Services;

use App\Models\AngkaKinerja;
use App\Models\Ikk;
use App\Models\IkkKomponen;
use App\Models\IkkSumberRealisasi;
use App\Models\LaporanBulanan;
use App\Models\TargetIkk;
use Illuminate\Support\Facades\DB;

class KinerjaCalculator
{
    private static array $cache = [];

    public static function clearCache(): void
    {
        self::$cache = [];
    }

    public function hitungTarget(int $ikkId, int $tahun): float
    {
        return (float) TargetIkk::query()
            ->where('tahun', $tahun)
            ->whereHas('ikkPeriode', fn ($query) => $query->where('ikk_id', $ikkId))
            ->value('nilai_target');
    }

    public function hitungRealisasiBulanan(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $sumber = IkkSumberRealisasi::query()
            ->where('ikk_id', $ikkId)
            ->first();

        if (! $sumber) {
            return 0;
        }

        if ($sumber->sumber_tipe === 'field') {
            return (float) LaporanBulanan::query()
                ->where('ikk_id', $ikkId)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum($sumber->sumber_field);
        }

        if ($sumber->sumber_tipe === 'sum') {
            return (float) DB::table($sumber->sumber_tabel)
                ->join(
                    'laporan_bulanans',
                    'laporan_bulanans.id',
                    '=',
                    $sumber->sumber_tabel . '.laporan_bulanan_id'
                )
                ->where('laporan_bulanans.ikk_id', $ikkId)
                ->where('laporan_bulanans.bulan', $bulan)
                ->where('laporan_bulanans.tahun', $tahun)
                ->sum(
                    $sumber->sumber_tabel . '.' . $sumber->sumber_field
                );
        }

        if ($sumber->sumber_tipe === 'count') {
            return (float) DB::table($sumber->sumber_tabel)
                ->join(
                    'laporan_bulanans',
                    'laporan_bulanans.id',
                    '=',
                    $sumber->sumber_tabel . '.laporan_bulanan_id'
                )
                ->where('laporan_bulanans.ikk_id', $ikkId)
                ->where('laporan_bulanans.bulan', $bulan)
                ->where('laporan_bulanans.tahun', $tahun)
                ->count(
                    $sumber->sumber_tabel . '.' . $sumber->sumber_field
                );
        }

        return 0;
    }

    public function hitungRealisasiKumulatif(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $sumber = IkkSumberRealisasi::query()
            ->where('ikk_id', $ikkId)
            ->first();

        if (! $sumber) {
            return 0;
        }

        // Tipe SUM (contoh: IKK 20 - Pelatihan, jumlah peserta)
        // Akumulasi langsung dalam 1 query (tanpa looping bulanan berulang)
        if ($sumber->sumber_tipe === 'sum') {
            return (float) DB::table($sumber->sumber_tabel)
                ->join(
                    'laporan_bulanans',
                    'laporan_bulanans.id',
                    '=',
                    $sumber->sumber_tabel . '.laporan_bulanan_id'
                )
                ->where('laporan_bulanans.ikk_id', $ikkId)
                ->where('laporan_bulanans.tahun', $tahun)
                ->where('laporan_bulanans.bulan', '<=', $bulan)
                ->sum(
                    $sumber->sumber_tabel . '.' . $sumber->sumber_field
                );
        }

        // Tipe COUNT (contoh: IKK 21 - NSPK, jumlah dokumen)
        // Akumulasi langsung dalam 1 query
        if ($sumber->sumber_tipe === 'count') {
            return (float) DB::table($sumber->sumber_tabel)
                ->join(
                    'laporan_bulanans',
                    'laporan_bulanans.id',
                    '=',
                    $sumber->sumber_tabel . '.laporan_bulanan_id'
                )
                ->where('laporan_bulanans.ikk_id', $ikkId)
                ->where('laporan_bulanans.tahun', $tahun)
                ->where('laporan_bulanans.bulan', '<=', $bulan)
                ->count(
                    $sumber->sumber_tabel . '.' . $sumber->sumber_field
                );
        }

        // Tipe FIELD (contoh: IKK 22, 25, 26 - Nilai Indeks / Realisasi Langsung)
        // Mengambil skor indeks dari laporan terbaru s.d. bulan berjalan (tidak menjumlahkan indeks antar bulan)
        if ($sumber->sumber_tipe === 'field') {
            $laporanTerbaru = LaporanBulanan::query()
                ->where('ikk_id', $ikkId)
                ->where('tahun', $tahun)
                ->where('bulan', '<=', $bulan)
                ->orderByDesc('bulan')
                ->orderByDesc('id')
                ->first();

            return (float) ($laporanTerbaru?->{$sumber->sumber_field} ?? 0);
        }

        return 0;
    }

    public function hitungCapaianKumulatif(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $realisasi = $this->hitungRealisasiKumulatif(
            $ikkId,
            $bulan,
            $tahun
        );

        $target = $this->hitungTarget(
            $ikkId,
            $tahun
        );

        if ($target <= 0) {
            return 0;
        }

        return ($realisasi / $target) * 100;
    }

    public function tentukanTriwulan(int $bulan): string
    {
        return match (true) {
            $bulan >= 1 && $bulan <= 3 => 'Triwulan I',
            $bulan >= 4 && $bulan <= 6 => 'Triwulan II',
            $bulan >= 7 && $bulan <= 9 => 'Triwulan III',
            $bulan >= 10 && $bulan <= 12 => 'Triwulan IV',
            default => 'Tidak valid',
        };
    }

    public function hitungGabunganBulanan(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $komponens = IkkKomponen::query()
            ->where('ikk_id', $ikkId)
            ->where('is_active', true)
            ->get();

        $hasil = 0;

        foreach ($komponens as $komponen) {
            $totalPembilang = AngkaKinerja::query()
                ->where('ikk_komponen_id', $komponen->id)
                ->whereHas('laporanBulanan', fn ($query) =>
                    $query->where('bulan', $bulan)->where('tahun', $tahun)
                )
                ->sum('pembilang');

            $totalPenyebut = AngkaKinerja::query()
                ->where('ikk_komponen_id', $komponen->id)
                ->whereHas('laporanBulanan', fn ($query) =>
                    $query->where('bulan', $bulan)->where('tahun', $tahun)
                )
                ->sum('penyebut');

            if ($totalPenyebut <= 0) {
                continue;
            }

            $nilaiKomponen = ($totalPembilang / $totalPenyebut) * 100;

            $hasil += $nilaiKomponen * ((float) $komponen->bobot / 100);
        }

        return $hasil;
    }

    public function hitungGabunganKumulatif(
        int $ikkId,
        int $bulan,
        int $tahun
    ): float {
        $komponens = IkkKomponen::query()
            ->where('ikk_id', $ikkId)
            ->where('is_active', true)
            ->get();

        $hasil = 0;

        foreach ($komponens as $komponen) {
            // Ambil angka kinerja dari laporan terbaru s.d. periode bulan berjalan
            // untuk mencegah penggandaan penyebut (target unit / layanan) akibat sum berulang
            $angkaTerbaru = AngkaKinerja::query()
                ->where('ikk_komponen_id', $komponen->id)
                ->whereHas('laporanBulanan', fn ($query) =>
                    $query->where('tahun', $tahun)->where('bulan', '<=', $bulan)
                )
                ->join('laporan_bulanans', 'laporan_bulanans.id', '=', 'angka_kinerjas.laporan_bulanan_id')
                ->orderByDesc('laporan_bulanans.bulan')
                ->orderByDesc('angka_kinerjas.id')
                ->select('angka_kinerjas.*')
                ->first();

            if (! $angkaTerbaru || (float) $angkaTerbaru->penyebut <= 0) {
                continue;
            }

            $persentase = ((float) $angkaTerbaru->pembilang / (float) $angkaTerbaru->penyebut) * 100;

            $hasil += $persentase * ((float) $komponen->bobot / 100);
        }

        return $hasil;
    }

    public function hitung(
        int $ikkId,
        int $bulan,
        int $tahun
    ): array {
        $cacheKey = "{$ikkId}_{$bulan}_{$tahun}";
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $ikk = Ikk::find($ikkId);

        if (! $ikk) {
            return self::$cache[$cacheKey] = [
                'realisasi_bulanan' => 0,
                'realisasi_kumulatif' => 0,
                'target' => 0,
                'capaian_kumulatif' => 0,
                'triwulan' => $this->tentukanTriwulan($bulan),
            ];
        }

        $target = $this->hitungTarget($ikkId, $tahun);

        if ($ikk->jenis_perhitungan === 'gabungan') {
            $realisasiBulanan = $this->hitungGabunganBulanan(
                $ikkId,
                $bulan,
                $tahun
            );

            $realisasiKumulatif = $this->hitungGabunganKumulatif(
                $ikkId,
                $bulan,
                $tahun
            );

            $capaianKumulatif = $realisasiKumulatif;
        } else {
            $realisasiBulanan = $this->hitungRealisasiBulanan(
                $ikkId,
                $bulan,
                $tahun
            );

            $realisasiKumulatif = $this->hitungRealisasiKumulatif(
                $ikkId,
                $bulan,
                $tahun
            );

            $capaianKumulatif = $target > 0
                ? ($realisasiKumulatif / $target) * 100
                : 0;
        }

        return self::$cache[$cacheKey] = [
            'realisasi_bulanan' => $realisasiBulanan,
            'realisasi_kumulatif' => $realisasiKumulatif,
            'target' => $target,
            'capaian_kumulatif' => $capaianKumulatif,
            'triwulan' => $this->tentukanTriwulan($bulan),
        ];
    }
}
