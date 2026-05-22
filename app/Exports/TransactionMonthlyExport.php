<?php
// app/Exports/TransactionMonthlyExport.php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class TransactionMonthlyExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function __construct(private string $month) {}

    public function collection(): Collection
    {
        [$year, $m] = explode('-', $this->month);

        return Transaction::with(['cashier', 'items'])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $m)
            ->latest('transaction_date')
            ->get()
            ->map(fn($t) => [
                $t->invoice_no,
                $t->transaction_date->format('d/m/Y H:i'),
                $t->cashier->name ?? '-',
                $t->items->count(),
                $t->items->sum('qty'),
                number_format($t->total_amount + $t->discount, 0, ',', '.'),
                number_format($t->discount, 0, ',', '.'),
                number_format($t->total_amount, 0, ',', '.'),
                strtoupper($t->payment_method),
            ]);
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal & Waktu',
            'Kasir',
            'Jml Item',
            'Total Qty',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Total Bayar (Rp)',
            'Metode Bayar',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1C1917']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 18, 'C' => 18,
            'D' => 10, 'E' => 10, 'F' => 18,
            'G' => 14, 'H' => 18, 'I' => 14,
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->month;
    }
}