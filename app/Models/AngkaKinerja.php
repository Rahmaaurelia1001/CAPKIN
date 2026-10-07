<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AngkaKinerja extends Model
{
    protected $fillable = [
        'laporan_bulanan_id',
        'ikk_komponen_id',
        'pembilang',
        'penyebut',
    ];

    protected function casts(): array
    {
        return [
            'pembilang' => 'decimal:2',
            'penyebut' => 'decimal:2',
        ];
    }

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class);
    }

    public function ikkKomponen(): BelongsTo
    {
        return $this->belongsTo(IkkKomponen::class);
    }
}