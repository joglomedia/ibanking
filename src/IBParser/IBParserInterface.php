<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBanking\IBParser;

/**
 * IBParser interface
 */
interface IBParserInterface
{
    /**
     * Set IBanking credential
     *
     * @param string $username
     * @param string $password
     * @param string $account
     * @param string $corpid
     * @return void
     */
    public function setCredential($username, $password, $account, $corpid);

    /**
     * Set IBanking login
     *
     * @param string $username
     * @param string $password
     * @param string $account
     * @param string $corpid
     * @return bool
     */
    public function login($username, $password, $account, $corpid);

    /**
     * Set IBanking logout
     *
     * @return void
     */
    public function logout();

    /**
     * Get IBanking final balance
     *
     * @return float
     */
    public function getBalance();

    /**
     * Get IBanking statements for given date range
     *
     * @param date $start format d/m/Y
     * @param date $end format d/m/Y
     * @param string $type of statement: credit, debit, % (all)
     * @return array $statements
     */
    public function getStatements($start, $end, $type);

    /**
     * Check / search single statement
     *
     * @param mixed $needle
     * @param mixed $key
     * @param array $haystack
     * @return array of statement or empty
     */
    public function checkStatement($needle, $key, $haystack);
}
