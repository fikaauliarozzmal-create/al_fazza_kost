@vite(['resources/css/app.css', 'resources/css/style.css'])
<div class="dashboard">@include('admin.partials.sidebar', ['active' => 'kamar'])
<main class="dashboard-main"><header class="dashboard-header"><div><h1>Edit Kamar</h1><p>Perbarui data kamar Al Fazza Kost.</p></div></header>
<section class="dashboard-section">@if($errors->any())<div class="login-error">{{ $errors->first() }}</div>@endif
<form class="kamar-form" action="{{ route('kamar.update', $kamar->id_kamar) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
<div class="kamar-form-group"><label>Nomor Kamar</label><input name="nomor_kamar" value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" required></div>
<div class="kamar-form-group"><label for="lokasi_kos">Lokasi Kos</label><select id="lokasi_kos" name="lokasi_kos" required><option value="Al Fazza Kost 1" @selected(old('lokasi_kos', $kamar->lokasi_kos)==='Al Fazza Kost 1')>Al Fazza Kost 1</option><option value="Al Fazza Kost 2" @selected(old('lokasi_kos', $kamar->lokasi_kos)==='Al Fazza Kost 2')>Al Fazza Kost 2</option></select></div>
<div class="kamar-form-group"><label for="tipe_kamar">Tipe Kamar</label><select id="tipe_kamar" name="tipe_kamar" required></select></div>
<div class="kamar-form-group"><label>Harga per Bulan</label><input type="number" name="harga" min="0" value="{{ old('harga', $kamar->harga) }}" required></div>
<div class="kamar-form-group"><label>Fasilitas</label><textarea name="fasilitas">{{ old('fasilitas', $kamar->fasilitas) }}</textarea></div>
<div class="kamar-form-group"><label>Foto Kamar</label>@if($kamar->foto_kamar)<img src="{{ asset('storage/'.$kamar->foto_kamar) }}" alt="Foto kamar" style="width:150px;height:100px;object-fit:cover">@endif<input type="file" name="foto_kamar" accept="image/jpeg,image/png,image/jpg"></div>
<div class="kamar-form-group"><label>Status</label><select name="status_kamar">@foreach(['tersedia'=>'Tersedia','terisi'=>'Terisi','perbaikan'=>'Perbaikan'] as $value=>$label)<option value="{{ $value }}" @selected(old('status_kamar', $kamar->status_kamar)===$value)>{{ $label }}</option>@endforeach</select></div>
<div class="kamar-form-actions"><button class="btn-simpan">Simpan Perubahan</button><a href="{{ route('kamar.index') }}" class="btn-batal">Batal</a></div></form></section></main></div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const lokasi = document.getElementById('lokasi_kos');
    const tipe = document.getElementById('tipe_kamar');

    let selected = @json(old('tipe_kamar', $kamar->tipe_kamar));

    const options = {
        'Al Fazza Kost 1': [
            ['kamar_bawah', 'Kamar Bawah'],
            ['kamar_atas', 'Kamar Atas']
        ],

        'Al Fazza Kost 2': [
            ['1_lantai', 'Kamar 1 Lantai']
        ]
    };

    function refreshTipeKamar() {

        tipe.innerHTML = '<option value="">Pilih tipe kamar</option>';

        const daftarTipe = options[lokasi.value] || [];

        daftarTipe.forEach(function (item) {

            const value = item[0];
            const label = item[1];

            const option = new Option(label, value);

            if (value === selected) {
                option.selected = true;
            }

            tipe.add(option);
        });
    }

    lokasi.addEventListener('change', function () {

        // Reset tipe kamar ketika lokasi berubah
        selected = '';

        refreshTipeKamar();
    });

    // Tampilkan tipe kamar sesuai lokasi saat halaman pertama dibuka
    refreshTipeKamar();

});
</script>
