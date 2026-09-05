<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SiPintu API Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi koneksi ke SiPintu Identity & API Gateway.
    | Digunakan untuk mengambil data SIJUNA (Siswa & Guru) via
    | Server-to-Server Header Authentication.
    |
    */

    'api_url' => env('SIPINTU_API_URL', 'http://sipintu.smkn1bangsri.sch.id'),

    'client_id' => env('SIPINTU_CLIENT_ID', 'app_1p03mtss7tbl'),

    'client_secret' => env('SIPINTU_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Timeout (seconds) & Cache TTL (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => env('SIPINTU_TIMEOUT', 60),

    'connect_timeout' => env('SIPINTU_CONNECT_TIMEOUT', 10),

    'cache_ttl' => env('SIPINTU_CACHE_TTL', 1800), // 30 minutes cache

];
