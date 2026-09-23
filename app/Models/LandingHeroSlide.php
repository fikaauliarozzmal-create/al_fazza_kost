<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingHeroSlide extends Model
{
    protected $table = 'landing_hero_slides';

    protected $fillable = [
        'judul',
        'subjudul',
        'lokasi',
        'foto',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];
}