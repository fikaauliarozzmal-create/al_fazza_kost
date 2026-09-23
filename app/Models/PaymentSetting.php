<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = ['qris_path', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}
