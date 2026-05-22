<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductPriceImport implements ToCollection, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Skip header
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $id = $row[0];
            $newBuyPrice = $row[7] ?? null;
            $newSellPrice = $row[8] ?? null;
            $newWholesalePrice = $row[9] ?? null;
            $newWholesaleMinQty = $row[10] ?? null;

            // Cek apakah ID ada dan minimal ada satu harga baru yang diisi
            if ($id && ($newBuyPrice !== null || $newSellPrice !== null || $newWholesalePrice !== null || $newWholesaleMinQty !== null)) {
                $product = Product::find($id);

                if ($product) {
                    $updateData = [];

                    if ($newBuyPrice !== null && is_numeric($newBuyPrice)) {
                        $updateData['buy_price'] = $newBuyPrice;
                    }

                    if ($newSellPrice !== null && is_numeric($newSellPrice)) {
                        $updateData['sell_price'] = $newSellPrice;
                    }

                    if ($newWholesalePrice !== null && is_numeric($newWholesalePrice)) {
                        $updateData['wholesale_price'] = $newWholesalePrice;
                    }

                    if ($newWholesaleMinQty !== null && is_numeric($newWholesaleMinQty)) {
                        $updateData['wholesale_min_qty'] = $newWholesaleMinQty;
                    }

                    if (!empty($updateData)) {
                        $product->update($updateData);
                    }
                }
            }
        }
    }
}
