<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';

    protected $primaryKey = 'id_invoice';

    public $timestamps = false;

    protected $fillable = [
        'id_penghuni',
        'id_booking',
        'id_pembayaran',
        'jenis_invoice',
        'periode_bayar',
        'nomor_invoice',
        'tanggal_invoice',
        'total_tagihan',
        'total_terbayar',
        'status_invoice',
        'aktivitas_dibuat_pada',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'total_tagihan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'aktivitas_dibuat_pada' => 'datetime',
    ];

    public function penghuni()
    {
        return $this->belongsTo(
            Penghuni::class,
            'id_penghuni',
            'id_penghuni'
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

    public function pembayaran()
    {
        return $this->belongsTo(
            Pembayaran::class,
            'id_pembayaran',
            'id_pembayaran'
        );
    }

    /**
     * Semua pembayaran yang termasuk dalam invoice ini.
     *
     * Relasi menggunakan id_booking karena satu invoice
     * dapat terdiri dari lebih dari satu pembayaran.
     */
    public function pembayaranBooking()
    {
        return $this->hasMany(
            Pembayaran::class,
            'id_booking',
            'id_booking'
        );
    }
}
