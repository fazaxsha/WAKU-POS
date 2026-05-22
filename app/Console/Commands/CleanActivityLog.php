<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanActivityLog extends Command
{
    protected $signature = 'activitylog:clean {--days=90 : Hapus log lebih tua dari N hari}';

    protected $description = 'Hapus record activity_log yang lebih tua dari N hari (default: 90)';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);
        $deleted = DB::table('activity_log')->where('created_at', '<', $cutoff)->delete();

        $this->info("Activity log cleanup selesai: {$deleted} record dihapus (lebih tua dari {$days} hari).");

        return self::SUCCESS;
    }
}
