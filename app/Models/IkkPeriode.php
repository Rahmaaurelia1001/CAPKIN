<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IkkPeriode extends Model
{
    protected $fillable = [
        'ikk_id',
        'tahun_mulai',
    ];

    protected function casts(): array
    {
        return [
            'tahun_mulai' => 'integer',
        ];
    }

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }
    public function targetIkks(): HasMany
    {
    return $this->hasMany(TargetIkk::class);
    }
}