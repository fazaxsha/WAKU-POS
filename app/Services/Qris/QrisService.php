<?php

namespace App\Services\Qris;

use Illuminate\Support\Facades\Log;

class QrisService
{
    /**
     * Generate a dynamic QRIS string from the configured static QRIS.
     *
     * @param int        $amount  Transaction amount in Rupiah
     * @param array|null $fee     Optional: ['type' => 'fixed'|'percentage', 'value' => number]
     * @return array ['success' => bool, 'qris_string' => ?string, 'merchant' => ?array, 'error' => ?string]
     */
    public function generateDynamic(int $amount, ?array $fee = null): array
    {
        $staticQris = config('qris.static_string');

        if (empty($staticQris)) {
            return [
                'success'     => false,
                'qris_string' => null,
                'merchant'    => null,
                'error'       => 'QRIS static string belum dikonfigurasi. Tambahkan QRIS_STATIC_STRING di .env',
            ];
        }

        // Validate the static QRIS
        $validation = Validator::validate($staticQris);
        if (!$validation['valid']) {
            Log::warning('QRIS validation failed', $validation['errors']);
            return [
                'success'     => false,
                'qris_string' => null,
                'merchant'    => null,
                'error'       => 'QRIS static string tidak valid: ' . implode(', ', $validation['errors']),
            ];
        }

        // Parse merchant info
        $parsed = Parser::parseQRIS($staticQris);

        // Convert to dynamic
        $dynamicQris = Converter::convert($staticQris, $amount, $fee);

        return [
            'success'     => true,
            'qris_string' => $dynamicQris,
            'merchant'    => [
                'name' => $parsed['merchantName'],
                'city' => $parsed['merchantCity'],
            ],
            'amount'      => $amount,
            'error'       => null,
        ];
    }
}
