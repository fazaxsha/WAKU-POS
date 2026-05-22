<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.qty'      => 'required|numeric|min:0.001',
            'items.*.discount' => 'nullable|numeric|min:0',
            'paid_amount'      => 'required|numeric|min:0',
            'payment_method'   => 'required|in:cash,transfer,qris',
            'discount'         => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:255',
            'is_reseller_mode' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'       => 'Keranjang tidak boleh kosong.',
            'items.min'            => 'Keranjang tidak boleh kosong.',
            'items.*.id.exists'    => 'Produk tidak ditemukan.',
            'items.*.qty.min'      => 'Jumlah produk minimal 1.',
            'paid_amount.required' => 'Nominal pembayaran harus diisi.',
            'paid_amount.min'      => 'Nominal pembayaran tidak valid.',
            'payment_method.in'    => 'Metode pembayaran tidak valid.',
        ];
    }
}
