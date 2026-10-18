<?php

namespace App\Support;

use Closure;

class TimeFormat
{
    public static function validationRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            if (!is_string($value) || self::normalize($value) === null) {
                $fail('The ' . str_replace('_', ' ', $attribute) . ' must be a valid time such as 8:30 AM.');
            }
        };
    }

    public static function normalize(string $time): ?string
    {
        if (!preg_match('/^\s*(\d{1,2}):([0-5]\d)(?::[0-5]\d)?\s*(AM|PM)?\s*$/i', $time, $matches)) {
            return null;
        }

        $hour = (int) $matches[1];
        $period = strtoupper($matches[3] ?? '');

        if ($period !== '') {
            if ($hour < 1 || $hour > 12) {
                return null;
            }

            $hour = ($hour % 12) + ($period === 'PM' ? 12 : 0);
        } elseif ($hour > 23) {
            return null;
        }

        return sprintf('%02d:%s', $hour, $matches[2]);
    }

    public static function display(?string $time): string
    {
        $normalized = $time === null ? null : self::normalize($time);

        if ($normalized === null) {
            return '';
        }

        return \Carbon\Carbon::createFromFormat('H:i', $normalized)->format('g:i A');
    }
}
