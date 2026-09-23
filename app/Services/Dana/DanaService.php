<?php

namespace App\Services\Dana;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/** Official DANA Widget Non-Binding (SNAP) adapter.
 *
 * It deliberately refuses all gateway calls until real credentials and public
 * HTTPS callback URLs are supplied. No URL or payment result is fabricated.
 */
class DanaService
{
    private const PAYMENT_PATH = '/payment-gateway/v1.0/debit/payment-host-to-host.htm';

    public function ready(): bool
{
    foreach ([
        'base_url',
        'client_id',
        'merchant_id',
        'private_key_path',
        'public_key_path',
        'notify_url',
        'channel_id',
        'mcc',
    ] as $key) {
        if (! filled(config("services.dana.{$key}"))) {
            return false;
        }
    }

    return is_readable(config('services.dana.private_key_path'))
        && is_readable(config('services.dana.public_key_path'));
}

    public function createPayment(Pembayaran $payment, string $origin, ?string $clientIp = null): array
    {
        if (! $this->ready()) {
            throw new RuntimeException('Integrasi DANA Sandbox belum dikonfigurasi. Transaksi tidak dibuat.');
        }

        $partnerReference = $payment->gateway_partner_reference ?: 'AFK-DANA-'.$payment->id_pembayaran.'-'.Str::upper(Str::random(12));
        $externalId = $payment->gateway_external_id ?: now('Asia/Jakarta')->format('YmdHis').$payment->id_pembayaran.Str::upper(Str::random(6));
        $expiresAt = now('Asia/Jakarta')->addMinutes((int) config('services.dana.expiry_minutes'));
        $payload = $this->payload($payment, $partnerReference, $expiresAt, $clientIp);
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $timestamp = now('Asia/Jakarta')->format('Y-m-d\\TH:i:sP');

        $payment->update([
            'metode_bayar' => 'DANA',
            'tanggal_bayar' => now()->toDateString(),
            'gateway_provider' => 'DANA',
            'gateway_partner_reference' => $partnerReference,
            'gateway_external_id' => $externalId,
            'gateway_status' => 'INITIATED',
            'gateway_expires_at' => $expiresAt,
        ]);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(8)
                ->withHeaders($this->headers('POST', self::PAYMENT_PATH, $json, $timestamp, $externalId, $origin))
                ->withBody($json, 'application/json')
                ->post(rtrim(config('services.dana.base_url'), '/').self::PAYMENT_PATH);
        } catch (ConnectionException $exception) {
            Log::warning('DANA create payment connection failed.', ['payment_id' => $payment->id_pembayaran]);
            throw new RuntimeException('DANA belum dapat dihubungi. Tidak ada pembayaran yang dikonfirmasi.');
        }

        $response->throw();
        $data = $response->json();
        if (($data['responseCode'] ?? null) !== '2005400' || ! filled($data['webRedirectUrl'] ?? null) || ! filled($data['referenceNo'] ?? null)) {
            Log::warning('DANA rejected payment creation.', ['payment_id' => $payment->id_pembayaran, 'response_code' => $data['responseCode'] ?? null]);
            throw new RuntimeException('DANA tidak menerima permintaan pembayaran. Tidak ada pembayaran yang dikonfirmasi.');
        }

        $payment->update([
            'gateway_reference' => $data['referenceNo'],
            'gateway_redirect_url' => $data['webRedirectUrl'],
            'gateway_status' => 'PENDING',
            'gateway_metadata' => ['response_code' => $data['responseCode']],
        ]);

        return ['redirect_url' => $data['webRedirectUrl']];
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        if (! $this->ready() || ! filled($headers['x-signature'] ?? null) || ! filled($headers['x-timestamp'] ?? null)) return false;

        $string = 'POST:'.config('services.dana.webhook_path').':'.hash('sha256', $rawBody).':'.$headers['x-timestamp'];
        $publicKey = file_get_contents(config('services.dana.public_key_path'));

        return openssl_verify($string, base64_decode($headers['x-signature'], true) ?: '', $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    private function payload(
    Pembayaran $payment,
    string $partnerReference,
    Carbon $expiresAt,
    ?string $clientIp
): array {
    $booking = $payment->booking()->with('kamar')->firstOrFail();

    $amount = number_format(
        (float) $payment->jumlah_bayar,
        2,
        '.',
        ''
    );

    return [
        'partnerReferenceNo' => $partnerReference,

        'merchantId' => config('services.dana.merchant_id'),

        'subMerchantId' => config('services.dana.sub_merchant_id'),

        'amount' => [
            'value' => $amount,
            'currency' => 'IDR',
        ],

        'externalStoreId' => config('services.dana.external_store_id'),

        'validUpTo' => $expiresAt
            ->format('Y-m-d\\TH:i:sP'),

        'urlParams' => [
            [
                'url' => route('dana.return'),
                'type' => 'PAY_RETURN',
                'isDeeplink' => 'N',
            ],
            [
                'url' => config('services.dana.notify_url'),
                'type' => 'NOTIFICATION',
                'isDeeplink' => 'N',
            ],
        ],

        'additionalInfo' => [
            'order' => [
                'orderTitle' => 'Pembayaran Al Fazza Kost',

                'scenario' => 'REDIRECT',

                'buyer' => [
                    'externalUserId' => (string) (
                        $booking->id_user
                        ?? $payment->id_pembayaran
                    ),
                ],
            ],

            'mcc' => config('services.dana.mcc'),

            'envInfo' => [
                'clientIp' => $clientIp,
                'sourcePlatform' => 'IPG',
                'orderTerminalType' => 'WEB',
                'terminalType' => 'WEB',
            ],
        ],
    ];
}

    private function headers(string $method, string $path, string $body, string $timestamp, string $externalId, string $origin): array
    {
        return [
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $this->sign("{$method}:{$path}:".hash('sha256', $body).":{$timestamp}"),
            'X-PARTNER-ID' => config('services.dana.client_id'),
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID' => config('services.dana.channel_id'),
            'ORIGIN' => $origin,
        ];
    }

    private function sign(string $string): string
    {
        $key = file_get_contents(config('services.dana.private_key_path'));
        if (! openssl_sign($string, $signature, $key, OPENSSL_ALGO_SHA256)) throw new RuntimeException('Private key DANA tidak dapat digunakan.');

        return base64_encode($signature);
    }
}
