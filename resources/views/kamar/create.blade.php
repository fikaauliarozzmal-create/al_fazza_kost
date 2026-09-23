<title>Tambah Kamar | Al Fazza Kost</title>

@vite(['resources/css/app.css', 'resources/css/style.css'])

<div class="dashboard">

    @include('admin.partials.sidebar', ['active' => 'kamar'])

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

        <header class="dashboard-header">
            <div>
                <h1>Tambah Kamar</h1>
                <p>
                    Tambahkan data kamar baru.
                </p>
            </div>
        </header>

        <!-- FORM TAMBAH KAMAR -->
        <section class="dashboard-section">

            <form
                action="{{ route('kamar.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="kamar-form"
            >

                @csrf

                <!-- NOMOR KAMAR -->
                <div class="kamar-form-group">

                    <label for="nomor_kamar">
                        Nomor Kamar
                    </label>

                    <input
                        type="text"
                        id="nomor_kamar"
                        name="nomor_kamar"
                        placeholder="Contoh: A01"
                        value="{{ old('nomor_kamar') }}"
                        required
                    >

                </div>

                <!-- LOKASI -->
                <div class="kamar-form-group">

                    <label for="lokasi_kos">
                        Lokasi Kos
                    </label>

                    <select
                        id="lokasi_kos"
                        name="lokasi_kos"
                        required
                    >

                        <option value="">
                            Pilih lokasi kos
                        </option>

                        <option
                            value="Al Fazza Kost 1"
                            {{ old('lokasi_kos') == 'Al Fazza Kost 1' ? 'selected' : '' }}
                        >
                            Al Fazza Kost 1
                        </option>

                        <option
                            value="Al Fazza Kost 2"
                            {{ old('lokasi_kos') == 'Al Fazza Kost 2' ? 'selected' : '' }}
                        >
                            Al Fazza Kost 2
                        </option>

                    </select>

                </div>

                <!-- TIPE KAMAR -->
                <div class="kamar-form-group">

                    <label for="tipe_kamar">
                        Tipe Kamar
                    </label>

                    <select
                        id="tipe_kamar"
                        name="tipe_kamar"
                        required
                    >

                        <option value="">
                            Pilih lokasi terlebih dahulu
                        </option>

                    </select>

                </div>

                <!-- HARGA -->
                <div class="kamar-form-group">

                    <label for="harga">
                        Harga per Bulan
                    </label>

                    <input
                        type="number"
                        id="harga"
                        name="harga"
                        placeholder="Contoh: 800000"
                        value="{{ old('harga') }}"
                        min="0"
                        required
                    >

                </div>

                <!-- FASILITAS -->
                <div class="kamar-form-group">

                    <label for="fasilitas">
                        Fasilitas
                    </label>

                    <textarea
                        id="fasilitas"
                        name="fasilitas"
                        placeholder="Contoh: Kasur, lemari, WiFi, kamar mandi"
                    >{{ old('fasilitas') }}</textarea>

                </div>

                <!-- FOTO -->
                <div class="kamar-form-group">

                    <label for="foto_kamar">
                        Foto Kamar
                    </label>

                    <input
                        type="file"
                        id="foto_kamar"
                        name="foto_kamar"
                        accept="image/jpeg,image/png,image/jpg"
                    >

                    <small class="form-help">
                        Format JPG, JPEG, atau PNG. Maksimal 2 MB.
                    </small>

                </div>

                <!-- STATUS -->
                <div class="kamar-form-group">

                    <label for="status_kamar">
                        Status Kamar
                    </label>

                    <select
                        id="status_kamar"
                        name="status_kamar"
                        required
                    >

                        <option
                            value="tersedia"
                            {{ old('status_kamar', 'tersedia') == 'tersedia' ? 'selected' : '' }}
                        >
                            Tersedia
                        </option>

                        <option
                            value="terisi"
                            {{ old('status_kamar') == 'terisi' ? 'selected' : '' }}
                        >
                            Terisi
                        </option>

                        <option
                            value="perbaikan"
                            {{ old('status_kamar') == 'perbaikan' ? 'selected' : '' }}
                        >
                            Perbaikan
                        </option>

                    </select>

                </div>

                <!-- TOMBOL -->
                <div class="kamar-form-actions">

                    <button
                        type="submit"
                        class="btn-simpan"
                    >
                        Simpan Kamar
                    </button>

                    <a
                        href="{{ route('kamar.index') }}"
                        class="btn-batal"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const lokasi = document.getElementById('lokasi_kos');
    const tipe = document.getElementById('tipe_kamar');

    if (!lokasi || !tipe) {
        return;
    }

    const tipeLama = @json(old('tipe_kamar'));

    function updateTipeKamar() {

        const lokasiValue = lokasi.value;

        // Reset pilihan tipe kamar
        tipe.innerHTML = '';

        // Belum memilih lokasi
        if (!lokasiValue) {

            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Pilih lokasi terlebih dahulu';

            tipe.appendChild(option);

            return;
        }

        // Al Fazza Kost 1
        if (lokasiValue === 'Al Fazza Kost 1') {

            tipe.innerHTML = `
                <option value="">Pilih tipe kamar</option>
                <option value="kamar_bawah">Kamar Bawah</option>
                <option value="kamar_atas">Kamar Atas</option>
            `;

        }

        // Al Fazza Kost 2
        else if (lokasiValue === 'Al Fazza Kost 2') {

            tipe.innerHTML = `
                <option value="">Pilih tipe kamar</option>
                <option value="1_lantai">Kamar 1 Lantai</option>
            `;

        }

        // Pertahankan pilihan lama jika valid
        if (tipeLama) {
            tipe.value = tipeLama;
        }
    }

    // Jalankan saat lokasi berubah
    lokasi.addEventListener('change', updateTipeKamar);

    // Jalankan saat halaman pertama kali dibuka
    updateTipeKamar();

});
</script>