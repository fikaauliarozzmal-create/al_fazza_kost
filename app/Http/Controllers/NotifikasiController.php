<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $notifikasi = $this->milikUser($request)->latest('id_notifikasi')->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function ringkas(Request $request)
    {
        $query = $this->milikUser($request);

        return response()->json([
            'unread_count' => (clone $query)->whereNull('dibaca_pada')->count(),
            'notifications' => $query->latest('id_notifikasi')->limit(8)->get()->map(fn (Notifikasi $item) => [
                'id' => $item->id_notifikasi,
                'judul' => $item->judul,
                'pesan' => $item->pesan,
                'tipe' => $item->tipe,
                'dibaca' => $item->dibaca_pada !== null,
                'waktu' => $item->created_at?->locale('id')->diffForHumans(),
            ]),
        ]);
    }

    public function baca(Request $request, Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->id_user === $request->user()->id, 403);
        $notifikasi->update(['dibaca_pada' => now()]);

        return response()->noContent();
    }

    public function bacaSemua(Request $request)
    {
        $this->milikUser($request)->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);

        return response()->noContent();
    }

    private function milikUser(Request $request)
    {
        return Notifikasi::where('id_user', $request->user()->id);
    }
}
