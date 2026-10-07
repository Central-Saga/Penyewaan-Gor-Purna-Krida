<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rekening Resmi GOR Purnakrida
    |--------------------------------------------------------------------------
    |
    | Sumber kebenaran tunggal untuk instruksi pembayaran. Ubah di satu tempat
    | ini agar halaman panduan dan halaman pembayaran selalu konsisten.
    |
    */

    'rekening' => [
        'bank' => 'Bank BPD Bali',
        'nomor' => '010 02 02 019283 1',
        'atas_nama' => 'Penerimaan Sewa GOR DISDIKPORA Badung',
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Surat Peminjaman Resmi
    |--------------------------------------------------------------------------
    |
    | Sumber kebenaran tunggal path unduhan template surat resmi (docx).
    | File publik di public/ sehingga dapat diunduh tanpa login.
    |
    */

    'template_surat' => 'templates/template-surat-peminjaman.docx',

];
