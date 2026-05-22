<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.actual_qty' => 'required|integer|min:0',
            'items.*.notes'      => 'nullable|string|max:255',
            'notes'              => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Pilih setidaknya 1 produk.',
            'items.min'                   => 'Pilih setidaknya 1 produk.',
            'items.*.actual_qty.required' => 'Stok fisik harus diisi.',
            'items.*.actual_qty.min'      => 'Stok fisik tidak boleh negatif.',
        ];
    }
}
