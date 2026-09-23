<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $this->admin();
        $paymentSetting = PaymentSetting::active()->latest('id')->first();

        return view('payment-settings.index', compact('paymentSetting'));
    }

    public function store(Request $request)
    {
        $this->admin();
        $request->validate(['qris' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);

        $current = PaymentSetting::active()->latest('id')->first();
        $oldPath = $current?->qris_path;
        $newPath = $request->file('qris')->store('qris', 'public');

        try {
            if ($current) {
                $current->update(['qris_path' => $newPath, 'status' => 'Aktif']);
                $paymentSetting = $current;
            } else {
                $paymentSetting = PaymentSetting::create(['qris_path' => $newPath, 'status' => 'Aktif']);
            }
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPath);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) Storage::disk('public')->delete($oldPath);

        return redirect()->route('admin.payment-settings.index')->with('success', 'QRIS berhasil diperbarui.');
    }

    public function destroy()
    {
        $this->admin();
        $paymentSetting = PaymentSetting::active()->latest('id')->first();
        if (! $paymentSetting) return redirect()->route('admin.payment-settings.index')->with('error', 'QRIS belum tersedia.');

        $path = $paymentSetting->qris_path;
        $paymentSetting->delete();
        if ($path) Storage::disk('public')->delete($path);

        return redirect()->route('admin.payment-settings.index')->with('success', 'QRIS berhasil dihapus.');
    }

    private function admin(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['Admin', 'Super Admin'], true), 403);
    }
}
