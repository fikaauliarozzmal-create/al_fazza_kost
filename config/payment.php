<?php

return [
    // DANA routes and integration are retained, but manual payment is active by default.
    'dana_enabled' => env('PAYMENT_DANA_ENABLED', false),

    /* Do not invent bank details. Set these only when official details exist. */
    'bank' => [
        'name' => env('PAYMENT_BANK_NAME'),
        'account_number' => env('PAYMENT_BANK_ACCOUNT_NUMBER'),
        'account_name' => env('PAYMENT_BANK_ACCOUNT_NAME'),
    ],
];
