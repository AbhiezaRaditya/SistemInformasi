<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Carbon;

class UpdateLastLogoutTimestamp
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        // Memaksa Carbon menggunakan timezone Asia/Makassar (WITA) saat mencatat logout
        $localTime = Carbon::now('Asia/Makassar');

        $event->user->forceFill([
            'last_logout_at' => $localTime,
        ])->saveQuietly();
    }
}