<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembayaran | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])

</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'pembayaran'])


    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

        <header class="dashboard-header">

            <div>

                <h1>Data Pembayaran</h1>

                <p>
                    Kelola data pembayaran penghuni Al Fazza Kost.
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


        <!-- DATA PEMBAYARAN -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <h2>Daftar Pembayaran</h2>

                    <p>
                        Data pembayaran yang masuk ke sistem.
                    </p>

                </div>

            </div>


            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Pemesan</th>
                            <th>Jenis / Periode</th>
                            <th>Kamar</th>
                            <th>Metode Bayar</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($pembayaran as $item)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>

                                    <strong>
                                        {{ $item->booking->nama_pemesan ?? '-' }}
                                    </strong>

                                </td>


                                <td>
                                    {{ $item->jenis_pembayaran }}
                                    @if($item->periode_bayar)<br><small>{{ $item->periode_bayar }}</small>@endif
                                </td>


                                <td>
                                    {{ $item->booking->kamar->nomor_kamar ?? '-' }}
                                </td>


                                <td>
                                    {{ $item->metode_bayar }}
                                </td>


                                <td>

                                    {{ $item->tanggal_bayar
                                        ? $item->tanggal_bayar->format('d-m-Y')
                                        : '-' }}

                                </td>


                                <td>
                                    Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                </td>


                                <td>
                                    {{ $item->status_label }}
                                    @if($item->refund)<br><small>{{ $item->refund->status_refund }}</small>@endif
                                </td>

                                <td>
                                    <a href="{{ route('pembayaran.show', $item->id_pembayaran) }}"
                                       class="btn-detail">
                                        Detail
                                    </a>
                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="empty-table"
                                >
                                    Belum ada data pembayaran.
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
