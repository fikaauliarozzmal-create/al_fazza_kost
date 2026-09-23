<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanWebsite extends Model
{
    protected $table = 'pengaturan_website';

    protected $fillable = [
        'lokasi',
        'nama_kost',
        'logo_path',
        'alamat',
        'whatsapp',
        'google_maps_url',
    ];
}
