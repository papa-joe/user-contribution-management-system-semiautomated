<?php

namespace App\Helpers;

use App\Models\Payment;
use App\Models\User;

class General
{
    

    public static function generateRandomString($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function paymentid()
    {
        $urlid = self::generateRandomString($length = 15);

        while (Payment::where('paymentid', $urlid)->count() > 0) {
            $urlid = self::generateRandomString($length = 15);
        }

        return $urlid;
    }

    public static function payment_urlid()
    {
        $urlid = self::generateRandomString($length = 15);

        while (Payment::where('urlid', $urlid)->count() > 0) {
            $urlid = self::generateRandomString($length = 15);
        }

        return $urlid;
    }

    public static function user_urlid()
    {
        $urlid = self::generateRandomString($length = 15);

        while (User::where('urlid', $urlid)->count() > 0) {
            $urlid = self::generateRandomString($length = 15);
        }

        return $urlid;
    }

    
}
