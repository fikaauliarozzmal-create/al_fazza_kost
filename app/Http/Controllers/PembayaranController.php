<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Invoice, PaymentSetting, Pembayaran, Penghuni, Refund, User};
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{

    public function cekBookingForm() { return view('pembayaran.cek-booking'); }

    public function cekBooking(Request $request)
    {
        $data = $request->validate(['kode_booking' => ['required', 'integer'], 'no_whatsapp' => ['required', 'string', 'max:20']]);
        $booking = Booking::where('id_booking', $data['kode_booking'])->where('no_whatsapp', $data['no_whatsapp'])->first();
        if (! $booking) return back()->withErrors(['kode_booking' => 'Kode booking atau nomor WhatsApp tidak sesuai.'])->withInput();
        $request->session()->put('booking_check_id', $booking->id_booking);
        return redirect()->route('booking.status', $booking);
    }

    public function statusBooking(Request $request, Booking $booking)
    {
        $this->publicAccess($request, $booking); $booking->load('kamar', 'pembayaran');
        $dp = $booking->pembayaran->where('jenis_pembayaran', 'DP')->sortByDesc('id_pembayaran')->first();
        $sisa = $booking->pembayaran->where('jenis_pembayaran', 'Sisa Sewa Pertama')->sortByDesc('id_pembayaran')->first();
        $sisaPertama = max(0, (float) $booking->kamar->harga - $this->dpUntukBooking($booking));
        $siapAktivasi = $dp?->status_bayar === 'Valid' && $sisa?->status_bayar === 'Valid';
        return view('pembayaran.status-booking', compact('booking', 'dp', 'sisa', 'sisaPertama', 'siapAktivasi'));
    }

    public function bayarAwal(Request $request, $id)
    {
        $pembayaran = Pembayaran::with('booking.kamar')->findOrFail($id); $this->publicAccess($request, $pembayaran->booking); $this->initialPayable($pembayaran);
        $qrisSetting = PaymentSetting::active()->latest('id')->first();
        $bank = config('payment.bank');
        return view('pembayaran.bayar-awal', compact('pembayaran', 'qrisSetting', 'bank'));
    }

    public function prosesBayarAwal(Request $request, $id)
    {
        $pembayaran = Pembayaran::with('booking.kamar')->findOrFail($id); $this->publicAccess($request, $pembayaran->booking); $this->initialPayable($pembayaran);
        $data = $this->validatePayment($request);
        if ($data['metode_bayar'] === 'QRIS' && ! PaymentSetting::active()->exists()) return back()->withErrors(['metode_bayar' => 'QRIS belum tersedia. Silakan gunakan metode pembayaran lain atau hubungi Admin Al Fazza Kost.'])->withInput();
        $pembayaran = DB::transaction(function () use ($pembayaran, $request, $data) {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($pembayaran->id_pembayaran);
            $pembayaran = $this->buatUlangJikaDitolak($pembayaran);
            $file = $this->storeProof($request, $pembayaran->bukti_bayar);
            $pembayaran->update(['metode_bayar' => $data['metode_bayar'], 'tanggal_bayar' => now()->toDateString(), 'aktivitas_dikirim_pada' => now(), 'bukti_bayar' => $file, 'status_bayar' => 'Pending', 'alasan_penolakan' => null]);

            return $pembayaran;
        });
        app(NotifikasiService::class)->untukAdmin('Pembayaran Baru', $pembayaran->booking->nama_pemesan . ' mengirim bukti pembayaran sebesar Rp' . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . '.', 'pembayaran');
        return redirect()->route('booking.status', $pembayaran->booking)->with('success', 'Bukti pembayaran dikirim dan menunggu validasi admin.');
    }

    public function aktivasiForm(Request $request, Booking $booking)
    {
        $this->publicAccess($request, $booking); abort_unless($this->firstRentSettled($booking), 403, 'Sewa pertama belum lunas.');
        abort_if($booking->penghuni()->exists(), 400, 'Data penghuni sudah aktif.'); return view('penghuni.aktivasi', compact('booking'));
    }

    public function aktivasi(Request $request, Booking $booking)
    {
        $this->publicAccess($request, $booking);
        $data = $request->validate(['email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'string', 'min:8', 'confirmed']]);
        DB::transaction(function () use ($booking, $data) {
            $booking = Booking::lockForUpdate()->findOrFail($booking->id_booking);
            abort_unless($this->firstRentSettled($booking), 400, 'Sewa pertama belum lunas.'); abort_if(Penghuni::where('id_booking', $booking->id_booking)->exists(), 400, 'Data penghuni sudah aktif.');
            $user = User::create(['name' => $booking->nama_pemesan, 'no_whatsapp' => $booking->no_whatsapp, 'email' => $data['email'], 'password' => Hash::make($data['password']), 'role' => 'User', 'status_akun' => 'Aktif']);
            $booking->update(['id_user' => $user->id]);
            $penghuni = Penghuni::create(['id_booking' => $booking->id_booking, 'id_user' => $user->id, 'id_kamar' => $booking->id_kamar, 'tanggal_masuk' => now()->toDateString(), 'aktivitas_masuk_pada' => now(), 'status_penghuni' => 'Aktif']);
            Pembayaran::where('id_booking', $booking->id_booking)->where('status_bayar', 'Valid')->lockForUpdate()->get()->each(fn (Pembayaran $pembayaran) => $this->invoice($pembayaran, $penghuni));
            app(NotifikasiService::class)->buat($user->id, 'Akun Berhasil Diaktifkan', 'Akun penghuni kamu sudah aktif dan dapat digunakan.', 'akun');
        });
        return redirect()->route('login')->with('success', 'Akun penghuni berhasil dibuat. Silakan masuk menggunakan email dan password.');
    }

    public function index() { $this->admin(); $pembayaran = Pembayaran::with(['booking.kamar', 'refund'])->orderByDesc('id_pembayaran')->get(); return view('pembayaran.index', compact('pembayaran')); }
    public function show($id) { $this->admin(); $pembayaran = Pembayaran::with(['booking.user', 'booking.kamar', 'refund.admin', 'adminVerifikasi'])->findOrFail($id); return view('pembayaran.show', compact('pembayaran')); }
    public function pembayaranPenghuni()
    {
        abort_unless(auth()->user()?->isPenghuniAktif(), 403);

        $pembayaran = Pembayaran::with('booking.kamar')
            ->whereHas('booking', fn ($q) => $q->where('id_user', Auth::id()))
            ->orderByDesc('id_pembayaran')
            ->get();

        $booking = Booking::where('id_user', Auth::id())->latest('id_booking')->first();

        return view('penghuni.pembayaran', compact('pembayaran', 'booking'));
    }

public function buatPembayaranBulanan(Request $request)
    {
    abort_unless(auth()->user()?->isPenghuniAktif(), 403);

    $pembayaran = DB::transaction(function () {

        $penghuni = Penghuni::where('id_user', Auth::id())
            ->where('status_penghuni', 'Aktif')
            ->latest('id_penghuni')
            ->lockForUpdate()
            ->firstOrFail();

        $kamar = $penghuni->kamar()->firstOrFail();

        /*
         * Cari pembayaran bulanan terakhir yang tidak ditolak.
         */
        $terakhir = Pembayaran::where('id_booking', $penghuni->id_booking)
            ->where('jenis_pembayaran', 'Bulanan')
            ->whereNotNull('periode_bayar')
            ->where('status_bayar', '!=', 'Ditolak')
            ->orderByDesc('periode_bayar')
            ->lockForUpdate()
            ->first();

        /*
         * Jika sudah pernah ada pembayaran bulanan,
         * periode berikutnya adalah bulan setelah periode terakhir.
         *
         * Jika belum ada, gunakan bulan saat ini.
         */
        if ($terakhir?->periode_bayar) {
            $periodeBerikutnya = \Carbon\Carbon::createFromFormat(
                'Y-m',
                $terakhir->periode_bayar
            )->addMonth()->format('Y-m');
        } else {
            $periodeBerikutnya = now()->format('Y-m');
        }

        /*
         * Pastikan periode tersebut belum mempunyai
         * pembayaran aktif.
         */
        $exists = Pembayaran::where('id_booking', $penghuni->id_booking)
            ->where('jenis_pembayaran', 'Bulanan')
            ->where('periode_bayar', $periodeBerikutnya)
            ->where('status_bayar', '!=', 'Ditolak')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'periode_bayar' => 'Tagihan bulan tersebut sudah dibuat.'
            ]);
        }

        return Pembayaran::create([
            'id_booking' => $penghuni->id_booking,
            'jenis_pembayaran' => 'Bulanan',
            'periode_bayar' => $periodeBerikutnya,
            'jumlah_bayar' => $kamar->harga,
            'status_bayar' => 'Pending',
            'aktivitas_dibuat_pada' => now(),
        ]);
    });

    return redirect()
        ->route('pembayaran.bayar', $pembayaran)
        ->with(
            'success',
            'Tagihan bulanan untuk periode ' .
            \Carbon\Carbon::createFromFormat('Y-m', $pembayaran->periode_bayar)
                ->translatedFormat('F Y') .
            ' berhasil dibuat.'
        );
    }
    public function bayar($id)
    {
        abort_unless(auth()->user()?->isPenghuniAktif(), 403);
        $pembayaran = Pembayaran::with('booking.kamar')->findOrFail($id);
        abort_unless($pembayaran->booking->id_user === Auth::id(), 403);
        abort_unless(
    in_array($pembayaran->status_bayar, ['Pending', 'Ditolak'], true),
    400
    );
        if ($pembayaran->jenis_pembayaran === 'Sisa Sewa Pertama') {
            abort_unless(Pembayaran::where('id_booking', $pembayaran->id_booking)->where('jenis_pembayaran', 'DP')->where('status_bayar', 'Valid')->exists(), 403);
        }
        $qrisSetting = PaymentSetting::active()->latest('id')->first();
        $bank = config('payment.bank');
        return view('pembayaran.bayar', compact('pembayaran', 'qrisSetting', 'bank'));
    }
    public function prosesBayar(Request $request, $id)
    {
        abort_unless(auth()->user()?->isPenghuniAktif(), 403);
        $pembayaran = Pembayaran::with('booking.kamar')->findOrFail($id);
        abort_unless($pembayaran->booking->id_user === Auth::id() && $this->readyToSubmit($pembayaran), 403);
        $data = $this->validatePayment($request);
        if ($data['metode_bayar'] === 'QRIS' && ! PaymentSetting::active()->exists()) return back()->withErrors(['metode_bayar' => 'QRIS belum tersedia. Silakan gunakan metode pembayaran lain atau hubungi Admin Al Fazza Kost.'])->withInput();
        $pembayaran = DB::transaction(function () use ($pembayaran, $request, $data) {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($pembayaran->id_pembayaran);
            $pembayaran = $this->buatUlangJikaDitolak($pembayaran);
            $pembayaran->update(['metode_bayar' => $data['metode_bayar'], 'tanggal_bayar' => now()->toDateString(), 'aktivitas_dikirim_pada' => now(), 'bukti_bayar' => $this->storeProof($request, $pembayaran->bukti_bayar), 'status_bayar' => 'Pending', 'alasan_penolakan' => null]);

            return $pembayaran;
        });
        app(NotifikasiService::class)->untukAdmin('Pembayaran Baru', auth()->user()->name . ' mengirim bukti pembayaran sebesar Rp' . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . '.', 'pembayaran');
        return redirect()->route('pembayaran.bayar', $pembayaran)->with('success', 'Pembayaran dikirim dan menunggu validasi admin.');
    }

	public function validasi($id)
    {
    $this->admin();

    $p = Pembayaran::with('booking.kamar')->findOrFail($id);

    if ($p->status_bayar !== 'Pending') {
        return redirect()
            ->route('pembayaran.show', $id)
            ->with('error', 'Pembayaran ini sudah diproses dan tidak dapat divalidasi kembali.');
    }

    if (! $p->metode_bayar || ! $p->tanggal_bayar) {

    return redirect()
        ->route('pembayaran.show', $id)
        ->with('error', 'Pembayaran belum dikirim oleh pemesan. Metode pembayaran dan tanggal pembayaran harus tersedia sebelum divalidasi.');
    }

    if (
    in_array($p->metode_bayar, ['Transfer', 'DANA', 'QRIS'], true)
    && ! $p->bukti_bayar
    ) {

    return redirect()
        ->route('pembayaran.show', $id)
        ->with('error', 'Bukti pembayaran wajib tersedia untuk metode Transfer, DANA, atau QRIS sebelum divalidasi.');
    }

    if ($p->gateway_provider === 'DANA' && $p->gateway_status !== 'SUCCESS') {
        return redirect()
            ->route('pembayaran.show', $id)
            ->with('error', 'Pembayaran DANA belum dikonfirmasi secara resmi oleh DANA.');
    }

    DB::transaction(function () use ($p) {

        $p = Pembayaran::with('booking.kamar')
            ->lockForUpdate()
            ->findOrFail($p->id_pembayaran);

        if ($p->status_bayar !== 'Pending') {
            abort(400, 'Pembayaran sudah diproses.');
        }

        $p->update([
            'status_bayar' => 'Valid',
            'id_admin_verifikasi' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);
	
	// Sinkronkan invoice yang terkait dengan pembayaran.

        if ($p->jenis_pembayaran === 'DP') {
            Pembayaran::firstOrCreate(
                [
                    'id_booking' => $p->id_booking,
                    'jenis_pembayaran' => 'Sisa Sewa Pertama',
                ],
                [
                    'jumlah_bayar' => max(
                        0,
                        (float) $p->booking->kamar->harga - (float) $p->jumlah_bayar
                    ),
                    'status_bayar' => 'Pending',
                    'aktivitas_dibuat_pada' => now(),
                ]
            );
        }

        $penghuni = Penghuni::where(
            'id_booking',
            $p->id_booking
        )->first();

        if ($p->jenis_pembayaran === 'Bulanan' && $penghuni) {
            $this->invoice($p, $penghuni);
        }

        if (
            ! $penghuni &&
            $p->booking->id_user &&
            $this->firstRentSettled($p->booking)
        ) {
            $penghuni = Penghuni::create([
                'id_booking' => $p->booking->id_booking,
                'id_user' => $p->booking->id_user,
                'id_kamar' => $p->booking->id_kamar,
                'tanggal_masuk' => now()->toDateString(),
                'aktivitas_masuk_pada' => now(),
                'status_penghuni' => 'Aktif',
            ]);

            $p->booking->user()->update([
                'status_akun' => 'Aktif',
            ]);

            Pembayaran::where('id_booking', $p->id_booking)
                ->where('status_bayar', 'Valid')
                ->get()
                ->each(
                    fn (Pembayaran $item) =>
                        $this->invoice($item, $penghuni)
                );

            app(NotifikasiService::class)->buat(
                $p->booking->id_user,
                'Penghuni Aktif',
                'Pembayaran sewa pertama telah valid. Status hunian kamu sekarang aktif.',
                'akun'
            );
        }

        if ($p->booking->id_user) {
            app(NotifikasiService::class)->buat(
                $p->booking->id_user,
                'Pembayaran Divalidasi',
                'Pembayaran kamu sebesar Rp' .
                    number_format($p->jumlah_bayar, 0, ',', '.') .
                    ' telah divalidasi oleh Admin.',
                'pembayaran'
            );
        }
    });

    return redirect()
        ->route('pembayaran.show', $id)
        ->with('success', 'Pembayaran berhasil divalidasi.');
    }

    public function tolak(Request $request, $id)
    {
    $this->admin();

    $data = $request->validate([
        'alasan_penolakan' => ['required', 'string', 'max:1000'],
    ]);

    $hasil = DB::transaction(function () use ($id, $data) {
        $p = Pembayaran::with('booking')
            ->lockForUpdate()
            ->findOrFail($id);

        abort_unless(
            $p->status_bayar === 'Pending',
            400,
            'Pembayaran ini sudah diproses.'
        );

        $p->update([
            'status_bayar' => 'Ditolak',
            'alasan_penolakan' => $data['alasan_penolakan'],
            'id_admin_verifikasi' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);

        $refundDibuat = false;

        /*
         * Jika pembayaran sudah tercatat diterima,
         * buat refund secara otomatis.
         *
         * Aturan berlaku untuk SEMUA pembayaran:
         * - DANA   : gateway harus SUCCESS
         * - Cash   : tanggal pembayaran tersedia
         * - Transfer/QRIS : tanggal + bukti tersedia
         */
        if ($this->dapatDirefund($p)) {
            Refund::firstOrCreate(
                [
                    'id_pembayaran' => $p->id_pembayaran,
                ],
                [
                    'status_refund' => 'Refund Belum Diproses',
                    'nominal_refund' => $p->jumlah_bayar,
                ]
            );

            $refundDibuat = true;
        }

        return [
            'pembayaran' => $p,
            'refundDibuat' => $refundDibuat,
        ];
    });

    $p = $hasil['pembayaran'];
    $refundDibuat = $hasil['refundDibuat'];

    if ($p->booking->id_user) {
        $pesan = $refundDibuat
            ? 'Pembayaran Anda ditolak. Karena pembayaran sudah tercatat diterima, refund sebesar Rp'
                . number_format($p->jumlah_bayar, 0, ',', '.')
                . ' telah dicatat dan akan diproses oleh Admin.'
            : 'Pembayaran Anda ditolak. Silakan periksa alasan penolakan dan lakukan pembayaran atau upload bukti kembali jika diperlukan.';

        app(NotifikasiService::class)->buat(
            $p->booking->id_user,
            'Pembayaran Ditolak',
            $pesan,
            'pembayaran'
        );
    }

    return redirect()
        ->route('pembayaran.show', $id)
        ->with(
            'success',
            $refundDibuat
                ? 'Pembayaran ditolak. Refund sebesar Rp'
                    . number_format($p->jumlah_bayar, 0, ',', '.')
                    . ' telah dicatat dan belum diproses.'
                : 'Pembayaran berhasil ditolak.'
        );
        }

    public function selesaiRefund(Request $request, $id)
    {
        $this->admin();
        $data = $request->validate(['catatan_refund' => ['nullable', 'string', 'max:1000']]);
        $refund = DB::transaction(function () use ($id, $data) {
            $p = Pembayaran::with('booking')->lockForUpdate()->findOrFail($id);
            abort_unless($this->dapatDirefund($p), 400, 'Pembayaran ini tidak memenuhi syarat refund.');
            $refund = Refund::where('id_pembayaran', $p->id_pembayaran)->lockForUpdate()->firstOrFail();
            abort_unless($refund->status_refund === 'Refund Belum Diproses', 400, 'Refund sudah selesai diproses.');
            $refund->update(['status_refund' => 'Refund Selesai', 'tanggal_refund' => now()->toDateString(), 'catatan_refund' => $data['catatan_refund'], 'id_admin' => Auth::id()]);
            return $refund;
        });
        $refund->load('pembayaran.booking');
        if ($refund->pembayaran->booking->id_user) app(NotifikasiService::class)->buat($refund->pembayaran->booking->id_user, 'Refund Selesai', 'Refund DP telah selesai diproses secara manual oleh Admin.', 'pembayaran');
        return redirect()->route('pembayaran.show', $id)->with('success', 'Refund telah ditandai selesai. Status pembayaran tetap Ditolak.');
    }

    private function initialPayable(Pembayaran $p): void { abort_unless($p->booking->status_booking === 'Disetujui' && in_array($p->jenis_pembayaran, ['DP', 'Sisa Sewa Pertama'], true) && $this->readyToSubmit($p), 403); if ($p->jenis_pembayaran === 'Sisa Sewa Pertama') abort_unless(Pembayaran::where('id_booking', $p->id_booking)->where('jenis_pembayaran', 'DP')->where('status_bayar', 'Valid')->exists(), 403); }
    private function firstRentSettled(Booking $b): bool { return Pembayaran::where('id_booking', $b->id_booking)->where('jenis_pembayaran', 'DP')->where('status_bayar', 'Valid')->exists() && Pembayaran::where('id_booking', $b->id_booking)->where('jenis_pembayaran', 'Sisa Sewa Pertama')->where('status_bayar', 'Valid')->exists(); }
    private function publicAccess(Request $r, Booking $b): void
    {
        if ($r->user()) {
            abort_unless(
                $r->user()->role === 'User' && (int) $b->id_user === (int) $r->user()->id,
                403
            );

            return;
        }

        abort_unless((int) $r->session()->get('booking_check_id') === (int) $b->id_booking, 403);
    }
    private function readyToSubmit(Pembayaran $p): bool { return ($p->status_bayar === 'Pending' && ! filled($p->metode_bayar) && ! $p->tanggal_bayar) || $p->status_bayar === 'Ditolak'; }
    private function buatUlangJikaDitolak(Pembayaran $p): Pembayaran
    {
        if ($p->status_bayar !== 'Ditolak') return $p;

        return Pembayaran::create([
            'id_booking' => $p->id_booking,
            'jenis_pembayaran' => $p->jenis_pembayaran,
            'periode_bayar' => $p->periode_bayar,
            'jumlah_bayar' => $p->jumlah_bayar,
            'status_bayar' => 'Pending',
            'aktivitas_dibuat_pada' => now(),
        ]);
    }
    private function dapatDirefund(Pembayaran $p): bool
{
    if ($p->status_bayar !== 'Ditolak') {
        return false;
    }

    $metode = $p->metode_bayar;

    /*
     * Semua metode yang sudah benar-benar dibayar
     * dapat dibuatkan refund ketika ditolak.
     */

    // DANA:
    // gateway harus SUCCESS dan bukti pembayaran harus tersedia.
    if ($metode === 'DANA') {
        return $p->gateway_provider === 'DANA'
            && $p->gateway_status === 'SUCCESS'
            && filled($p->bukti_bayar);
    }

    // Cash:
    // dianggap sudah dibayar jika tanggal pembayaran tercatat.
    if ($metode === 'Cash') {
        return filled($p->tanggal_bayar);
    }

    // Transfer / QRIS:
    // harus ada tanggal pembayaran dan bukti pembayaran.
    if (in_array($metode, ['Transfer', 'QRIS'], true)) {
        return filled($p->tanggal_bayar)
            && filled($p->bukti_bayar);
    }

    return false;
    }
    private function validatePayment(Request $r): array { return $r->validate(['metode_bayar' => ['required', 'in:Transfer,QRIS,Cash'], 'bukti_bayar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]); }
    private function storeProof(Request $r, ?string $old): ?string { return $r->hasFile('bukti_bayar') ? $r->file('bukti_bayar')->store('bukti-pembayaran', 'public') : $old; }
    private function dpUntukBooking(Booking $booking): int { return $booking->kamar?->lokasi_kos === 'Al Fazza Kost 1' ? 200000 : 100000; }
    private function invoice(Pembayaran $p, Penghuni $h): void
{
    /*
     * ==========================================================
     * INVOICE SEWA PERTAMA
     * ==========================================================
     *
     * DP dan Sisa Sewa Pertama merupakan satu tagihan.
     * Contoh:
     *
     * Harga kamar       Rp600.000
     * DP                 Rp100.000
     * Sisa               Rp500.000
     * Total terbayar     Rp600.000
     * Status              Lunas
     */
    if (in_array($p->jenis_pembayaran, ['DP', 'Sisa Sewa Pertama'], true)) {

        $invoice = Invoice::where('id_penghuni', $h->id_penghuni)
            ->where('id_booking', $p->id_booking)
            ->where('jenis_invoice', 'Sewa Pertama')
            ->first();

        if (! $invoice) {
            $last = Invoice::lockForUpdate()
                ->orderByDesc('id_invoice')
                ->first();

            $n = $last
                ? (int) preg_replace('/\D/', '', $last->nomor_invoice)
                : 0;

            $invoice = Invoice::create([
                'id_penghuni' => $h->id_penghuni,
                'id_booking' => $p->id_booking,
                'id_pembayaran' => $p->id_pembayaran,
                'jenis_invoice' => 'Sewa Pertama',
                'periode_bayar' => null,
                'nomor_invoice' => 'INV' . str_pad((string) ($n + 1), 3, '0', STR_PAD_LEFT),
                'tanggal_invoice' => now()->toDateString(),
                'total_tagihan' => $p->booking->kamar->harga,
                'total_terbayar' => 0,
                'status_invoice' => 'belum lunas',
                'aktivitas_dibuat_pada' => now(),
            ]);
        }

        /*
         * Hitung seluruh pembayaran sewa pertama yang sudah VALID.
         */
        $totalTerbayar = Pembayaran::where('id_booking', $p->id_booking)
            ->whereIn('jenis_pembayaran', ['DP', 'Sisa Sewa Pertama'])
            ->where('status_bayar', 'Valid')
            ->sum('jumlah_bayar');

        $totalTagihan = (float) $invoice->total_tagihan;

        $invoice->update([
            'id_pembayaran' => $p->id_pembayaran,
            'total_terbayar' => $totalTerbayar,
            'status_invoice' => $totalTerbayar >= $totalTagihan
                ? 'lunas'
                : 'belum lunas',
        ]);

        return;
    }

    /*
     * ==========================================================
     * INVOICE BULANAN
     * ==========================================================
     *
     * Satu periode pembayaran = satu invoice.
     * Contoh:
     *
     * Agustus 2026 → Rp600.000
     * September 2026 → Rp600.000
     */
    if ($p->jenis_pembayaran === 'Bulanan') {

        $invoice = Invoice::where('id_penghuni', $h->id_penghuni)
            ->where('id_booking', $p->id_booking)
            ->where('jenis_invoice', 'Bulanan')
            ->where('periode_bayar', $p->periode_bayar)
            ->first();

        if (! $invoice) {
            $last = Invoice::lockForUpdate()
                ->orderByDesc('id_invoice')
                ->first();

            $n = $last
                ? (int) preg_replace('/\D/', '', $last->nomor_invoice)
                : 0;

            $invoice = Invoice::create([
                'id_penghuni' => $h->id_penghuni,
                'id_booking' => $p->id_booking,
                'id_pembayaran' => $p->id_pembayaran,
                'jenis_invoice' => 'Bulanan',
                'periode_bayar' => $p->periode_bayar,
                'nomor_invoice' => 'INV' . str_pad((string) ($n + 1), 3, '0', STR_PAD_LEFT),
                'tanggal_invoice' => now()->toDateString(),
                'total_tagihan' => $p->jumlah_bayar,
                'total_terbayar' => 0,
                'status_invoice' => 'belum lunas',
                'aktivitas_dibuat_pada' => now(),
            ]);
        }

        /*
         * Untuk invoice bulanan, hanya pembayaran pada
         * invoice/periode tersebut yang dihitung.
         */
        $totalTerbayar = Pembayaran::where('id_pembayaran', $p->id_pembayaran)
            ->where('status_bayar', 'Valid')
            ->sum('jumlah_bayar');

        $totalTagihan = (float) $invoice->total_tagihan;

        $invoice->update([
            'id_pembayaran' => $p->id_pembayaran,
            'total_terbayar' => $totalTerbayar,
            'status_invoice' => $totalTerbayar >= $totalTagihan
                ? 'lunas'
                : 'belum lunas',
        ]);
    }
}	
    private function admin(): void { abort_unless(in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true), 403); }
}
