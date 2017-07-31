<?php
/**
 * This file is part of the IBank library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBank;

use IBank\IBParser\IBParserInterface;

class IBank
{
	const VERSION = "1.0.0-dev";

	protected $ib;

	public function __construct(IBParserInterface $ib, array $credentials = array())
	{
		$this->ib = $ib;
		$this->ib->setCredentials($credentials['username'], $credentials['password'], $credentials['account'], $credentials['corpid']);
	}

	public function login()
	{
		return $this->ib->login();
	}

	public function logout()
	{
		$this->ib->logout();
	}

	public function getBalance()
	{
		return $this->ib->getBalance();
	}

	public function getTransactions($start, $end, $type = '%')
	{
		return $this->ib->getTransactions($start, $end, $type);
	}

	public function checkTransaction($transaction)
	{
		return $this->ib->checkTransaction($transaction);
	}

	/**
	 * Check is logged in
	 *
	 * return mixed
	 */
	public function isLoggedin($session = false)
	{
		if($session) {
			return !empty($this->ib->_session) ? $this->ib->_session : false;
		} else {
			return $this->ib->loggedin;
		}
	}
	
	/**
	 * Set logged in cookie session
	 * re-use cookie if not yet expired
	 */
	public function setLoggedin($session = '')
	{
		if($session != '') {
			$this->ib->_session = $session;
			$this->ib->loggedin = true;
		}
	}
}
