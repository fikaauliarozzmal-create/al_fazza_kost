<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Booking;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    // MENAMPILKAN DATA KAMAR
    public function index()
    {
        $kamar = Kamar::orderBy('id_kamar', 'desc')->get();

        return view('kamar.index', compact('kamar'));
    }

    // FORM TAMBAH KAMAR
    public function create()
    {
        return view('kamar.create');
    }

    // PROSES TAMBAH KAMAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:kamar_bawah,kamar_atas,1_lantai',
            'lokasi_kos' => 'required|in:Al Fazza Kost 1,Al Fazza Kost 2',
            'harga' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,perbaikan',
        ]);
        if (($validated['lokasi_kos'] === 'Al Fazza Kost 1' && ! in_array($validated['tipe_kamar'], ['kamar_bawah', 'kamar_atas'], true)) || ($validated['lokasi_kos'] === 'Al Fazza Kost 2' && $validated['tipe_kamar'] !== '1_lantai')) return back()->withInput()->withErrors(['tipe_kamar' => 'Tipe kamar harus sesuai lokasi kos.']);

        // Upload foto
        if ($request->hasFile('foto_kamar')) {
            $validated['foto_kamar'] = $request->file('foto_kamar')
                ->store('kamar', 'public');
        }

        Kamar::create($validated);

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Data kamar berhasil ditambahkan.');
    }

    // FORM EDIT KAMAR
    public function edit($id)
    {
        $kamar = Kamar::findOrFail($id);

        return view('kamar.edit', compact('kamar'));
    }

    // PROSES UPDATE KAMAR
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $validated = $request->validate([
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:kamar_bawah,kamar_atas,1_lantai',
            'lokasi_kos' => 'required|in:Al Fazza Kost 1,Al Fazza Kost 2',
            'harga' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,perbaikan',
        ]);
        if (($validated['lokasi_kos'] === 'Al Fazza Kost 1' && ! in_array($validated['tipe_kamar'], ['kamar_bawah', 'kamar_atas'], true)) || ($validated['lokasi_kos'] === 'Al Fazza Kost 2' && $validated['tipe_kamar'] !== '1_lantai')) return back()->withInput()->withErrors(['tipe_kamar' => 'Tipe kamar harus sesuai lokasi kos.']);

        // Kamar yang telah dialokasikan tidak boleh dikembalikan ke status
        // tersedia/perbaikan lewat form admin. Statusnya dikendalikan oleh
        // alur booking dan penghuni agar data tetap sinkron.
        $masihDialokasikan = Booking::where('id_kamar', $kamar->id_kamar)
            ->where('status_booking', 'Disetujui')
            ->exists()
            || Penghuni::where('id_kamar', $kamar->id_kamar)
                ->where('status_penghuni', 'Aktif')
                ->exists();

        if ($masihDialokasikan && $validated['status_kamar'] !== 'terisi') {
            return back()
                ->withInput()
                ->withErrors([
                    'status_kamar' => 'Kamar yang masih dialokasikan untuk booking atau penghuni aktif harus berstatus terisi.',
                ]);
        }

        $fotoLama = $kamar->foto_kamar;

        // Simpan foto baru terlebih dahulu. File lama baru dihapus setelah
        // data kamar berhasil diperbarui dan tidak dipakai kamar lain.
        if ($request->hasFile('foto_kamar')) {
            $validated['foto_kamar'] = $request->file('foto_kamar')
                ->store('kamar', 'public');
        }

        $kamar->update($validated);

        if ($request->hasFile('foto_kamar')) {
            $this->deleteFotoIfUnused($fotoLama);
        }

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    // PROSES HAPUS KAMAR
    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);

        // Cek apakah kamar masih digunakan oleh booking
        $adaBooking = Booking::where('id_kamar', $id)->exists();

        if ($adaBooking) {
            return redirect()
                ->route('kamar.index')
                ->with(
                    'error',
                    'Kamar tidak dapat dihapus karena masih memiliki data booking.'
                );
        }

        // Cek apakah kamar masih digunakan oleh penghuni
        $adaPenghuni = Penghuni::where('id_kamar', $id)->exists();

        if ($adaPenghuni) {
            return redirect()
                ->route('kamar.index')
                ->with(
                    'error',
                    'Kamar tidak dapat dihapus karena masih memiliki data penghuni.'
                );
        }

        $fotoKamar = $kamar->foto_kamar;

        // Hapus data kamar
        $kamar->delete();

        $this->deleteFotoIfUnused($fotoKamar);

        return redirect()
            ->route('kamar.index')
            ->with('success', 'Data kamar berhasil dihapus.');
    }

    private function deleteFotoIfUnused(?string $path): void
    {
        if (! filled($path) || Kamar::where('foto_kamar', $path)->exists()) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
