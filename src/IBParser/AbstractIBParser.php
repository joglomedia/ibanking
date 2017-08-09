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

use IBanking\IBParser\IBParserInterface;
use IBanking\Utils\HttpRequest as HttpRequest;
use IBanking\Utils\HttpHelper as HttpHelper;
use IBanking\Utils\HtmlParser as HtmlParser;

/**
 * IBParser abstract
 */
abstract class AbstractIBParser implements IBParserInterface
{
    /**
     * Ibanking host
     *
     * @var string
     */
    protected $host;

    /**
     * IBanking url endpoints
     *
     * @var array
     */
    protected $endpoints = [];
    
    /**
     * IBanking credentials
     *
     * @var array
     */
    protected $credentials = [];

    /**
     * Http request instance
     *
     * @var object
     */
    protected $http;

    /**
     * Http request headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Http request options (curl)
     *
     * @var array
     */
    protected $options = [];

    /**
     * Html parser (object instance of DOM parser)
     *
     * @var object
     */
    protected $htmlp;

    /**
     * Account statements / mutasi
     *
     * @var array
     */
    protected $statements = [];

    /**
     * Logged in status
     *
     * @var bool
     */
    public $loggedin = false;

    /**
     * Logged in cookies session
     *
     * @var string
     */
    public $_session = '';

    /**
     * IBParser class constructor
     *
     * @return void
     */
    public function __construct()
    {
        // init request header (if required)
        $this->headers = [
            "connection: keep-alive",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ];

        // init default curl http request options
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
    public function setCredential($username, $password, $account = '', $corpid = '')
    {
        $this->credentials = [ 
            'username' => $username, // Internet Banking username
            'password' => $password, // Internet Banking password
            'account'  => $account,  // Internet Banking account number
            'corpid'   => $corpid,   // Internet Banking corporate ID
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function checkStatement($needle, $key, $haystack)
    {
        if (! is_array($haystack)) {
            return [];
        }
        
        // return array of statement if exists
        foreach ($haystack as $item) {
            if ($item[$key] == $needle || false !== strstr($item[$key], $needle)) {
                return $item;
            }
        }

        return [];
    }
}
