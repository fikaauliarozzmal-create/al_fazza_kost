<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dashboard Penghuni | Al Fazza Kost</title>@vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])</head>
<body><div class="dashboard">
@include('penghuni.partials.sidebar', ['active' => 'dashboard'])
<main class="dashboard-main penghuni-main">
    <header class="dashboard-header"><div><h1>Dashboard User</h1><p>Halo, {{ auth()->user()->name }}.</p></div><div class="admin-profile"><x-user-avatar :user="auth()->user()" :show-photo="(bool) $penghuni" class="admin-avatar" /><div><strong>{{ auth()->user()->name }}</strong><small>{{ $penghuni ? 'Penghuni Aktif' : 'Calon Penghuni' }}</small></div></div></header>
    @if(session('success'))<div class="login-success">{{ session('success') }}</div>@endif
    @if($penghuni)
        <section class="tenant-summary-grid">
            <article class="tenant-summary-card"><div class="tenant-card-top"><span class="tenant-card-icon"><x-ui-icon name="bed" /></span><span>Kamar</span></div><strong>Kamar {{ $penghuni->kamar->nomor_kamar ?? '-' }}</strong><p>{{ $penghuni->kamar->tipe_kamar ?? '-' }} · {{ $penghuni->kamar->lokasi_kos ?? '-' }}</p></article>
            <article class="tenant-summary-card"><div class="tenant-card-top"><span class="tenant-card-icon"><x-ui-icon name="resident" /></span><span>Status Akun</span></div><strong>Penghuni Aktif</strong><p>{{ $penghuni->status_penghuni }}</p></article>
            <article class="tenant-summary-card"><div class="tenant-card-top"><span class="tenant-card-icon"><x-ui-icon name="wallet" /></span><span>Pembayaran</span></div><strong>{{ $pembayaranTerakhir?->status_bayar ?? 'Belum ada' }}</strong><p><a href="{{ route('penghuni.pembayaran') }}">Lihat pembayaran <span aria-hidden="true">→</span></a></p></article>
            <article class="tenant-summary-card"><div class="tenant-card-top"><span class="tenant-card-icon"><x-ui-icon name="receipt" /></span><span>Invoice</span></div><strong>{{ $pembayaran->filter(fn($item) => $item->invoice)->count() }}</strong><p><a href="{{ route('penghuni.invoice.index') }}">Lihat invoice <span aria-hidden="true">→</span></a></p></article>
        </section>
    @else
        <section class="dashboard-section">
            @if(!$booking)
                <div class="empty-state"><div class="empty-icon"><x-ui-icon name="dashboard" size="34" /></div><h3>Anda belum memiliki booking.</h3><p>Gunakan menu Booking Saya di navbar landing page untuk memilih kamar.</p></div>
            @else
                @php($pembayaranAwalTerbaru = $booking->pembayaran->sortByDesc('id_pembayaran')->first())
                <div class="section-heading"><div><h2>Status Booking</h2><p>Informasi booking dan pembayaran awal Anda.</p></div></div>
                <div class="tenant-detail-grid"><div><span>Nomor booking</span><strong>#{{ $booking->id_booking }}</strong></div><div><span>Kamar</span><strong>{{ $booking->kamar?->nomor_kamar ?? '-' }}</strong></div><div><span>Lokasi kos</span><strong>{{ $booking->kamar?->lokasi_kos ?? '-' }}</strong></div><div><span>Tanggal booking</span><strong>{{ $booking->tanggal_booking?->format('d M Y') }}</strong></div><div><span>Status booking</span><strong>{{ $booking->status_booking }}</strong></div><div><span>Status pembayaran</span><strong>{{ $pembayaranAwalTerbaru?->metode_bayar ? 'Menunggu Verifikasi' : ($pembayaranAwalTerbaru?->status_bayar === 'Pending' ? 'Menunggu Pembayaran' : ($pembayaranAwalTerbaru?->status_bayar ?? 'Belum tersedia')) }}</strong></div></div>
                @if($booking->status_booking === 'Menunggu')
                    <p class="tenant-alert tenant-alert-warning">Booking Anda sedang menunggu verifikasi admin.</p>
                @elseif($booking->status_booking === 'Disetujui')
                    <p class="tenant-alert tenant-alert-info">Booking Anda disetujui. Silakan lakukan pembayaran awal untuk melanjutkan proses hunian.</p>
                    <a class="btn-primary" href="{{ route('booking.status', $booking) }}">Bayar Sekarang</a>
                @elseif($booking->status_booking === 'Ditolak')
                    <p class="tenant-alert tenant-alert-danger">Booking Anda ditolak oleh admin. Silakan hubungi pengelola untuk informasi lebih lanjut.</p>
                @endif
            @endif
        </section>
    @endif
    <section class="dashboard-section">
        <div class="section-heading"><div><h2>{{ $penghuni ? 'Aktivitas Saya' : 'Aktivitas Saya' }}</h2><p>{{ $penghuni ? 'Informasi terbaru terkait hunian dan akun Anda.' : 'Informasi terbaru terkait proses booking dan pembayaran awal Anda.' }}</p></div></div>
        @if($activities->isNotEmpty())
            <div class="activity-list">
                @foreach($activities as $activity)
                    @php($icon = ['booking' => 'booking', 'payment' => 'wallet', 'resident' => 'resident', 'complaint' => 'service', 'invoice' => 'receipt'][$activity['type']] ?? 'bell')
                    @if($activity['url'])<a class="activity-item" href="{{ $activity['url'] }}">@else<div class="activity-item">@endif
                        <span class="activity-icon activity-icon--{{ $activity['type'] }}"><x-ui-icon :name="$icon" /></span>
                        <span class="activity-content"><strong>{{ $activity['title'] }}</strong><span>{{ $activity['description'] }}</span><small title="{{ $activity['time_full_label'] }}" data-activity-time="{{ $activity['time_iso'] }}">{{ $activity['time_label'] }}</small></span>
                        @if($activity['url'])<span class="activity-arrow" aria-hidden="true">→</span>@endif
                    @if($activity['url'])</a>@else</div>@endif
                @endforeach
            </div>
        @else
            <div class="empty-state"><div class="empty-icon"><x-ui-icon name="mail" size="34" /></div><h3>Belum ada aktivitas</h3><p>Aktivitas akun Anda akan muncul di sini.</p></div>
        @endif
    </section>
    <section class="dashboard-section"><div class="section-heading"><div><h2>Layanan Akun</h2><p>Notifikasi, pembayaran, invoice, dan keluhan tersedia setelah status penghuni aktif.</p></div></div></section>
</main></div></body></html>
