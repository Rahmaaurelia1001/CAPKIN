<?php

namespace App\Services;

use App\Models\AnggaranBulanan;
use App\Models\Ikk;
use App\Models\PaguAnggaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AnggaranCalculator
{
    private static array $paguCache = [];
    private static array $realisasiCache = [];

    public static function clearCache(): void
    {
        self::$paguCache = [];
        self::$realisasiCache = [];
    }

    /**
     * Mengambil pagu aktif/terakhir yang berlaku untuk setiap IKK pada periode bulan & tahun tertentu.
     */
    public function ambilPaguPerIkk(int $tahun, int $bulan = 12, ?int $unitId = null): Collection
    {
        $key = "{$tahun}_{$bulan}_" . ($unitId ?? 'all');
        if (isset(self::$paguCache[$key])) {
            return self::$paguCache[$key];
        }

        $query = PaguAnggaran::query()
            ->with('ikk:id,kode_ikk,nama_ikk,is_active')
            ->where('tahun', $tahun)
            ->where('berlaku_mulai_bulan', '<=', $bulan)
            ->whereHas('ikk', function (Builder $q) use ($unitId) {
                $q->where('is_active', true);
                if ($unitId) {
                    $q->whereHas('units', fn (Builder $uq) => $uq->where('units.id', $unitId));
                }
            })
            ->orderBy('ikk_id')
            ->orderByDesc('berlaku_mulai_bulan')
            ->orderByDesc('nomor_revisi');

        return self::$paguCache[$key] = $query->get()->unique('ikk_id')->keyBy('ikk_id');
    }

    /**
     * Menghitung total pagu anggaran yang berlaku pada tahun & bulan tertentu.
     */
    public function hitungTotalPagu(int $tahun, int $bulan = 12, ?int $unitId = null): float
    {
        return (float) $this->ambilPaguPerIkk($tahun, $bulan, $unitId)->sum('nilai_pagu');
    }

    /**
     * Mengambil realisasi anggaran kumulatif terakhir untuk setiap IKK pada atau sebelum bulan tertentu.
     */
    public function ambilRealisasiPerIkk(int $tahun, int $bulan = 12, ?int $unitId = null): Collection
    {
        $key = "{$tahun}_{$bulan}_" . ($unitId ?? 'all');
        if (isset(self::$realisasiCache[$key])) {
            return self::$realisasiCache[$key];
        }

        $query = AnggaranBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->whereHas('ikk', function (Builder $q) use ($unitId) {
                $q->where('is_active', true);
                if ($unitId) {
                    $q->whereHas('units', fn (Builder $uq) => $uq->where('units.id', $unitId));
                }
            })
            ->orderBy('ikk_id')
            ->orderByDesc('bulan');

        return self::$realisasiCache[$key] = $query->get()->unique('ikk_id')->keyBy('ikk_id');
    }

    /**
     * Menghitung total realisasi anggaran kumulatif s.d. bulan berjalan.
     * Mengambil nilai kumulatif terakhir setiap IKK (tidak menjumlahkan baris kumulatif berulang kali).
     */
    public function hitungTotalRealisasi(int $tahun, int $bulan = 12, ?int $unitId = null): float
    {
        return (float) $this->ambilRealisasiPerIkk($tahun, $bulan, $unitId)->sum('realisasi_anggaran');
    }

    /**
     * Menghitung persentase penyerapan anggaran kumulatif.
     */
    public function hitungPersentasePenyerapan(int $tahun, int $bulan = 12, ?int $unitId = null): ?float
    {
        $totalPagu = $this->hitungTotalPagu($tahun, $bulan, $unitId);
        $totalRealisasi = $this->hitungTotalRealisasi($tahun, $bulan, $unitId);

        if ($totalPagu <= 0) {
            return null;
        }

        return ($totalRealisasi / $totalPagu) * 100;
    }

    /**
     * Menghasilkan dataset perbandingan Pagu vs Realisasi per IKK
     * Menampilkan SELURUH IKK aktif yang memiliki pagu, meskipun realisasinya masih Rp 0.
     */
    public function ambilPerbandinganPerIkk(int $tahun, int $bulan = 12, ?int $unitId = null): Collection
    {
        $paguPerIkk = $this->ambilPaguPerIkk($tahun, $bulan, $unitId);
        $realisasiPerIkk = $this->ambilRealisasiPerIkk($tahun, $bulan, $unitId);

        // Query seluruh IKK aktif yang relevan
        $ikkQuery = Ikk::query()->where('is_active', true);
        if ($unitId) {
            $ikkQuery->whereHas('units', fn (Builder $uq) => $uq->where('units.id', $unitId));
        }

        return $ikkQuery->orderBy('kode_ikk')->get()->map(function (Ikk $ikk) use ($paguPerIkk, $realisasiPerIkk) {
            $pagu = $paguPerIkk->get($ikk->id);
            $realisasiRecord = $realisasiPerIkk->get($ikk->id);
            $nilaiPagu = $pagu ? (float) $pagu->nilai_pagu : 0;
            $nilaiRealisasi = $realisasiRecord ? (float) $realisasiRecord->realisasi_anggaran : 0;
            $sisa = max(0, $nilaiPagu - $nilaiRealisasi);
            $persentase = $nilaiPagu > 0 ? ($nilaiRealisasi / $nilaiPagu) * 100 : 0;

            return [
                'ikk_id' => $ikk->id,
                'kode_ikk' => $ikk->kode_ikk,
                'nama_ikk' => $ikk->nama_ikk,
                'pagu' => $nilaiPagu,
                'realisasi' => $nilaiRealisasi,
                'sisa' => $sisa,
                'persentase' => $persentase,
                'lebihi_pagu' => $nilaiRealisasi > $nilaiPagu && $nilaiPagu > 0,
            ];
        });
    }

    /**
     * Menghasilkan deret tren bulanan (Januari s.d. Desember)
     * Untuk grafik tren penyerapan anggaran pimpinan.
     */
    public function ambilTrenBulanan(int $tahun, ?int $unitId = null): array
    {
        $bulanLabels = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $labels = [];
        $dataPenyerapan = [];
        $dataRealisasi = [];

        foreach ($bulanLabels as $bulan => $label) {
            $labels[] = $label;
            $persen = $this->hitungPersentasePenyerapan($tahun, $bulan, $unitId);
            $realisasi = $this->hitungTotalRealisasi($tahun, $bulan, $unitId);

            $dataPenyerapan[] = $persen !== null ? round($persen, 2) : 0;
            $dataRealisasi[] = round($realisasi, 2);
        }

        return [
            'labels' => $labels,
            'persentase_penyerapan' => $dataPenyerapan,
            'realisasi_nominal' => $dataRealisasi,
        ];
    }
}
