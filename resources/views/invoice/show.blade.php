<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Invoice | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'invoice'])
    <main class="dashboard-main">
        <header class="dashboard-header"><div><h1>Detail Invoice</h1><p>Rincian invoice pembayaran penghuni.</p></div><div class="admin-profile"><div class="admin-avatar">A</div><div><strong>Admin Al Fazza</strong><small>Administrator</small></div></div></header>
        <section class="dashboard-section">
            <div class="table-wrapper"><table class="data-table">
                <tr><th>Nomor Invoice</th><td>{{ $invoice->nomor_invoice }}</td></tr>
                <tr><th>Tanggal Invoice</th><td>{{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d-m-Y') : '-' }}</td></tr>
                <tr><th>Status Invoice</th><td>{{ ucfirst($invoice->status_invoice) }}</td></tr>
                <tr><th>Nama Penghuni</th><td>{{ $invoice->penghuni?->user?->name ?? '-' }}</td></tr>
                <tr><th>Email</th><td>{{ $invoice->penghuni?->user?->email ?? '-' }}</td></tr>
                <tr><th>Nomor WhatsApp</th><td>{{ $invoice->penghuni?->user?->no_whatsapp ?? $invoice->penghuni?->booking?->no_whatsapp ?? '-' }}</td></tr>
                <tr><th>Nomor Kamar</th><td>{{ $invoice->penghuni?->kamar?->nomor_kamar ?? '-' }}</td></tr>
                <tr><th>Tipe Kamar</th><td>{{ $invoice->penghuni?->kamar?->tipe_kamar ?? '-' }}</td></tr>
                <tr><th>Lokasi Kost</th><td>{{ $invoice->penghuni?->kamar?->lokasi_kos ?? '-' }}</td></tr>
                <tr><th>Jenis Pembayaran</th><td>{{ $invoice->pembayaran?->jenis_pembayaran ?? '-' }}</td></tr>
                <tr><th>Periode Pembayaran</th><td>{{ $invoice->pembayaran?->periode_bayar ?? '-' }}</td></tr>
                <tr><th>Metode Pembayaran</th><td>{{ $invoice->pembayaran?->metode_bayar ?? '-' }}</td></tr>
                <tr><th>Jumlah Tagihan</th><td>Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td></tr>
                <tr><th>Tanggal Pembayaran</th><td>{{ $invoice->pembayaran?->tanggal_bayar ? $invoice->pembayaran->tanggal_bayar->format('d-m-Y') : '-' }}</td></tr>
                <tr><th>Status Pembayaran</th><td>{{ $invoice->pembayaran?->status_bayar ?? '-' }}</td></tr>
            </table></div>
            <div style="margin-top: 20px;"><x-back-button href="{{ route('invoice.index') }}" aria-label="Kembali ke daftar invoice" /></div>
        </section>
    </main>
</div>
</body>
</html>
