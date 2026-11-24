<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetPenarikanDana extends Model
{
    use HasFactory;

    protected $table = 'target_penarikan_dana';

    protected $fillable = [
        'user_id',
        'tahun',
        'bulan',
        'percent',
    ];
}
?>
