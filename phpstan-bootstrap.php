<?php

declare(strict_types=1);

/*
 * PHPStan 2 tidak lagi mendukung parameter `memoryLimit` di phpstan.neon,
 * sehingga batas memori dinaikkan di sini agar `vendor/bin/phpstan analyse`
 * tidak gagal OOM dengan memory_limit CLI default (128M).
 */
ini_set('memory_limit', '512M');
