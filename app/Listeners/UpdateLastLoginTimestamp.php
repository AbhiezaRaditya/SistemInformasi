<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;

class UpdateLastLoginTimestamp
{
    public function handle(Login $event): void
    {
        // Memaksa Carbon menggunakan timezone Asia/Makassar (WITA) saat menyimpan data
        $localTime = Carbon::now('Asia/Makassar');

        $event->user->forceFill([
            'last_login_at' => $localTime,
        ])->saveQuietly();
    }
}