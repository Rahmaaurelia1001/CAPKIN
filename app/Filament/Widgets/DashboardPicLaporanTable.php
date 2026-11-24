<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use App\Models\LaporanBulanan;
use App\Services\KinerjaCalculator;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class DashboardPicLaporanTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Monitoring Pelaporan Kinerja Bulanan Saya';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'pic';
    }

    public function table(Table $table): Table
    {
        $user  = auth()->user();
        $tahun = (int) ($this->filters['tahun'] ?? now()->year);

        return $table
            ->query(
                LaporanBulanan::query()
                    ->where('pic_id', $user?->id)
                    ->where('tahun', $tahun)
                    ->with(['ikk', 'unit'])
                    ->orderBy('bulan', 'desc')
            )
            ->columns([
                TextColumn::make('bulan')
                    ->label('Periode')
                    ->state(fn (LaporanBulanan $r): string => [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ][$r->bulan] ?? "Bulan {$r->bulan}")
                    ->description(fn (LaporanBulanan $r): string => "Tahun {$r->tahun}")
                    ->icon('heroicon-o-calendar')
                    ->weight('bold')
                    ->width('140px'),

                TextColumn::make('ikk.kode_ikk')
                    ->label('Indikator Kinerja')
                    ->badge()
                    ->color('primary')
                    ->description(fn (LaporanBulanan $r): string => $r->ikk?->nama_ikk ?? '-')
                    ->wrap(),

                TextColumn::make('realisasi')
                    ->label('Realisasi Kinerja')
                    ->alignCenter()
                    ->state(function (LaporanBulanan $r): string {
                        $hasil = app(KinerjaCalculator::class)->hitung(
                            $r->ikk_id,
                            $r->bulan,
                            $r->tahun
                        );
                        $satuan = $r->ikk?->satuan ?? '';
                        $nilai = number_format($hasil['realisasi_bulanan'] ?? 0, 2, ',', '.');
                        return "{$nilai} {$satuan}";
                    })
                    ->description(function (LaporanBulanan $r): string {
                        $hasil = app(KinerjaCalculator::class)->hitung(
                            $r->ikk_id,
                            $r->bulan,
                            $r->tahun
                        );
                        $capaian = number_format($hasil['capaian_kumulatif'] ?? 0, 1, ',', '.');
                        return "Kumulatif: {$capaian}%";
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'final'                 => 'Final (Disetujui)',
                        'perlu_revisi'          => 'Perlu Revisi',
                        'draft'                 => 'Draf',
                        'diajukan'              => 'Menunggu Kabid',
                        'disetujui_kabid'       => 'Menunggu Katim UMPer',
                        'disetujui_katim_umper' => 'Menunggu Kabag Umum',
                        'disetujui_kabag_umum'  => 'Menunggu Kapus',
                        default                 => ucwords(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'final'        => 'success',
                        'perlu_revisi' => 'danger',
                        'draft'        => 'gray',
                        default        => 'warning',
                    }),

                TextColumn::make('posisi_verifikasi')
                    ->label('Posisi Saat Ini')
                    ->state(function (LaporanBulanan $r): string {
                        $isUmper = (int) $r->unit_id === 6;
                        return match ($r->status) {
                            'final'                 => '✅ Disahkan Kepala Pusat',
                            'perlu_revisi'          => '⚠️ Dikembalikan ke PIC',
                            'draft'                 => '📝 Belum Diajukan',
                            'diajukan'              => $isUmper ? '⏳ Di Meja Katim UMPer' : '⏳ Di Meja Kabid/Kabag',
                            'disetujui_kabid'       => '⏳ Di Meja Katim UMPer',
                            'disetujui_katim_umper' => '⏳ Di Meja Kabag Umum',
                            'disetujui_kabag_umum'  => '⏳ Di Meja Kepala Pusat',
                            default                 => '—',
                        };
                    })
                    ->color(fn (LaporanBulanan $r): string => match ($r->status) {
                        'final'        => 'success',
                        'perlu_revisi' => 'danger',
                        'draft'        => 'gray',
                        default        => 'info',
                    }),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Buat Laporan Baru')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->url(LaporanBulananResource::getUrl('create')),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (LaporanBulanan $r): string => LaporanBulananResource::getUrl('view', ['record' => $r])),

                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn (LaporanBulanan $r): bool => in_array($r->status, ['draft', 'perlu_revisi'], true))
                    ->url(fn (LaporanBulanan $r): string => LaporanBulananResource::getUrl('edit', ['record' => $r])),
            ])
            ->emptyStateHeading('Belum Ada Laporan Bulanan')
            ->emptyStateDescription('Anda belum membuat laporan bulanan untuk tahun anggaran yang dipilih.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}

