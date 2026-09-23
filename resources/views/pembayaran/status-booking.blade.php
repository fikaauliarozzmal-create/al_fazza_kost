<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Booking #{{ $booking->id_booking }} | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .status-page { min-height: 100vh; padding: 36px 20px; background: linear-gradient(135deg, #f4f8f7, #eef5f1); }
        .status-wrap { width: min(100%, 760px); margin: auto; }.status-nav { display:flex; justify-content:space-between; margin-bottom:28px; color:#16423c; }.status-nav a { color:inherit; text-decoration:none; font-weight:700; }
        .status-card { overflow:hidden; background:#fff; border:1px solid #dce9e4; border-radius:20px; box-shadow:0 18px 45px rgba(22,66,60,.12); }.status-header { padding:30px 34px; color:#fff; background:#1c6b59; }.status-header h1 { margin:0 0 6px; font-size:clamp(1.55rem,5vw,2rem); }.status-header p { margin:0; opacity:.9; }
        .status-body { padding:30px 34px; }.notice { margin:0 0 22px; padding:14px 16px; border-radius:10px; line-height:1.55; }.notice-wait { background:#fff7df; color:#805b00; }.notice-reject { background:#fff0ef; color:#9d2f2b; }.notice-success { background:#e8f5ee; color:#1f6b47; }
        .detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }.detail { padding:14px; border:1px solid #e2ece8; border-radius:10px; background:#fbfdfc; }.detail-label { display:block; margin-bottom:5px; color:#71827c; font-size:.83rem; }.detail-value { color:#27443d; font-weight:700; word-break:break-word; }.badge { display:inline-block; padding:5px 10px; border-radius:999px; font-size:.85rem; }.badge-wait { background:#fff1c5; color:#805b00; }.badge-approve { background:#dff4e9; color:#1f6b47; }.badge-reject { background:#ffe1df; color:#9d2f2b; }
        .payment-box { margin-top:26px; padding-top:24px; border-top:1px solid #e2ece8; }.payment-box h2 { margin:0 0 14px; color:#16423c; font-size:1.15rem; }.payment-row { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid #edf2ef; }.payment-row strong { color:#27443d; }.status-action { display:inline-block; margin-top:22px; padding:13px 18px; border-radius:10px; background:#1c6b59; color:#fff; text-decoration:none; font-weight:700; }.status-action:hover { background:#145445; }
        @media(max-width:560px){.status-page{padding:20px 14px}.status-header,.status-body{padding:24px 20px}.detail-grid{grid-template-columns:1fr}.status-nav{font-size:.9rem}}
    </style>
</head>
<body>
    <div class="status-page"><div class="status-wrap">
        <nav class="status-nav"><a href="{{ route('landing') }}">Al Fazza Kost</a><x-back-button href="{{ route('penghuni.dashboard') }}" aria-label="Kembali ke booking saya" /></nav>
        <main class="status-card">
            <header class="status-header"><h1>Status Booking #{{ $booking->id_booking }}</h1><p>Berikut detail booking dan status pembayaran Anda.</p></header>
            <div class="status-body">
                @if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
                @if($booking->status_booking === 'Menunggu')<div class="notice notice-wait"><strong>Booking masih menunggu persetujuan admin.</strong><br>Anda akan dapat melanjutkan pembayaran DP setelah booking disetujui.</div>@elseif($booking->status_booking === 'Ditolak')<div class="notice notice-reject"><strong>Booking ditolak.</strong><br>Silakan hubungi pengelola Al Fazza Kost untuk informasi lebih lanjut.</div>@endif
                <section class="detail-grid" aria-label="Detail booking">
                    <div class="detail"><span class="detail-label">Kode Booking</span><span class="detail-value">#{{ $booking->id_booking }}</span></div>
                    <div class="detail"><span class="detail-label">Status Booking</span><span class="detail-value"><span class="badge {{ $booking->status_booking === 'Disetujui' ? 'badge-approve' : ($booking->status_booking === 'Ditolak' ? 'badge-reject' : 'badge-wait') }}">{{ $booking->status_booking }}</span></span></div>
                    <div class="detail"><span class="detail-label">Nama Pemesan</span><span class="detail-value">{{ $booking->nama_pemesan }}</span></div>
                    <div class="detail"><span class="detail-label">Nomor WhatsApp</span><span class="detail-value">{{ $booking->no_whatsapp }}</span></div>
                    <div class="detail"><span class="detail-label">Kamar</span><span class="detail-value">Kamar {{ $booking->kamar->nomor_kamar ?? '-' }}</span></div>
                    <div class="detail"><span class="detail-label">Lokasi Kost</span><span class="detail-value">{{ $booking->kamar->lokasi_kos ?? '-' }}</span></div>
                    <div class="detail"><span class="detail-label">Harga Kamar</span><span class="detail-value">Rp {{ number_format($booking->kamar->harga ?? 0, 0, ',', '.') }} / bulan</span></div>
                    <div class="detail"><span class="detail-label">Tanggal Booking</span><span class="detail-value">{{ $booking->tanggal_booking?->format('d F Y') ?? '-' }}</span></div>
                </section>

                @if($booking->status_booking === 'Disetujui')
                    <section class="payment-box"><h2>Informasi Pembayaran Pertama</h2>
                        <div class="payment-row"><span>DP</span><strong>Rp {{ number_format($dp?->jumlah_bayar ?? 0, 0, ',', '.') }} — {{ $dp?->metode_bayar ? $dp->status_label : 'Belum Dibayar' }}</strong></div>
                        @if($dp?->status_bayar === 'Ditolak')<div class="notice notice-reject"><strong>Pembayaran DP Ditolak</strong><br>Bukti pembayaran belum dapat diverifikasi. Silakan periksa kembali dan lakukan pembayaran/upload bukti sesuai instruksi pengelola.@if($dp->alasan_penolakan)<br><strong>Alasan:</strong> {{ $dp->alasan_penolakan }}@endif</div>@endif
                        <div class="payment-row"><span>Sisa sewa pertama</span><strong>Rp {{ number_format($sisaPertama, 0, ',', '.') }} — {{ $sisa?->metode_bayar ? $sisa->status_label : 'Belum Dibayar' }}</strong></div>
                        @if($sisa?->status_bayar === 'Ditolak')<div class="notice notice-reject"><strong>Pembayaran Sisa Sewa Ditolak</strong><br>Bukti pembayaran belum dapat diverifikasi. Silakan lakukan pembayaran/upload bukti kembali.@if($sisa->alasan_penolakan)<br><strong>Alasan:</strong> {{ $sisa->alasan_penolakan }}@endif</div>@endif
                        @if($dp && (($dp->status_bayar === 'Pending' && !$dp->metode_bayar) || $dp->status_bayar === 'Ditolak'))<a class="status-action" href="{{ route('pembayaran.awal.bayar', $dp) }}">{{ $dp->status_bayar === 'Ditolak' ? 'Bayar / Upload Bukti Lagi' : 'Bayar DP Rp ' . number_format($dp->jumlah_bayar, 0, ',', '.') }}</a>@elseif($sisa && $dp?->status_bayar === 'Valid' && (($sisa->status_bayar === 'Pending' && !$sisa->metode_bayar) || $sisa->status_bayar === 'Ditolak'))<a class="status-action" href="{{ route('pembayaran.awal.bayar', $sisa) }}">{{ $sisa->status_bayar === 'Ditolak' ? 'Bayar / Upload Bukti Lagi' : 'Lanjut Bayar Sisa Sewa' }}</a>@elseif($siapAktivasi)<a class="status-action" href="{{ route('penghuni.dashboard') }}">Buka Dashboard Penghuni</a>@endif
                    </section>
                @endif
            </div>
        </main>
    </div></div>
</body>
</html>
