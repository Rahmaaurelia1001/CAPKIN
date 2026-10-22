<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom realisasi perlu nullable karena IKK jenis tunggal (20, 21)
     * dan gabungan (24, 28) menghitung realisasi otomatis dari tabel
     * detail (rincian_pelatihans, rincian_n_s_p_k_s, angka_kinerjas),
     * sehingga PIC tidak mengisi kolom ini.
     */
    public function up(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->decimal('realisasi', 12, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->decimal('realisasi', 12, 2)->default(0)->change();
        });
    }
};
