<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login {{ $portal === 'admin' ? 'Admin' : 'Penghuni' }} | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <x-back-button href="{{ route('login') }}" aria-label="Kembali ke pilihan portal" />
            <header class="login-header">
                <h1>{{ $portal === 'admin' ? 'Portal Admin 🛡️' : 'Portal Penghuni 🏠' }}</h1>
                <p>{{ $portal === 'admin' ? 'Masuk dengan akun Admin Al Fazza Kost.' : 'Masuk dengan akun penghuni Al Fazza Kost.' }}</p>
            </header>

            @if ($errors->any())
                <div class="login-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ $portal === 'admin' ? route('login.admin.process') : route('login.penghuni.process') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@gmail.com" autocomplete="email" required autofocus>
                    <small>{{ $portal === 'admin' ? 'Gunakan email yang terdaftar sebagai akun Admin Al Fazza Kost.' : 'Gunakan email Google kamu.' }}</small>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                    <small>{{ $portal === 'admin' ? 'Masukkan password akun Admin Al Fazza Kost.' : 'Buat password sendiri untuk akun Al Fazza Kost. Jangan gunakan password Google kamu.' }}</small>
                </div>
                <button type="submit" class="login-button">{{ $portal === 'admin' ? 'Masuk sebagai Admin' : 'Masuk sebagai Penghuni' }}</button>
            </form>
        </section>
    </main>
</body>
</html>
