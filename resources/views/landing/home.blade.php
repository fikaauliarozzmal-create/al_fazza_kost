<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Al Fazza Kost, hunian khusus wanita di Karangmanyar dan Toyareka, Purbalingga.">

    <title>Al Fazza Kost | Kost Khusus Wanita di Purbalingga</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])

</head>

<body class="af-page">

<div class="af-splash" data-af-splash aria-hidden="true">
    <div class="af-splash__halo"></div>
    <x-site-logo class="site-logo site-logo--splash" />
</div>

@php

    $user = auth()->user();

    $isAdmin = $user?->isAdmin() ?? false;

    $isPenghuniAktif = $user?->isPenghuniAktif() ?? false;

    $hasBooking = auth()->check() && ! $isAdmin && $user->bookings()

        ->whereIn('status_booking', ['Menunggu', 'Disetujui'])

        ->exists();

    $bookingUrl = $isAdmin

        ? route('dashboard')

        : ($hasBooking

            ? route('penghuni.dashboard')

            : (auth()->check()

                ? route('booking.create')

                : route('login')));

    $bookingLabel = $isAdmin

        ? 'Buka Dashboard Admin'

        : ($hasBooking

            ? 'Lihat Booking Saya'

            : 'Booking Sekarang');

    $userInitial = auth()->check()

        ? strtoupper(substr($user->name, 0, 1))

        : null;

    /*

     * Hero Landing Page

     * Hanya 2 slide:

     * 01 = Al Fazza Kost 1

     * 02 = Al Fazza Kost 2

     */

    $kamarDenganFoto = $kamar->filter(fn ($item) => filled($item->foto_kamar));

    $heroSlides = collect($heroSlides ?? [])

        ->sortBy('urutan')

        ->take(2)

        ->values();

    $heroSlideData = $heroSlides->map(function ($slide) {

        return [

            'judul' => $slide->judul,

            'subjudul' => $slide->subjudul,

            'lokasi' => $slide->lokasi,

        ];

    })->values();

@endphp

<header class="af-nav">

    <a class="af-brand" href="{{ route('landing') }}" aria-label="Beranda Al Fazza Kost"><x-site-logo class="site-logo site-logo--navbar" /><span class="af-brand-copy">Al Fazza Kost<small>Hunian khusus wanita</small></span></a>

    <button class="af-menu-toggle" type="button" aria-controls="af-nav-links" aria-expanded="false">Menu</button>

    <nav id="af-nav-links" class="af-nav-links" aria-label="Navigasi utama">

        <a href="#beranda" data-nav-section="beranda">Beranda</a><a href="#tentang" data-nav-section="tentang">Tentang</a><a href="#lokasi" data-nav-section="lokasi">Lokasi</a><a href="#kamar" data-nav-section="kamar">Kamar</a><a href="#harga" data-nav-section="harga">Harga</a><a href="#fasilitas" data-nav-section="fasilitas">Fasilitas</a><a href="#booking" data-nav-section="booking">Booking</a><a href="#galeri" data-nav-section="galeri">Galeri</a><a href="#pelayanan-pengaduan" data-nav-section="pelayanan-pengaduan">Pelayanan &amp; Pengaduan</a><a href="#kontak" data-nav-section="kontak">Kontak</a>

    </nav>

    <div class="af-account">

        @guest

            <a class="af-login" href="{{ route('login') }}">Login</a>

        @else

            <details class="af-user-menu">

                <summary><span class="af-user-avatar" aria-hidden="true">{{ $userInitial }}</span><span class="af-user-name">{{ $user->name }}</span><span class="af-user-chevron" aria-hidden="true">▾</span></summary>

                <div class="af-user-dropdown">

                    <div class="af-user-dropdown-head"><strong>{{ $user->name }}</strong><small>Status: {{ $accountStatus }}</small></div>

                    <div class="af-user-dropdown-links">

                @if($isAdmin)

                    <a href="{{ route('dashboard') }}">Dashboard Admin</a><a href="{{ route('kamar.index') }}">Kelola Kamar</a><a href="{{ route('booking.index') }}">Kelola Booking</a><a href="{{ route('penghuni.index') }}">Data Penghuni</a><a href="{{ route('pembayaran.index') }}">Kelola Pembayaran</a><a href="{{ route('invoice.index') }}">Invoice</a><a href="{{ route('admin.keluhan.index') }}">Kelola Keluhan</a><a href="{{ route('admin.payment-settings.index') }}">Pengaturan Pembayaran</a><a href="{{ route('pengaturan-website.index') }}">Pengaturan Website</a><a href="{{ route('admin.laporan.index') }}">Laporan</a>

                @elseif($isPenghuniAktif)

                    <a href="{{ route('penghuni.kamar') }}">Kamar Saya</a><a href="{{ route('penghuni.dashboard') }}">Booking Saya</a><a href="{{ route('penghuni.pembayaran') }}">Pembayaran Saya</a><a href="{{ route('penghuni.invoice.index') }}">Invoice</a><a href="{{ route('penghuni.keluhan.index') }}">Keluhan</a><a href="{{ route('penghuni.profil') }}">Profil</a>

                @else

                    <a href="{{ route('penghuni.dashboard') }}">Booking Saya</a>

                @endif

                    </div>

                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>

                </div>

            </details>

        @endguest

    </div>

</header>

<main>

    <section class="af-hero af-hero-premium" id="beranda">

    <div class="af-hero-bg"></div>

    <div class="af-wrap af-hero-grid">

        {{-- LEFT : HERO TEXT --}}

        <div class="af-hero-copy">

            <span class="af-pill">
                ✦ Khusus wanita · Purbalingga
            </span>

            <h1>
                Temukan Kost
                <span>Nyaman, Bersih,</span>
                dan Aman
            </h1>

            <p>
                Al Fazza Kost menyediakan hunian khusus wanita
                dengan pilihan dua lokasi di Purbalingga,
                fasilitas lengkap, harga terjangkau,
                serta proses booking yang mudah.
            </p>

            <div class="af-actions">

                <a
                    class="af-btn af-btn-primary"
                    href="#kamar"
                >
                    <span>Lihat Kamar</span>
                    <b>→</b>
                </a>

                <a
                    class="af-btn af-btn-secondary"
                    href="{{ $bookingUrl }}"
                >
                    @if($isAdmin)
                        <span>Buka Dashboard Admin</span>
                    @else
                        <span>{{ $bookingLabel }}</span>
                    @endif
                </a>

            </div>

            {{-- HERO FEATURE CARDS --}}

            <div class="af-hero-features">

                <article class="af-hero-feature">

                    <span class="af-hero-feature-icon">
                        <x-ui-icon name="home-shield" size="24" />
                    </span>

                    <div>
                        <strong>Aman &amp; Nyaman</strong>
                        <small>Hunian nyaman untuk beristirahat</small>
                    </div>

                </article>

                <article class="af-hero-feature">

                    <span class="af-hero-feature-icon">
                        <x-ui-icon name="map-pin" size="24" />
                    </span>

                    <div>
                        <strong>Dua Lokasi</strong>
                        <small>Karangmanyar &amp; Toyareka</small>
                    </div>

                </article>

                <article class="af-hero-feature">

                    <span class="af-hero-feature-icon">
                        <x-ui-icon name="building" size="24" />
                    </span>

                    <div>
                        <strong>Fasilitas Lengkap</strong>
                        <small>Kebutuhan sehari-hari lebih mudah</small>
                    </div>

                </article>

                <article class="af-hero-feature">

                    <span class="af-hero-feature-icon">
                        <x-ui-icon name="wallet" size="24" />
                    </span>

                    <div>
                        <strong>Harga Terjangkau</strong>
                        <small>Pilihan sesuai kebutuhan</small>
                    </div>

                </article>

            </div>

        </div>


        {{-- RIGHT : PHOTO CAROUSEL --}}

        <div class="af-hero-visual">

            <div
                class="af-hero-photo-card"
                id="afHeroCarousel"
            >

                @if($heroSlides->isNotEmpty())

                    <div class="af-hero-slides">

                        @foreach($heroSlides as $index => $slide)

                            <div
                                class="af-hero-slide {{ $index === 0 ? 'is-active' : '' }}"
                                data-hero-slide="{{ $index }}"
                            >

                                @if(filled($slide->foto))

                                    <img
                                        src="{{ asset('storage/' . $slide->foto) }}"
                                        alt="{{ $slide->judul }}"
                                        draggable="false"
                                    >

                                @else

                                    <div class="af-hero-photo-placeholder">

                                        <span class="af-placeholder-icon">
                                            ⌂
                                        </span>

                                        <strong>
                                            {{ $slide->judul }}
                                        </strong>

                                        <small>
                                            Foto belum diatur oleh Admin.
                                        </small>

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="af-hero-photo-placeholder">

                        <span class="af-placeholder-icon">
                            ⌂
                        </span>

                        <strong>
                            Al Fazza Kost
                        </strong>

                        <small>
                            Foto Hero akan ditampilkan setelah diatur Admin.
                        </small>

                    </div>

                @endif


                {{-- DARK INFORMATION PANEL --}}

                <div class="af-hero-info">

                    <div class="af-hero-info-icon">
                        <span>⌂</span>
                    </div>

                    <div class="af-hero-info-content">

                        @if($heroSlides->isNotEmpty())

                            <span
                                class="af-hero-info-label"
                                id="afHeroInfoLabel"
                            >
                                {{ $heroSlides->first()->judul }}
                            </span>

                            <h2 id="afHeroInfoTitle">
                                {{ $heroSlides->first()->judul }}
                            </h2>

                            <p id="afHeroInfoSubtitle">
                                {{ $heroSlides->first()->subjudul }}
                            </p>

                        @else

                            <span class="af-hero-info-label">
                                Al Fazza Kost
                            </span>

                            <h2>
                                Dua lokasi untuk dipilih
                            </h2>

                            <p>
                                Karangmanyar dan Toyareka, Purbalingga.
                            </p>

                        @endif

                    </div>


                    {{-- SLIDER INDICATOR --}}

                    @if($heroSlides->isNotEmpty())

                        <div class="af-hero-slider">

                            <button
                                type="button"
                                class="af-slider-number is-active"
                                data-hero-dot="0"
                                aria-label="Foto Al Fazza Kost 1"
                            >
                                01
                            </button>

                            <div class="af-slider-track">

                                <span
                                    class="af-slider-progress"
                                    style="width: 50%;"
                                ></span>

                            </div>

                            @if($heroSlides->count() > 1)

                                <button
                                    type="button"
                                    class="af-slider-number"
                                    data-hero-dot="1"
                                    aria-label="Foto Al Fazza Kost 2"
                                >
                                    02
                                </button>

                            @endif

                        </div>

                    @endif

                </div>


                {{-- NEXT BUTTON --}}

                @if($heroSlides->count() > 1)

                    <button
                        type="button"
                        class="af-hero-next"
                        id="afHeroNext"
                        aria-label="Foto berikutnya"
                    >
                        →
                    </button>

                @endif

            </div>

        </div>

    </div>

</section>

    <section class="af-section" id="tentang"><div class="af-wrap af-about">

        <div><span class="af-eyebrow">Tentang Al Fazza Kost</span><h2>Hunian yang membantu Anda memilih sesuai kebutuhan.</h2></div>

        <div><p>Al Fazza Kost merupakan kost khusus wanita dengan dua lokasi berbeda di Purbalingga. Calon penghuni dapat melihat informasi kamar, fasilitas, ketersediaan kamar, melakukan booking, dan melihat informasi pembayaran melalui sistem.</p><div class="af-highlights"><span><b>2</b> Lokasi Kost</span><span><b>♀</b> Khusus Wanita</span></div></div>

    </div></section>

    <section class="af-section af-soft" id="lokasi"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Pilih lokasi</span><h2>Dua lokasi Al Fazza Kost</h2><p>Pilih lokasi yang paling sesuai dengan kebutuhan hunian Anda.</p></div><div class="af-location-grid">

        <article class="af-location-card"><div class="af-location-number">01</div><span class="af-card-label">Karangmanyar, Purbalingga</span><h3>Al Fazza Kost 1</h3><p class="af-room-size">Ukuran kamar <b>3 × 2,8 meter</b></p><ul><li>Free WiFi IndiHome, air PDAM, dan listrik</li><li>Kasur, lemari &amp; kursi</li><li>Dapur bersama dan ruang tamu</li><li>Parkiran depan dan belakang</li><li>Pembantu kebersihan kos</li></ul><a href="#harga" class="af-text-link">Lihat Detail Kamar <span>→</span></a></article>

        <article class="af-location-card af-location-card-alt"><div class="af-location-number">02</div><span class="af-card-label">Toyareka, Purbalingga</span><h3>Al Fazza Kost 2</h3><p class="af-room-size">Ukuran kamar <b>3 × 4 meter</b></p><ul><li>Free air, listrik, dan WiFi</li><li>Kasur, lemari, dan kursi</li><li>Dapur bersama</li><li>Kursi dan meja tamu</li><li>Parkiran</li></ul><a href="#harga-kost-2" class="af-text-link">Lihat Detail Kamar <span>→</span></a></article>

    </div></div></section>

    <section class="af-section" id="kamar"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Kamar tersedia</span><h2>Kamar dan tipe kamar</h2><p>Informasi kamar, tipe, harga bulanan, fasilitas, dan status berikut mengikuti data yang dikelola Admin.</p></div><div class="af-type-grid">

        @forelse($kamar as $item)

            @php

                $tipeKamar = match ($item->tipe_kamar) {

                    'kamar_bawah' => 'Kamar Bawah',

                    'kamar_atas' => 'Kamar Atas',

                    '1_lantai' => 'Kamar 1 Lantai',

                    default => $item->tipe_kamar,

                };

                $statusKamar = match ($item->status_kamar) {

                    'tersedia' => 'Tersedia',

                    'terisi' => 'Terisi',

                    'perbaikan' => 'Perbaikan',

                    default => $item->status_kamar,

                };

            @endphp

            <article class="af-room-card"><span class="af-room-location">{{ $item->lokasi_kos }}</span><h3>Kamar {{ $item->nomor_kamar }}</h3><p class="af-room-type">{{ $tipeKamar }}</p><dl><div><dt>Harga per bulan</dt><dd>Rp{{ number_format($item->harga, 0, ',', '.') }}</dd></div><div><dt>Status</dt><dd class="af-room-status af-room-status-{{ $item->status_kamar }}">{{ $statusKamar }}</dd></div></dl>@if(filled($item->fasilitas))<p class="af-room-facilities">{{ $item->fasilitas }}</p>@endif</article>

        @empty

            <p class="af-empty-gallery">Data kamar akan ditampilkan di sini setelah dikelola oleh Admin.</p>

        @endforelse

    </div></div></section>

    <section class="af-section af-soft" id="harga"><div class="af-wrap"><div class="af-section-heading af-price-heading"><div><span class="af-eyebrow">Harga Al Fazza Kost 1</span><h2>Karangmanyar, Purbalingga</h2></div><span class="af-price-note">Harga untuk 1 orang</span></div><div class="af-price-grid">

        <article class="af-price-card"><h3>Kamar Mandi Dalam</h3><dl><div><dt>Per bulan</dt><dd>Rp600.000 <small>/ orang</small></dd></div><div><dt>Per 6 bulan</dt><dd>Rp3.400.000 <small>/ orang</small></dd></div><div><dt>Per tahun</dt><dd>Rp6.700.000 <small>/ orang</small></dd></div></dl></article>

        <article class="af-price-card"><h3>Kamar Mandi Luar</h3><dl><div><dt>Per bulan</dt><dd>Rp500.000 <small>/ orang</small></dd></div><div><dt>Per 6 bulan</dt><dd>Rp2.800.000 <small>/ orang</small></dd></div><div><dt>Per tahun</dt><dd>Rp5.500.000 <small>/ orang</small></dd></div></dl></article>

    </div></div></section>

    <section class="af-section" id="harga-kost-2"><div class="af-wrap"><div class="af-section-heading af-price-heading"><div><span class="af-eyebrow">Harga Al Fazza Kost 2</span><h2>Toyareka, Purbalingga</h2></div><span class="af-price-note">Harga untuk 1 orang</span></div><div class="af-price-grid af-price-grid-small"><article class="af-price-card"><h3>Kamar Mandi Dalam</h3><dl><div><dt>Per bulan</dt><dd>Rp450.000 <small>/ orang</small></dd></div></dl></article><article class="af-price-card"><h3>Kamar Mandi Luar</h3><dl><div><dt>Per bulan</dt><dd>Rp400.000 <small>/ orang</small></dd></div></dl></article></div></div></section>

    <section class="af-section af-soft"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Ketentuan penghuni</span><h2>Informasi penting sebelum memilih kamar</h2></div><div class="af-condition-grid"><article><b>1 orang</b><p>Harga berlaku untuk 1 orang. Penghuni tambahan dikenakan Rp150.000 per anak/orang tambahan sesuai ketentuan pengelola.</p></article><article><b>Hunian wanita</b><p>Kost khusus wanita, jam keluar masuk bebas, dan setiap penghuni membawa kunci sendiri.</p></article><article><b>Kost 2</b><p>Suami istri wajib menunjukkan fotokopi KTP dan buku nikah.</p></article></div></div></section>

    <section class="af-section af-soft"><div class="af-wrap af-payment"><div><span class="af-eyebrow">Metode pembayaran</span><h2>Pembayaran sesuai pilihan Anda</h2><p>Metode yang tersedia: tunai, transfer, dan QRIS.</p></div><div class="af-payment-methods"><span>💵 Tunai</span><span>⇄ Transfer</span><span>▣ QRIS</span></div><div class="af-periods"><b>Periode pembayaran</b><span>Bulanan</span><span>Tahunan</span><small>Al Fazza Kost 1 juga menyediakan periode 6 bulan.</small></div></div></section>

    <section class="af-section"><div class="af-wrap"><div class="af-dp-grid"><article><span>DP Al Fazza Kost 1</span><strong>Minimal Rp200.000</strong><p>DP tidak dapat dikembalikan apabila dibatalkan sesuai ketentuan pengelola.</p></article><article><span>DP Al Fazza Kost 2</span><strong>Minimal Rp100.000</strong><p>DP tidak dapat dikembalikan apabila dibatalkan secara sepihak.</p></article></div><div class="af-payment-info"><span>Informasi pembayaran</span><h3>Sistem membantu penghuni memantau pembayaran.</h3><p>Ketahui status pembayaran, tagihan yang berjalan, riwayat pembayaran, dan informasi pembayaran dari akun Anda.</p></div></div></section>

    <section class="af-section" id="fasilitas"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Fasilitas yang tersedia</span><h2>Fasilitas menurut lokasi</h2><p>Pilih lokasi untuk melihat daftar fasilitasnya.</p></div><div class="af-tabs" role="tablist"><button class="is-active" data-af-tab="one" role="tab">Al Fazza Kost 1</button><button data-af-tab="two" role="tab">Al Fazza Kost 2</button></div><div class="af-facility-panel is-active" data-af-panel="one"><span>◉ WiFi IndiHome</span><span>◉ Air PDAM</span><span>◉ Listrik</span><span>◉ Kasur</span><span>◉ Lemari &amp; kursi</span><span>◉ Dapur bersama</span><span>◉ Ruang tamu</span><span>◉ Parkiran depan &amp; belakang</span><span>◉ Pembantu kebersihan kos</span></div><div class="af-facility-panel" data-af-panel="two"><span>◉ Air</span><span>◉ Listrik</span><span>◉ WiFi</span><span>◉ Kasur</span><span>◉ Lemari</span><span>◉ Kursi</span><span>◉ Dapur bersama</span><span>◉ Kursi dan meja tamu</span><span>◉ Parkiran</span></div></div></section>

    <section class="af-section af-soft"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Ketentuan tinggal</span><h2>Aturan hunian</h2><p>Informasi disusun ringkas agar mudah dipahami sebelum booking.</p></div><div class="af-accordion"><details open><summary>Ketentuan umum <span>+</span></summary><ul><li>Khusus wanita.</li><li>Jam keluar masuk bebas.</li><li>Penghuni membawa kunci sendiri.</li><li>Penghuni tambahan dikenakan biaya sesuai ketentuan.</li><li>Fasilitas mengikuti daftar fasilitas masing-masing lokasi.</li></ul></details><details><summary>Al Fazza Kost 1 <span>+</span></summary><ul><li>Bukan kosan bebas.</li><li>Khusus perempuan/wanita.</li></ul></details><details><summary>Al Fazza Kost 2 <span>+</span></summary><ul><li>Bukan kosan bebas.</li><li>Khusus perempuan/wanita dan suami istri.</li><li>Suami istri wajib menunjukkan fotokopi KTP dan buku nikah.</li></ul></details></div></div></section>

    <section class="af-section" id="booking"><div class="af-wrap af-booking"><div><span class="af-eyebrow">Booking kamar lebih mudah</span><h2>Pesan kamar lewat sistem dengan langkah yang sederhana.</h2><p>Proses awal biasanya melalui media sosial dan WhatsApp, dilanjutkan pengumpulan data, penentuan lokasi yang sesuai, pemilihan kamar, booking, lalu proses pengelola sesuai ketentuan.</p><p class="af-flow">Login <b>→</b> Pilih Lokasi <b>→</b> Pilih Tipe Kamar <b>→</b> Pilih Kamar <b>→</b> Booking <b>→</b> Pembayaran</p>

        @if(! $hasBooking && ! $isAdmin)

            <a class="af-btn af-btn-primary" href="{{ $bookingUrl }}">Booking Sekarang</a>

            @guest

                <small class="af-login-note">Booking akan meminta Anda login terlebih dahulu.</small>

            @endguest

        @endif

    </div><ol><li><b>1</b><span>Kenali Al Fazza Kost melalui media sosial.</span></li><li><b>2</b><span>Hubungi WhatsApp untuk informasi awal.</span></li><li><b>3</b><span>Pilih lokasi dan kamar yang tersedia.</span></li><li><b>4</b><span>Booking dan lanjutkan sesuai ketentuan pengelola.</span></li></ol></div></section>

    <section class="af-section af-soft" id="galeri"><div class="af-wrap"><div class="af-section-heading"><span class="af-eyebrow">Galeri kamar</span><h2>Lihat foto kamar yang tersedia</h2><p>Foto dapat diperbarui oleh admin melalui data kamar di sistem.</p></div>

        @if($kamarDenganFoto->isNotEmpty())

            <div class="af-gallery">

                @foreach($kamarDenganFoto as $item)

                    <figure><img src="{{ asset('storage/' . $item->foto_kamar) }}" alt="Foto kamar {{ $item->nomor_kamar }}"><figcaption>Kamar {{ $item->nomor_kamar }} · {{ $item->lokasi_kos }}</figcaption></figure>

                @endforeach

            </div>

        @else

            <div class="af-empty-gallery"><span>▧</span><p>Foto kamar akan ditampilkan di sini setelah tersedia.</p></div>

        @endif

    </div></section>

    <section class="af-section" id="pelayanan-pengaduan"><div class="af-wrap af-complaint"><div><span class="af-eyebrow">Pelayanan dan Pengaduan</span><h2>Kami siap mendengarkan keluhan Anda.</h2><p>Keluhan terkait kebisingan, listrik padam, fasilitas, maupun lingkungan kost dapat disampaikan melalui sistem. Pengelola menangani keluhan secara humanis.</p></div>

        @if($isPenghuniAktif)

            <a class="af-btn af-btn-secondary" href="{{ route('penghuni.keluhan.index') }}">Sampaikan Keluhan</a>

        @endif

    </div></section>

    @php

    $kost1 = $pengaturan->firstWhere('lokasi', 'kost1');

    $kost2 = $pengaturan->firstWhere('lokasi', 'kost2');

@endphp

<section class="af-section af-contact" id="kontak">

    <div class="af-wrap">

        <div class="af-contact-intro">

            <span class="af-eyebrow">Hubungi Al Fazza Kost</span>

            <h2>Butuh informasi lebih lanjut?</h2>

            <p>

                Untuk informasi lebih lanjut, silakan hubungi pengelola

                melalui WhatsApp.

            </p>

            @if(

                filled($kost1?->whatsapp) ||

                filled($kost2?->whatsapp)

            )

                <div class="af-contact-actions">

                    @if(filled($kost1?->whatsapp))

                        <a

                            class="af-btn af-btn-primary"

                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kost1->whatsapp) }}"

                            target="_blank"

                            rel="noopener noreferrer"

                        >

                            WhatsApp Kost 1

                        </a>

                    @endif

                    @if(filled($kost2?->whatsapp))

                        <a

                            class="af-btn af-btn-secondary"

                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kost2->whatsapp) }}"

                            target="_blank"

                            rel="noopener noreferrer"

                        >

                            WhatsApp Kost 2

                        </a>

                    @endif

                </div>

            @else

                <span class="af-contact-pending">

                    Kontak WhatsApp akan ditampilkan setelah nomor resmi

                    dikonfigurasi oleh Admin.

                </span>

            @endif

        </div>



        <div class="af-map-grid">

            {{-- AL FAZZA KOST 1 --}}

            <article>

                <span>01</span>

                <h3>

                    {{ $kost1?->nama_kost ?? 'Al Fazza Kost 1' }}

                </h3>

                <p>

                    {{ $kost1?->alamat ?? 'Karangmanyar, Purbalingga' }}

                </p>

                @if(filled($kost1?->google_maps_url))

                    <a

                        class="af-text-link"

                        href="{{ $kost1->google_maps_url }}"

                        target="_blank"

                        rel="noopener noreferrer"

                    >

                        Buka Google Maps <span>→</span>

                    </a>

                @else

                    <span class="af-map-pending">

                        Link Google Maps dapat ditambahkan oleh Admin.

                    </span>

                @endif

            </article>



            {{-- AL FAZZA KOST 2 --}}

            <article>

                <span>02</span>

                <h3>

                    {{ $kost2?->nama_kost ?? 'Al Fazza Kost 2' }}

                </h3>

                <p>

                    {{ $kost2?->alamat ?? 'Toyareka, Purbalingga' }}

                </p>

                @if(filled($kost2?->google_maps_url))

                    <a

                        class="af-text-link"

                        href="{{ $kost2->google_maps_url }}"

                        target="_blank"

                        rel="noopener noreferrer"

                    >

                        Buka Google Maps <span>→</span>

                    </a>

                @else

                    <span class="af-map-pending">

                        Link Google Maps dapat ditambahkan oleh Admin.

                    </span>

                @endif

            </article>

        </div>

    </div>

</section>

</main>

<footer class="af-footer"><div class="af-wrap"><x-site-logo class="site-logo site-logo--footer" /><div><span>Al Fazza Kost</span><small>Hunian khusus wanita di Purbalingga.</small></div></div></footer>

<script>
const menuToggle = document.querySelector('.af-menu-toggle');
const splash = document.querySelector('[data-af-splash]');
if (splash && !window.matchMedia('(prefers-reduced-motion: reduce)').matches && !sessionStorage.getItem('af-splash-seen')) {
    sessionStorage.setItem('af-splash-seen', 'true');
    window.setTimeout(() => splash.classList.add('af-splash--done'), 1850);
} else if (splash) {
    splash.remove();
}
const navbar = document.querySelector('.af-nav');
const navLinks = [...document.querySelectorAll('.af-nav-links a[data-nav-section]')];
const navSections = navLinks
    .map(link => document.getElementById(link.dataset.navSection))
    .filter(Boolean);

const setActiveNavLink = (sectionId) => {
    if (!navSections.some(section => section.id === sectionId)) return;

    navLinks.forEach(link => {
        const isActive = link.dataset.navSection === sectionId;
        link.classList.toggle('is-active', isActive);
        if (isActive) {
            link.setAttribute('aria-current', 'page');
        } else {
            link.removeAttribute('aria-current');
        }
    });
};

const closeMobileMenu = () => {
    const nav = document.querySelector('.af-nav-links');
    nav?.classList.remove('is-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
};

menuToggle?.addEventListener('click', function () {
    const nav = document.querySelector('.af-nav-links');
    const open = nav.classList.toggle('is-open');
    this.setAttribute('aria-expanded', open);
});

let pendingSectionId = null;
let scrollEndTimer = null;
let animationFrame = null;

const getSectionAtReferencePoint = () => {
    if (!navSections.length) {
        return null;
    }

    const navbarHeight = navbar?.getBoundingClientRect().height ?? 0;

    // Titik acuan berada sedikit di bawah navbar.
    const referencePoint = navbarHeight + Math.min(window.innerHeight * 0.30, 260);

    let activeSection = navSections[0];

    navSections.forEach(section => {
        const top = section.getBoundingClientRect().top;

        if (top <= referencePoint) {
            activeSection = section;
        }
    });

    return activeSection;
};

const syncActiveNavLink = () => {
    if (pendingSectionId) {
        return;
    }

    const activeSection = getSectionAtReferencePoint();

    if (!activeSection) {
        return;
    }

    // Ubah tombol navbar yang aktif.
    setActiveNavLink(activeSection.id);

    // Sinkronkan URL dengan section yang sedang terlihat.
    const newHash = `#${activeSection.id}`;

    if (window.location.hash !== newHash) {
        window.history.replaceState(null, '', newHash);
    }
};

const scheduleActiveNavSync = () => {
    if (animationFrame !== null) {
        return;
    }

    animationFrame = window.requestAnimationFrame(() => {
        animationFrame = null;
        syncActiveNavLink();
    });
};

const finishNavigation = () => {
    window.clearTimeout(scrollEndTimer);

    scrollEndTimer = window.setTimeout(() => {
        pendingSectionId = null;
        syncActiveNavLink();
    }, 150);
};

// Klik semua menu navbar.
navLinks.forEach(link => {
    link.addEventListener('click', event => {
        const target = document.getElementById(link.dataset.navSection);

        if (!target) {
            return;
        }

        event.preventDefault();

        pendingSectionId = target.id;

        // Langsung aktifkan menu yang diklik.
        setActiveNavLink(target.id);

        // Jangan membuat history baru setiap klik.
        window.history.replaceState(null, '', `#${target.id}`);

        closeMobileMenu();

        window.requestAnimationFrame(() => {
            const navbarHeight = navbar?.getBoundingClientRect().height ?? 0;

            const targetPosition =
                target.getBoundingClientRect().top +
                window.scrollY -
                navbarHeight;

            window.scrollTo({
                top: Math.max(0, targetPosition),
                behavior: 'smooth'
            });

            finishNavigation();
        });
    });
});

// Saat scroll, cek SEMUA section.
window.addEventListener('scroll', () => {
    scheduleActiveNavSync();

    if (pendingSectionId) {
        finishNavigation();
    }
}, {
    passive: true
});

// Saat ukuran layar berubah.
window.addEventListener('resize', scheduleActiveNavSync);

// Saat URL/hash berubah.
window.addEventListener('hashchange', () => {
    window.requestAnimationFrame(() => {
        pendingSectionId = null;
        syncActiveNavLink();
    });
});

// Saat halaman pertama kali dibuka.
window.addEventListener('load', () => {
    pendingSectionId = null;
    syncActiveNavLink();
}, {
    once: true
});

// Sinkronisasi awal.
syncActiveNavLink();
document.querySelectorAll('[data-af-tab]').forEach(button => button.addEventListener('click', () => { document.querySelectorAll('[data-af-tab]').forEach(item => item.classList.toggle('is-active', item === button)); document.querySelectorAll('[data-af-panel]').forEach(panel => panel.classList.toggle('is-active', panel.dataset.afPanel === button.dataset.afTab)); }));
/* =========================================================
   HERO IMAGE CAROUSEL
   ========================================================= */
const heroSlides = [...document.querySelectorAll('[data-hero-slide]')];
const heroDots = [...document.querySelectorAll('[data-hero-dot]')];
const heroProgress = document.querySelector('.af-slider-progress');
const heroNextButton = document.getElementById('afHeroNext');
const heroInfoLabel = document.getElementById('afHeroInfoLabel');
const heroInfoTitle = document.getElementById('afHeroInfoTitle');
const heroInfoSubtitle = document.getElementById('afHeroInfoSubtitle');
const heroCarousel = document.getElementById('afHeroCarousel');
const heroVisual = document.querySelector('.af-hero-visual');
const heroSlideData = @json($heroSlideData);

let activeHeroSlide = 0;
let heroTouchStartX = 0;
let heroAutoPlay = null;

const showHeroSlide = (index) => {
    if (!heroSlides.length) return;

    activeHeroSlide = ((index % heroSlides.length) + heroSlides.length) % heroSlides.length;

    heroSlides.forEach((slide, slideIndex) => {
        slide.classList.toggle('is-active', slideIndex === activeHeroSlide);
    });

    heroVisual?.classList.toggle(
    'slide-two-glow',
    activeHeroSlide === 1
    );

    heroDots.forEach((dot, dotIndex) => {
        dot.classList.toggle('is-active', dotIndex === activeHeroSlide);
        dot.setAttribute('aria-current', dotIndex === activeHeroSlide ? 'true' : 'false');
    });

    if (heroProgress) {
        heroProgress.style.width = `${((activeHeroSlide + 1) / heroSlides.length) * 100}%`;
    }

    const data = heroSlideData[activeHeroSlide];
    if (data) {
        if (heroInfoLabel) {
            heroInfoLabel.textContent = data.lokasi === 'kost1' ? 'Al Fazza Kost 1' : 'Al Fazza Kost 2';
        }
        if (heroInfoTitle) heroInfoTitle.textContent = data.judul || '';
        if (heroInfoSubtitle) heroInfoSubtitle.textContent = data.subjudul || '';
    }
};

const nextHeroSlide = () => showHeroSlide(activeHeroSlide + 1);
const previousHeroSlide = () => showHeroSlide(activeHeroSlide - 1);

const resetHeroAutoPlay = () => {
    if (heroAutoPlay) window.clearInterval(heroAutoPlay);
    if (heroSlides.length > 1) {
        heroAutoPlay = window.setInterval(nextHeroSlide, 6000);
    }
};

heroNextButton?.addEventListener('click', () => {
    nextHeroSlide();
    resetHeroAutoPlay();
});

heroDots.forEach((dot) => {
    dot.addEventListener('click', () => {
        showHeroSlide(Number(dot.dataset.heroDot));
        resetHeroAutoPlay();
    });
});

heroCarousel?.addEventListener('touchstart', (event) => {
    heroTouchStartX = event.changedTouches[0]?.screenX ?? 0;
}, { passive: true });

heroCarousel?.addEventListener('touchend', (event) => {
    const endX = event.changedTouches[0]?.screenX ?? heroTouchStartX;
    const distance = heroTouchStartX - endX;

    if (Math.abs(distance) > 50) {
        if (distance > 0) {
            nextHeroSlide();
        } else {
            previousHeroSlide();
        }
        resetHeroAutoPlay();
    }
}, { passive: true });

showHeroSlide(0);
resetHeroAutoPlay();

/* =========================================================
   LOCATION CARD SLIDER
   AL FAZZA KOST 1 & 2
   ========================================================= */

const locationSlides = [
    ...document.querySelectorAll('[data-location-slide]')
];

const locationDots = [
    ...document.querySelectorAll('[data-location-dot]')
];

const locationPrev = document.getElementById('afLocationPrev');
const locationNext = document.getElementById('afLocationNext');
const locationProgress = document.getElementById('afLocationProgress');
const locationSlider = document.getElementById('afLocationSlider');

let activeLocationSlide = 0;
let locationTouchStartX = 0;

const showLocationSlide = (index) => {

    if (!locationSlides.length) {
        return;
    }

    activeLocationSlide =
        ((index % locationSlides.length) + locationSlides.length)
        % locationSlides.length;

    locationSlides.forEach((slide, slideIndex) => {
        slide.classList.toggle(
            'is-active',
            slideIndex === activeLocationSlide
        );
    });

    locationDots.forEach((dot, dotIndex) => {

        const active = dotIndex === activeLocationSlide;

        dot.classList.toggle('is-active', active);

        dot.setAttribute(
            'aria-current',
            active ? 'true' : 'false'
        );

    });

    if (locationProgress) {

        locationProgress.style.width =
            `${((activeLocationSlide + 1) / locationSlides.length) * 100}%`;

    }

};

locationPrev?.addEventListener('click', () => {

    showLocationSlide(activeLocationSlide - 1);

});

locationNext?.addEventListener('click', () => {

    showLocationSlide(activeLocationSlide + 1);

});

locationDots.forEach((dot) => {

    dot.addEventListener('click', () => {

        showLocationSlide(
            Number(dot.dataset.locationDot)
        );

    });

});


/* SWIPE / GESER */

locationSlider?.addEventListener(
    'touchstart',
    (event) => {

        locationTouchStartX =
            event.changedTouches[0]?.screenX ?? 0;

    },
    { passive: true }
);

locationSlider?.addEventListener(
    'touchend',
    (event) => {

        const endX =
            event.changedTouches[0]?.screenX ??
            locationTouchStartX;

        const distance =
            locationTouchStartX - endX;

        if (Math.abs(distance) > 50) {

            if (distance > 0) {

                showLocationSlide(
                    activeLocationSlide + 1
                );

            } else {

                showLocationSlide(
                    activeLocationSlide - 1
                );

            }

        }

    },
    { passive: true }
);

showLocationSlide(0);

</script>
</body>
</html>
