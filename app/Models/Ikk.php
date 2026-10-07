<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Unit;


class Ikk extends Model
{
    protected $table = 'ikks';

    protected $fillable = [
        'sasaran_kegiatan_id',
        'kode_ikk',
        'nama_ikk',
        'target_tahunan',
        'satuan',
        'jenis_perhitungan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_tahunan' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function sasaranKegiatan(): BelongsTo
    {
        return $this->belongsTo(SasaranKegiatan::class);
    }

    public function komponens(): HasMany
    {
    return $this->hasMany(IkkKomponen::class);
    }

    public function units(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'ikk_unit');
    }

    public function picUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ikk_user')
            ->where('role', 'pic')
            ->withTimestamps();
    }
    public function paguAnggarans(): HasMany
    {
    return $this->hasMany(PaguAnggaran::class);
    }
    public function anggaranBulanans(): HasMany
    {
    return $this->hasMany(AnggaranBulanan::class);
    }
    public function periode(): HasMany
    {
    return $this->hasMany(IkkPeriode::class);
    }
}