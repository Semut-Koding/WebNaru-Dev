<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MarkInactiveUsersOffline extends Command
{
    protected $signature = 'users:mark-offline';
    protected $description = 'Set is_online = false untuk user yang tidak aktif (session habis)';

    public function handle(): int
    {
        $sessionLifetime = (int) config('session.lifetime', 120); // in minutes

        $updated = User::where('is_online', true)
            ->where(function ($query) use ($sessionLifetime) {
                $query->whereNull('last_activity_at')
                    ->orWhere('last_activity_at', '<', now()->subMinutes($sessionLifetime));
            })
            ->update(['is_online' => false]);

        $this->info("Marked {$updated} user(s) as offline.");

        return self::SUCCESS;
    }
}
