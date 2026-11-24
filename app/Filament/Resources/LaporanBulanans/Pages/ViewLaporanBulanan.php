<?php

namespace App\Filament\Resources\LaporanBulanans\Pages;

use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use App\Services\KinerjaCalculator;

class ViewLaporanBulanan extends ViewRecord
{
    protected static string $resource = LaporanBulananResource::class;

    protected static ?string $title = 'Detail Laporan';

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            ViewEntry::make('hero_banner')
                ->view('filament.infolists.laporan-hero-banner')
                ->columnSpanFull(),

            Section::make('Alur & Riwayat Persetujuan')
                ->icon('heroicon-o-check-badge')
                ->columnSpanFull()
                ->schema([
                    ViewEntry::make('timeline')
                        ->view('filament.infolists.riwayat-persetujuan-timeline')
                        ->columnSpanFull(),
                ]),

            Section::make('Detail Pelatihan — IKK 20')
                ->icon('heroicon-o-academic-cap')
                ->columnSpanFull()
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK20'
                )
                ->schema([
                    ViewEntry::make('detail_pelatihan')
                        ->view('filament.infolists.detail-pelatihan-cards')
                        ->columnSpanFull(),
                ]),

            Section::make('Detail NSPK — IKK 21')
                ->icon('heroicon-o-document-text')
                ->columnSpanFull()
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK21'
                )
                ->schema([
                    ViewEntry::make('detail_nspk')
                        ->view('filament.infolists.detail-nspk-cards')
                        ->columnSpanFull(),
                ]),

            Section::make('Detail Komponen Akuntabilitas — IKK 24')
                ->icon('heroicon-o-chart-pie')
                ->columnSpanFull()
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK24'
                )
                ->schema([
                    ViewEntry::make('detail_komponen')
                        ->view('filament.infolists.detail-komponen-cards')
                        ->columnSpanFull(),
                ]),

            Section::make('Detail Kerja Sama — IKK 28')
                ->icon('heroicon-o-user-group')
                ->columnSpanFull()
                ->visible(fn ($record) =>
                    strtoupper(str_replace([' ', '_', '-'], '', (string) $record->ikk?->kode_ikk)) === 'IKK28'
                )
                ->schema([
                    ViewEntry::make('detail_kerjasama')
                        ->view('filament.infolists.detail-kerjasama-cards')
                        ->columnSpanFull(),
                ]),

            Section::make('Catatan Pelaporan & Bukti Pendukung')
                ->icon('heroicon-o-clipboard-document-list')
                ->columnSpanFull()
                ->schema([
                    ViewEntry::make('detail_info')
                        ->view('filament.infolists.detail-info-laporan')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(LaporanBulananResource::getUrl('index')),

            \Filament\Actions\Action::make('ajukan')
                ->label('Ajukan Laporan')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Laporan')
                ->modalDescription('Setelah diajukan, laporan tidak dapat diedit lagi oleh PIC.')
                ->visible(fn (): bool =>
                    auth()->user()?->role === 'pic'
                    && (int) $this->record->pic_id === (int) auth()->id()
                    && in_array($this->record->status, ['draft', 'perlu_revisi'], true)
                )
                ->action(function (): void {
                    app(\App\Services\PersetujuanLaporanService::class)->ajukan($this->record);
                    \Filament\Notifications\Notification::make()->title('Laporan berhasil diajukan')->success()->send();
                }),

            \Filament\Actions\Action::make('setujui')
                ->label(fn (): string => auth()->user()?->role === 'kapus' ? 'Setujui (Final)' : 'Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation(fn () => auth()->user()?->role !== 'kapus')
                ->schema(fn () => auth()->user()?->role === 'kapus' ? [
                    \Filament\Forms\Components\Textarea::make('catatan_kapus')
                        ->label('Catatan / Arahan Kapus untuk Kabid/Kabag (Opsional)')
                        ->placeholder('Tuliskan arahan, masukan, atau apresiasi untuk bidang terkait (opsional)...')
                        ->rows(3),
                ] : [])
                ->visible(fn (): bool => $this->canUserApprove())
                ->action(function (array $data): void {
                    $tahap = match (auth()->user()?->role) {
                        'kabid_kabag' => 2,
                        'katim_umper' => 3,
                        'kabag_umum' => 4,
                        'kapus' => 5,
                        default => null,
                    };
                    if ($tahap) {
                        app(\App\Services\PersetujuanLaporanService::class)->setujui(
                            $this->record, $tahap, $data['catatan_kapus'] ?? null
                        );
                        \Filament\Notifications\Notification::make()->title('Laporan berhasil disetujui')->success()->send();
                    }
                }),

            \Filament\Actions\Action::make('kembalikan')
                ->label('Kembalikan untuk Revisi')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Revisi')
                        ->required()
                        ->maxLength(2000),
                ])
                ->visible(fn (): bool => $this->canUserReturn())
                ->action(function (array $data): void {
                    $tahap = match (auth()->user()?->role) {
                        'kabid_kabag' => 2,
                        'katim_umper' => 3,
                        'kabag_umum' => 4,
                        default => null,
                    };
                    if ($tahap) {
                        app(\App\Services\PersetujuanLaporanService::class)->kembalikan($this->record, $tahap, $data['catatan']);
                        \Filament\Notifications\Notification::make()->title('Laporan dikembalikan untuk revisi')->warning()->send();
                    }
                }),
        ];
    }

    private function canUserApprove(): bool
    {
        $user = auth()->user();
        if (! $user) return false;

        $r = $this->record;

        if ($user->role === 'kabid_kabag') {
            return (int) $r->unit_id === (int) $user->unit_id && (int) $r->unit_id !== 6 && $r->status === 'diajukan';
        }

        if ($user->role === 'katim_umper') {
            return ((int) $r->unit_id === 6 && $r->status === 'diajukan') || ((int) $r->unit_id !== 6 && $r->status === 'disetujui_kabid');
        }

        if ($user->role === 'kabag_umum') {
            return $r->status === 'disetujui_katim_umper';
        }

        if ($user->role === 'kapus') {
            return $r->status === 'disetujui_kabag_umum';
        }

        return false;
    }

    private function canUserReturn(): bool
    {
        $user = auth()->user();
        if (! $user || $user->role === 'kapus') return false;

        return $this->canUserApprove();
    }
}

