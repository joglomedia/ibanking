<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBanking;

use IBanking\IBParser\IBParserInterface;

class IBanking
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

	public function getStatements($start, $end, $type = '%')
	{
		return $this->ib->getStatements($start, $end, $type);
	}

	/**
	 * Check if statement (value) is exists
	 *
	 * return mixed
	 */
	public function checkStatement($needle, $key, $haystack)
	{
		return $this->ib->checkStatement($needle, $key, $haystack);
	}

	/**
	 * Check is logged in
	 *
	 * return mixed
	 */
	public function isLoggedin($session = false)
	{
		if ($session) {
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
		if ($session != '') {
			$this->ib->_session = $session;
			$this->ib->loggedin = true;

			return true;
		}

		return false;
	}
}
