<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Lightweight Safaricom Daraja (M-Pesa) STK Push client.
 *
 * Runs in SIMULATION mode automatically when no consumer key/secret are
 * configured, so the POS checkout flow works out of the box. Provide real
 * Daraja credentials in .env to perform live STK pushes.
 */
class Mpesa
{
    public function simulating(): bool
    {
        $forced = config('mpesa.simulate');
        if ($forced !== null) {
            return (bool) $forced;
        }

        return empty(config('mpesa.key')) || empty(config('mpesa.secret'));
    }

    private function baseUrl(): string
    {
        return config('mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    private function token(): ?string
    {
        $resp = Http::withBasicAuth(config('mpesa.key'), config('mpesa.secret'))
            ->get($this->baseUrl().'/oauth/v1/generate?grant_type=client_credentials');

        return $resp->ok() ? ($resp->json('access_token') ?? null) : null;
    }

    /**
     * Initiate an STK push. Returns ['ok'=>bool, 'checkoutid'=>string, 'message'=>string].
     */
    public function stkPush(string $phone, float $amount, string $reference): array
    {
        $phone = $this->normalisePhone($phone);

        if ($this->simulating()) {
            return [
                'ok' => true,
                'checkoutid' => 'SIM-'.strtoupper(Str::random(12)),
                'message' => 'Simulated STK push sent. Confirm on the customer phone.',
                'simulated' => true,
            ];
        }

        $token = $this->token();
        if (! $token) {
            return ['ok' => false, 'checkoutid' => null, 'message' => 'Could not authenticate with M-Pesa.'];
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $shortcode = config('mpesa.shortcode');
        $password = base64_encode($shortcode.config('mpesa.passkey').$timestamp);

        $resp = Http::withToken($token)->post($this->baseUrl().'/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) ceil($amount),
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => config('mpesa.callback'),
            'AccountReference' => Str::limit($reference, 12, ''),
            'TransactionDesc' => 'PharmacyPOS sale '.$reference,
        ]);

        if ($resp->ok() && $resp->json('ResponseCode') === '0') {
            return [
                'ok' => true,
                'checkoutid' => $resp->json('CheckoutRequestID'),
                'message' => 'STK push sent. Ask the customer to enter their M-Pesa PIN.',
                'simulated' => false,
            ];
        }

        return [
            'ok' => false,
            'checkoutid' => null,
            'message' => $resp->json('errorMessage') ?? 'M-Pesa request failed.',
        ];
    }

    /**
     * Query the status of an STK push. In simulation mode this always succeeds.
     */
    public function status(string $checkoutId): array
    {
        if ($this->simulating() || str_starts_with($checkoutId, 'SIM-')) {
            return [
                'ok' => true,
                'paid' => true,
                'receipt' => 'SIM'.strtoupper(Str::random(8)),
                'message' => 'Payment confirmed (simulated).',
            ];
        }

        $token = $this->token();
        if (! $token) {
            return ['ok' => false, 'paid' => false, 'message' => 'Could not authenticate with M-Pesa.'];
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $shortcode = config('mpesa.shortcode');
        $password = base64_encode($shortcode.config('mpesa.passkey').$timestamp);

        $resp = Http::withToken($token)->post($this->baseUrl().'/mpesa/stkpushquery/v1/query', [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutId,
        ]);

        $paid = $resp->ok() && $resp->json('ResultCode') === '0';

        return [
            'ok' => $resp->ok(),
            'paid' => $paid,
            'receipt' => null,
            'message' => $resp->json('ResultDesc') ?? 'Status unknown.',
        ];
    }

    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (Str::startsWith($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        } elseif (Str::startsWith($phone, '7') || Str::startsWith($phone, '1')) {
            $phone = '254'.$phone;
        }

        return $phone;
    }
}
