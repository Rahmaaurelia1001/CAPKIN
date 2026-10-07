<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanLaporan extends Model
{
    protected $table = 'persetujuan_laporan';

    protected $fillable = [
        'laporan_bulanan_id',
        'tahap',
        'user_id',
        'keputusan',
        'catatan',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'tahap' => 'integer',
            'diproses_pada' => 'datetime',
        ];
    }

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}