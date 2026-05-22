<?php
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Minuman
            ['category' => 'Minuman', 'name' => 'Aqua Botol 600ml',          'sku' => 'MNM-001', 'sell' => 4000,  'buy' => 2800,  'stock' => 150, 'min' => 20],
            ['category' => 'Minuman', 'name' => 'Aqua Galon 19L',            'sku' => 'MNM-002', 'sell' => 24000, 'buy' => 18000, 'stock' => 40,  'min' => 10],
            ['category' => 'Minuman', 'name' => 'Teh Botol Sosro 350ml',     'sku' => 'MNM-003', 'sell' => 6000,  'buy' => 4200,  'stock' => 120, 'min' => 15],
            ['category' => 'Minuman', 'name' => 'Coca-Cola Kaleng 330ml',    'sku' => 'MNM-004', 'sell' => 9000,  'buy' => 6500,  'stock' => 80,  'min' => 10],
            ['category' => 'Minuman', 'name' => 'Good Day Cappuccino 200ml', 'sku' => 'MNM-005', 'sell' => 5500,  'buy' => 3800,  'stock' => 60,  'min' => 10],
            ['category' => 'Minuman', 'name' => 'Pocari Sweat 500ml',        'sku' => 'MNM-006', 'sell' => 10000, 'buy' => 7200,  'stock' => 55,  'min' => 10],

            // Makanan Ringan
            ['category' => 'Makanan Ringan', 'name' => 'Chitato Rasa Sapi 68g',  'sku' => 'MKR-001', 'sell' => 13000, 'buy' => 9500,  'stock' => 90,  'min' => 15],
            ['category' => 'Makanan Ringan', 'name' => 'Oreo Original 133g',     'sku' => 'MKR-002', 'sell' => 14000, 'buy' => 10000, 'stock' => 85,  'min' => 15],
            ['category' => 'Makanan Ringan', 'name' => 'Indomie Goreng',         'sku' => 'MKR-003', 'sell' => 3500,  'buy' => 2400,  'stock' => 200, 'min' => 30],
            ['category' => 'Makanan Ringan', 'name' => 'Indomie Soto',           'sku' => 'MKR-004', 'sell' => 3500,  'buy' => 2400,  'stock' => 175, 'min' => 30],
            ['category' => 'Makanan Ringan', 'name' => 'Beng-Beng Share It 78g', 'sku' => 'MKR-005', 'sell' => 10000, 'buy' => 7000,  'stock' => 70,  'min' => 10],
            ['category' => 'Makanan Ringan', 'name' => 'Richeese Ahh 65g',       'sku' => 'MKR-006', 'sell' => 8000,  'buy' => 5500,  'stock' => 95,  'min' => 15],

            // Sembako
            ['category' => 'Sembako', 'name' => 'Beras Premium 5kg',          'sku' => 'SMB-001', 'sell' => 78000, 'buy' => 65000, 'stock' => 50, 'min' => 10],
            ['category' => 'Sembako', 'name' => 'Gula Pasir 1kg',             'sku' => 'SMB-002', 'sell' => 17000, 'buy' => 13500, 'stock' => 80, 'min' => 15],
            ['category' => 'Sembako', 'name' => 'Minyak Goreng Tropical 2L',  'sku' => 'SMB-003', 'sell' => 42000, 'buy' => 35000, 'stock' => 45, 'min' => 10],
            ['category' => 'Sembako', 'name' => 'Tepung Terigu Segitiga 1kg', 'sku' => 'SMB-004', 'sell' => 14000, 'buy' => 10500, 'stock' => 60, 'min' => 10],
            ['category' => 'Sembako', 'name' => 'Kecap Manis ABC 135ml',      'sku' => 'SMB-005', 'sell' => 10000, 'buy' => 7200,  'stock' => 70, 'min' => 10],

            // Rokok
            ['category' => 'Rokok', 'name' => 'Sampoerna Mild 16',    'sku' => 'RKK-001', 'sell' => 30000, 'buy' => 26500, 'stock' => 100, 'min' => 20],
            ['category' => 'Rokok', 'name' => 'Gudang Garam Merah 12','sku' => 'RKK-002', 'sell' => 24000, 'buy' => 21000, 'stock' => 90,  'min' => 20],
            ['category' => 'Rokok', 'name' => 'Dji Sam Soe 12',       'sku' => 'RKK-003', 'sell' => 33000, 'buy' => 29500, 'stock' => 75,  'min' => 15],
            ['category' => 'Rokok', 'name' => 'Marlboro Merah 20',    'sku' => 'RKK-004', 'sell' => 38000, 'buy' => 34000, 'stock' => 60,  'min' => 10],

            // Alat Tulis
            ['category' => 'Alat Tulis', 'name' => 'Pulpen Pilot BPS-GP 0.7', 'sku' => 'ATK-001', 'sell' => 7000, 'buy' => 4500, 'stock' => 80,  'min' => 20],
            ['category' => 'Alat Tulis', 'name' => 'Pensil 2B Faber Castell', 'sku' => 'ATK-002', 'sell' => 5000, 'buy' => 3200, 'stock' => 100, 'min' => 20],
            ['category' => 'Alat Tulis', 'name' => 'Buku Tulis Sidu 58lbr',   'sku' => 'ATK-003', 'sell' => 8000, 'buy' => 5500, 'stock' => 120, 'min' => 20],

            // Kebersihan
            ['category' => 'Kebersihan', 'name' => 'Sabun Lifebuoy 85g',        'sku' => 'KBR-001', 'sell' => 5500,  'buy' => 3800,  'stock' => 150, 'min' => 25],
            ['category' => 'Kebersihan', 'name' => 'Shampo Clear Men 170ml',    'sku' => 'KBR-002', 'sell' => 28000, 'buy' => 20500, 'stock' => 65,  'min' => 10],
            ['category' => 'Kebersihan', 'name' => 'Rinso Detergen 1kg',        'sku' => 'KBR-003', 'sell' => 22000, 'buy' => 16500, 'stock' => 55,  'min' => 10],
            ['category' => 'Kebersihan', 'name' => 'Wipol Pembersih Lantai 750ml','sku' => 'KBR-004','sell' => 19000, 'buy' => 13500, 'stock' => 45,  'min' => 8],

            // Kesehatan
            ['category' => 'Kesehatan', 'name' => 'Paracetamol 500mg Strip 10', 'sku' => 'KSH-001', 'sell' => 8000,  'buy' => 5000,  'stock' => 200, 'min' => 30],
            ['category' => 'Kesehatan', 'name' => 'Promag Strip 10',            'sku' => 'KSH-002', 'sell' => 12000, 'buy' => 8500,  'stock' => 130, 'min' => 20],
            ['category' => 'Kesehatan', 'name' => 'Betadine 30ml',              'sku' => 'KSH-003', 'sell' => 22000, 'buy' => 16000, 'stock' => 80,  'min' => 15],

            // Elektronik
            ['category' => 'Elektronik', 'name' => 'Baterai ABC AA 2pcs',  'sku' => 'ELK-001', 'sell' => 12000, 'buy' => 8500, 'stock' => 110, 'min' => 20],
            ['category' => 'Elektronik', 'name' => 'Baterai ABC AAA 2pcs', 'sku' => 'ELK-002', 'sell' => 11000, 'buy' => 7800, 'stock' => 95,  'min' => 20],
        ];

        foreach ($products as $p) {
            $category = Category::where('name', $p['category'])->first();
            if (!$category) continue;

            Product::firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'category_id' => $category->id,
                    'name'        => $p['name'],
                    'sell_price'  => $p['sell'],
                    'buy_price'   => $p['buy'],
                    'stock_qty'   => $p['stock'],
                    'stock_min'   => $p['min'],
                    'is_active'   => true,
                ]
            );
        }

        $this->command->info('Products seeded: ' . count($products));
    }
}