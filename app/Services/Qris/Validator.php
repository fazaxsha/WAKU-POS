<?php

namespace App\Services\Qris;

class Validator
{
    /**
     * Validate a QRIS string for structural correctness.
     *
     * @return array ['valid' => bool, 'errors' => string[]]
     */
    public static function validate(string $qrisString): array
    {
        $errors = [];

        if (empty(trim($qrisString))) {
            return ['valid' => false, 'errors' => ['QRIS string is empty']];
        }

        $str = trim($qrisString);

        if (!str_starts_with($str, '000201')) {
            $errors[] = 'QRIS must start with Payload Format Indicator "000201"';
        }

        if (strlen($str) < 20) {
            $errors[] = 'QRIS string is too short';
            return ['valid' => false, 'errors' => $errors];
        }

        // CRC validation
        $dataWithoutCRC = substr($str, 0, -4);
        $declaredCRC    = strtoupper(substr($str, -4));
        $calculatedCRC  = Crc16::calculate($dataWithoutCRC);

        if ($declaredCRC !== $calculatedCRC) {
            $errors[] = "CRC mismatch: expected {$calculatedCRC}, got {$declaredCRC}";
        }

        // Try to parse TLV structure
        $elements = Parser::parseTLV($str);
        if (empty($elements)) {
            $errors[] = 'Failed to parse any TLV elements';
            return ['valid' => false, 'errors' => $errors];
        }

        $tags = array_map(fn($e) => $e['tag'], $elements);

        $requiredTags = [
            '00' => 'Payload Format Indicator',
            '01' => 'Point of Initiation Method',
            '52' => 'Merchant Category Code',
            '53' => 'Transaction Currency',
            '58' => 'Country Code',
            '59' => 'Merchant Name',
            '60' => 'Merchant City',
            '63' => 'CRC',
        ];

        foreach ($requiredTags as $tag => $name) {
            if (!in_array($tag, $tags)) {
                $errors[] = "Missing required tag {$tag} ({$name})";
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}
