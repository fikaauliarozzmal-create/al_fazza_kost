<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghuni';

    protected $primaryKey = 'id_penghuni';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_booking',
        'id_kamar',
        'tanggal_masuk',
        'taanggal_keluar',
        'status_penghuni',
        'aktivitas_masuk_pada',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'taanggal_keluar' => 'date',
        'aktivitas_masuk_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id'
        );
    }

    public function kamar()
    {
        return $this->belongsTo(
            Kamar::class,
            'id_kamar',
            'id_kamar'
        );
    }

    public function booking()
    {
        return $this->belongsTo(
            Booking::class,
            'id_booking',
            'id_booking'
        );
    }

    public function invoice()
    {
        return $this->hasMany(
            Invoice::class,
            'id_penghuni',
            'id_penghuni'
        );
    }

    public function pengaduan()
    {
        return $this->hasMany(
            Pengaduan::class,
            'id_penghuni',
            'id_penghuni'
        );
    }
}
