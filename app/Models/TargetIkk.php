<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetIkk extends Model
{
    protected $fillable = [
        'ikk_periode_id',
        'tahun',
        'nilai_target',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'nilai_target' => 'decimal:2',
        ];
    }

    public function ikkPeriode(): BelongsTo
    {
        return $this->belongsTo(IkkPeriode::class);
    }
}