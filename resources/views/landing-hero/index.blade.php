<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Hero | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'landing-hero'])

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h1>Landing Hero</h1>
                <p>Atur dua foto utama yang tampil pada Beranda Al Fazza Kost.</p>
            </div>
            <div class="admin-profile">
                <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>Admin</small>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali data:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="landing-hero-admin-intro">
            <div>
                <span class="landing-hero-admin-kicker">Hero Beranda</span>
                <h2>Hanya 2 slide, sesuai 2 lokasi Al Fazza Kost.</h2>
                <p>Slide 01 untuk Karangmanyar dan slide 02 untuk Toyareka. Foto dapat diganti Admin kapan saja.</p>
            </div>
        </section>

        <section class="landing-hero-admin-grid">
            @foreach($slides as $index => $slide)
                <article class="landing-hero-admin-card">
                    <div class="landing-hero-admin-card-head">
                        <div>
                            <span class="landing-hero-admin-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $slide->lokasi === 'kost1' ? 'Al Fazza Kost 1' : 'Al Fazza Kost 2' }}</h3>
                            <p>{{ $slide->lokasi === 'kost1' ? 'Karangmanyar, Purbalingga' : 'Toyareka, Purbalingga' }}</p>
                        </div>
                        <span class="landing-hero-admin-status {{ $slide->aktif ? 'is-active' : '' }}">
                            {{ $slide->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="landing-hero-admin-preview">
                        @if($slide->foto)
                            <img src="{{ asset('storage/' . $slide->foto) }}" alt="{{ $slide->judul }}">
                        @else
                            <div class="landing-hero-admin-placeholder">
                                <span>⌂</span>
                                <strong>Foto belum dipasang</strong>
                                <small>Upload foto hero di bawah.</small>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('landing-hero.update', $slide) }}" method="POST" enctype="multipart/form-data" class="landing-hero-admin-form">
                        @csrf
                        @method('PUT')

                        <label>
                            <span>Judul</span>
                            <input type="text" name="judul" value="{{ old('judul', $slide->judul) }}" required>
                        </label>

                        <label>
                            <span>Subjudul</span>
                            <input type="text" name="subjudul" value="{{ old('subjudul', $slide->subjudul) }}">
                        </label>

                        <label>
                            <span>Ganti foto</span>
                            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
                            <small>JPG, JPEG, PNG, WEBP. Maksimal 2 MB.</small>
                        </label>

                        <label class="landing-hero-admin-check">
                            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $slide->aktif) ? 'checked' : '' }}>
                            <span>Tampilkan slide ini di Beranda</span>
                        </label>

                        <button type="submit" class="landing-hero-admin-submit">Simpan Slide {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</button>
                    </form>
                </article>
            @endforeach
        </section>
    </main>
</div>
</body>
</html>
