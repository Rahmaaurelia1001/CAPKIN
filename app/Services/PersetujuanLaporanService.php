<?php

namespace App\Services;

use App\Models\LaporanBulanan;
use App\Models\PersetujuanLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersetujuanLaporanService
    {/**
    * Semua PIC mengajukan laporan ke tahap persetujuan pertama.
    * Unit 6: PIC -> Katim Umper.
    * Unit lainnya: PIC -> Kabid/Kabag.
    */
    private function statusSetelahDiajukan(
        LaporanBulanan $laporan
    ): string {
        return 'diajukan';
    }

   private function statusUntukTahapKabagUmum(
    LaporanBulanan $laporan
    ): string {
        return 'disetujui_katim_umper';
    }

    
    public function ajukan(LaporanBulanan $laporan): void
    {
        DB::transaction(function () use ($laporan): void {
            $laporan = LaporanBulanan::query()
                ->lockForUpdate()
                ->findOrFail($laporan->id);

            $user = auth()->user();

            // Pastikan hanya PIC pemilik laporan yang dapat mengajukan.
            if (
                ! $user
                || $user->role !== 'pic'
                || (int) $laporan->pic_id !== (int) $user->id
            ) {
                abort(403);
            }

            // Pastikan IKK laporan memang ditugaskan kepada PIC ini.
            if (! $laporan->ikk()
                ->whereHas('picUsers', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'ikk_id' => 'Anda tidak ditugaskan untuk mengelola IKK pada laporan ini.',
                ]);
            }

            // Laporan hanya dapat diajukan dari status draft atau revisi.
            if (! in_array(
                $laporan->status,
                ['draft', 'perlu_revisi'],
                true
            )) {
                throw ValidationException::withMessages([
                    'status' => 'Laporan ini tidak dapat diajukan pada status sekarang.',
                ]);
            }

            // Catat riwayat pengajuan.
            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => 1,
                'user_id' => $user->id,
                'keputusan' => 'diajukan',
                'diproses_pada' => now(),
            ]);

            // Ubah status laporan menjadi diajukan.
            $laporan->update([
                'status' => $this->statusSetelahDiajukan($laporan),
            ]);
        });
    }


    public function setujui(
        LaporanBulanan $laporan,
        int $tahap,
        ?string $catatan = null,
    ): void {
        DB::transaction(function () use ($laporan, $tahap, $catatan): void {
            $laporan = LaporanBulanan::query()
                ->lockForUpdate()
                ->findOrFail($laporan->id);

            $user = auth()->user();

            $tahapan = [
                2 => [
                    'role' => 'kabid_kabag',
                    'status_sekarang' => 'diajukan',
                    'status_berikutnya' => 'disetujui_kabid',
                ],
                3 => [
                    'role' => 'katim_umper',
                    'status_sekarang' => (int) $laporan->unit_id === 6
                        ? 'diajukan'
                        : 'disetujui_kabid',
                    'status_berikutnya' => 'disetujui_katim_umper',
                ],
                4 => [
                    'role' => 'kabag_umum',
                    'status_sekarang' => 'disetujui_katim_umper',
                    'status_berikutnya' => 'disetujui_kabag_umum',
                ],
                5 => [
                    'role' => 'kapus',
                    'status_sekarang' => 'disetujui_kabag_umum',
                    'status_berikutnya' => 'final',
                ],
            ];

            $aturan = $tahapan[$tahap] ?? null;

            if (
                ! $aturan
                || ! $user
                || $user->role !== $aturan['role']
            ) {
                abort(403);
            }

            if (
                $tahap === 2
                && (int) $laporan->unit_id === 6
            ) {
                abort(403);
            }

            // Kabid/Kabag hanya memproses laporan unitnya sendiri.
            if (
                $tahap === 2
                && (int) $laporan->unit_id !== (int) $user->unit_id
            ) {
                abort(403);
            }

            if ($laporan->status !== $aturan['status_sekarang']) {
                throw ValidationException::withMessages([
                    'status' => 'Laporan belum berada pada tahap persetujuan ini.',
                ]);
            }

            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => $tahap,
                'user_id' => $user->id,
                'keputusan' => 'disetujui',
                'catatan' => $catatan ? trim($catatan) : null,
                'diproses_pada' => now(),
            ]);

            $laporan->update([
                'status' => $aturan['status_berikutnya'],
            ]);
        });
    }

    public function kembalikan(
        LaporanBulanan $laporan,
        int $tahap,
        string $catatan,
    ): void {
        if (! in_array($tahap, [2, 3, 4], true)) {
            abort(403);
        }

        if (trim($catatan) === '') {
            throw ValidationException::withMessages([
                'catatan' => 'Catatan pengembalian wajib diisi.',
            ]);
        }

        DB::transaction(function () use (
            $laporan,
            $tahap,
            $catatan,
        ): void {
            $laporan = LaporanBulanan::query()
                ->lockForUpdate()
                ->findOrFail($laporan->id);

            $user = auth()->user();

            $tahapan = [
                2 => [
                    'role' => 'kabid_kabag',
                    'status_sekarang' => 'diajukan',
                ],
                3 => [
                    'role' => 'katim_umper',
                    'status_sekarang' => (int) $laporan->unit_id === 6
                        ? 'diajukan'
                        : 'disetujui_kabid',
                ],
                4 => [
                    'role' => 'kabag_umum',
                    'status_sekarang' => 'disetujui_katim_umper',
                ],
            ];

            $aturan = $tahapan[$tahap];

            if (! $user || $user->role !== $aturan['role']) {
                abort(403);
            }

            if (
                $tahap === 2
                && (int) $laporan->unit_id === 6
            ) {
                abort(403);
            }

            if (
                $tahap === 2
                && (int) $laporan->unit_id !== (int) $user->unit_id
            ) {
                abort(403);
            }

            if ($laporan->status !== $aturan['status_sekarang']) {
                throw ValidationException::withMessages([
                    'status' => 'Laporan belum berada pada tahap ini.',
                ]);
            }

            PersetujuanLaporan::create([
                'laporan_bulanan_id' => $laporan->id,
                'tahap' => $tahap,
                'user_id' => $user->id,
                'keputusan' => 'dikembalikan',
                'catatan' => trim($catatan),
                'diproses_pada' => now(),
            ]);

            $laporan->update([
                'status' => 'perlu_revisi',
            ]);
        });
    }
}