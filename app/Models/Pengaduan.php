<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'id_penghuni',
        'tanggal',
        'kategori',
        'judul',
        'isi_pengaduan',
        'status',
        'tanggapan_admin',
        'lampiran_foto',
        'lampiran_video',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penghuni()
    {
        return $this->belongsTo(
            Penghuni::class,
            'id_penghuni',
            'id_penghuni'
        );
    }
}
