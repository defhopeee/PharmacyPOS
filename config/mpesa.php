<?php

return [
    /*
    | Safaricom Daraja (M-Pesa) credentials. Leave the key/secret blank to run
    | in SIMULATION mode — the STK push is mocked so the POS flow works end to
    | end without live credentials. Fill these in (sandbox or production) to do
    | real STK pushes.
    */
    'env' => env('MPESA_ENV', 'sandbox'), // sandbox | production
    'key' => env('MPESA_CONSUMER_KEY', ''),
    'secret' => env('MPESA_CONSUMER_SECRET', ''),
    'shortcode' => env('MPESA_SHORTCODE', '174379'),
    'passkey' => env('MPESA_PASSKEY', ''),
    'callback' => env('MPESA_CALLBACK_URL', ''),

    'simulate' => env('MPESA_SIMULATE', null), // force on/off; null = auto-detect
];
