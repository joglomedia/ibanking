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
    /**
     * IBanking version
     *
     * @const string
     */
    const VERSION = "1.0.0-dev";

    /**
     * IBanking IBParser instance
     *
     * @var object
     */
    protected $ib;

    /**
     * IBanking class constructor
     *
     * @param IBParserInterface $ib the current IBParser instance
     * @param array $credentials
     */
    public function __construct(IBParserInterface $ib, Array $credentials = [])
    {
        $this->ib = $ib;
        $this->ib->setCredential($credentials['username'], $credentials['password'], $credentials['account'], $credentials['corpid']);
    }

    /**
     * {@inheritdoc}
     */
    public function login()
    {
        return $this->ib->login();
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->ib->logout();
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->ib->getBalance();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatements($start, $end, $type = '%')
    {
        return $this->ib->getStatements($start, $end, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function checkStatement($needle, $key, $haystack)
    {
        return $this->ib->checkStatement($needle, $key, $haystack);
    }

    /**
     * Check logged in status
     *
     * @param bool $session
     * @return mixed
     */
    public function isLoggedin($session = false)
    {
        if ($session) {
            return (! empty($this->ib->_session)) ? $this->ib->_session : false;
        }
        else {
            return $this->ib->loggedin;
        }
    }

    /**
     * Set logged in cookie session, re-use cookie if not yet expired
     *
     * @param string $session
     * @return bool
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
