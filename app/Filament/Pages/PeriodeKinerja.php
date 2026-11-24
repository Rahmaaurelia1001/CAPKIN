<?php

namespace App\Filament\Pages;

use App\Models\Ikk;
use App\Models\IkkPeriode;
use App\Models\LaporanBulanan;
use App\Models\PaguAnggaran;
use App\Models\SasaranKegiatan;
use App\Models\TargetIkk;
use App\Models\TargetPenarikanDana;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class PeriodeKinerja extends Page
{
    protected string $view = 'filament.pages.periode-kinerja';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $title = 'Rekapitulasi Struktur Kinerja Tahunan';

    protected static ?string $navigationLabel = 'Periode Kinerja';

    protected static ?string $slug = 'periode-kinerja';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 0;

    public int $tahun = 2026;

    // Modal Cari / Lihat Tahun Tertentu
    public bool $showPilihTahunModal = false;
    public ?int $tahunCariInput = null;

    // Modal Siapkan Periode Baru
    public bool $showSetupModal = false;
    public ?int $setupTahunInput = null;
    public array $setupTargetInputs = [];
    public array $setupIkkChecked = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->tahun = (int) request()->query('tahun', 2026);
    }

    public function setTahun(int $thn): void
    {
        $this->tahun = $thn;
    }

    /**
     * Mengambil daftar tahun yang benar-benar memiliki data di database
     */
    public function getDaftarTahun(): array
    {
        $years = collect([
            TargetIkk::distinct()->pluck('tahun'),
            PaguAnggaran::distinct()->pluck('tahun'),
            TargetPenarikanDana::distinct()->pluck('tahun'),
            LaporanBulanan::distinct()->pluck('tahun'),
        ])->flatten()->filter()->map(fn ($y) => (int) $y)->unique()->sortDesc()->values()->toArray();

        if (empty($years)) {
            $years = [2026];
        }

        // Jika tahun aktif belum ada di daftar (misal sedang melihat tahun kosong), tetap masukkan agar tombolnya aktif
        if (! in_array($this->tahun, $years, true)) {
            $years[] = $this->tahun;
            rsort($years);
        }

        return $years;
    }

    /**
     * Data Sasaran Kegiatan & IKK yang BENAR-BENAR memiliki data di tahun terpilih
     */
    public function getDataSasaranDanIkkProperty(): Collection
    {
        $tahun = $this->tahun;

        $targetIkkMap = TargetIkk::where('tahun', $tahun)
            ->with('ikkPeriode')
            ->get()
            ->keyBy(fn ($item) => $item->ikkPeriode?->ikk_id);

        $paguTahun = PaguAnggaran::where('tahun', $tahun)->get()->groupBy('ikk_id');
        $laporanTahun = LaporanBulanan::where('tahun', $tahun)->where('status', 'final')->get()->groupBy('ikk_id');

        $validIkkIds = collect()
            ->merge($targetIkkMap->keys())
            ->merge($paguTahun->keys())
            ->merge($laporanTahun->keys())
            ->unique()
            ->filter();

        if ($validIkkIds->isEmpty()) {
            return collect();
        }

        return SasaranKegiatan::query()
            ->where('is_active', true)
            ->whereHas('ikks', fn ($q) => $q->whereIn('id', $validIkkIds)->where('is_active', true))
            ->with([
                'ikks' => function ($q) use ($validIkkIds) {
                    $q->whereIn('id', $validIkkIds)
                        ->where('is_active', true)
                        ->with(['units:id,nama', 'picUsers:id,name']);
                },
            ])
            ->get()
            ->sortBy(function ($sk) {
                preg_match('/\d+/', (string) $sk->kode, $m);
                return (int) ($m[0] ?? 0);
            })
            ->values()
            ->map(function ($sk) use ($tahun, $targetIkkMap, $paguTahun, $laporanTahun) {
                $ikks = $sk->ikks
                    ->sortBy(function ($ikk) {
                        preg_match('/\d+/', (string) $ikk->kode_ikk, $m);
                        return (int) ($m[0] ?? 0);
                    })
                    ->values()
                    ->map(function ($ikk) use ($tahun, $targetIkkMap, $paguTahun, $laporanTahun) {
                        $targetRecord = $targetIkkMap->get($ikk->id);
                        $nilaiTarget = $targetRecord ? (float) $targetRecord->nilai_target : null;

                        $pagus = $paguTahun->get($ikk->id, collect());
                        $paguAwal = $pagus->sortBy('nomor_revisi')->first();
                        $paguAktif = $pagus->sortByDesc('nomor_revisi')->first();
                        $revisiCount = $pagus->where('nomor_revisi', '>', 0)->count();

                        $laporans = $laporanTahun->get($ikk->id, collect());
                        $laporanCount = $laporans->count();

                        return [
                            'id'                => $ikk->id,
                            'kode_ikk'          => $ikk->kode_ikk,
                            'nama_ikk'          => $ikk->nama_ikk,
                            'satuan'            => $ikk->satuan ?? '',
                            'target'            => $nilaiTarget,
                            'jenis_perhitungan' => $ikk->jenis_perhitungan,
                            'units'             => $ikk->units->pluck('nama')->toArray(),
                            'pics'              => $ikk->picUsers->pluck('name')->toArray(),
                            'pagu_awal'         => $paguAwal ? (float) $paguAwal->nilai_pagu : 0,
                            'pagu_aktif'        => $paguAktif ? (float) $paguAktif->nilai_pagu : 0,
                            'revisi_count'      => $revisiCount,
                            'laporan_count'     => $laporanCount,
                        ];
                    });

                return [
                    'id'    => $sk->id,
                    'kode'  => $sk->kode,
                    'nama'  => $sk->nama,
                    'ikks'  => $ikks,
                ];
            });
    }

    /**
     * Ringkasan Metrik untuk Header
     */
    public function getRingkasanKinerjaProperty(): array
    {
        $data = $this->data_sasaran_dan_ikk;
        $totalSk = $data->count();
        $totalIkk = $data->sum(fn ($sk) => $sk['ikks']->count());
        $totalPagu = $data->sum(fn ($sk) => $sk['ikks']->sum('pagu_aktif'));
        $totalLaporan = $data->sum(fn ($sk) => $sk['ikks']->sum('laporan_count'));

        return [
            'total_sk'      => $totalSk,
            'total_ikk'     => $totalIkk,
            'total_pagu'    => $totalPagu,
            'total_laporan' => $totalLaporan,
        ];
    }

    // =========================================================
    // MODAL PILIH TAHUN
    // =========================================================
    public function bukaPilihTahunModal(): void
    {
        $this->tahunCariInput = $this->tahun;
        $this->showPilihTahunModal = true;
    }

    public function tutupPilihTahunModal(): void
    {
        $this->showPilihTahunModal = false;
        $this->tahunCariInput = null;
    }

    public function cariTahun(): void
    {
        $thn = (int) $this->tahunCariInput;
        if ($thn >= 2020 && $thn <= 2100) {
            $this->tahun = $thn;
        }
        $this->tutupPilihTahunModal();
    }

    // =========================================================
    // MODAL SIAPKAN PERIODE BARU
    // =========================================================
    public function bukaSetupModal(?int $targetTahun = null): void
    {
        $this->setupTahunInput = $targetTahun ?: (max($this->getDaftarTahun()) + 1);
        $this->setupTargetInputs = [];
        $this->setupIkkChecked = [];

        // Ambil seluruh IKK aktif dan prefill target dari tahun sebelumnya (misal 2026)
        $ikks = Ikk::where('is_active', true)->get();
        foreach ($ikks as $ikk) {
            $targetPrev = TargetIkk::where('tahun', $this->tahun)
                ->whereHas('ikkPeriode', fn ($q) => $q->where('ikk_id', $ikk->id))
                ->value('nilai_target') ?? $ikk->target_tahunan;

            $this->setupTargetInputs[$ikk->id] = (float) $targetPrev;
            $this->setupIkkChecked[$ikk->id] = true;
        }

        $this->showSetupModal = true;
    }

    public function tutupSetupModal(): void
    {
        $this->showSetupModal = false;
        $this->setupTahunInput = null;
        $this->setupTargetInputs = [];
        $this->setupIkkChecked = [];
    }

    /**
     * Helper: Kembalikan daftar Sasaran & IKK master untuk ditampilkan di modal setup
     */
    public function getMasterSasaranDanIkkProperty(): Collection
    {
        return SasaranKegiatan::query()
            ->where('is_active', true)
            ->with(['ikks' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->sortBy(function ($sk) {
                preg_match('/\d+/', (string) $sk->kode, $m);
                return (int) ($m[0] ?? 0);
            })
            ->values()
            ->map(function ($sk) {
                $sk->ikks = $sk->ikks->sortBy(function ($ikk) {
                    preg_match('/\d+/', (string) $ikk->kode_ikk, $m);
                    return (int) ($m[0] ?? 0);
                })->values();
                return $sk;
            });
    }

    /**
     * Opsi isi target dari tahun berjalan
     */
    public function isiDariTahunLalu(): void
    {
        $ikks = Ikk::where('is_active', true)->get();
        foreach ($ikks as $ikk) {
            $targetPrev = TargetIkk::where('tahun', $this->tahun)
                ->whereHas('ikkPeriode', fn ($q) => $q->where('ikk_id', $ikk->id))
                ->value('nilai_target') ?? $ikk->target_tahunan;

            $this->setupTargetInputs[$ikk->id] = (float) $targetPrev;
            $this->setupIkkChecked[$ikk->id] = true;
        }

        Notification::make()
            ->title('Target Terisi')
            ->body("Nilai target berhasil disalin dari Tahun {$this->tahun}.")
            ->info()
            ->send();
    }

    /**
     * Opsi kosongkan nilai target
     */
    public function kosongkanTargetInput(): void
    {
        foreach (array_keys($this->setupTargetInputs) as $id) {
            $this->setupTargetInputs[$id] = 0;
        }
    }

    /**
     * Simpan seluruh target untuk tahun baru
     */
    public function simpanSetupPeriode(): void
    {
        $thn = (int) $this->setupTahunInput;
        if (! $thn || $thn < 2020 || $thn > 2100) {
            Notification::make()->title('Tahun periode tidak valid')->danger()->send();
            return;
        }

        $checkedCount = 0;
        foreach ($this->setupIkkChecked as $ikkId => $isChecked) {
            if ($isChecked) {
                $targetVal = isset($this->setupTargetInputs[$ikkId]) ? (float) $this->setupTargetInputs[$ikkId] : 0;

                $periode = IkkPeriode::firstOrCreate([
                    'ikk_id'      => $ikkId,
                    'tahun_mulai' => min(2025, $thn),
                ]);

                TargetIkk::updateOrCreate(
                    [
                        'ikk_periode_id' => $periode->id,
                        'tahun'          => $thn,
                    ],
                    [
                        'nilai_target'   => $targetVal,
                    ]
                );

                $checkedCount++;
            }
        }

        if ($checkedCount === 0) {
            Notification::make()->title('Pilih minimal satu IKK yang akan dimasukkan ke periode ini')->warning()->send();
            return;
        }

        Notification::make()
            ->title("Periode Tahun {$thn} Berhasil Disimpan!")
            ->body("Berhasil menyimpan target untuk {$checkedCount} IKK pada Tahun Anggaran {$thn}.")
            ->success()
            ->send();

        $this->tahun = $thn;
        $this->tutupSetupModal();
    }
}
