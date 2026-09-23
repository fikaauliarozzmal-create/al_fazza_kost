<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';

    protected $primaryKey = 'id_kamar';

    public $timestamps = false;

    protected $fillable = [
        'nomor_kamar',
        'tipe_kamar',
        'lokasi_kos',
        'harga',
        'fasilitas',
        'foto_kamar',
        'status_kamar',
    ];

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'id_kamar',
            'id_kamar'
        );
    }

    public function penghuni()
    {
        return $this->hasMany(
            Penghuni::class,
            'id_kamar',
            'id_kamar'
        );
    }
}