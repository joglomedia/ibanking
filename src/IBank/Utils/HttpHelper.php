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

class HttpHelper
{
	/**
	 * Encodes the string or array passed in a way compatible with OAuth.
	 * If an array is passed each array value will will be encoded.
	 *
	 * @param mixed $data the scalar or array to encode
	 * @return $data encoded in a way compatible with OAuth
	 */
	protected static function safeEncode($data) 
	{
		if (is_array($data)) {
			return array_map(array($this, 'safeEncode'), $data);
		} else if (is_scalar($data)) {
			return str_ireplace(
				array('+', '%7E'),
				array(' ', '~'),
				rawurlencode($data)
			);
		} else {
			return '';
		}
	}

	/**
	 * Decodes the string or array from it's URL encoded form
	 * If an array is passed each array value will will be decoded.
	 *
	 * @param mixed $data the scalar or array to decode
	 * @return string $data decoded from the URL encoded form
	 */
	protected static function safeDecode($data) 
	{
		if (is_array($data)) {
			return array_map(array($this, 'safeDecode'), $data);
		} else if (is_scalar($data)) {
			return rawurldecode($data);
		} else {
			return '';
		}
	}

	/**
	 * Public access to the private safe decode/encode methods
	 *
	 * @param string $text the text to transform
	 * @param string $mode the transformation mode. either encode or decode
	 * @return string $text transformed by the given $mode
	 */
	public static function transformText($text, $mode='encode') {
		$mode = ucfirst($mode);
		return self::{"safe$mode"}($text);
	}

	/**
	 * Utility method to fetch cookie string from header response.
	 * this doesn't really belong here, but mostly when this class is used, 
	 * i use this function method as well, so i have placed it here.
	 *
	 * @param string $headerString
	 * @return string $cookies
	 **/
	public static function getCookie($headerString, $arrayReturn=false) 
	{
		$cookies = array();	
		
		if (function_exists('http_parse_headers')) {
			$headers = http_parse_headers($headerString);

			$_cookies = array();
			foreach ($headers as $key => $header) {
				if (strtolower($key) == 'set-cookie') {
					foreach ($header as $k => $value) {
						$_cookies[] = http_parse_cookie($value);
					}
				}
			}

			$__cookies = array();
			foreach ($_cookies as $row) {
				$__cookies[] = $row->cookies;
			}

			// sort k=>v format
			foreach($__cookies as $v) {
				foreach ($v as $k1 => $v1) {
					$cookies[$k1] = $v1;
				}
			}
		} else {
			// match cookie string
			preg_match_all("#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m", $headerString, $matches);
			//preg_match_all("#^Set-Cookie: (.*?);#sm", $headerstring, $matches);

			foreach ($matches['cookie'] as $cookie) {
				if ($cookie{0} == '=')
					continue;
				
				// Skip over "expired cookies which were causing problems; by Neerav; 4 Apr 2006
				if ((strpos($cookie, "EXPIRED") !== false) || (strpos($cookie, "GoogleAccountsLocale_session") !== false)) 
					continue;
		
				$cookies = array_merge($cookies, array($cookie));
			}
		}
		
		return $return = ($arrayReturn) ? $cookies : implode(';', $cookies);
	}
}
