<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AnggaranBulanans\AnggaranBulananResource;
use App\Filament\Resources\LaporanBulanans\LaporanBulananResource;
use Filament\Widgets\Widget;

class DashboardJadwalDeadline extends Widget
{
    protected static ?int $sort = 1; // Paling atas

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.dashboard-jadwal-deadline';

    public static function canView(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, ['pic', 'kabid_kabag', 'katim_umper', 'kabag_umum', 'kapus'], true);
    }

    public function getViewData(): array
    {
        $user  = auth()->user();
        $role  = $user?->role;
        $today = now();

        // Ambil daftar IKK khusus PIC yang sedang login (jika ada relasi)
        $ikkAmpu = $user?->ikks?->pluck('kode_ikk')->filter()->implode(', ');
        $tugasPic = $ikkAmpu
            ? "Mengisi capaian realisasi fisik dan mengunggah dokumen bukti dukung indikator kinerja ({$ikkAmpu})."
            : "Mengisi capaian realisasi fisik dan mengunggah dokumen bukti dukung indikator kinerja yang Anda ampu.";

        $daftarJadwal = [
            'pic' => [
                'role_title'  => 'PIC (Person in Charge)',
                'tugas_utama' => $tugasPic,
                'deadline'    => 'Sebelum Tanggal 1 (Maks. Tanggal 28)',
                'tgl_target'  => 1, // Target tgl 1 awal bulan berikutnya
                'action_url'  => LaporanBulananResource::getUrl('index'),
                'action_text' => 'Buka Laporan Saya',
            ],
            'kabid_kabag' => [
                'role_title'  => 'Kabid / Kabag',
                'tugas_utama' => 'Meninjau dan menyetujui laporan kinerja bulanan yang diajukan oleh PIC unit kerja Anda.',
                'deadline'    => 'Maksimal Tanggal 1',
                'tgl_target'  => 1,
                'action_url'  => LaporanBulananResource::getUrl('index'),
                'action_text' => 'Tinjau Laporan Tim',
            ],
            'katim_umper' => [
                'role_title'  => 'Katim Umper',
                'tugas_utama' => 'Mencocokkan bukti output fisik, mengisi angka realisasi anggaran, serta mengunggah bukti anggaran per bulan.',
                'deadline'    => 'Maksimal Tanggal 2',
                'tgl_target'  => 2,
                'action_url'  => AnggaranBulananResource::getUrl('index'),
                'action_text' => 'Kelola Anggaran Bulanan',
            ],
            'kabag_umum' => [
                'role_title'  => 'Kabag Umum',
                'tugas_utama' => 'Validasi lanjutan atas seluruh data laporan kinerja & realisasi anggaran sebelum pengesahan Kepala Pusat.',
                'deadline'    => 'Maksimal Tanggal 3',
                'tgl_target'  => 3,
                'action_url'  => LaporanBulananResource::getUrl('index'),
                'action_text' => 'Tinjau Validasi Laporan',
            ],
            'kapus' => [
                'role_title'  => 'Kepala Pusat (Kapus)',
                'tugas_utama' => 'Persetujuan akhir (Final approval) laporan kinerja bulanan terpadu agar data terkunci resmi.',
                'deadline'    => 'Maksimal Tanggal 3',
                'tgl_target'  => 3,
                'action_url'  => LaporanBulananResource::getUrl('index'),
                'action_text' => 'Tinjau Persetujuan Akhir',
            ],
        ];

        $myConfig = $daftarJadwal[$role] ?? [
            'role_title'  => strtoupper((string) $role),
            'tugas_utama' => 'Memantau dan memastikan pengisian pelaporan kinerja berjalan sesuai jadwal.',
            'deadline'    => 'Sesuai jadwal tahapan operasional',
            'tgl_target'  => 1,
            'action_url'  => '#',
            'action_text' => 'Lihat Laporan',
        ];

        // Hitung target tanggal deadline
        $tglDeadline = $today->copy()->addMonthNoOverflow()->startOfMonth()->addDays(max(0, $myConfig['tgl_target'] - 1));
        $hariTersisa = (int) $today->diffInDays($tglDeadline, false);
        $sisaHari    = max(0, $hariTersisa);

        if ($sisaHari <= 1) {
            $urgency = 'danger';
        } elseif ($sisaHari <= 3) {
            $urgency = 'warning';
        } else {
            $urgency = 'normal';
        }

        return [
            'config'      => $myConfig,
            'role'        => $role,
            'sisaHari'    => $sisaHari,
            'urgency'     => $urgency,
            'bulanIni'    => $today->translatedFormat('F Y'),
        ];
    }
}
