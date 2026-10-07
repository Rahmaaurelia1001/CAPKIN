<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ikks', function (Blueprint $table) {
        $table->id();
        $table->string('kode_ikk')->unique();
        $table->string('nama_ikk');
        $table->decimal('target_tahunan', 12, 2)->default(0);
        $table->string('satuan')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikks');
    }
};
