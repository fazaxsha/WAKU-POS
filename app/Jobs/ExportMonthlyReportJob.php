<?php

namespace App\Jobs;

use App\Models\User;
use App\Exports\TransactionMonthlyExport;
use App\Notifications\ReportExportedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

class ExportMonthlyReportJob implements ShouldQueue
{
    use Queueable;

    public string $month;
    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(string $month, User $user)
    {
        $this->month = $month;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filename = "reports/laporan-{$this->month}-" . time() . ".xlsx";
        Excel::store(new TransactionMonthlyExport($this->month), $filename, 'public');

        $this->user->notify(new ReportExportedNotification(
            'Laporan Bulanan',
            $filename
        ));
    }
}
