<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pilih Portal | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>

<body>

    <div class="login-page">

        <div class="login-card portal-card">

            <div class="login-header">

                <h1>Selamat Datang Kembali 👋</h1>

                <p>Pilih portal yang ingin kamu akses.</p>

            </div>


            @if (session('success'))
                <div class="login-success">{{ session('success') }}</div>
            @endif

            <div class="portal-options">
                <article class="portal-option">
                    <div class="portal-icon" aria-hidden="true">🏠</div>
                    <h2>Portal Penghuni</h2>
                    <p>Untuk penghuni Al Fazza Kost yang sudah memiliki akun aktif.</p>
                    <a href="{{ route('login.penghuni') }}" class="login-button">Masuk sebagai Penghuni</a>
                </article>

                <article class="portal-option portal-option-admin">
                    <div class="portal-icon" aria-hidden="true">🛡️</div>
                    <h2>Portal Admin</h2>
                    <p>Khusus Admin dan Super Admin Al Fazza Kost.</p>
                    <a href="{{ route('login.admin') }}" class="login-button">Masuk sebagai Admin</a>
                </article>
            </div>


            <x-back-button href="{{ route('landing') }}" aria-label="Kembali ke landing page Al Fazza Kost" />

        </div>

    </div>

</body>

</html>
