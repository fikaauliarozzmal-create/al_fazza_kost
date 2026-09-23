<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refund';
    protected $primaryKey = 'id_refund';
    public $timestamps = false;

    protected $fillable = ['id_pembayaran', 'status_refund', 'nominal_refund', 'tanggal_refund', 'catatan_refund', 'id_admin'];

    protected $casts = ['nominal_refund' => 'decimal:2', 'tanggal_refund' => 'date'];

    public function pembayaran() { return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'id_pembayaran'); }
    public function admin() { return $this->belongsTo(User::class, 'id_admin', 'id'); }
}
