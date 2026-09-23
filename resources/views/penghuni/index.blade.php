<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penghuni | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])

</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'penghuni'])


    <!-- MAIN CONTENT -->

    <main class="dashboard-main">

        <header class="dashboard-header">

            <div>

                <h1>Data Penghuni</h1>

                <p>
                    Kelola data penghuni Al Fazza Kost.
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

        <x-flash-toast />


        <!-- DATA PENGHUNI -->

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <h2>Daftar Penghuni</h2>

                    <p>
                        Data penghuni yang sedang menempati kamar.
                    </p>

                </div>

            </div>


            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Nama Penghuni</th>
                            <th>Username</th>
                            <th>Kamar</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($penghuni as $item)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>
                                    <strong>
                                        {{ $item->user->name ?? '-' }}
                                    </strong>
                                </td>


                                <td>
                                    {{ $item->user->username ?? '-' }}
                                </td>


                                <td>
                                    {{ $item->kamar->nomor_kamar ?? '-' }}
                                </td>


                                <td>

                                    {{ $item->tanggal_masuk
                                        ? $item->tanggal_masuk->format('d-m-Y')
                                        : '-' }}

                                </td>


                                <td>

                                    {{ $item->taanggal_keluar
                                        ? $item->taanggal_keluar->format('d-m-Y')
                                        : '-' }}

                                </td>


                                <td>

                                    <span class="status-penghuni">

                                        {{ $item->status_penghuni }}

                                    </span>

                                </td>

                                <td>
    @if ($item->status_penghuni === 'Aktif')
        <form
            action="{{ route('penghuni.keluarkan', $item->id_penghuni) }}"
            method="POST"
            onsubmit="return confirm('Yakin ingin mengeluarkan penghuni ini dari kost? Akun penghuni juga akan dinonaktifkan.')"
        >
            @csrf

            <button type="submit" class="btn-keluarkan-penghuni">
                Keluarkan
            </button>
        </form>
    @elseif ($item->status_penghuni === 'Keluar')
        <form
            action="{{ route('penghuni.aktifkan-kembali', $item->id_penghuni) }}"
            method="POST"
            onsubmit="return confirm('Aktifkan kembali penghuni ini? Status penghuni akan dikembalikan menjadi Aktif.')"
        >
            @csrf

            <button type="submit" class="btn-aktifkan-kembali">
                Aktifkan Kembali
            </button>
        </form>
    @else
        <span class="text-muted">-</span>
    @endif
</td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="empty-table"
                                >
                                    Belum ada data penghuni.
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
