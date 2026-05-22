<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adjustment' => 'required|numeric',
            'notes'      => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'adjustment.required' => 'Jumlah penyesuaian harus diisi.',
            'adjustment.numeric'  => 'Jumlah penyesuaian harus berupa angka.',
        ];
    }
}
