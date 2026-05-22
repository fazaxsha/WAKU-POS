<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Minuman',        'description' => 'Minuman kemasan, jus, air mineral, dsb.'],
            ['name' => 'Makanan Ringan', 'description' => 'Snack, keripik, biskuit, permen, coklat.'],
            ['name' => 'Sembako',        'description' => 'Beras, minyak, gula, tepung, dan kebutuhan pokok.'],
            ['name' => 'Rokok',          'description' => 'Rokok berbagai merek dan ukuran.'],
            ['name' => 'Alat Tulis',     'description' => 'Pulpen, pensil, buku tulis, penggaris.'],
            ['name' => 'Kebersihan',     'description' => 'Sabun, sampo, detergen, pembersih lantai.'],
            ['name' => 'Kesehatan',      'description' => 'Obat-obatan, vitamin, dan produk kesehatan.'],
            ['name' => 'Elektronik',     'description' => 'Baterai, charger, aksesoris elektronik kecil.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name'        => $cat['name'],
                    'description' => $cat['description'],
                    'is_active'   => true,
                ]
            );
        }

        $this->command->info('Categories seeded: ' . count($categories));
    }
}