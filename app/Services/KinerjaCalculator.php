<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\IkkSumberRealisasi;
use App\Models\LaporanBulanan;
use App\Models\TargetIkk;

class KinerjaCalculator
{
    public function hitungTarget(int $ikkId, int $tahun): float
    {
        return (float) TargetIkk::query()
            ->where('tahun', $tahun)
            ->whereHas('ikkPeriode', function ($query) use ($ikkId) {
                $query->where('ikk_id', $ikkId);
            })
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
                ->sum($sumber->sumber_tabel . '.' . $sumber->sumber_field);
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
                ->count($sumber->sumber_tabel . '.' . $sumber->sumber_field);
        }

        return 0;
    }

    public function hitungRealisasiKumulatif(
    int $ikkId,
    int $bulan,
    int $tahun
    ): float {
        $total = 0;

        for ($i = 1; $i <= $bulan; $i++) {
            $total += $this->hitungRealisasiBulanan(
                $ikkId,
                $i,
                $tahun
            );
        }

        return $total;
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

    public function hitungKomponenKumulatif(
    int $ikkKomponenId,
    int $bulan,
    int $tahun
    ): float {
    $totalPembilang = \App\Models\AngkaKinerja::query()
        ->where('ikk_komponen_id', $ikkKomponenId)
        ->whereHas('laporanBulanan', function ($query) use ($bulan, $tahun) {
            $query->where('bulan', '<=', $bulan)
                ->where('tahun', $tahun);
        })
        ->sum('pembilang');

    $totalPenyebut = \App\Models\AngkaKinerja::query()
        ->where('ikk_komponen_id', $ikkKomponenId)
        ->whereHas('laporanBulanan', function ($query) use ($bulan, $tahun) {
            $query->where('bulan', '<=', $bulan)
                ->where('tahun', $tahun);
        })
        ->sum('penyebut');

    if ($totalPenyebut <= 0) {
        return 0;
    }

    return ($totalPembilang / $totalPenyebut) * 100;
}   

public function hitungGabunganKumulatif(
    int $ikkId,
    int $bulan,
    int $tahun
): float {
    $komponens = \App\Models\IkkKomponen::query()
        ->where('ikk_id', $ikkId)
        ->where('is_active', true)
        ->get();

    $hasil = 0;

    foreach ($komponens as $komponen) {
        $nilaiKomponen = $this->hitungKomponenKumulatif(
            $komponen->id,
            $bulan,
            $tahun
        );

        $hasil += $nilaiKomponen * ((float) $komponen->bobot / 100);
    }

    return $hasil;
}

public function hitung(
    int $ikkId,
    int $bulan,
    int $tahun
): array {
    $ikk = \App\Models\Ikk::find($ikkId);

    if (! $ikk) {
        return [
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

    return [
        'realisasi_bulanan' => $realisasiBulanan,
        'realisasi_kumulatif' => $realisasiKumulatif,
        'target' => $target,
        'capaian_kumulatif' => $capaianKumulatif,
        'triwulan' => $this->tentukanTriwulan($bulan),
    ];
}

public function hitungGabunganBulanan(
    int $ikkId,
    int $bulan,
    int $tahun
): float {
    $komponens = \App\Models\IkkKomponen::query()
        ->where('ikk_id', $ikkId)
        ->where('is_active', true)
        ->get();

    $hasil = 0;

    foreach ($komponens as $komponen) {
        $totalPembilang = \App\Models\AngkaKinerja::query()
            ->where('ikk_komponen_id', $komponen->id)
            ->whereHas('laporanBulanan', function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->sum('pembilang');

        $totalPenyebut = \App\Models\AngkaKinerja::query()
            ->where('ikk_komponen_id', $komponen->id)
            ->whereHas('laporanBulanan', function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->sum('penyebut');

        if ($totalPenyebut <= 0) {
            continue;
        }

        $nilaiKomponen = ($totalPembilang / $totalPenyebut) * 100;

        $hasil += $nilaiKomponen * ((float) $komponen->bobot / 100);
    }

    return $hasil;
}
    
}