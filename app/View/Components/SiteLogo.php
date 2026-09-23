<?php

namespace App\View\Components;

use App\Models\PengaturanWebsite;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Throwable;

class SiteLogo extends Component
{
    public string $url;

    private static ?bool $customLogoSupported = null;

    public function __construct()
    {
        $this->url = self::resolveUrl();
    }

    public static function resolveUrl(): string
    {
        if (! self::supportsCustomLogo()) {
            return self::defaultUrl();
        }

        try {
            $path = PengaturanWebsite::query()->orderBy('id')->value('logo_path');

            if (filled($path) && Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        } catch (Throwable) {
            // Branding tidak boleh membuat halaman gagal saat database/storage belum siap.
        }

        return self::defaultUrl();
    }

    public static function supportsCustomLogo(): bool
    {
        if (self::$customLogoSupported !== null) {
            return self::$customLogoSupported;
        }

        try {
            return self::$customLogoSupported = Schema::hasTable('pengaturan_website')
                && Schema::hasColumn('pengaturan_website', 'logo_path');
        } catch (Throwable) {
            return self::$customLogoSupported = false;
        }
    }

    private static function defaultUrl(): string
    {
        return asset('images/logo al fazza.jpg');
    }

    public function render(): View|Closure|string
    {
        return view('components.site-logo');
    }
}
