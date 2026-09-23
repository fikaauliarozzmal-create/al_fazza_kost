<button class="sidebar-mobile-toggle" type="button" aria-controls="penghuni-sidebar" aria-expanded="false">
    <span class="sidebar-mobile-toggle__icon" aria-hidden="true">☰</span><span>Menu</span>
</button>
<div class="sidebar-mobile-backdrop" aria-hidden="true"></div>
<aside class="sidebar" id="penghuni-sidebar">
    @php
        $bookingDisetujui = auth()->user()?->bookings()
            ->where('status_booking', 'Disetujui')
            ->latest('id_booking')
            ->first();
    @endphp
    <div class="sidebar-logo">
        <x-site-logo class="site-logo site-logo--sidebar" />
        <h2>Al Fazza Kost</h2>
        <span>Portal Penghuni</span>
    </div>
    <nav class="sidebar-menu">
        <a href="{{ route('penghuni.dashboard') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="dashboard" /></span><span>Dashboard</span></a>
        @if(auth()->user()?->isPenghuniAktif())
            <a href="{{ route('penghuni.kamar') }}" class="{{ ($active ?? '') === 'kamar' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="bed" /></span><span>Kamar Saya</span></a>
            <a href="{{ route('penghuni.pembayaran') }}" class="{{ ($active ?? '') === 'pembayaran' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="wallet" /></span><span>Pembayaran</span></a>
            <a href="{{ route('penghuni.invoice.index') }}" class="{{ ($active ?? '') === 'invoice' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="receipt" /></span><span>Invoice</span></a>
            <a href="{{ route('penghuni.keluhan.index') }}" class="{{ ($active ?? '') === 'keluhan' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="service" /></span><span>Keluhan</span></a>
            <a href="{{ route('penghuni.profil') }}" class="{{ ($active ?? '') === 'profil' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="profile" /></span><span>Profil</span></a>
        @elseif($bookingDisetujui)
            <a href="{{ route('booking.status', $bookingDisetujui) }}" class="{{ ($active ?? '') === 'pembayaran-awal' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="wallet" /></span><span>Pembayaran</span></a>
        @endif
    </nav>
    <div class="sidebar-bottom">
        <a href="{{ route('landing') }}"><span class="sidebar-menu-icon"><x-ui-icon name="globe" /></span><span>Ke Landing Page</span></a>
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"><span class="sidebar-menu-icon"><x-ui-icon name="logout" /></span><span>Logout</span></button></form>
    </div>
</aside>
<script>
(() => {
    const toggle = document.querySelector('.sidebar-mobile-toggle');
    const sidebar = document.getElementById('penghuni-sidebar');
    const backdrop = document.querySelector('.sidebar-mobile-backdrop');
    if (!toggle || !sidebar || toggle.dataset.ready) return;
    toggle.dataset.ready = 'true';
    const close = () => { sidebar.classList.remove('is-open'); backdrop?.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); };
    toggle.addEventListener('click', () => { const open = sidebar.classList.toggle('is-open'); backdrop?.classList.toggle('is-open', open); toggle.setAttribute('aria-expanded', String(open)); });
    backdrop?.addEventListener('click', close);
    sidebar.querySelectorAll('a, button').forEach((item) => item.addEventListener('click', close));
    window.addEventListener('resize', () => { if (window.innerWidth > 768) close(); });
})();
</script>
