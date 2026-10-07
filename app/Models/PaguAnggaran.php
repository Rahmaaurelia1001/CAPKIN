<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaguAnggaran extends Model
{
    protected $fillable = [
        'ikk_id',
        'tahun',
        'nomor_revisi',
        'nilai_pagu',
        'berlaku_mulai_bulan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'nomor_revisi' => 'integer',
            'nilai_pagu' => 'decimal:2',
            'berlaku_mulai_bulan' => 'integer',
        ];
    }

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }
}