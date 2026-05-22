<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QRIS Static String
    |--------------------------------------------------------------------------
    |
    | String QRIS statis dari merchant. Didapat dari aplikasi payment provider
    | (DANA, GoPay, OVO, dll). String ini akan dikonversi ke QRIS dinamis
    | dengan nominal transaksi yang sudah ditentukan saat checkout di POS.
    |
    */

    'static_string' => env(
        'QRIS_STATIC_STRING',
        '00020101021126570011ID.DANA.WWW011893600915335974980802093597498080303UMI51440014ID.CO.QRIS.WWW0215ID10222312431250303UMI5204511153033605802ID5917FAGAMBY PRINTING 6014Kab. Mojokerto610561383630404FB'
    ),

    /*
    |--------------------------------------------------------------------------
    | QRIS Merchant Name Override
    |--------------------------------------------------------------------------
    |
    | Jika diisi, akan menampilkan nama ini di UI POS. Jika kosong, akan
    | diambil dari parsing QRIS string.
    |
    */

    'merchant_name' => env('QRIS_MERCHANT_NAME', ''),

];
