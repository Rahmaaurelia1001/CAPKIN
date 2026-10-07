<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE data_dukung
            ADD COLUMN kunci_anggaran VARCHAR(20)
            AS (
                CASE
                    WHEN laporan_bulanan_id IS NULL
                    THEN CONCAT(bulan, '-', tahun)
                    ELSE NULL
                END
            ) PERSISTENT,
            ADD UNIQUE KEY data_dukung_kunci_anggaran_unique (kunci_anggaran)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE data_dukung
            DROP INDEX data_dukung_kunci_anggaran_unique,
            DROP COLUMN kunci_anggaran
        ");
    }
};