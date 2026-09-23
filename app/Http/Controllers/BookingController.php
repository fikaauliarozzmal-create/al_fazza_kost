<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * ADMIN - DAFTAR BOOKING
     */
    public function index()
    {
        $this->ensureAdmin();

        $booking = Booking::with(['user', 'kamar'])
            ->orderByDesc('id_booking')
            ->get();

        return view('booking.index', compact('booking'));
    }


    /**
     * CALON PENGHUNI - FORM BOOKING
     *
     * Hanya untuk user yang sudah login.
     */
    public function create()
    {
        $kamar = Kamar::where('status_kamar', 'tersedia')
            ->orderBy('nomor_kamar')
            ->get();

        return view('booking.create', compact('kamar'));
    }


/**
 * CALON PENGHUNI - SIMPAN BOOKING
 *
 * Booking selalu terhubung dengan akun yang sedang login.
 */
public function store(Request $request)
{
    $data = $request->validate([
        'id_kamar' => [
            'required',
            'exists:kamar,id_kamar',
        ],

        'jenis_kelamin' => [
            'required',
            'in:Perempuan',
        ],

        'lokasi_kerja' => [
            'required',
            'string',
            'max:100',
        ],
    ]);

    $user = Auth::user();

    try {
        $booking = DB::transaction(function () use ($data, $user) {

            /*
             * Kunci kamar terlebih dahulu.
             *
             * Ini penting supaya dua orang yang memilih
             * kamar yang sama secara bersamaan tidak bisa
             * membuat booking ganda.
             */
            $kamar = Kamar::lockForUpdate()
                ->findOrFail($data['id_kamar']);

            /*
 * Status kamar menjadi sumber utama ketersediaan.
 *
 * Jika kamar masih "tersedia", calon penghuni
 * boleh membuat booking.
 *
 * Jika kamar sudah "terisi" atau "perbaikan",
 * booking tidak boleh dibuat.
 */
if ($kamar->status_kamar !== 'tersedia') {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'id_kamar' => 'Kamar yang dipilih sudah tidak tersedia. Silakan pilih kamar lain.',
    ]);

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'id_kamar' => 'Kamar yang dipilih sudah tidak tersedia. Silakan pilih kamar lain.',
                ]);
            }

            /*
             * Satu pengguna tidak boleh memiliki
             * lebih dari satu booking yang masih berjalan.
             */
            $hasActiveBooking = Booking::where('id_user', $user->id)
                ->whereIn('status_booking', ['Menunggu', 'Disetujui'])
                ->lockForUpdate()
                ->exists();

            if ($hasActiveBooking) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'id_kamar' => 'Anda masih memiliki booking yang sedang diproses. Silakan lihat status booking Anda terlebih dahulu.',
                ]);
            }

            return Booking::create([
                'id_user' => $user->id,
                'id_kamar' => $kamar->id_kamar,
                'no_whatsapp' => $user->no_whatsapp,
                'tanggal_booking' => now()->toDateString(),
                'nama_pemesan' => $user->name,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'lokasi_kerja' => $data['lokasi_kerja'],
                'status_booking' => 'Menunggu',
                'aktivitas_dibuat_pada' => now(),
                'aktivitas_status_pada' => now(),
            ]);
        });
    } catch (\Illuminate\Validation\ValidationException $exception) {
        return back()
            ->withInput()
            ->withErrors($exception->errors());
    }

    /*
     * Ambil data kamar untuk kebutuhan notifikasi Admin.
     */
    $booking->load('kamar');

    app(NotifikasiService::class)->untukAdmin(
        'Booking Baru',
        $booking->nama_pemesan
            . ' mengirim booking baru untuk Kamar '
            . ($booking->kamar->nomor_kamar ?? '-')
            . '.',
        'booking'
    );

    return redirect()
        ->route('booking.create')
        ->with(
            'success',
            'Booking berhasil dikirim. Booking Anda sedang menunggu verifikasi admin.'
        );
}
    /**
     * ADMIN - DETAIL BOOKING
     */
    public function show($id)
    {
        $this->ensureAdmin();

        $booking = Booking::with(['user', 'kamar'])
            ->findOrFail($id);

        return view('booking.show', compact('booking'));
    }


    /**
     * ADMIN - TERIMA BOOKING
     *
     * Booking yang disetujui hanya mengalokasikan kamar.
     * Data akun, penghuni, dan pembayaran dibuat pada tahap berikutnya.
     */
    public function terima($id)
    {
        $this->ensureAdmin();

        DB::transaction(function () use ($id) {

            /*
             * Lock booking supaya tidak bisa diproses
             * dua kali secara bersamaan.
             */
            $booking = Booking::lockForUpdate()
                ->findOrFail($id);

            // Booking harus masih menunggu.
            if ($booking->status_booking !== 'Menunggu') {
                abort(400, 'Booking ini sudah diproses.');
            }

            /*
             * Lock kamar supaya dua booking tidak bisa
             * mengambil kamar yang sama secara bersamaan.
             */
            $kamar = Kamar::lockForUpdate()
                ->findOrFail($booking->id_kamar);

            if ($kamar->status_kamar !== 'tersedia') {
                abort(400, 'Kamar sudah tidak tersedia.');
            }

            $booking->update([
                'status_booking' => 'Disetujui',
                'aktivitas_status_pada' => now(),
            ]);

            $kamar->update([
                'status_kamar' => 'terisi',
            ]);

            // DP adalah bagian dari sewa pertama, bukan biaya tambahan.
            Pembayaran::create([
                'id_booking' => $booking->id_booking,
                'jenis_pembayaran' => 'DP',
                'jumlah_bayar' => $this->dpUntukKamar($kamar),
                'status_bayar' => 'Pending',
                'aktivitas_dibuat_pada' => now(),
            ]);

            if ($booking->id_user) {
                app(NotifikasiService::class)->buat($booking->id_user, 'Booking Disetujui', 'Booking kamu telah disetujui oleh Admin.', 'booking');
            }
        });

        return redirect()
            ->route('booking.show', $id)
            ->with('success', 'Booking berhasil disetujui dan kamar telah dialokasikan.');
    }


    /**
     * ADMIN - TOLAK BOOKING
     */
    public function tolak($id)
    {
        $this->ensureAdmin();

        $booking = Booking::findOrFail($id);

        if ($booking->status_booking !== 'Menunggu') {
            abort(400, 'Booking ini sudah diproses.');
        }

        $booking->update([
            'status_booking' => 'Ditolak',
            'aktivitas_status_pada' => now(),
        ]);

        if ($booking->id_user) {
            app(NotifikasiService::class)->buat($booking->id_user, 'Booking Ditolak', 'Booking kamu ditolak oleh Admin. Silakan cek detail booking.', 'booking');
        }

        return redirect()
            ->route('booking.index')
            ->with(
                'success',
                'Booking berhasil ditolak.'
            );
    }

    /**
     * Membatasi tindakan administrasi booking untuk Admin dan Super Admin.
     */
    private function ensureAdmin(): void
    {
        abort_unless(
            in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true),
            403
        );
    }

    private function dpUntukKamar(Kamar $kamar): int
    {
        return $kamar->lokasi_kos === 'Al Fazza Kost 1' ? 200000 : 100000;
    }
}
