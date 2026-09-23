<?php

use App\Http\Controllers\{AccountProfileController, LandingHeroSlideController, PengaturanWebsiteController, BookingController, DashboardController, DanaPaymentController, InvoiceController, KamarController, LandingController, LaporanController, LoginController, NotifikasiController, PembayaranController, PaymentSettingController, PengaduanController, PenghuniController, PenghuniDashboardController};
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'portal'])->name('login');
    Route::post('/login', [LoginController::class, 'loginPenghuni'])->name('login.process');
    Route::get('/register', [LoginController::class, 'registerForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register.process');
    Route::redirect('/login/admin', '/login');
    Route::redirect('/login/penghuni', '/login');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/v1.0/debit/notify', [DanaPaymentController::class, 'webhook'])
    ->name('dana.webhook');

Route::get('/dana/return', [DanaPaymentController::class, 'returned'])
    ->name('dana.return');

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/ringkas', [NotifikasiController::class, 'ringkas'])->name('notifikasi.ringkas');
    Route::patch('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::patch('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

    Route::middleware('user')->group(function () {
        Route::get('/penghuni/dashboard', [PenghuniDashboardController::class, 'index'])->name('penghuni.dashboard');
        Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/booking-saya/{booking}', [PembayaranController::class, 'statusBooking'])->name('booking.status');
        Route::get('/pembayaran-awal/{id}/bayar', [PembayaranController::class, 'bayarAwal'])->name('pembayaran.awal.bayar');
        Route::post('/pembayaran-awal/{id}/bayar', [PembayaranController::class, 'prosesBayarAwal'])->name('pembayaran.awal.proses');
	Route::post('/pembayaran-awal/{pembayaran}/dana', [DanaPaymentController::class, 'startInitial'])
        ->name('dana.initial.start');
        Route::get('/penghuni/pembayaran', [PembayaranController::class, 'pembayaranPenghuni'])->middleware('penghuni.active')->name('penghuni.pembayaran');
        Route::post('/penghuni/pembayaran', [PembayaranController::class, 'buatPembayaranBulanan'])->middleware('penghuni.active')->name('penghuni.pembayaran.buat');
	Route::post('/pembayaran/{pembayaran}/dana', [DanaPaymentController::class, 'startResident'])
   	 ->middleware('penghuni.active')
   	 ->name('dana.resident.start');
        Route::get('/pembayaran/{id}/bayar', [PembayaranController::class, 'bayar'])->middleware('penghuni.active')->name('pembayaran.bayar');
        Route::post('/pembayaran/{id}/bayar', [PembayaranController::class, 'prosesBayar'])->middleware('penghuni.active')->name('pembayaran.proses');
        Route::get('/penghuni/invoice', [InvoiceController::class, 'penghuniIndex'])->middleware('penghuni.active')->name('penghuni.invoice.index');
        Route::get('/penghuni/invoice/{id}', [InvoiceController::class, 'penghuniShow'])->middleware('penghuni.active')->name('penghuni.invoice.show');
        Route::get('/penghuni/kamar', [PenghuniDashboardController::class, 'kamarSaya'])->middleware('penghuni.active')->name('penghuni.kamar');
        Route::get('/penghuni/keluhan', [PengaduanController::class, 'index'])->middleware('penghuni.active')->name('penghuni.keluhan.index');
        Route::post('/penghuni/keluhan', [PengaduanController::class, 'store'])->middleware('penghuni.active')->name('penghuni.keluhan.store');
        Route::get('/penghuni/profil', [PenghuniDashboardController::class, 'profil'])->middleware('penghuni.active')->name('penghuni.profil');
        Route::put('/penghuni/profil/foto', [AccountProfileController::class, 'updatePenghuniPhoto'])->middleware('penghuni.active')->name('penghuni.profil.photo');
        Route::put('/penghuni/profil/password', [PenghuniDashboardController::class, 'updatePassword'])->middleware('penghuni.active')->name('penghuni.profil.password');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/admin/profil', [AccountProfileController::class, 'adminProfile'])->name('admin.profil');
        Route::put('/admin/profil/foto', [AccountProfileController::class, 'updateAdminPhoto'])->name('admin.profil.photo');
        Route::resource('kamar', KamarController::class)->except(['show']);
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
        Route::post('/booking/{id}/terima', [BookingController::class, 'terima'])->name('booking.terima');
        Route::post('/booking/{id}/tolak', [BookingController::class, 'tolak'])->name('booking.tolak');
        Route::get('/penghuni', [PenghuniController::class, 'index'])->name('penghuni.index');
        Route::post('/penghuni/{id}/keluarkan', [PenghuniController::class, 'keluarkan'])->name('penghuni.keluarkan');
        Route::post('/penghuni/{id}/aktifkan-kembali', [PenghuniController::class, 'aktifkanKembali'])->name('penghuni.aktifkan-kembali');
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
        Route::post('/pembayaran/{id}/validasi', [PembayaranController::class, 'validasi'])->name('pembayaran.validasi');
        Route::post('/pembayaran/{id}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');
        Route::post('/pembayaran/{id}/refund-selesai', [PembayaranController::class, 'selesaiRefund'])->name('pembayaran.refund.selesai');
        Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/admin/keluhan', [PengaduanController::class, 'adminIndex'])->name('admin.keluhan.index');
        Route::get('/admin/keluhan/{pengaduan}', [PengaduanController::class, 'adminShow'])->name('admin.keluhan.show');
        Route::put('/admin/keluhan/{pengaduan}', [PengaduanController::class, 'adminUpdate'])->name('admin.keluhan.update');
        Route::get('/admin/pengaturan-pembayaran', [PaymentSettingController::class, 'index'])->name('admin.payment-settings.index');
        Route::post('/admin/pengaturan-pembayaran/qris', [PaymentSettingController::class, 'store'])->name('admin.payment-settings.store');
        Route::delete('/admin/pengaturan-pembayaran/qris', [PaymentSettingController::class, 'destroy'])->name('admin.payment-settings.destroy');
        Route::get('/pengaturan-website', [PengaturanWebsiteController::class, 'index'])
            ->name('pengaturan-website.index');
        Route::put('/pengaturan-website/logo', [PengaturanWebsiteController::class, 'updateLogo'])
            ->name('pengaturan-website.logo.update');
        Route::put('/pengaturan-website/{pengaturanWebsite}', [PengaturanWebsiteController::class, 'update'])
            ->name('pengaturan-website.update');
        Route::get('/landing-hero', [LandingHeroSlideController::class, 'index'])
            ->name('landing-hero.index');
        Route::put('/landing-hero/{landingHeroSlide}', [LandingHeroSlideController::class, 'update'])
            ->name('landing-hero.update');
        Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    });
});
