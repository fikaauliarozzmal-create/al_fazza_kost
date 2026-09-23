<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'jenis_pembayaran',
        'periode_bayar',
        'metode_bayar',
        'tanggal_bayar',
        'jumlah_bayar',
        'bukti_bayar',
        'status_bayar',
        'alasan_penolakan',
        'id_admin_verifikasi',
        'tanggal_verifikasi',
        'aktivitas_dibuat_pada',
        'aktivitas_dikirim_pada',
        'gateway_provider', 'gateway_partner_reference', 'gateway_external_id', 'gateway_reference', 'gateway_status', 'gateway_redirect_url', 'gateway_expires_at', 'gateway_notified_at', 'gateway_metadata',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'aktivitas_dibuat_pada' => 'datetime',
        'aktivitas_dikirim_pada' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
        'gateway_expires_at' => 'datetime',
        'gateway_notified_at' => 'datetime',
        'gateway_metadata' => 'array',
    ];

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
        return $this->hasOne(
            Invoice::class,
            'id_pembayaran',
            'id_pembayaran'
        );
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'id_pembayaran', 'id_pembayaran');
    }

    public function adminVerifikasi()
    {
        return $this->belongsTo(User::class, 'id_admin_verifikasi');
    }

    /** Label UI tanpa mengubah nilai status lama yang sudah tersimpan. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_bayar) {
            'Pending' => filled($this->metode_bayar) ? 'Menunggu Verifikasi' : 'Menunggu Pembayaran',
            'Valid' => 'Lunas',
            default => $this->status_bayar,
        };
    }
}
