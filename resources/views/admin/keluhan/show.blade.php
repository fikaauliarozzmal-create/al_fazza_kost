<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Keluhan | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'keluhan'])
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div><h1>Detail Keluhan</h1><p>Tinjau keluhan penghuni dan kirimkan tindak lanjut.</p></div>
            <div class="admin-profile"><div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div></div>
        </header>
        @if(session('success'))<div class="tenant-alert tenant-alert-success">{{ session('success') }}</div>@endif

        <section class="dashboard-section complaint-detail-section">
            <div class="section-heading"><div><h2>Informasi Penghuni</h2><p>Data penghuni yang mengirimkan keluhan.</p></div><x-back-button href="{{ route('admin.keluhan.index') }}" aria-label="Kembali ke daftar keluhan" /></div>
            <div class="complaint-info-grid">
                <div><span>Nama penghuni</span><strong>{{ $pengaduan->penghuni?->user?->name ?? '-' }}</strong></div>
                <div><span>Nomor kamar</span><strong>{{ $pengaduan->penghuni?->kamar?->nomor_kamar ?? '-' }}</strong></div>
                <div><span>Lokasi kost</span><strong>{{ $pengaduan->penghuni?->kamar?->lokasi_kos ?? '-' }}</strong></div>
            </div>
        </section>

        <section class="dashboard-section complaint-detail-section">
            <div class="section-heading"><div><h2>Informasi Keluhan</h2><p>Rincian keluhan yang disampaikan penghuni.</p></div></div>
            <div class="complaint-info-grid complaint-info-grid-wide">
                <div><span>Kategori</span><strong>{{ $pengaduan->kategori ?? 'Lainnya' }}</strong></div>
                <div><span>Judul</span><strong>{{ $pengaduan->judul }}</strong></div>
                <div><span>Tanggal dibuat</span><strong>{{ $pengaduan->tanggal?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div><span>Status</span><strong><span class="complaint-status complaint-status-{{ strtolower($pengaduan->status) }}">{{ $pengaduan->status }}</span></strong></div>
            </div>
            <div class="complaint-message"><span>Isi keluhan</span><p>{{ $pengaduan->isi_pengaduan }}</p></div>
        </section>

        <section class="dashboard-section complaint-detail-section">
            <div class="section-heading"><div><h2>Lampiran</h2><p>Foto atau video dari penghuni, jika tersedia.</p></div></div>
            @if($pengaduan->lampiran_foto || $pengaduan->lampiran_video)
                <div class="complaint-attachments">
                    @if($pengaduan->lampiran_foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pengaduan->lampiran_foto))
                        <figure><figcaption>Foto lampiran</figcaption><img src="{{ \Illuminate\Support\Facades\Storage::url($pengaduan->lampiran_foto) }}" alt="Foto lampiran keluhan"></figure>
                    @endif
                    @if($pengaduan->lampiran_video && \Illuminate\Support\Facades\Storage::disk('public')->exists($pengaduan->lampiran_video))
                        <figure><figcaption>Video lampiran</figcaption><video controls preload="metadata"><source src="{{ \Illuminate\Support\Facades\Storage::url($pengaduan->lampiran_video) }}">Browser Anda tidak mendukung pemutar video.</video></figure>
                    @endif
                    @if(($pengaduan->lampiran_foto && ! \Illuminate\Support\Facades\Storage::disk('public')->exists($pengaduan->lampiran_foto)) || ($pengaduan->lampiran_video && ! \Illuminate\Support\Facades\Storage::disk('public')->exists($pengaduan->lampiran_video)))<p class="complaint-empty-attachment">Lampiran tidak tersedia.</p>@endif
                </div>
            @else
                <p class="complaint-empty-attachment">Tidak ada lampiran.</p>
            @endif
        </section>

        <section class="dashboard-section complaint-detail-section">
            <div class="section-heading"><div><h2>Tindak Lanjut</h2><p>Perbarui status dan berikan tanggapan yang dapat dilihat penghuni.</p></div></div>
            <form class="complaint-response-form" action="{{ route('admin.keluhan.update', $pengaduan) }}" method="POST">
                @csrf @method('PUT')
                <label for="status">Status</label>
                <select id="status" name="status" required>@foreach(['Baru', 'Diproses', 'Selesai'] as $status)<option value="{{ $status }}" @selected(old('status', $pengaduan->status) === $status)>{{ $status }}</option>@endforeach</select>
                @error('status')<small class="tenant-form-error">{{ $message }}</small>@enderror
                <label for="tanggapan_admin">Tanggapan Admin</label>
                <textarea id="tanggapan_admin" name="tanggapan_admin" rows="6" maxlength="5000" placeholder="Keluhan sudah diterima dan akan segera diperbaiki.">{{ old('tanggapan_admin', $pengaduan->tanggapan_admin) }}</textarea>
                @error('tanggapan_admin')<small class="tenant-form-error">{{ $message }}</small>@enderror
                <button class="btn-success" type="submit">Simpan Tindak Lanjut</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
