<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function penghuniIndex()
    {
        abort_unless(Auth::user()?->isPenghuniAktif(), 403);

        $invoice = Invoice::with([
            'penghuni.user',
            'penghuni.kamar',
            'penghuni.booking',
            'pembayaran',
        ])
            ->whereHas('penghuni', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->orderByDesc('id_invoice')
            ->get();

        return view('penghuni.invoice.index', compact('invoice'));
    }

    public function penghuniShow($id)
    {
        abort_unless(Auth::user()?->isPenghuniAktif(), 403);

        // Filter kepemilikan di query, sehingga invoice pengguna lain tampil
        // sebagai 404 dan tidak dapat dibuka dengan mengganti ID di URL.
        $invoice = Invoice::with([
            'penghuni.user',
            'penghuni.kamar',
            'penghuni.booking',
            'pembayaran',
        ])
            ->where('id_invoice', $id)
            ->whereHas('penghuni', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->firstOrFail();

        return view('penghuni.invoice.show', compact('invoice'));
    }

    public function index()
    {
        $this->authorizeAdmin();

        $invoice = Invoice::with([
            'penghuni.user',
            'penghuni.kamar',
            'penghuni.booking',
            'pembayaran',
        ])
            ->orderByDesc('id_invoice')
            ->get();

        return view('invoice.index', compact('invoice'));
    }

    public function show($id)
    {
        $this->authorizeAdmin();

        $invoice = Invoice::with([
            'penghuni.user',
            'penghuni.kamar',
            'penghuni.booking',
            'pembayaran',
        ])->findOrFail($id);

        return view('invoice.show', compact('invoice'));
    }

    private function authorizeAdmin(): void
    {
        abort_unless(
            in_array(Auth::user()->role, ['Admin', 'Super Admin'], true),
            403
        );
    }
}
