<div class="notification-widget" data-notification-widget data-feed-url="{{ route('notifikasi.ringkas') }}" data-read-all-url="{{ route('notifikasi.baca-semua') }}" data-read-url="{{ url('/notifikasi') }}" data-all-url="{{ route('notifikasi.index') }}">
    <button class="notification-trigger" type="button" aria-label="Buka notifikasi" aria-expanded="false" data-notification-toggle><x-ui-icon name="bell" size="20" /><b hidden data-notification-badge></b></button>
    <section class="notification-dropdown" hidden data-notification-panel>
        <header><strong>Notifikasi</strong><button type="button" data-notification-read-all>Tandai semua sudah dibaca</button></header>
        <div class="notification-list" data-notification-list><p class="notification-empty">Memuat notifikasi…</p></div>
        <a class="notification-all-link" href="{{ route('notifikasi.index') }}">Lihat semua notifikasi</a>
    </section>
</div>
