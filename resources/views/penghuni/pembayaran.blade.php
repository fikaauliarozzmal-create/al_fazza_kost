<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pembayaran | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>

<body>

<div class="dashboard">

    @include('penghuni.partials.sidebar', ['active' => 'pembayaran'])

    <main class="dashboard-main">

        <header class="dashboard-header">
            <div>
                <h1>Pembayaran</h1>
                <p>Kelola tagihan dan pembayaran Anda.</p>
            </div>
        </header>


        @if(!$booking)

            <section class="dashboard-section">

                <div class="empty-state">

                    <h3>
                        Anda belum memiliki booking yang dapat dibayar.
                    </h3>

                    <p>
                        Gunakan menu Booking Saya di navbar landing page
                        untuk memilih kamar.
                    </p>

                </div>

            </section>


        @elseif($booking->status_booking === 'Menunggu')

            <section class="dashboard-section">

                <div class="tenant-alert tenant-alert-warning">
                    Booking Anda masih menunggu verifikasi Admin.
                </div>

            </section>


        @elseif($booking->status_booking === 'Ditolak')

            <section class="dashboard-section">

                <div class="tenant-alert tenant-alert-danger">
                    Booking Anda ditolak oleh Admin.
                </div>

            </section>


        @else

            {{-- =====================================================
                 TAGIHAN BULANAN
            ====================================================== --}}

            @if(auth()->user()->isPenghuniAktif())

                @php
                    $tagihanBulananAktif = $pembayaran
                        ->where('jenis_pembayaran', 'Bulanan')
                        ->where('status_bayar', '!=', 'Ditolak')
                        ->sortByDesc('periode_bayar')
                        ->first();

                    $periodeBerikutnya = now()->format('Y-m');

                    if ($tagihanBulananAktif?->periode_bayar) {
                        $periodeBerikutnya = \Carbon\Carbon::createFromFormat(
                            'Y-m',
                            $tagihanBulananAktif->periode_bayar
                        )->addMonth()->format('Y-m');
                    }

                    $periodeLabel = \Carbon\Carbon::createFromFormat(
                        'Y-m',
                        $periodeBerikutnya
                    )->translatedFormat('F Y');

                    $sudahAdaTagihanBerikutnya = $pembayaran
                        ->where('jenis_pembayaran', 'Bulanan')
                        ->where('periode_bayar', $periodeBerikutnya)
                        ->where('status_bayar', '!=', 'Ditolak')
                        ->isNotEmpty();
                @endphp


                <section class="dashboard-section">

                    <div class="payment-card">

                        <div class="payment-card-content">

                            <span class="payment-card-label">
                                Tagihan Bulanan Berikutnya
                            </span>

                            <h2>
                                {{ $periodeLabel }}
                            </h2>

                            <p>
                                Tagihan bulanan akan dibuat sesuai periode
                                pembayaran terakhir Anda.
                            </p>

                        </div>


                        @if(!$sudahAdaTagihanBerikutnya)

                            <form
                                method="POST"
                                action="{{ route('penghuni.pembayaran.buat') }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="btn-success"
                                >
                                    Buat Tagihan {{ $periodeLabel }}
                                </button>

                            </form>

                        @else

                            <span class="tenant-alert tenant-alert-info">
                                Tagihan {{ $periodeLabel }} sudah dibuat.
                            </span>

                        @endif

                    </div>

                </section>

            @endif


            {{-- =====================================================
                 DAFTAR PEMBAYARAN
            ====================================================== --}}

            <section class="dashboard-section">

                <div class="section-heading">
                    <div>
                        <h2>Riwayat Pembayaran</h2>

                        <p>
                            Daftar tagihan dan pembayaran Anda.
                        </p>
                    </div>
                </div>


                <div class="table-wrapper">

                    <table class="data-table">

                        <thead>

                            <tr>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($pembayaran as $item)

                                <tr>

                                    <td>

                                        {{ $item->jenis_pembayaran }}

                                        @if($item->status_bayar === 'Ditolak')

                                            <br>

                                            <small>
                                                Pembayaran belum dapat
                                                diverifikasi.

                                                @if($item->alasan_penolakan)
                                                    Alasan:
                                                    {{ $item->alasan_penolakan }}
                                                @endif
                                            </small>

                                        @endif

                                    </td>


                                    <td>

                                        @if($item->periode_bayar)

                                            {{ \Carbon\Carbon::createFromFormat(
                                                'Y-m',
                                                $item->periode_bayar
                                            )->translatedFormat('F Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    <td>
                                        Rp
                                        {{ number_format(
                                            $item->jumlah_bayar,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>


                                    <td>
                                        {{ $item->metode_bayar ?? '-' }}
                                    </td>


                                    <td>

                                        @if($item->status_bayar === 'Valid')

                                            <span class="status-badge status-success">
                                                Valid
                                            </span>

                                        @elseif($item->status_bayar === 'Ditolak')

                                            <span class="status-badge status-danger">
                                                Ditolak
                                            </span>

                                        @else

                                            <span class="status-badge status-warning">
                                                {{ $item->metode_bayar ? 'Menunggu Verifikasi' : 'Menunggu Pembayaran' }}
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        @if(
                                            ($item->status_bayar === 'Pending' && !$item->metode_bayar)
                                            || $item->status_bayar === 'Ditolak'
                                        )

                                            <a
                                                class="btn-primary"
                                                href="{{ route(
                                                    'pembayaran.bayar',
                                                    $item->id_pembayaran
                                                ) }}"
                                            >

                                                @if($item->status_bayar === 'Ditolak')
                                                    Bayar / Upload Lagi
                                                @else
                                                    Bayar Sekarang
                                                @endif

                                            </a>

                                        @elseif($item->status_bayar === 'Valid')

                                            <span class="text-success">
                                                Pembayaran diterima
                                            </span>

                                        @else

                                            <span>
                                                Menunggu validasi
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        style="text-align:center;"
                                    >
                                        Belum ada tagihan.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>

        @endif

    </main>

</div>

</body>

</html>
