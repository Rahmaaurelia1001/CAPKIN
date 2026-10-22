<?php

namespace App\Filament\Pages;

use App\Models\Ikk;
use App\Services\IkkDetailVisualizer;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DetailIkk extends Page
{
    protected string $view = 'filament.pages.detail-ikk';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $title = 'Detail Kinerja & Anggaran IKK';

    protected static ?string $slug = 'detail-ikk';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?int $record = null;
    public ?Ikk $ikk = null;
    public int $tahun = 2026;
    public int $bulan = 7;
    public array $dataDetail = [];

    public function mount($record = null): void
    {
        $ikkId = $record ?? request()->query('ikk_id') ?? Ikk::where('is_active', true)->first()?->id;
        $this->ikk = Ikk::findOrFail($ikkId);
        $this->record = $this->ikk->id;
        $this->tahun = (int) request()->query('tahun', 2026);
        $this->bulan = (int) request()->query('bulan', 7);

        $this->loadData();
    }

    public function updatedRecord($value): void
    {
        if ($value) {
            $this->ikk = Ikk::findOrFail($value);
            $this->loadData();
        }
    }

    public function updatedTahun($value): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->dataDetail = IkkDetailVisualizer::ambilDataBulanan($this->ikk, $this->tahun, $this->bulan);
    }

    public function getTitle(): string
    {
        return $this->ikk ? "{$this->ikk->kode_ikk} — {$this->ikk->nama_ikk}" : 'Detail IKK';
    }

    public function getSubheading(): ?string
    {
        return "Rincian Capaian Kinerja dan Penyerapan Anggaran Bulanan — Tahun Anggaran {$this->tahun}";
    }
}
