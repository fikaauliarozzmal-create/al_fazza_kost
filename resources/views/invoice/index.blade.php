<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Invoice - Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'invoice'])

    <main class="dashboard-main">

        <header class="dashboard-header">
            <div>
                <h1>Data Invoice</h1>
                <p>Daftar invoice pembayaran penghuni Al Fazza Kost.</p>
            </div>
        </header>

        <section class="dashboard-section">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Invoice</th>
                            <th>Penghuni</th>
                            <th>Kamar</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Total Tagihan</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($invoice as $item)

                            @php
                                $tagihan = (float) $item->total_tagihan;
                                $terbayar = (float) $item->total_terbayar;
                                $sisa = max(0, $tagihan - $terbayar);
                            @endphp

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $item->nomor_invoice }}</strong>
                                </td>

                                <td>
                                    {{ $item->penghuni?->user?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->penghuni?->kamar?->nomor_kamar ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->jenis_invoice ?? '-' }}
                                </td>

                                <td>
                                    @if($item->periode_bayar)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $item->periode_bayar)->translatedFormat('F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    Rp {{ number_format($tagihan, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($terbayar, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($sisa, 0, ',', '.') }}
                                </td>

                                <td>

                                    @if($item->status_invoice === 'lunas')
                                        <span class="status-badge status-success">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="status-badge status-warning">
                                            Belum Lunas
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    <a
                                        href="{{ route('invoice.show', $item->id_invoice) }}"
                                        class="btn-detail"
                                    >
                                        Detail
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="11" style="text-align:center;">
                                    Belum ada invoice.
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
