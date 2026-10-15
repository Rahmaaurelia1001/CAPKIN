<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataDukung extends Model
{
    // Menyesuaikan nama tabel di database
    protected $table = 'data_dukung';

    protected $fillable = [
        'laporan_bulanan_id',
        'anggaran_bulanan_id',
        'bulan',
        'tahun',
        'nama_file',
        'path_file',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
        ];
    }

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }

    public function anggaranBulanan(): BelongsTo
    {
        return $this->belongsTo(AnggaranBulanan::class);
    }
}
