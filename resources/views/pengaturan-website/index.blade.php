<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Website | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'pengaturan-website'])

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h1>Pengaturan Website</h1>
                <p>Kelola logo dan tampilan Landing Hero Beranda Al Fazza Kost.</p>
            </div>

            <div class="admin-profile">
                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>Admin</small>
                </div>
            </div>
        </header>

        <x-flash-toast />

        {{-- LOGO UTAMA --}}
        <section class="website-brand-card" aria-labelledby="logo-utama-title">
            <div class="website-section-heading">
                <span class="website-section-number">01</span>

                <div>
                    <span class="website-section-kicker">Identitas Website</span>
                    <h2 id="logo-utama-title">Logo Utama Al Fazza Kost</h2>
                    <p>
                        Logo ini digunakan pada navbar, login, dashboard,
                        splash, footer, dan halaman lainnya.
                    </p>
                </div>
            </div>

            <form
                action="{{ route('pengaturan-website.logo.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="website-brand-form"
            >
                @csrf
                @method('PUT')

                <div class="website-logo-setting__preview website-logo-setting__preview--global">
                    <x-site-logo
                        class="site-logo site-logo--settings"
                        data-logo-preview
                    />
                </div>

                <div class="website-logo-setting__control">
                    <label for="website-logo">
                        <span>Ganti logo utama</span>

                        <input
                            id="website-logo"
                            type="file"
                            name="logo"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-logo-input
                            {{ $logoCustomSupported ? '' : 'disabled' }}
                        >

                        <small>
                            JPG, JPEG, PNG, atau WEBP · maksimal 2 MB.
                        </small>
                    </label>

                    @if(! $logoCustomSupported)
                        <p class="website-logo-notice">
                            Upload custom belum tersedia pada database.
                            Logo default tetap digunakan.
                        </p>
                    @endif

                    <button
                        type="submit"
                        class="landing-hero-admin-submit"
                        {{ $logoCustomSupported ? '' : 'disabled' }}
                    >
                        Simpan Logo Utama
                    </button>
                </div>
            </form>
        </section>

        {{-- LANDING HERO --}}
        <section class="landing-hero-admin-section" aria-labelledby="landing-hero-title">
            <div class="website-section-heading">
                <span class="website-section-number">02</span>

                <div>
                    <span class="website-section-kicker">Beranda</span>
                    <h2 id="landing-hero-title">Landing Hero</h2>
                    <p>
                        Atur tampilan foto utama yang muncul pada bagian
                        Landing Hero halaman beranda.
                    </p>
                </div>
            </div>

            <div class="landing-hero-admin-grid">
                @foreach($heroSlides->values() as $index => $slide)
                    <article class="landing-hero-admin-card">
                        <div class="landing-hero-admin-card__header">
                            <div>
                                <span class="landing-hero-admin-card__number">
                                    0{{ $index + 1 }}
                                </span>

                                <h3>
                                    Slider {{ $index + 1 }}
                                </h3>
                            </div>

                            <span class="landing-hero-admin-location">
                                {{ $slide->lokasi === 'kost1' ? 'Al Fazza Kost 1' : 'Al Fazza Kost 2' }}
                            </span>
                        </div>

                        <div class="landing-hero-admin-preview">
                            @if($slide->foto)
                                <img
                                    src="{{ asset('storage/' . $slide->foto) }}"
                                    alt="{{ $slide->judul }}"
                                >
                            @else
                                <div class="landing-hero-admin-preview__empty">
                                    <span>Belum ada foto</span>
                                </div>
                            @endif
                        </div>

                        <form
                            action="{{ route('landing-hero.update', $slide) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="landing-hero-admin-form"
                        >
                            @csrf
                            @method('PUT')

                            <input
                                type="hidden"
                                name="from_website_settings"
                                value="1"
                            >

                            <div class="landing-hero-admin-field">
                                <label for="judul-{{ $slide->id }}">
                                    Judul
                                </label>

                                <input
                                    id="judul-{{ $slide->id }}"
                                    type="text"
                                    name="judul"
                                    value="{{ old('judul', $slide->judul) }}"
                                    maxlength="150"
                                    required
                                >
                            </div>

                            <div class="landing-hero-admin-field">
                                <label for="subjudul-{{ $slide->id }}">
                                    Subjudul
                                </label>

                                <input
                                    id="subjudul-{{ $slide->id }}"
                                    type="text"
                                    name="subjudul"
                                    value="{{ old('subjudul', $slide->subjudul) }}"
                                    maxlength="255"
                                >
                            </div>

                            <div class="landing-hero-admin-field">
                                <label for="foto-{{ $slide->id }}">
                                    Foto Slider
                                </label>

                                <input
                                    id="foto-{{ $slide->id }}"
                                    type="file"
                                    name="foto"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                >

                                <small>
                                    JPG, JPEG, PNG, atau WEBP · maksimal 2 MB.
                                </small>
                            </div>

                            <label class="landing-hero-admin-checkbox">
                                <input
                                    type="checkbox"
                                    name="aktif"
                                    value="1"
                                    {{ $slide->aktif ? 'checked' : '' }}
                                >

                                <span>Aktifkan slider ini</span>
                            </label>

                            <button
                                type="submit"
                                class="landing-hero-admin-submit"
                            >
                                Simpan Slider {{ $index + 1 }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-logo-input]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];

            if (!file) {
                return;
            }

            const preview = input
                .closest('section')
                ?.querySelector('[data-logo-preview]');

            if (!preview) {
                console.log('Preview logo tidak ditemukan.');
                return;
            }

            const url = URL.createObjectURL(file);

            preview.src = url;
            preview.removeAttribute('srcset');
            preview.style.display = 'block';
        });
    });
});
</script>

</body>
</html>
