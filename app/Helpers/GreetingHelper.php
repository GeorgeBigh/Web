<?php

namespace App\Helpers;

class GreetingHelper
{
    public static function greeting()
    {
        $hour = date('H');
        if ($hour >= 5 && $hour < 12) {
            return 'Good morning🌅';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Good afternoon🕑';
        } elseif ($hour >= 17 && $hour < 21) {
            return 'Good evening🌃';
        } else {
            return 'Good night🌙';
        }
    }
}
