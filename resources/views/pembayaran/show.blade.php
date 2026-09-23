<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Pembayaran | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'pembayaran'])


    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

    @if (session('success'))

    <div style="margin-bottom: 20px; padding: 15px; background: #d1fae5; border-radius: 10px;">
        {{ session('success') }}
    </div>

    @endif

    @if (session('error'))
    <div style="margin-bottom: 20px; padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px;">
        <strong>⚠️ Tidak dapat memvalidasi pembayaran</strong>
        <div style="margin-top: 5px;">
            {{ session('error') }}
        </div>
    </div>
   @endif


        <header class="dashboard-header">

            <div>

                <h1>Detail Pembayaran</h1>

                <p>
                    Informasi pembayaran penghuni Al Fazza Kost.
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


        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <h2>Informasi Pembayaran</h2>

                    <p>
                        Detail transaksi pembayaran.
                    </p>

                </div>

            </div>


            <div class="table-wrapper">

                <table class="data-table">

                    <tr>
                        <th>Pemesan</th>
                        <td>
                            {{ $pembayaran->booking->nama_pemesan ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Nomor WhatsApp</th>
                        <td>
                            {{ $pembayaran->booking->no_whatsapp ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jenis Pembayaran</th>
                        <td>{{ $pembayaran->jenis_pembayaran }} {{ $pembayaran->periode_bayar ? '(' . $pembayaran->periode_bayar . ')' : '' }}</td>
                    </tr>

                    <tr>
                        <th>Kamar</th>
                        <td>
                            {{ $pembayaran->booking->kamar->nomor_kamar ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Metode Pembayaran</th>
                        <td>
                            {{ $pembayaran->metode_bayar ?? 'Belum dipilih' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Pembayaran</th>
                        <td>
                            {{ $pembayaran->tanggal_bayar
                                ? $pembayaran->tanggal_bayar->format('d-m-Y')
                                : 'Belum melakukan pembayaran' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jumlah Pembayaran</th>
                        <td>
                            Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            {{ $pembayaran->status_label }}
                        </td>
                    </tr>

                    @if($pembayaran->tanggal_verifikasi)
                        <tr><th>Diverifikasi Oleh</th><td>{{ $pembayaran->adminVerifikasi?->name ?? '-' }}</td></tr>
                        <tr><th>Tanggal Verifikasi</th><td>{{ $pembayaran->tanggal_verifikasi->format('d-m-Y H:i') }}</td></tr>
                    @endif

                    @if($pembayaran->status_bayar === 'Ditolak')
                        <tr><th>Alasan Penolakan</th><td>{{ $pembayaran->alasan_penolakan ?? 'Tidak dicantumkan' }}</td></tr>
                        <tr><th>Keterangan</th><td>Pembayaran tetap Ditolak dan tidak dianggap lunas. Jika memenuhi syarat, pengembalian dicatat terpisah sebagai refund manual.</td></tr>
                    @endif

                    <tr>
                        <th>Bukti Pembayaran</th>
                        <td>
                            @if($pembayaran->bukti_bayar)
                                <button type="button" class="proof-viewer-trigger" data-proof-open>
                                    <span aria-hidden="true">▧</span>
                                    Lihat Bukti Pembayaran
                                </button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                </table>

            </div>

            @if($pembayaran->refund)
                <section class="dashboard-section" style="margin-top: 20px;">
                    <div class="section-heading"><div><h2>Refund</h2><p>Pengembalian uang dilakukan manual di luar sistem. Halaman ini hanya mencatat prosesnya.</p></div></div>
                    <div class="table-wrapper"><table class="data-table">
                        <tr><th>Nominal Refund</th><td>Rp {{ number_format($pembayaran->refund->nominal_refund, 0, ',', '.') }}</td></tr>
                        <tr><th>Status Refund</th><td>{{ $pembayaran->refund->status_refund }}</td></tr>
                        <tr><th>Tanggal Refund</th><td>{{ $pembayaran->refund->tanggal_refund?->format('d-m-Y') ?? 'Belum diproses' }}</td></tr>
                        <tr><th>Diproses Oleh</th><td>{{ $pembayaran->refund->admin?->name ?? '-' }}</td></tr>
                        <tr><th>Catatan Refund</th><td>{{ $pembayaran->refund->catatan_refund ?? '-' }}</td></tr>
                    </table></div>

                    @if($pembayaran->refund->status_refund === 'Refund Belum Diproses')
                        <form action="{{ route('pembayaran.refund.selesai', $pembayaran->id_pembayaran) }}" method="POST" style="margin-top: 20px;" onsubmit="return confirm('Pastikan uang sudah dikembalikan secara manual. Tandai refund ini sebagai selesai?');">
                            @csrf
                            <label for="catatan_refund">Catatan refund (opsional)</label>
                            <textarea id="catatan_refund" name="catatan_refund" maxlength="1000" rows="3" placeholder="Contoh: Dikembalikan melalui transfer BCA."></textarea>
                            <button type="submit" class="btn-success">✓ Tandai Refund Selesai</button>
                        </form>
                    @endif
                </section>
            @endif


            <div style="margin-top: 20px;">

                <x-back-button href="{{ route('pembayaran.index') }}" aria-label="Kembali ke daftar pembayaran" />

            </div>

    @if ($pembayaran->status_bayar === 'Pending')

    <div style="margin-top: 20px; display: flex; gap: 10px; align-items: flex-start; flex-wrap: wrap;">

        <form action="{{ route('pembayaran.validasi', $pembayaran->id_pembayaran) }}"
              method="POST">

            @csrf

            <button type="submit" class="btn-success">
                ✓ Validasi Pembayaran
            </button>

        </form>

        <form action="{{ route('pembayaran.tolak', $pembayaran->id_pembayaran) }}"
              method="POST"
              style="display: flex; flex-direction: column; gap: 8px;">

            @csrf

            <label for="alasan_penolakan">
                Alasan penolakan
            </label>

            <textarea
                id="alasan_penolakan"
                name="alasan_penolakan"
                required
                maxlength="1000"
                rows="3"
                placeholder="Contoh: Bukti pembayaran tidak sesuai nominal."
            ></textarea>

            <button type="submit" class="btn-danger">
                ✕ Tolak Bukti Pembayaran
            </button>

        </form>

    </div>

@endif
       

        </section>

    </main>

</div>

@if($pembayaran->bukti_bayar)
    <div class="proof-viewer" data-proof-viewer aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="proof-viewer-title">
        <div class="proof-viewer-backdrop" data-proof-close></div>
        <section class="proof-viewer-panel" tabindex="-1">
            <header class="proof-viewer-header">
                <div>
                    <span class="proof-viewer-kicker">Al Fazza Kost</span>
                    <h2 id="proof-viewer-title">Bukti Pembayaran</h2>
                    <p>{{ $pembayaran->jenis_pembayaran }}{{ $pembayaran->periode_bayar ? ' · ' . $pembayaran->periode_bayar : '' }}</p>
                </div>
                <button type="button" class="proof-viewer-close" data-proof-close aria-label="Tutup viewer bukti pembayaran">
                    <span class="proof-viewer-close-icon" aria-hidden="true">×</span>
                    <span>Tutup</span>
                </button>
            </header>

            <div class="proof-viewer-preview">
                <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti pembayaran {{ $pembayaran->jenis_pembayaran }}" class="proof-viewer-image">
            </div>

            <footer class="proof-viewer-footer">
                <div><span>Tanggal Upload</span><strong>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d M Y') : '-' }}</strong></div>
                <div><span>Metode</span><strong>{{ $pembayaran->metode_bayar ?? '-' }}</strong></div>
                <div><span>Jumlah</span><strong>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></div>
                <div><span>Status</span><strong>{{ $pembayaran->status_label }}</strong></div>
            </footer>
        </section>
    </div>

    <script>
        (() => {
            const viewer = document.querySelector('[data-proof-viewer]');
            const panel = viewer?.querySelector('.proof-viewer-panel');
            const opener = document.querySelector('[data-proof-open]');
            let closeTimer;
            const finishClose = () => {
                window.clearTimeout(closeTimer);
                viewer?.classList.remove('is-open', 'is-closing');
                viewer?.setAttribute('aria-hidden', 'true');
                opener?.focus();
            };
            const closeViewer = () => {
                if (!viewer || !viewer.classList.contains('is-open')) return;
                viewer.classList.add('is-closing');
                viewer.addEventListener('animationend', finishClose, { once: true });
                closeTimer = window.setTimeout(finishClose, 300);
            };
            opener?.addEventListener('click', () => {
                window.clearTimeout(closeTimer);
                viewer.classList.remove('is-closing');
                viewer.classList.add('is-open');
                viewer.setAttribute('aria-hidden', 'false');
                panel?.focus();
            });
            viewer?.querySelectorAll('[data-proof-close]').forEach((button) => button.addEventListener('click', closeViewer));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && viewer?.classList.contains('is-open')) closeViewer();
            });
        })();
    </script>
@endif

</body>

</html>
