<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'no_whatsapp',
    'username',
    'email',
    'password',
    'role',
    'status_akun',
    'profile_photo_path',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'no_whatsapp',
        'username',
        'email',
        'password',
        'role',
        'status_akun',
        'profile_photo_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'id_user',
            'id'
        );
    }

    public function penghuni()
    {
        return $this->hasMany(
            Penghuni::class,
            'id_user',
            'id'
        );
    }

    public function laporan()
    {
        return $this->hasMany(
            Laporan::class,
            'id_user',
            'id'
        );
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id');
    }

    /**
     * Akses layanan penghuni harus berasal dari data hunian yang aktif,
     * sehingga berlaku untuk setiap akun tanpa kondisi akun khusus.
     */
    public function isPenghuniAktif(): bool
    {
        return $this->role === 'User'
            && $this->status_akun === 'Aktif'
            && $this->penghuni()
                ->where('status_penghuni', 'Aktif')
                ->exists();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['Admin', 'Super Admin'], true);
    }

    public function isCalonPenghuni(): bool
    {
        return $this->role === 'User' && ! $this->isPenghuniAktif();
    }

    public function profilePhotoUrl(): ?string
    {
        $path = $this->managedProfilePhotoPath();

        if ($path === null || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        // Jangan gunakan APP_URL di sini. Pada instalasi lokal aplikasi bisa
        // dibuka dari port atau subfolder berbeda; URL relatif selalu mengikuti
        // public path dari request yang sedang aktif.
        $baseUrl = rtrim(request()->getBaseUrl(), '/');
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));

        return ($baseUrl ?: '') . '/storage/' . $encodedPath;
    }

    public static function isManagedProfilePhotoPath(mixed $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));

        return str_starts_with($path, 'profile-photos/')
            && ! str_contains($path, '..')
            && count(array_filter(explode('/', $path))) >= 2;
    }

    private function managedProfilePhotoPath(): ?string
    {
        if (! static::isManagedProfilePhotoPath($this->profile_photo_path)) {
            return null;
        }

        return str_replace('\\', '/', ltrim($this->profile_photo_path, '/'));
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return Str::upper(collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::substr($part, 0, 1))
            ->implode('')) ?: '?';
    }
}
