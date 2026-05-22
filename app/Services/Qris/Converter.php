<?php

namespace App\Services\Qris;

class Converter
{
    /**
     * Rebuild a QRIS string from TLV elements (without CRC).
     */
    private static function buildTLVString(array $elements): string
    {
        $result = '';
        foreach ($elements as $el) {
            $value  = isset($el['children']) ? self::buildTLVString($el['children']) : $el['value'];
            $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
            $result .= $el['tag'] . $length . $value;
        }
        return $result;
    }

    /**
     * Create a TLV element array.
     */
    private static function makeTLV(string $tag, string $value, string $name = ''): array
    {
        return [
            'tag'    => $tag,
            'name'   => $name,
            'length' => strlen($value),
            'value'  => $value,
        ];
    }

    /**
     * Convert a static QRIS string to dynamic by injecting amount and optional fee.
     *
     * @param string $qrisString  The static QRIS string
     * @param int    $amount      Transaction amount in Rupiah
     * @param array|null $fee     Optional fee: ['type' => 'fixed'|'percentage', 'value' => number]
     * @return string Dynamic QRIS string
     */
    public static function convert(string $qrisString, int $amount, ?array $fee = null): string
    {
        $elements = Parser::parseTLV($qrisString);

        $result = [];
        $amountInserted = false;

        // Tags we manage ourselves
        $managedTags = ['54', '55', '56', '57', '63'];

        foreach ($elements as $el) {
            if (in_array($el['tag'], $managedTags)) continue;

            if ($el['tag'] === '01') {
                // Change static → dynamic
                $result[] = self::makeTLV('01', '12', 'Point of Initiation Method');
                continue;
            }

            // Insert amount + fee before tag 58 (Country Code)
            if ($el['tag'] === '58' && !$amountInserted) {
                $result[] = self::makeTLV('54', (string) $amount, 'Transaction Amount');

                if ($fee) {
                    if ($fee['type'] === 'fixed') {
                        $result[] = self::makeTLV('55', '02', 'Tip or Convenience Indicator');
                        $result[] = self::makeTLV('56', (string) $fee['value'], 'Value of Convenience Fee (Fixed)');
                    } else {
                        $result[] = self::makeTLV('55', '03', 'Tip or Convenience Indicator');
                        $result[] = self::makeTLV('57', (string) $fee['value'], 'Value of Convenience Fee (%)');
                    }
                }

                $amountInserted = true;
            }

            $result[] = $el;
        }

        // Build string without CRC, then append CRC
        $withoutCRC = self::buildTLVString($result);
        $crcInput   = $withoutCRC . '6304';
        $crc        = Crc16::calculate($crcInput);

        return $crcInput . $crc;
    }
}
