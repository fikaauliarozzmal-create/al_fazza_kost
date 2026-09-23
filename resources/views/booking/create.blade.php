<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking Kamar | Al Fazza Kost</title>

    @vite(['resources/css/app.css', 'resources/css/style.css'])
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-logo">
            <x-site-logo class="site-logo site-logo--sidebar" />
            <h2>Al Fazza Kost</h2>
            <span>Booking Kamar</span>
        </div>

        <nav class="sidebar-menu">

            <a href="{{ route('landing') }}">
                <x-ui-icon name="dashboard" />
                <span>Home</span>
            </a>

            <a href="{{ route('booking.create') }}" class="active">
                <x-ui-icon name="booking" />
                <span>Booking Kamar</span>
            </a>

        </nav>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

        <!-- HEADER -->
        <header class="dashboard-header">

            <div>
                <h1>Booking Kamar</h1>

                <p>
                    Silakan isi data untuk melakukan booking kamar.
                </p>
            </div>

        </header>


        <!-- FORM BOOKING -->
        <section class="dashboard-section booking-section">

            <div class="booking-header">

                <div class="booking-icon">
                    <x-ui-icon name="bed" />
                </div>

                <div>
                    <h2>Form Booking</h2>

                    <p>
                        Lengkapi data berikut dengan benar untuk mengajukan booking kamar.
                    </p>
                </div>

            </div>


            <!-- SUCCESS MESSAGE -->
            @if (session('success'))

                <div class="booking-alert booking-alert-success">
                    <span>✓</span>

                    <div>
                        {{ session('success') }}
                    </div>
                </div>

            @endif


            <!-- ERROR MESSAGE -->
            @if ($errors->any())

                <div class="booking-alert booking-alert-error">

                    <span>!</span>

                    <div>

                        <strong>Terjadi kesalahan:</strong>

                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>

                </div>

            @endif


            <!-- FORM -->
            <form
                action="{{ route('booking.store') }}"
                method="POST"
                class="booking-form"
            >

                @csrf


                <!-- INFORMASI PEMESAN -->
                <div class="booking-user-card">

                    <div class="booking-user-icon">
                        <x-ui-icon name="profile" />
                    </div>

                    <div>

                        <span class="booking-user-label">
                            Data Pemesan
                        </span>

                        <h3>
                            {{ auth()->user()->name }}
                        </h3>

                        <p>
                            WhatsApp:
                            <strong>
                                {{ auth()->user()->no_whatsapp }}
                            </strong>
                        </p>

                    </div>

                </div>


                <!-- PILIH KAMAR -->
                <div class="booking-form-group">

                    <label for="id_kamar">
                        Pilih Kamar
                    </label>

                    <select
                        name="id_kamar"
                        id="id_kamar"
                        required
                    >

                        <option value="">
                            -- Pilih Kamar --
                        </option>

                        @forelse ($kamar as $item)

                            @if ($item->status_kamar === 'tersedia')

                                <option
                                    value="{{ $item->id_kamar }}"
                                    {{ old('id_kamar') == $item->id_kamar ? 'selected' : '' }}
                                >

                                    Kamar {{ $item->nomor_kamar }}
                                    —
                                    {{ ucwords(str_replace('_', ' ', $item->tipe_kamar)) }}
                                    —
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}

                                </option>

                            @endif

                        @empty

                            <option value="" disabled>
                                Belum ada data kamar.
                            </option>

                        @endforelse

                    </select>

                    <small>
                        Hanya kamar dengan status <strong>TERSEDIA</strong> yang dapat dipilih.
                    </small>

                </div>


                <!-- JENIS KELAMIN -->
                <div class="booking-form-group">

                    <label for="jenis_kelamin">
                        Jenis Kelamin
                    </label>

                    <div class="booking-readonly">

                        <span>
                            <x-ui-icon name="profile" />
                        </span>

                        <input
                            type="text"
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            value="Perempuan"
                            readonly
                        >

                    </div>

                    <small>
                        Al Fazza Kost merupakan kost khusus perempuan.
                    </small>

                </div>


                <!-- LOKASI KERJA -->
                <div class="booking-form-group">

                    <label for="lokasi_kerja">
                        Lokasi Kerja
                    </label>

                    <input
                        type="text"
                        name="lokasi_kerja"
                        id="lokasi_kerja"
                        value="{{ old('lokasi_kerja') }}"
                        placeholder="Contoh: Purbalingga"
                        required
                    >

                </div>


                <!-- INFORMASI -->
                <div class="booking-info">

                    <div class="booking-info-icon">
                        <x-ui-icon name="service" />
                    </div>

                    <div>
                        <strong>Informasi Booking</strong>

                        <p>
                            Setelah booking dikirim, admin akan melakukan
                            verifikasi terlebih dahulu sebelum proses pembayaran.
                        </p>
                    </div>

                </div>


                <!-- BUTTON -->
                <div class="booking-actions">

                    <button
    type="submit"
    class="booking-btn-primary"
>
    <x-ui-icon name="booking" />
    <span>Kirim Booking</span>
</button>

                    <a
                        href="{{ route('landing') }}"
                        class="booking-btn-secondary"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>
