<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Services\Qris\QrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products', 'categories'));
    }

    public function store(StorePosRequest $request): RedirectResponse
    {
        try {
            $transaction = DB::transaction(function () use ($request) {
                // ── 1. Load semua produk sekaligus dengan Pessimistic Locking ──────
                $productIds = collect($request->items)->pluck('id');
                $products   = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                // ── 2. Cek stok & hitung total ──────────────────────────
                $itemRows       = [];
                $subtotalBefore = 0;

                foreach ($request->items as $item) {
                    $product = $products->get($item['id']);

                    if (!$product) {
                        throw new \RuntimeException('Produk tidak ditemukan. Silakan refresh halaman.');
                    }

                    $qty = (float) $item['qty'];

                    if ($product->stock_qty < $qty) {
                        throw new \RuntimeException("Stok \"{$product->name}\" tidak mencukupi. Tersisa: {$product->stock_qty}");
                    }

                    $isResellerMode = $request->boolean('is_reseller_mode');
                    $activePrice = $product->sell_price;

                    if ($product->wholesale_price > 0 && ($isResellerMode || ($product->wholesale_min_qty > 0 && $qty >= $product->wholesale_min_qty))) {
                        $activePrice = $product->wholesale_price;
                    }

                    $itemDiscount = (float) ($item['discount'] ?? 0);
                    $subtotal     = ($activePrice * $qty) - $itemDiscount;
                    $subtotalBefore += $subtotal;

                    $itemRows[] = [
                        'product_id' => $product->id,
                        'qty'        => $qty,
                        'unit_price' => $activePrice,
                        'buy_price'  => $product->buy_price,  // snapshot harga beli untuk kalkulasi HPP
                        'subtotal'   => $subtotal,
                    ];
                }

                $globalDiscount = (float) $request->input('discount', 0);
                $grandTotal     = max(0, $subtotalBefore - $globalDiscount);

                // ── 3a. Buat record transaksi
                $trx = Transaction::create([
                    'user_id'          => auth()->id(),
                    'invoice_no'       => Transaction::generateInvoice(),
                    'total_amount'     => $grandTotal,
                    'discount'         => $globalDiscount,
                    'paid_amount'      => (float) $request->paid_amount,
                    'payment_method'   => $request->payment_method,
                    'notes'            => $request->notes,
                    'transaction_date' => now(),
                ]);

                // 3b. Buat transaction items (dengan buy_price snapshot)
                foreach ($itemRows as $row) {
                    $trx->items()->create($row);
                }

                // 3c. Kurangi stok & catat stock movement
                foreach ($itemRows as $row) {
                    $prod      = $products->get($row['product_id']);
                    $qtyBefore = $prod->stock_qty;

                    DB::statement(
                        'UPDATE products SET stock_qty = stock_qty - ?, updated_at = ? WHERE id = ?',
                        [$row['qty'], now(), $row['product_id']]
                    );

                    StockMovement::record(
                        productId:     $row['product_id'],
                        userId:        auth()->id(),
                        type:          'sale',
                        qtyBefore:     $qtyBefore,
                        qtyChange:     -$row['qty'],
                        referenceType: Transaction::class,
                        referenceId:   $trx->id,
                        notes:         "Penjualan #{$trx->invoice_no}",
                    );
                }

                return $trx;
            });

        } catch (\RuntimeException $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('POS transaction failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan transaksi. Silakan coba lagi.');
        }

        return redirect()
            ->route('pos.receipt', $transaction->id)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['items.product', 'cashier']);
        return view('pos.receipt', compact('transaction'));
    }

    public function rawReceipt(Transaction $transaction): JsonResponse
    {
        $transaction->load(['items.product', 'cashier']);
        
        $esc = "\x1B"; // ESC byte
        $gs = "\x1D";  // GS byte

        // Initialize printer
        $data = $esc . "@";
        
        // Center alignment
        $data .= $esc . "a" . "\x01";
        
        // Store Name
        $data .= $esc . "!" . "\x38" . "WAKU STORE\n" . $esc . "!" . "\x00";
        $data .= "Alamat Toko Anda\n";
        $data .= "Telp: 08123456789\n";
        $data .= str_repeat("-", 32) . "\n";
        
        // Left alignment
        $data .= $esc . "a" . "\x00";
        $data .= "No   : " . $transaction->invoice_no . "\n";
        $data .= "Tgl  : " . $transaction->transaction_date->format('d/m/Y H:i') . "\n";
        $data .= "Kasir: " . $transaction->cashier->name . "\n";
        $data .= str_repeat("-", 32) . "\n";
        
        // Items
        foreach ($transaction->items as $item) {
            $data .= $item->product->name . "\n";
            $qtyPrice = $item->qty . " x " . number_format($item->unit_price, 0, ',', '.');
            $subtotal = number_format($item->subtotal, 0, ',', '.');
            
            // Pad spaces
            $spaces = 32 - strlen($qtyPrice) - strlen($subtotal);
            if ($spaces < 1) $spaces = 1;
            
            $data .= $qtyPrice . str_repeat(" ", $spaces) . $subtotal . "\n";
        }
        $data .= str_repeat("-", 32) . "\n";
        
        // Right alignment for totals
        $data .= $esc . "a" . "\x02";
        $data .= "Subtotal: " . number_format($transaction->total_amount + $transaction->discount, 0, ',', '.') . "\n";
        if ($transaction->discount > 0) {
            $data .= "Diskon  : " . number_format($transaction->discount, 0, ',', '.') . "\n";
        }
        $data .= "Total   : " . number_format($transaction->total_amount, 0, ',', '.') . "\n";
        $data .= "Bayar   : " . number_format($transaction->paid_amount, 0, ',', '.') . "\n";
        $data .= "Kembali : " . number_format($transaction->paid_amount - $transaction->total_amount, 0, ',', '.') . "\n";
        
        // Center alignment
        $data .= $esc . "a" . "\x01";
        $data .= str_repeat("-", 32) . "\n";
        $data .= "Terima Kasih\n";
        $data .= "Atas Kunjungan Anda\n";
        
        // Feed paper & Cut
        $data .= "\n\n\n\n\n" . $gs . "V" . "\x41" . "\x00";
        
        return response()->json([
            'success' => true,
            'raw_data' => base64_encode($data)
        ]);
    }

    /**
     * Generate dynamic QRIS QR code for the given amount.
     */
    public function generateQris(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        try {
            $service = new QrisService();
            $result  = $service->generateDynamic((int) $request->amount);

            if (!$result['success']) {
                return response()->json(['success' => false, 'error' => $result['error']], 422);
            }

            // Generate QR code as SVG — cast to string for JSON encoding
            $qrSvg = (string) QrCode::format('svg')
                ->size(280)
                ->errorCorrection('M')
                ->generate($result['qris_string']);

            return response()->json([
                'success'     => true,
                'qr_svg'      => $qrSvg,
                'qris_string' => $result['qris_string'],
                'merchant'    => $result['merchant'],
                'amount'      => $result['amount'],
            ]);
        } catch (\Throwable $e) {
            Log::error('QRIS generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Gagal generate QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }
}