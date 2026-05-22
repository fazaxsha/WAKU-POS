<?php

namespace App\Services\Qris;

class Parser
{
    /** Map of known EMVCo / QRIS tag IDs to human-readable names */
    private const TAG_NAMES = [
        '00' => 'Payload Format Indicator',
        '01' => 'Point of Initiation Method',
        '52' => 'Merchant Category Code',
        '53' => 'Transaction Currency',
        '54' => 'Transaction Amount',
        '55' => 'Tip or Convenience Indicator',
        '56' => 'Value of Convenience Fee (Fixed)',
        '57' => 'Value of Convenience Fee (%)',
        '58' => 'Country Code',
        '59' => 'Merchant Name',
        '60' => 'Merchant City',
        '61' => 'Postal Code',
        '62' => 'Additional Data Field',
        '63' => 'CRC',
    ];

    /** Tags that contain nested TLV sub-elements (26-51, 62) */
    private static function isNestedTag(string $tag): bool
    {
        $num = intval($tag);
        return ($num >= 26 && $num <= 51) || $tag === '62';
    }

    /**
     * Parse a raw TLV string into an array of elements.
     * Each element: ['tag' => string, 'name' => string, 'length' => int, 'value' => string, 'children' => ?array]
     */
    public static function parseTLV(string $data): array
    {
        $elements = [];
        $pos = 0;
        $len = strlen($data);

        while ($pos < $len) {
            if ($pos + 4 > $len) break;

            $tag    = substr($data, $pos, 2);
            $length = intval(substr($data, $pos + 2, 2));

            if ($pos + 4 + $length > $len) break;

            $value = substr($data, $pos + 4, $length);
            $name  = self::TAG_NAMES[$tag] ?? "Unknown ({$tag})";

            $element = [
                'tag'    => $tag,
                'name'   => $name,
                'length' => $length,
                'value'  => $value,
            ];

            if (self::isNestedTag($tag)) {
                $element['children'] = self::parseTLV($value);
            }

            $elements[] = $element;
            $pos += 4 + $length;
        }

        return $elements;
    }

    /**
     * Parse a QRIS string into a structured associative array.
     */
    public static function parseQRIS(string $qrisString): array
    {
        $raw = self::parseTLV($qrisString);

        $findTag = function (string $tag) use ($raw) {
            foreach ($raw as $el) {
                if ($el['tag'] === $tag) return $el;
            }
            return null;
        };

        $methodValue = $findTag('01')['value'] ?? '11';
        $method = $methodValue === '12' ? 'dynamic' : 'static';

        $tipValue = $findTag('55')['value'] ?? null;
        $tipIndicator = match ($tipValue) {
            '01' => 'prompt',
            '02' => 'fixed',
            '03' => 'percentage',
            default => null,
        };

        return [
            'version'              => $findTag('00')['value'] ?? '01',
            'method'               => $method,
            'merchantCategoryCode' => $findTag('52')['value'] ?? '',
            'currency'             => $findTag('53')['value'] ?? '360',
            'amount'               => $findTag('54')['value'] ?? null,
            'tipIndicator'         => $tipIndicator,
            'tipFixed'             => $findTag('56')['value'] ?? null,
            'tipPercentage'        => $findTag('57')['value'] ?? null,
            'countryCode'          => $findTag('58')['value'] ?? 'ID',
            'merchantName'         => $findTag('59')['value'] ?? '',
            'merchantCity'         => $findTag('60')['value'] ?? '',
            'postalCode'           => $findTag('61')['value'] ?? '',
            'crc'                  => $findTag('63')['value'] ?? '',
            'raw'                  => $raw,
        ];
    }
}
