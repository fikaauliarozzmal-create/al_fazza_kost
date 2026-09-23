<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Kamar | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])

</head>

<body>

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'kamar'])


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="dashboard-main">


        <!-- HEADER -->

        <header class="dashboard-header">

            <div>

                <h1>Data Kamar</h1>

                <p>
                    Kelola data kamar Al Fazza Kost.
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


        <!-- =========================
             DATA KAMAR
        ========================== -->

        <section class="dashboard-section">


            <!-- JUDUL -->

            <div class="section-heading">

                <div>

                    <h2>Daftar Kamar</h2>

                    <p>
                        Daftar kamar yang tersedia di Al Fazza Kost.
                    </p>

                </div>


                <!-- TOMBOL TAMBAH -->

                <a href="{{ route('kamar.create') }}" class="btn-tambah">

                    + Tambah Kamar

                </a>

            </div>


            <!-- =========================
                 TABLE
            ========================== -->

            <div class="table-wrapper">

                <table class="kamar-table">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Foto</th>

                            <th>Nomor Kamar</th>

                            <th>Tipe Kamar</th>

                            <th>Lokasi</th>

                            <th>Harga</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($kamar as $item)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    @if ($item->foto_kamar)
                                        <img
                                            src="{{ asset('storage/' . $item->foto_kamar) }}"
                                            alt="Foto Kamar {{ $item->nomor_kamar }}"
                                            class="foto-kamar"
                                        >
                                    @else
                                        <span>Tidak ada foto</span>
                                    @endif
                                </td>


                                <td>
                                    {{ $item->nomor_kamar }}
                                </td>


                                <td>
                                    {{ $item->tipe_kamar }}
                                </td>


                                <td>
                                    {{ $item->lokasi_kos }}
                                </td>


                                <td>
                                    Rp{{ number_format($item->harga, 0, ',', '.') }}
                                </td>


                                <td>

                                    <span class="status-kamar">

                                        {{ $item->status_kamar }}

                                    </span>

                                </td>


                             
                                <td>

    <div class="kamar-action">

        <a
            href="{{ route('kamar.edit', $item->id_kamar) }}"
            class="btn-edit"
        >
            Edit
        </a>

        <form
            action="{{ route('kamar.destroy', $item->id_kamar) }}"
            method="POST"
            style="display: inline;"
            onsubmit="return confirm('Yakin ingin menghapus kamar ini?');"
        >

            @csrf
            @method('DELETE')

            <form
    action="{{ route('kamar.destroy', $item->id_kamar) }}"
    method="POST"
    onsubmit="return confirm('Yakin ingin menghapus kamar ini?')"
>
    @csrf
    @method('DELETE')

    <button
        type="submit"
        class="btn-hapus"
    >
        Hapus
    </button>
</form>

        </form>

    </div>

</td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="empty-table"
                                >
                                    Belum ada data kamar.
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
