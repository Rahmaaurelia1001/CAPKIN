<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggaranBulanan extends Model
{
    protected $fillable = [
        'ikk_id',
        'bulan',
        'tahun',
        'pagu_anggaran_id',
        'realisasi_anggaran',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'realisasi_anggaran' => 'decimal:2',
        ];
    }

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }

    public function paguAnggaran(): BelongsTo
    {
        return $this->belongsTo(PaguAnggaran::class);
    }
}