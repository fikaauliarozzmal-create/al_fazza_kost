<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Admin | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'laporan'])

    <main class="dashboard-main laporan-main">
        <header class="dashboard-header">
            <div>
                <h1>Laporan Al Fazza Kost</h1>
                <p>Ringkasan data kamar, booking, penghuni, pembayaran, invoice, dan keluhan.</p>
            </div>
            <div class="admin-profile laporan-header-actions">
                <button type="button" class="btn-secondary laporan-print-button" onclick="window.print()">🖨️ Cetak Laporan</button>
                <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div>
            </div>
        </header>

        <section class="dashboard-section laporan-filter-section">
            <div class="section-heading">
                <div><h2>Filter Periode</h2><p>Data bertanggal difilter berdasarkan tanggal transaksi, masuk, booking, invoice, atau keluhan.</p></div>
                <span class="laporan-period-label">{{ $labelPeriode }}</span>
            </div>
            <form class="laporan-filter-form" method="GET" action="{{ route('admin.laporan.index') }}">
                <label for="periode">Periode cepat</label>
                <select id="periode" name="periode">
                    <option value="semua" @selected(request('periode', 'semua') === 'semua')>Semua</option>
                    <option value="bulan_ini" @selected(request('periode') === 'bulan_ini')>Bulan ini</option>
                    <option value="bulan_lalu" @selected(request('periode') === 'bulan_lalu')>Bulan lalu</option>
                    <option value="tahun_ini" @selected(request('periode') === 'tahun_ini')>Tahun ini</option>
                </select>
                <label for="tanggal_mulai">Tanggal mulai</label>
                <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ request('tanggal_mulai') }}">
                <label for="tanggal_selesai">Tanggal akhir</label>
                <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ request('tanggal_selesai') }}">
                <button type="submit" class="btn-success">Terapkan Filter</button>
                <a class="btn-secondary" href="{{ route('admin.laporan.index') }}">Reset</a>
            </form>
            @error('tanggal_selesai')<p class="laporan-filter-error">{{ $message }}</p>@enderror
        </section>

        <section class="laporan-summary-grid">
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="bed" /></span><h2>Ringkasan Kamar</h2></div>
                <strong>{{ $ringkasan['kamar']['total'] }}</strong><p>Total kamar</p>
                <dl><div><dt>Tersedia</dt><dd>{{ $ringkasan['kamar']['tersedia'] }}</dd></div><div><dt>Terisi</dt><dd>{{ $ringkasan['kamar']['terisi'] }}</dd></div><div><dt>Perbaikan</dt><dd>{{ $ringkasan['kamar']['perbaikan'] }}</dd></div></dl>
            </article>
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="booking" /></span><h2>Ringkasan Booking</h2></div>
                <strong>{{ $ringkasan['booking']['total'] }}</strong><p>Total booking</p>
                <dl><div><dt>Menunggu</dt><dd>{{ $ringkasan['booking']['menunggu'] }}</dd></div><div><dt>Disetujui</dt><dd>{{ $ringkasan['booking']['disetujui'] }}</dd></div><div><dt>Ditolak</dt><dd>{{ $ringkasan['booking']['ditolak'] }}</dd></div></dl>
            </article>
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="resident" /></span><h2>Ringkasan Penghuni</h2></div>
                <strong>{{ $ringkasan['penghuni']['aktif'] }}</strong><p>Penghuni aktif</p>
            </article>
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="wallet" /></span><h2>Ringkasan Pembayaran</h2></div>
                <strong>{{ $ringkasan['pembayaran']['total'] }}</strong><p>Total pembayaran</p>
                <dl><div><dt>Nominal valid</dt><dd>Rp {{ number_format($ringkasan['pembayaran']['nominal_valid'], 0, ',', '.') }}</dd></div><div><dt>Pending</dt><dd>{{ $ringkasan['pembayaran']['pending'] }}</dd></div><div><dt>Ditolak</dt><dd>{{ $ringkasan['pembayaran']['ditolak'] }}</dd></div></dl>
            </article>
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="receipt" /></span><h2>Ringkasan Invoice</h2></div>
                <strong>{{ $ringkasan['invoice']['total'] }}</strong><p>Total invoice</p>
                <dl><div><dt>Lunas</dt><dd>{{ $ringkasan['invoice']['lunas'] }}</dd></div></dl>
            </article>
            <article class="laporan-summary-card">
                <div class="laporan-card-heading"><span class="laporan-icon"><x-ui-icon name="service" /></span><h2>Ringkasan Keluhan</h2></div>
                <strong>{{ $ringkasan['keluhan']['total'] }}</strong><p>Total keluhan</p>
                <dl><div><dt>Baru</dt><dd>{{ $ringkasan['keluhan']['baru'] }}</dd></div><div><dt>Diproses</dt><dd>{{ $ringkasan['keluhan']['diproses'] }}</dd></div><div><dt>Selesai</dt><dd>{{ $ringkasan['keluhan']['selesai'] }}</dd></div></dl>
            </article>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Kamar</h2><p>Status kamar saat ini tidak dibatasi periode karena tabel kamar tidak memiliki tanggal operasional.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nomor Kamar</th><th>Tipe</th><th>Lokasi Kost</th><th>Harga</th><th>Status</th></tr></thead><tbody>
            @forelse($kamar as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->nomor_kamar }}</td><td>{{ $item->tipe_kamar }}</td><td>{{ $item->lokasi_kos }}</td><td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td><td>{{ $item->status_kamar }}</td></tr>
            @empty<tr><td colspan="6" class="empty-table">Belum ada data kamar.</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Booking</h2><p>Booking sesuai periode yang dipilih.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nama Pemesan</th><th>Nomor WhatsApp</th><th>Kamar</th><th>Lokasi Kost</th><th>Tanggal Booking</th><th>Status</th></tr></thead><tbody>
            @forelse($booking as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->nama_pemesan }}</td><td>{{ $item->no_whatsapp }}</td><td>{{ $item->kamar?->nomor_kamar ?? '-' }}</td><td>{{ $item->kamar?->lokasi_kos ?? '-' }}</td><td>{{ $item->tanggal_booking?->translatedFormat('d M Y') ?? '-' }}</td><td>{{ $item->status_booking }}</td></tr>
            @empty<tr><td colspan="7" class="empty-table">Belum ada data booking untuk periode ini.</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Penghuni</h2><p>Penghuni dengan tanggal mulai sesuai periode yang dipilih.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nama Penghuni</th><th>Nomor WhatsApp</th><th>Kamar</th><th>Lokasi Kost</th><th>Status</th><th>Tanggal Mulai</th></tr></thead><tbody>
            @forelse($penghuni as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->user?->name ?? $item->booking?->nama_pemesan ?? '-' }}</td><td>{{ $item->user?->no_whatsapp ?? $item->booking?->no_whatsapp ?? '-' }}</td><td>{{ $item->kamar?->nomor_kamar ?? '-' }}</td><td>{{ $item->kamar?->lokasi_kos ?? '-' }}</td><td>{{ $item->status_penghuni }}</td><td>{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</td></tr>
            @empty<tr><td colspan="7" class="empty-table">Belum ada data penghuni untuk periode ini.</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Pembayaran</h2><p>Pembayaran sesuai tanggal pembayaran pada periode yang dipilih.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nama Penghuni</th><th>Kamar</th><th>Jenis</th><th>Metode</th><th>Periode</th><th>Tanggal</th><th>Jumlah</th><th>Status</th></tr></thead><tbody>
            @forelse($pembayaran as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->booking?->nama_pemesan ?? '-' }}</td><td>{{ $item->booking?->kamar?->nomor_kamar ?? '-' }}</td><td>{{ $item->jenis_pembayaran }}</td><td>{{ $item->metode_bayar ?? '-' }}</td><td>{{ $item->periode_bayar ? \Carbon\Carbon::createFromFormat('Y-m', $item->periode_bayar)->translatedFormat('F Y') : 'Sewa pertama' }}</td><td>{{ $item->tanggal_bayar?->translatedFormat('d M Y') ?? '-' }}</td><td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td><td>{{ $item->status_bayar }}</td></tr>
            @empty<tr><td colspan="9" class="empty-table">Belum ada data pembayaran untuk periode ini.</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Invoice</h2><p>Invoice yang dibuat pada periode yang dipilih.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nomor Invoice</th><th>Nama Penghuni</th><th>Kamar</th><th>Tanggal Invoice</th><th>Total Tagihan</th><th>Status</th></tr></thead><tbody>
            @forelse($invoice as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->nomor_invoice }}</td><td>{{ $item->penghuni?->user?->name ?? '-' }}</td><td>{{ $item->penghuni?->kamar?->nomor_kamar ?? '-' }}</td><td>{{ $item->tanggal_invoice?->translatedFormat('d M Y') ?? '-' }}</td><td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td><td>{{ ucfirst($item->status_invoice) }}</td></tr>
            @empty<tr><td colspan="7" class="empty-table">Belum ada data invoice untuk periode ini.</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="dashboard-section laporan-table-section">
            <div class="section-heading"><div><h2>Laporan Keluhan</h2><p>Keluhan sesuai tanggal pengajuan pada periode yang dipilih.</p></div></div>
            <div class="table-wrapper"><table class="data-table"><thead><tr><th>No</th><th>Nama Penghuni</th><th>Kategori</th><th>Judul</th><th>Isi Singkat</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>
            @forelse($pengaduan as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->penghuni?->user?->name ?? '-' }}</td><td>{{ $item->kategori ?? '-' }}</td><td>{{ $item->judul }}</td><td>{{ \Illuminate\Support\Str::limit($item->isi_pengaduan, 80) }}</td><td>{{ $item->tanggal?->translatedFormat('d M Y') ?? '-' }}</td><td>{{ $item->status }}</td></tr>
            @empty<tr><td colspan="7" class="empty-table">Belum ada data keluhan untuk periode ini.</td></tr>@endforelse
            </tbody></table></div>
        </section>
    </main>
</div>
</body>
</html>
