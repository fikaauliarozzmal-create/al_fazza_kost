<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluhan Penghuni | Al Fazza Kost</title>
    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>
<body>
<div class="dashboard">
    @include('admin.partials.sidebar', ['active' => 'keluhan'])
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div><h1>Keluhan Penghuni</h1><p>Kelola dan tindak lanjuti keluhan yang disampaikan oleh penghuni.</p></div>
            <div class="admin-profile"><div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div></div>
        </header>

        <section class="dashboard-section">
            <div class="section-heading complaint-heading">
                <div><h2>Daftar Keluhan</h2><p>Gunakan filter untuk melihat keluhan berdasarkan status penanganan.</p></div>
                <div class="complaint-filters">
                    @foreach(['' => 'Semua', 'Baru' => 'Baru', 'Diproses' => 'Diproses', 'Selesai' => 'Selesai'] as $value => $label)
                        <a href="{{ route('admin.keluhan.index', $value ? ['status' => $value] : []) }}" class="{{ $status === $value ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead><tr><th>No</th><th>Penghuni</th><th>Kamar</th><th>Kategori</th><th>Judul</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse($pengaduan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->penghuni?->user?->name ?? '-' }}</strong></td>
                            <td>{{ $item->penghuni?->kamar?->nomor_kamar ?? '-' }}</td>
                            <td>{{ $item->kategori ?? 'Lainnya' }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ $item->tanggal?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td><span class="complaint-status complaint-status-{{ strtolower($item->status) }}">{{ $item->status }}</span></td>
                            <td><a class="btn-detail" href="{{ route('admin.keluhan.show', $item) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-table">Tidak ada keluhan untuk filter ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
