<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RincianNSPK extends Model
{
    protected $fillable = [
        'laporan_bulanan_id',
        'jenis_nspk',
        'nama_nspk',
        'keterangan',
    ];

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }
}