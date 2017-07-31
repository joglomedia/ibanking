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

/**
 * IBank interface
 */
interface IBParserInterface
{
	public function setCredentials($username, $password, $account, $corpid);
	
	public function login($username, $password, $account, $corpid);
	
	public function logout();
	
	public function getBalance();
	
	public function getTransactions($start, $end, $type);
	
	public function checkTransaction($transaction);
}
