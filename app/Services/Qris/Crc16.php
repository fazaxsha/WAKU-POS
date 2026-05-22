<?php

namespace App\Services\Qris;

class Crc16
{
    /**
     * Calculate CRC16-CCITT checksum for QRIS/EMVCo QR codes.
     * Polynomial: 0x1021, Init: 0xFFFF
     */
    public static function calculate(string $str): string
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($str); $i++) {
            $crc ^= ord($str[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc & 0xFFFF), 4, '0', STR_PAD_LEFT));
    }
}
