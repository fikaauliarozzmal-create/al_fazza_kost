<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Booking | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'booking'])


    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

        <header class="dashboard-header">

            <div>

                <h1>Detail Booking</h1>

                <p>
                    Informasi lengkap calon penghuni Al Fazza Kost.
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


        <!-- DETAIL BOOKING -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <h2>Informasi Booking</h2>

                    <p>
                        Detail data booking yang masuk ke sistem.
                    </p>

                </div>

            </div>


            <div class="booking-detail">

                <div class="detail-item">

                    <span>ID Booking</span>

                    <strong>
                        #{{ $booking->id_booking }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Nama Pemesan</span>

                    <strong>
                        {{ $booking->nama_pemesan }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Nomor WhatsApp</span>

                    <strong>
                        {{ $booking->no_whatsapp }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Kamar</span>

                    <strong>
                        {{ $booking->kamar->nomor_kamar ?? '-' }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Tipe Kamar</span>

                    <strong>
                        {{ $booking->kamar->tipe_kamar ?? '-' }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Lokasi Kost</span>

                    <strong>
                        {{ $booking->kamar->lokasi_kos ?? '-' }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Harga Kamar</span>

                    <strong>
                        Rp{{ number_format($booking->kamar->harga ?? 0, 0, ',', '.') }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Tanggal Booking</span>

                    <strong>
                        {{ $booking->tanggal_booking?->format('d-m-Y') ?? '-' }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Jenis Kelamin</span>

                    <strong>
                        {{ $booking->jenis_kelamin }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Lokasi Kerja</span>

                    <strong>
                        {{ $booking->lokasi_kerja }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span>Status Booking</span>

                    <strong class="status-badge">
                        {{ $booking->status_booking }}
                    </strong>

                </div>

            </div>


            <!-- TOMBOL KEMBALI -->

            <div style="margin-top: 25px;">

                <x-back-button href="{{ route('booking.index') }}" aria-label="Kembali ke daftar booking" />

            </div>

        </section>

    </main>

</div>

</body>

</html>
