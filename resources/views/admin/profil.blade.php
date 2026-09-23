<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'profil'])
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div><h1>Profil Admin</h1><p>Kelola foto profil akun Anda.</p></div>
            <div class="admin-profile"><x-user-avatar :user="auth()->user()" class="admin-avatar" /><div><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div></div>
        </header>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors?->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <section class="dashboard-section tenant-section profile-photo-section">
            <div class="section-heading"><div><h2>Foto Profil</h2><p>JPG, JPEG, PNG, atau WEBP dengan ukuran maksimal 2 MB.</p></div></div>
            <form class="tenant-complaint-form profile-photo-form" action="{{ route('admin.profil.photo') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="profile-photo-preview"><x-user-avatar :user="auth()->user()" class="profile-photo-preview__avatar" data-profile-preview /></div>
                <label for="admin-profile-photo">Pilih atau ganti foto profil</label>
                <input id="admin-profile-photo" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-profile-input required>
                <button class="btn-success" type="submit">Simpan Foto Profil</button>
            </form>
        </section>
    </main>
</div>
@include('partials.profile-photo-preview')
</body>
</html>
