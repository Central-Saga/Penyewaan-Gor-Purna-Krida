<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun Awal (AdminSeeder)
    |--------------------------------------------------------------------------
    |
    | Kredensial akun admin dan pengelola awal. Nilai dibaca dari variabel
    | lingkungan agar tidak di-hardcode, namun tetap lewat config() supaya
    | aman ketika `php artisan config:cache` dijalankan di produksi.
    |
    */

    'admin_email' => env('ADMIN_EMAIL', 'admin@gorpurnakrida.test'),

    'admin_password' => env('ADMIN_PASSWORD', 'password'),

    'pengelola_email' => env('PENGELOLA_EMAIL', 'pengelola@gorpurnakrida.test'),

    'pengelola_password' => env('PENGELOLA_PASSWORD', 'password'),

];
