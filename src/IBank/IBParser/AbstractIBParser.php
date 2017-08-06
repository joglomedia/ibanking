<?php
/**
 * This file is part of the IBank library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBank\IBParser;

use IBank\IBParser\IBParserInterface;

abstract class AbstractIBParser implements IBParserInterface
{
	protected $credentials; // Internet Banking credentials (array)
	
	protected $host; // target host (string)

	protected $endpoints = []; // target endpoints (array)

	protected $http = null; // http adapter (object instance of http adapter)

	protected $htmlp = null; // html parser instance.

	protected $transactions = []; // account transactions / mutasi (array)

	public $loggedin = false; // logged in status (bool)

	public $_session = ''; // logged in cookies session

	public function setCredentials($username, $password, $account='', $corpid='')
	{
		$this->credentials = [ 
			'username'	=> $username,	// Internet Banking username
			'password'	=> $password,	// Internet Banking password
			'account'	=> $account,	// Internet Banking account number
			'corpid'	=> $corpid,		// Internet Banking corporate ID
		];
	}

	/**
	 * Searched transaction data $needle, looked transactions array $haystack
	 */
	public function checkTransaction($needle, $key, $haystack)
	{
		if (! is_array($haystack)) {
			return false;
		}
		
		// return true if transaction exists
		foreach ($haystack as $item) {
			if ($item[$key] == $needle) {
				return $item;
			}
		}

		return false;
	}
}
