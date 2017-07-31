<?php
/**
 * This file is part of the IBank library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBank\Utils;

class Functions
{
    public static function random()
	{
		return (float)rand()/(float)getrandmax();
	}
	
	public static function encodeURIComponent($str)
	{
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode($str), $revert);
	}
	
	public static function filterWhitespace($string)
	{
		$string = trim($string);
		$string = preg_replace('/\s+/', ' ', $string);
		$string = trim($string);
		
		return $string;
	}
}
