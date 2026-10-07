<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RincianPelatihan extends Model
{
    protected $fillable = [
        'laporan_bulanan_id',
        'nama_pelatihan',
        'sumber_dana',
        'jumlah_peserta',
        'jumlah_lulus',
        'jumlah_laki_laki',
        'jumlah_perempuan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_peserta' => 'integer',
            'jumlah_lulus' => 'integer',
            'jumlah_laki_laki' => 'integer',
            'jumlah_perempuan' => 'integer',
        ];
    }

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }
}