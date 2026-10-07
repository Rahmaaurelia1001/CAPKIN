<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RincianKerjasama extends Model
{
    protected $fillable = [
        'laporan_bulanan_id',
        'judul_kerjasama',
    ];

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }
}