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

use IBanking\Utils\HttpRequest as HttpRequest;
use IBanking\Utils\HttpHelper as HttpHelper;
use IBanking\Utils\HtmlParser as HtmlParser;
use IBanking\Utils\Function as Function;

/**
 * Sample IBParser class
 */
class SampleBankParser extends AbstractIBParser
{
    /**
     * {@inheritdoc}
     */
    protected $host = 'ib.samplebank.co.id';

    /**
     * {@inheritdoc}
     */
    protected $headers = [];

    /**
     * {@inheritdoc}
     */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // init request header (if required)
        $this->headers = [
            "connection: keep-alive",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ];

        // init http request (curl) options
        $this->options = [
            CURLOPT_URL => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ];

        // init http request object
        $this->http = new HttpRequest();

        // init html html parser object
        $this->htmlp = new HtmlParser();
    }

    /**
     * {@inheritdoc}
     */
    public function login($username = '', $password = '', $account = '', $corpid = '')
    {
        // prevent multiple logged in
        if ($this->loggedin) {
            return true;
        }

        // overwrite credentials
        if ($username != '' && $password != '') {
            $this->setCredentials($username, $password, $account, $corpid);
        }

         // TO DO: do login process, get cookie session / login status from IB host, api server, etc
        $this->loggedin = true;

        // if logged in, add response cookie to _session (if required)
        if ($this->loggedin) {
            $this->_session = $this->http->getResponseCookie();
        }

        return $this->loggedin;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        // TO DO: do logout process, hit the logout page, clear session, etc

        // reset cookie session (if required)
        $this->_session = '';

        // reset logged in status
        $this->loggedin = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        // balance
        $balance = 0;

        // retry login if logged in status is false
        if (!$this->loggedin) {
            $this->login();
        }

        // TO DO: get balance data from page scrapping, api, etc

        return (float)$balance;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStatements($start = '1/1/2017', $end = '30/1/2017', $type = '%')
    {
        // retry login if logged in status false
        if (!$this->loggedin) {
            $this->login();
        }

        // statement list saved as array
        $statements = [];

        // TO DO: get statements data from page scrapping, api, etc
        $statements[] = [
            'datetime' => '1/1/2017 00:00:01', // date time of this statement, not mandatory
            'date' => '1/1/2017',
            'description' => 'Trf PRMA',
            'type' => 'CR', // CR = credit, DB = debit
            'amount' => 100000.00,
            'balance' => 1000000.00 // final balance of this statement, not mandatory
        ];

        return $statements;
    }
}
