<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_user',
        'jenis_laporan',
        'periode',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id'
        );
    }
}
