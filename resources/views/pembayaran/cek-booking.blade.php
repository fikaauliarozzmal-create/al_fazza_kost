<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Booking | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .booking-page { min-height: 100vh; padding: 36px 20px; background: linear-gradient(135deg, #f4f8f7, #eef5f1); }
        .booking-nav { max-width: 960px; margin: 0 auto 36px; display: flex; justify-content: space-between; align-items: center; color: #16423c; }
        .booking-brand { font-size: 1.25rem; font-weight: 800; text-decoration: none; color: inherit; }
        .booking-back { color: #16423c; text-decoration: none; font-weight: 600; }
        .booking-card { width: min(100%, 560px); margin: 0 auto; padding: 38px; background: #fff; border: 1px solid #dce9e4; border-radius: 20px; box-shadow: 0 18px 45px rgba(22, 66, 60, .12); }
        .booking-icon { width: 54px; height: 54px; display: grid; place-items: center; margin-bottom: 18px; border-radius: 15px; background: #e2f1eb; font-size: 1.6rem; }
        .booking-card h1 { margin: 0 0 10px; color: #16423c; font-size: clamp(1.65rem, 5vw, 2rem); }
        .booking-copy { margin: 0 0 28px; color: #5d6d68; line-height: 1.6; }
        .booking-field { margin-bottom: 20px; }
        .booking-field label { display: block; margin-bottom: 8px; color: #294740; font-weight: 700; }
        .booking-field input { box-sizing: border-box; display: block; width: 100%; padding: 13px 14px; border: 1px solid #b9ccc5; border-radius: 10px; background: #fff; color: #213530; font: inherit; }
        .booking-field input:focus { outline: none; border-color: #227a65; box-shadow: 0 0 0 3px rgba(34, 122, 101, .14); }
        .booking-hint { display: block; margin-top: 6px; color: #71827c; font-size: .84rem; }
        .booking-error { margin-bottom: 22px; padding: 13px 15px; border-left: 4px solid #c2413b; border-radius: 8px; background: #fff0ef; color: #9d2f2b; }
        .booking-submit { display: block; width: 100%; padding: 13px 18px; border: 0; border-radius: 10px; background: #1c6b59; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .booking-submit:hover { background: #145445; }
        @media (max-width: 560px) { .booking-page { padding: 20px 14px; } .booking-nav { margin-bottom: 24px; } .booking-card { padding: 26px 20px; } }
    </style>
</head>
<body>
    <div class="booking-page">
        <nav class="booking-nav">
            <a class="booking-brand" href="{{ route('landing') }}">Al Fazza Kost</a>
            <x-back-button href="{{ route('landing') }}" aria-label="Kembali ke landing page Al Fazza Kost" />
        </nav>

        <main class="booking-card">
            <div class="booking-icon" aria-hidden="true">📋</div>
            <h1>Cek Status Booking</h1>
            <p class="booking-copy">Masukkan kode booking dan nomor WhatsApp yang digunakan saat melakukan booking untuk melihat status booking dan pembayaran.</p>

            @if ($errors->any())
                <div class="booking-error" role="alert">
                    <strong>Data belum dapat ditemukan.</strong><br>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.check.process') }}">
                @csrf
                <div class="booking-field">
                    <label for="kode_booking">Kode Booking</label>
                    <input id="kode_booking" type="number" name="kode_booking" value="{{ old('kode_booking') }}" placeholder="Contoh: 13" min="1" required autofocus>
                    <small class="booking-hint">Kode booking diberikan saat Anda mengirim formulir booking.</small>
                </div>

                <div class="booking-field">
                    <label for="no_whatsapp">Nomor WhatsApp</label>
                    <input id="no_whatsapp" type="text" name="no_whatsapp" value="{{ old('no_whatsapp') }}" placeholder="Contoh: 081234567890" maxlength="20" required>
                </div>

                <button class="booking-submit" type="submit">Cek Booking</button>
            </form>
        </main>
    </div>
</body>
</html>
