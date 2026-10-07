<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class LaporanBulanan extends Model
{
    protected $fillable = [
        'ikk_id',
        'unit_id',
        'pic_id',
        'bulan',
        'tahun',
        'realisasi',
        'catatan',
        'bukti_dukung',
        'status',
        'narasi_capaian',
        'kendala',
        'tindak_lanjut',
        'realisasi_anggaran',
        'detail_data',
    ];

    protected function casts(): array
    {
    return [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'realisasi' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'detail_data' => 'array',
    ];
    }

    public function ikk(): BelongsTo
    {
        return $this->belongsTo(Ikk::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function persetujuan(): HasMany
    {
    return $this->hasMany(PersetujuanLaporan::class);
    }

    public function rincianPelatihans(): HasMany
    {
        return $this->hasMany(RincianPelatihan::class);
    }
    public function rincianNSPKs(): HasMany
    {
    return $this->hasMany(RincianNSPK::class);
    }
   
    public function angkaKinerjas(): HasMany
    {
    return $this->hasMany(AngkaKinerja::class);
    }
    public function rincianKerjasamas(): HasMany
    {
    return $this->hasMany(RincianKerjasama::class);
    }   
    public function dataDukungs(): HasMany
    {
    return $this->hasMany(DataDukung::class);
    }
    
}