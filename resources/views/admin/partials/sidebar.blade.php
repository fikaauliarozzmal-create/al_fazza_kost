<aside class="sidebar">
    <div class="sidebar-logo">
        <x-site-logo class="site-logo site-logo--sidebar" />
        <h2>Al Fazza Kost</h2>
        <span>Admin Panel</span>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="dashboard" /></span><span>Dashboard</span></a>
        <a href="{{ route('admin.profil') }}" class="{{ ($active ?? '') === 'profil' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="profile" /></span><span>Profil</span></a>
        <a href="{{ route('kamar.index') }}" class="{{ ($active ?? '') === 'kamar' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="bed" /></span><span>Data Kamar</span></a>
        <a href="{{ route('booking.index') }}" class="{{ ($active ?? '') === 'booking' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="booking" /></span><span>Booking</span></a>
        <a href="{{ route('penghuni.index') }}" class="{{ ($active ?? '') === 'penghuni' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="resident" /></span><span>Penghuni</span></a>
        <a href="{{ route('pembayaran.index') }}" class="{{ ($active ?? '') === 'pembayaran' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="wallet" /></span><span>Pembayaran</span></a>
        <a href="{{ route('invoice.index') }}" class="{{ ($active ?? '') === 'invoice' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="receipt" /></span><span>Invoice</span></a>
        <a href="{{ route('admin.keluhan.index') }}" class="{{ ($active ?? '') === 'keluhan' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="service" /></span><span>Keluhan</span>@if($keluhanBaruCount ?? 0)<b class="sidebar-badge">{{ $keluhanBaruCount }}</b>@endif</a>
        <a href="{{ route('admin.payment-settings.index') }}" class="{{ ($active ?? '') === 'payment-settings' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="settings" /></span><span>Pengaturan Pembayaran</span></a>
        <a href="{{ route('pengaturan-website.index') }}" class="{{ ($active ?? '') === 'pengaturan-website' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="globe" /></span><span>Pengaturan Website</span></a>
        <a href="{{ route('admin.laporan.index') }}" class="{{ ($active ?? '') === 'laporan' ? 'active' : '' }}"><span class="sidebar-menu-icon"><x-ui-icon name="report" /></span><span>Laporan</span></a>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('landing') }}"><span class="sidebar-menu-icon"><x-ui-icon name="globe" /></span><span>Ke Landing Page</span></a>
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"><span class="sidebar-menu-icon"><x-ui-icon name="logout" /></span><span>Logout</span></button></form>
    </div>
</aside>
