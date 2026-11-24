<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('target_penarikan_dana', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Katim Umper who entered the target
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->year('tahun');
            $table->unsignedTinyInteger('bulan'); // 1‑12
            $table->decimal('percent', 5, 2); // e.g. 8.28 for 8,28%
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_penarikan_dana');
    }
};
?>
