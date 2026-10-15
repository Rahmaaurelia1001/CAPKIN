
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_dukung', function (Blueprint $table) {
            $table->dropUnique('data_dukung_kunci_anggaran_unique');
        });
    }

    public function down(): void
    {
        Schema::table('data_dukung', function (Blueprint $table) {
            $table->unique(
                'kunci_anggaran',
                'data_dukung_kunci_anggaran_unique'
            );
        });
    }
};
