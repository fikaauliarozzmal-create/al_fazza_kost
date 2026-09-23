<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>

<body>

    <div class="dashboard">

        @include('admin.partials.sidebar', ['active' => 'dashboard'])


        <!-- MAIN CONTENT -->

        <main class="dashboard-main">

            <header class="dashboard-header">

                <div>
                    <h1>Dashboard</h1>

                    <p>
                        Selamat datang kembali, Admin Al Fazza Kost 👋
                    </p>
                </div>

                <div class="admin-profile">

                    <x-user-avatar :user="auth()->user()" class="admin-avatar" />

                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Administrator</small>
                    </div>

                </div>

            </header>


            <!-- STATISTIK -->

           <section class="dashboard-stats">

    <div class="stat-card">

        <div class="stat-icon stat-icon--room"><x-ui-icon name="bed" size="25" /></div>

        <div>
            <span>Total Kamar</span>
            <h2>{{ $totalKamar }}</h2>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon--booking"><x-ui-icon name="booking" size="25" /></div>

        <div>
            <span>Booking</span>
            <h2>{{ $totalBooking }}</h2>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon--resident"><x-ui-icon name="resident" size="25" /></div>

        <div>
            <span>Penghuni</span>
            <h2>{{ $totalPenghuni }}</h2>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon--payment"><x-ui-icon name="wallet" size="25" /></div>

        <div>
            <span>Pembayaran</span>
            <h2>Rp{{ number_format($totalPembayaran, 0, ',', '.') }}</h2>
        </div>

    </div>

</section>


            <!-- AKTIVITAS -->

            <section class="dashboard-section">

                <div class="section-heading">

                    <div>
                        <h2>Aktivitas Terbaru</h2>

                        <p>
                            Informasi terbaru dari sistem Al Fazza Kost.
                        </p>
                    </div>

                </div>


                @if($activities->isNotEmpty())
                    <div class="activity-list">
                        @foreach($activities as $activity)
                            @php($icon = ['booking' => 'booking', 'payment' => 'wallet', 'resident' => 'resident', 'complaint' => 'service', 'invoice' => 'receipt'][$activity['type']] ?? 'bell')
                            @if($activity['url'])
                                <a class="activity-item" href="{{ $activity['url'] }}">
                            @else
                                <div class="activity-item">
                            @endif
                                <span class="activity-icon activity-icon--{{ $activity['type'] }}"><x-ui-icon :name="$icon" /></span>
                                <span class="activity-content">
                                    <strong>{{ $activity['title'] }}</strong>
                                    <span>{{ $activity['description'] }}</span>
                                    <small title="{{ $activity['time_full_label'] }}" data-activity-time="{{ $activity['time_iso'] }}">{{ $activity['time_label'] }}</small>
                                </span>
                                @if($activity['url'])<span class="activity-arrow" aria-hidden="true">→</span>@endif
                            @if($activity['url'])</a>@else</div>@endif
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><x-ui-icon name="mail" size="34" /></div>
                        <h3>Belum ada aktivitas</h3>
                        <p>Aktivitas booking, pembayaran, dan penghuni akan muncul di sini.</p>
                    </div>
                @endif

            </section>

                        <!-- PENGHUNI AKTIF -->

            <section class="dashboard-section">

                <div class="section-heading">

                    <div>
                        <h2>Penghuni Aktif</h2>

                        <p>
                            Kelola penghuni yang sedang menempati kamar.
                        </p>
                    </div>

                </div>

                @if($penghuniAktif->isNotEmpty())

                    <div class="table-wrapper">

                        <table class="dashboard-table">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penghuni</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($penghuniAktif as $item)

                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <strong>{{ $item->user?->name ?? '-' }}</strong>
                                        </td>

                                        <td>
                                            {{ $item->kamar?->nomor_kamar ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->tanggal_masuk
                                                ? $item->tanggal_masuk->format('d-m-Y')
                                                : '-' }}
                                        </td>

                                        <td>
                                            <span class="status-badge status-badge--active">
                                                Aktif
                                            </span>
                                        </td>

                                        <td>

                                            <form
                                                action="{{ route('penghuni.keluarkan', $item->id_penghuni) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin mengeluarkan penghuni ini dari kost? Akun penghuni juga akan dinonaktifkan.')"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn-keluarkan-penghuni"
                                                >
                                                    Keluarkan
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-icon">
                            <x-ui-icon name="resident" size="34" />
                        </div>

                        <h3>Belum ada penghuni aktif</h3>

                        <p>
                            Penghuni yang sudah aktif akan muncul di sini.
                        </p>

                    </div>

                @endif

            </section>

        </main>

    </div>

</body>

</html>
