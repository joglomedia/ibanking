<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBanking\Utils;

class Functions
{
    public static function generateRandom()
    {
        $random = (float) mt_rand() / (float) mt_getrandmax();

        return $random;
    }

    public static function generateRandomString($length = 8, $charset = '')
    {
        if ($charset == '') {
            $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charsetLength = strlen($charset);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            //$r = mt_rand() / mt_getrandmax();
            //$k = (int) floor($r * $b);
            //$randomString .= $charset[$k];
            $randomString .= $charset[mt_rand(0, $charsetLength - 1)];
        }

        return $randomString;
    }
    
    public static function encodeURIComponent($str)
    {
        $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29'=>')');

        return strtr(rawurlencode($str), $revert);
    }
    
    public static function filterWhiteSpace($string)
    {
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = trim($string);

        return $string;
    }
}
