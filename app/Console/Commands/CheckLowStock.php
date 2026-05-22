<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa produk yang stoknya menipis dan kirim notifikasi ke admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockProducts = Product::whereColumn('stock_qty', '<=', 'stock_min')
            ->where('is_active', true)
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('Tidak ada produk dengan stok menipis.');
            return 0;
        }

        // Cari admin/owner untuk dikirimkan notifikasi
        // Misal, kirim ke user dengan ID 1 atau user yang memiliki role admin
        $admins = User::role(['owner', 'admin'])->get();

        if ($admins->isEmpty()) {
            $this->error('Tidak ada admin/owner yang dapat menerima notifikasi.');
            return 1;
        }

        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($lowStockProducts));
        }

        $this->info("Berhasil mengirim notifikasi stok menipis untuk {$lowStockProducts->count()} produk ke {$admins->count()} admin.");
        return 0;
    }
}
