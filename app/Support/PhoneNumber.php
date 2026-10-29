<?php

namespace App\Support;

final class PhoneNumber
{
    /**
     * Normalise an Indonesian phone number to digits only, using the 62 prefix.
     *
     * "0812-3456-7890", "+62 812 3456 7890" and "62812 3456 7890" all
     * become "6281234567890".
     */
    public static function normalize(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }
}
