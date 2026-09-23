<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $primaryKey = 'id_booking';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kamar',
        'no_whatsapp',
        'tanggal_booking',
        'nama_pemesan',
        'jenis_kelamin',
        'lokasi_kerja',
        'status_booking',
        'aktivitas_dibuat_pada',
        'aktivitas_status_pada',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'aktivitas_dibuat_pada' => 'datetime',
        'aktivitas_status_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_booking', 'id_booking');
    }

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class, 'id_booking', 'id_booking');
    }
}
