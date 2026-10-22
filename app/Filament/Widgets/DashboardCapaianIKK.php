<?php

namespace App\Filament\Widgets;

use App\Models\Ikk;
use App\Models\LaporanBulanan;
use App\Services\DashboardKinerjaCalculator;
use App\Filament\Pages\DetailIkk;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class DashboardCapaianIKK extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Daftar Indikator Kinerja (7 IKK)';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);
        $bulan = (int) ($this->filters['bulan'] ?? now()->month);

        return $table
            ->query(
                Ikk::query()
                    ->where('is_active', true)
                    ->with([
                        'periode.targetIkks' => fn ($q) => $q->where('tahun', $tahun),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('kode_ikk')
                    ->label('Kode')
                    ->weight('bold')
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->width('90px'),

                Tables\Columns\TextColumn::make('nama_ikk')
                    ->label('Nama Indikator Kinerja')
                    ->wrap()
                    ->searchable()
                    ->description(fn (Ikk $r): string => $r->satuan ? 'Satuan: ' . $r->satuan : ''),

                Tables\Columns\TextColumn::make('target_tahun')
                    ->label('Target')
                    ->alignCenter()
                    ->state(function (Ikk $record): string {
                        $target = $record->periode
                            ->flatMap(fn ($p) => $p->targetIkks)
                            ->first();
                        return $target
                            ? number_format((float) $target->nilai_target, 0, ',', '.')
                            : '—';
                    }),

                Tables\Columns\TextColumn::make('realisasi')
                    ->label('Realisasi')
                    ->alignCenter()
                    ->state(function (Ikk $record) use ($bulan, $tahun): string {
                        $hasil = app(DashboardKinerjaCalculator::class)->hitung($record, $bulan, $tahun);
                        if (! ($hasil['ada_laporan_final'] ?? false)) {
                            return '—';
                        }
                        return number_format((float) $hasil['realisasi_kumulatif'], 0, ',', '.');
                    }),

                Tables\Columns\TextColumn::make('capaian')
                    ->label('Capaian (%)')
                    ->alignCenter()
                    ->badge()
                    ->state(function (Ikk $record) use ($bulan, $tahun): ?float {
                        $hasil = app(DashboardKinerjaCalculator::class)->hitung($record, $bulan, $tahun);
                        if (! ($hasil['ada_laporan_final'] ?? false)) {
                            return null;
                        }
                        return (float) $hasil['capaian_kumulatif'];
                    })
                    ->formatStateUsing(
                        fn (?float $state): string =>
                            $state === null ? 'Belum Ada' : number_format($state, 1, ',', '.') . '%'
                    )
                    ->color(
                        fn (?float $state): string =>
                            $state === null ? 'gray' :
                            ($state >= 100 ? 'success' : ($state >= 70 ? 'info' : 'danger'))
                    ),

                Tables\Columns\TextColumn::make('laporan_count')
                    ->label('Laporan Masuk')
                    ->alignCenter()
                    ->state(function (Ikk $record) use ($tahun): string {
                        static $countsCache = [];
                        if (! isset($countsCache[$tahun])) {
                            $records = LaporanBulanan::where('tahun', $tahun)
                                ->selectRaw('ikk_id, status, count(*) as total')
                                ->groupBy('ikk_id', 'status')
                                ->get();
                            
                            $counts = [];
                            foreach ($records as $row) {
                                $counts[$row->ikk_id]['total'] = ($counts[$row->ikk_id]['total'] ?? 0) + $row->total;
                                if ($row->status === 'final') {
                                    $counts[$row->ikk_id]['final'] = ($counts[$row->ikk_id]['final'] ?? 0) + $row->total;
                                }
                            }
                            $countsCache[$tahun] = $counts;
                        }

                        $final = $countsCache[$tahun][$record->id]['final'] ?? 0;
                        $total = $countsCache[$tahun][$record->id]['total'] ?? 0;
                        return "{$final} final / {$total} total";
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status_ikk')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->state(function (Ikk $record) use ($bulan, $tahun): string {
                        $hasil = app(DashboardKinerjaCalculator::class)->hitung($record, $bulan, $tahun);
                        if (! ($hasil['ada_laporan_final'] ?? false)) {
                            return 'Belum Ada Data';
                        }
                        $capaian = (float) $hasil['capaian_kumulatif'];
                        if ($capaian >= 100) {
                            return 'Target Tercapai';
                        }
                        if ($capaian >= 70) {
                            return 'On Track';
                        }
                        return 'Perlu Perhatian';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Target Tercapai' => 'success',
                        'On Track'        => 'info',
                        'Perlu Perhatian' => 'danger',
                        default           => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match ($state) {
                        'Target Tercapai' => 'heroicon-m-check-circle',
                        'On Track'        => 'heroicon-m-arrow-trending-up',
                        'Perlu Perhatian' => 'heroicon-m-exclamation-triangle',
                        default           => 'heroicon-m-minus-circle',
                    }),
            ])
            ->actions([
                Action::make('lihatDetail')
                    ->label('Detail & Grafik')
                    ->icon('heroicon-m-chart-bar-square')
                    ->button()
                    ->size('xs')
                    ->color('primary')
                    ->url(fn (Ikk $record) => DetailIkk::getUrl([
                        'ikk_id' => $record->id,
                        'tahun'  => $tahun,
                        'bulan'  => $bulan,
                    ]))
                    ->openUrlInNewTab(false),
            ])
            ->recordAction('lihatDetail')
            ->defaultSort('kode_ikk')
            ->paginated(false)
            ->striped();
    }
}
