<?php

namespace App\Classes;

class MyHelper
{
    /**
     * Convert string datetime to date
     *
     * Prints out date format.
     */
    public static function parseDate(string $dateTime): string
    {
        $date = date_create_from_format('Y-m-d H:i:s', $dateTime);

        if (!$date) {
            return date_create_from_format('Y-m-d', $dateTime);
        }

        return $date;
    }
}
