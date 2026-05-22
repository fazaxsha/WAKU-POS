<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductPriceExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::where('is_active', true)->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID Produk (JANGAN DIUBAH)',
            'SKU',
            'Nama Produk',
            'Harga Beli Saat Ini',
            'Harga Jual Saat Ini',
            'Harga Grosir Saat Ini',
            'Min Qty Grosir Saat Ini',
            'Harga Beli Baru',
            'Harga Jual Baru',
            'Harga Grosir Baru',
            'Min Qty Grosir Baru',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->name,
            $product->buy_price,
            $product->sell_price,
            $product->wholesale_price,
            $product->wholesale_min_qty,
            '', // Harga Beli Baru (Kosong)
            '', // Harga Jual Baru (Kosong)
            '', // Harga Grosir Baru (Kosong)
            '', // Min Qty Grosir Baru (Kosong)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 15,
            'C' => 40,
            'D' => 20,
            'E' => 20,
            'F' => 22,
            'G' => 24,
            'H' => 20,
            'I' => 20,
            'J' => 22,
            'K' => 24,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E5E4']
                ]
            ],
        ];
    }
}
