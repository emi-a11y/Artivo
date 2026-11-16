<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelStaleOrders extends Command
{
    protected $signature = 'orders:cancel-stale {--days=3 : Cancel unpaid orders older than this many days}';

    protected $description = 'Cancel unpaid orders that were never paid (orders under review are left alone)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        $count = Order::where('status', 'Pending')
            ->whereNull('payment_submitted_at')
            ->where('created_at', '<', now()->subDays($days))
            ->update(['status' => 'Cancelled']);

        $this->info("{$count} unpaid order(s) older than {$days} day(s) were cancelled.");

        return self::SUCCESS;
    }
}
