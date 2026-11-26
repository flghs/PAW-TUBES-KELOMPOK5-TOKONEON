<?php

return [
    // Format internasional tanpa tanda + dan tanpa 0 di depan, contoh: 6281234567890
    'whatsapp_number' => env('WHATSAPP_NUMBER'),

    // Info rekening toko yang ditampilkan ke pelanggan saat membayar.
    'payment' => [
        'bank_name' => env('PAYMENT_BANK_NAME'),
        'account_number' => env('PAYMENT_ACCOUNT_NUMBER'),
        'account_name' => env('PAYMENT_ACCOUNT_NAME'),
    ],
];
