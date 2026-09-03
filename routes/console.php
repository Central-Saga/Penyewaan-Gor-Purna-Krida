<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('peminjaman:release-expired')->daily()->at('00:05');
Schedule::command('peminjaman:selesaikan')->daily()->at('00:15');
