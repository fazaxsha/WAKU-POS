<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ReportExportedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportDailyReportJob implements ShouldQueue
{
    use Queueable;

    public string $date;
    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(string $date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start = Carbon::parse($this->date)->startOfDay();
        $end   = Carbon::parse($this->date)->endOfDay();

        $transactions = Transaction::with(['items.product', 'cashier'])
            ->whereBetween('transaction_date', [$start, $end])
            ->latest('transaction_date')
            ->get();

        $summary = [
            'revenue'    => $transactions->sum('total_amount'),
            'count'      => $transactions->count(),
            'items_sold' => $transactions->flatMap->items->sum('qty'),
        ];

        $date = $this->date;
        $pdf = Pdf::loadView('reports.pdf.daily', compact('transactions', 'summary', 'date'))
            ->setPaper('a4', 'portrait');

        $filename = "reports/laporan-harian-{$this->date}-" . time() . ".pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $this->user->notify(new ReportExportedNotification(
            'Laporan Harian',
            $filename
        ));
    }
}
