<?php

namespace App\Console\Commands;

use App\Mail\ReminderDeadlineMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class KirimReminderDeadline extends Command
{
    /**
     * Signature command:
     * - `php artisan simonika:kirim-reminder` : Rutin harian scheduler
     * - `php artisan simonika:kirim-reminder --email=aurelzami039@gmail.com` : Tes kirim langsung ke 1 email
     * - `php artisan simonika:kirim-reminder --test` : Kirim ke semua user di database tanpa cek tanggal
     */
    protected $signature = 'simonika:kirim-reminder {--email= : Kirim langsung tes email ke alamat tertentu} {--test : Kirim ke semua role di database tanpa cek tanggal}';

    protected $description = 'Kirim email pengingat deadline laporan bulanan ke masing-masing role';

    private array $jadwalDeadline = [
        'pic' => [
            'tanggal_deadline'   => 1,
            'label_deadline'     => 'Sebelum Tanggal 1 bulan berikutnya (Maks. Tanggal 28)',
            'aktivitas'          => 'Mengisi realisasi capaian fisik dan mengunggah dokumen bukti dukung indikator kinerja yang Anda ampu.',
            'hari_notif_sebelum' => 3,
            'next_month'         => true,
        ],
        'kabid_kabag' => [
            'tanggal_deadline'   => 1,
            'label_deadline'     => 'Maksimal Tanggal 1 bulan berikutnya',
            'aktivitas'          => 'Menyetujui data laporan yang diajukan tim/unit Anda di SIMONIKA.',
            'hari_notif_sebelum' => 1,
            'next_month'         => true,
        ],
        'katim_umper' => [
            'tanggal_deadline'   => 2,
            'label_deadline'     => 'Maksimal Tanggal 2 bulan berikutnya',
            'aktivitas'          => 'Mencocokkan bukti output, mengisi angka anggaran, dan mengunggah dokumen bukti anggaran per bulan.',
            'hari_notif_sebelum' => 1,
            'next_month'         => true,
        ],
        'kabag_umum' => [
            'tanggal_deadline'   => 3,
            'label_deadline'     => 'Maksimal Tanggal 3 bulan berikutnya',
            'aktivitas'          => 'Validasi lanjutan atas seluruh data laporan kinerja bulanan.',
            'hari_notif_sebelum' => 1,
            'next_month'         => true,
        ],
        'kapus' => [
            'tanggal_deadline'   => 3,
            'label_deadline'     => 'Maksimal Tanggal 3 bulan berikutnya',
            'aktivitas'          => 'Persetujuan akhir laporan — laporan menjadi final dan terkunci setelah disetujui.',
            'hari_notif_sebelum' => 1,
            'next_month'         => true,
        ],
    ];

    public function handle(): int
    {
        $targetEmail = $this->option('email');
        $isTest      = $this->option('test');
        $today       = now();
        $hariIni     = (int) $today->day;
        $bulanIni    = $today->translatedFormat('F Y');

        $this->info("=== SIMONIKA: Kirim Reminder Deadline ===");
        $this->info("Tanggal hari ini: {$today->format('d-m-Y')}");

        // JIKA DIJALANKAN DENGAN --email=alamat@email.com (Tes Langsung)
        if ($targetEmail) {
            $this->info("Mengirim email percobaan langsung ke: {$targetEmail} ...");
            try {
                Mail::to($targetEmail)->send(new ReminderDeadlineMail(
                    namaUser:    'Pengguna SIMONIKA',
                    role:        'PIC (Person in Charge)',
                    aktivitas:   $this->jadwalDeadline['pic']['aktivitas'],
                    deadline:    $this->jadwalDeadline['pic']['label_deadline'],
                    hariTersisa: 8,
                    bulanTahun:  $bulanIni,
                    urlSistem:   config('app.url') . '/app',
                ));
                $this->info("  ✅ BERHASIL! Email pengingat telah dikirim ke {$targetEmail}");
                $this->info("  Silakan cek Inbox (atau folder Spam/Promotions) email Anda.");
                return self::SUCCESS;
            } catch (\Exception $e) {
                $this->error("  ❌ GAGAL mengirim email: " . $e->getMessage());
                return self::FAILURE;
            }
        }

        $totalKirim = 0;

        foreach ($this->jadwalDeadline as $role => $jadwal) {
            if ($jadwal['next_month']) {
                $tglDeadline = $today->copy()->addMonthNoOverflow()->startOfMonth()->addDays($jadwal['tanggal_deadline'] - 1);
                $tglNotif    = (int) $today->copy()->endOfMonth()->subDays($jadwal['hari_notif_sebelum'] - 1)->day;
            } else {
                $tglDeadline = $today->copy()->setDay($jadwal['tanggal_deadline']);
                $tglNotif    = $jadwal['tanggal_deadline'] - $jadwal['hari_notif_sebelum'];
            }

            $hariTersisa = (int) $today->diffInDays($tglDeadline, false);

            if (! $isTest && $hariIni !== (int) ($jadwal['next_month']
                    ? $today->copy()->endOfMonth()->subDays($jadwal['hari_notif_sebelum'] - 1)->day
                    : $tglNotif)) {
                $this->line("  [{$role}] Bukan hari notifikasi (hari ini tgl {$hariIni}). Skip.");
                continue;
            }

            $users = User::where('role', $role)->whereNotNull('email')->get();

            if ($users->isEmpty()) {
                $this->warn("  [{$role}] Tidak ada user dengan email di database. Skip.");
                continue;
            }

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new ReminderDeadlineMail(
                        namaUser:    $user->name,
                        role:        $this->labelRole($role),
                        aktivitas:   $jadwal['aktivitas'],
                        deadline:    $jadwal['label_deadline'],
                        hariTersisa: max(1, $hariTersisa),
                        bulanTahun:  $bulanIni,
                        urlSistem:   config('app.url') . '/app',
                    ));

                    $this->info("  ✅ Terkirim ke: {$user->name} <{$user->email}> [{$role}]");
                    $totalKirim++;
                } catch (\Exception $e) {
                    $this->error("  ❌ Gagal kirim ke {$user->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("=== Selesai. Total email terkirim: {$totalKirim} ===");

        return self::SUCCESS;
    }

    private function labelRole(string $role): string
    {
        return match ($role) {
            'pic'         => 'PIC (Person in Charge)',
            'kabid_kabag' => 'Kabid / Kabag',
            'katim_umper' => 'Katim Umper',
            'kabag_umum'  => 'Kabag Umum',
            'kapus'       => 'Kepala Pusat (Kapus)',
            default       => $role,
        };
    }
}
