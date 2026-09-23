<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'booking'])


    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

        <header class="dashboard-header">

            <div>
                <h1>Data Booking</h1>

                <p>
                    Kelola data booking calon penghuni Al Fazza Kost.
                </p>
            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    A
                </div>

                <div>
                    <strong>Admin Al Fazza</strong>
                    <small>Administrator</small>
                </div>

            </div>

        </header>


        <!-- DATA BOOKING -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>
                    <h2>Daftar Booking</h2>

                    <p>
                        Data booking yang masuk ke sistem.
                    </p>
                </div>

            </div>


            <div class="table-wrapper">

                <table class="kamar-table">

                    <thead>

                       <tr>
                        <th>No</th>
                        <th>Pemesan</th>
                        <th>Nomor WhatsApp</th>
                        <th>Kamar</th>
                        <th>Tanggal Booking</th>
                        <th>Jenis Kelamin</th>
                        <th>Lokasi Kerja</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                    </thead>

                    <tbody>

                        @forelse ($booking as $item)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $item->nama_pemesan }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $item->no_whatsapp }}
                                </td>

                                <td>
                                    {{ $item->kamar->nomor_kamar ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->tanggal_booking }}
                                </td>

                                <td>
                                    {{ $item->jenis_kelamin }}
                                </td>

                                <td>
                                    {{ $item->lokasi_kerja }}
                                </td>

                                <td>

                                    <span class="status-badge" style="background-color: #fff3cd; color: #856404; padding: 7px 14px; border-radius: 20px; display: inline-block; font-weight: 600;">
                                        {{ $item->status_booking }}
                                    </span>

                                </td>

                                <td>
                                <div class="booking-action">

                                    <a href="{{ route('booking.show', $item->id_booking) }}" class="btn-detail">
                                        Detail
                                    </a>

        @if ($item->status_booking === 'Menunggu')

    <form
        action="{{ route('booking.terima', $item->id_booking) }}"
        method="POST"
        style="display: inline;"
        onsubmit="return confirm('Yakin ingin menerima booking ini?');"
    >
        @csrf

        <button
            type="submit"
            class="btn-terima"
        >
            Terima
        </button>
    </form>


    <form
        action="{{ route('booking.tolak', $item->id_booking) }}"
        method="POST"
        style="display: inline;"
        onsubmit="return confirm('Yakin ingin menolak booking ini?');"
    >
        @csrf

        <button
            type="submit"
            class="btn-tolak"
        >
            Tolak
        </button>
    </form>

@endif

    </div>
</td>

                            </tr>

                        @empty

                            <tr>

                              <td colspan="9" class="empty-table">

                                    Belum ada data booking.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>
