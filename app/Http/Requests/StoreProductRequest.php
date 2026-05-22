<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'sku'         => 'required|string|max:50|unique:products,sku',
            'description' => 'nullable|string',
            'sell_price'        => 'required|numeric|min:0',
            'wholesale_price'   => 'nullable|numeric|min:0',
            'wholesale_min_qty' => 'nullable|numeric|min:0',
            'buy_price'         => 'required|numeric|min:0',
            'stock_qty'         => 'required|numeric|min:0',
            'stock_min'         => 'required|numeric|min:0',
            'unit'              => 'required|string|in:pcs,kg,gram,liter,meter,pack,box,lusin',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'         => 'boolean',
        ];
    }
}
