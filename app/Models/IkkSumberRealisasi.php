<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IkkSumberRealisasi extends Model
{
    protected $table = 'ikk_sumber_realisasi';

    protected $fillable = [
        'ikk_id',
        'sumber_tipe',
        'sumber_tabel',
        'sumber_field',
    ];

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }
}