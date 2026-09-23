<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\Penghuni;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index()
    {
        $penghuni = $this->penghuniAktif();
        $pengaduan = $penghuni
            ? Pengaduan::where('id_penghuni', $penghuni->id_penghuni)->latest('id_pengaduan')->get()
            : collect();

        return view('penghuni.keluhan', compact('penghuni', 'pengaduan'));
    }

    public function store(Request $request)
    {
        $penghuni = $this->penghuniAktif();
        abort_unless($penghuni, 403, 'Akun ini belum memiliki hunian aktif.');

        $data = $request->validate([
            'kategori' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'isi_pengaduan' => ['required', 'string', 'max:5000'],
            'lampiran_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'lampiran_video' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:20480'],
        ]);

        $foto = $request->file('lampiran_foto')?->store('pengaduan/foto', 'public');
        $video = $request->file('lampiran_video')?->store('pengaduan/video', 'public');

        $pengaduan = Pengaduan::create([
            'id_penghuni' => $penghuni->id_penghuni,
            'tanggal' => now()->toDateString(),
            'kategori' => $data['kategori'],
            'judul' => $data['judul'],
            'isi_pengaduan' => $data['isi_pengaduan'],
            'status' => 'Baru',
            'lampiran_foto' => $foto,
            'lampiran_video' => $video,
        ]);

        app(NotifikasiService::class)->untukAdmin(
            'Keluhan Baru',
            $penghuni->user?->name . ' mengirim keluhan: ' . $pengaduan->judul . '.',
            'keluhan'
        );

        return redirect()->route('penghuni.keluhan.index')->with('success', 'Keluhan berhasil dikirim ke admin.');
    }

    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        abort_unless(in_array($status, [null, 'Baru', 'Diproses', 'Selesai'], true), 404);

        $pengaduan = Pengaduan::with(['penghuni.user', 'penghuni.kamar'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('tanggal')
            ->latest('id_pengaduan')
            ->get();

        return view('admin.keluhan.index', compact('pengaduan', 'status'));
    }

    public function adminShow(Pengaduan $pengaduan)
    {
        $pengaduan->load(['penghuni.user', 'penghuni.kamar']);

        return view('admin.keluhan.show', compact('pengaduan'));
    }

    public function adminUpdate(Request $request, Pengaduan $pengaduan)
    {
        $data = $request->validate([
            'status' => ['required', 'in:Baru,Diproses,Selesai'],
            'tanggapan_admin' => ['nullable', 'string', 'max:5000'],
        ]);

        $statusSebelumnya = $pengaduan->status;
        $pengaduan->update($data);

        if ($statusSebelumnya !== $pengaduan->status && in_array($pengaduan->status, ['Diproses', 'Selesai'], true)) {
            app(NotifikasiService::class)->buat(
                $pengaduan->penghuni->id_user,
                'Keluhan ' . $pengaduan->status,
                'Keluhan kamu "' . $pengaduan->judul . '" sedang ' . ($pengaduan->status === 'Diproses' ? 'ditangani oleh Admin.' : 'selesai ditangani.'),
                'keluhan'
            );
        }

        return redirect()
            ->route('admin.keluhan.show', $pengaduan)
            ->with('success', 'Status dan tanggapan keluhan berhasil diperbarui.');
    }

    private function penghuniAktif(): ?Penghuni
    {
        abort_unless(Auth::user()?->role === 'User', 403);

        return Penghuni::where('id_user', Auth::id())
            ->where('status_penghuni', 'Aktif')
            ->latest('id_penghuni')
            ->first();
    }
}
