<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IkkKomponen extends Model
{
    protected $fillable = [
        'ikk_id',
        'kode_komponen',
        'nama_komponen',
        'bobot',
        'sumber_pembilang',
        'sumber_penyebut',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }
    public function angkaKinerjas(): HasMany
    {
    return $this->hasMany(AngkaKinerja::class);
    }
}