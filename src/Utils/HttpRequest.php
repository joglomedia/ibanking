<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBanking\Utils;

use IBanking\Utils\HttpHelper;

class HttpRequest
{
    /**
     * HttpRequest version
     *
     * @const string
     */
    const VERSION = '1.0.0-dev';

    /**
     * HttpRequest method GET
     *
     * @const string
     */
    const METH_GET = 'GET';

    /**
     * HttpRequest method POST
     *
     * @const string
     */
    const METH_POST = 'POST';

    /**
     * HttpRequest request host
     *
     * @var string
     */
    public $host = '';

    /**
     * HttpRequest request port
     *
     * @var string
     */
    public $port = '';

    /**
     * HttpRequest use SSL (enabled)
     *
     * @var bool
     */
    public $useSsl = false;

    /**
     * HttpRequest (curl) options
     *
     * @var array
     */
    public $options = [];

    /**
     * HttpRequest setting
     *
     * @var array
     */
    protected $requestSetting = [];

    /**
     * HttpRequest (curl) raw response
     *
     * @var string
     */
    protected $rawResponse = '';

    /**
     * HttpRequest parsed response
     *
     * @var array
     */
    protected $response = [];

    /**
     * HttpRequest (curl) handler
     *
     * @var instance of curl
     */
    protected $ch = null;

    /**
     * HttpRequest class constructor
     *
     * @param string $url
     * @param string $method
     * @return void
     */
    public function __construct($url = '', $method = 'GET')
    {
        // initialize request setting
        $this->setRequestSetting(['url' => $url, 'method' => $method]);
        
        // initialize default curl options
        $this->options = [
            CURLOPT_URL => $this->requestSetting['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ];
        
        // initialize curl handler
        $this->ch = curl_init();
    }

    /**
     * Set curl option
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    
        // make chainable.
        return $this;
    }

    /**
     * Set curl options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        if (! is_array($options)) {
            return false;
        }

        // overwrite existing options
        $this->options = $options;

        return $this;
    }

    /**
     * Set request host
     *
     * @param string $host
     * @param mixed $port
     * @return $this
     */
    public function setRequestHost($host = '', $port = 80)
    {
        if (! empty($host)) {
            $this->host = $host;
        }
        
        if (is_numeric($port) && ($port > 0 && $port != 80)) {
            $this->port = $port;
        }
        
        return $this;
    }

    /**
     * Set request post
     *
     * @param mixed $port
     * @return $this
     */
    public function setRequestPort($port)
    {
        if (is_numeric($port) && ($port > 0 && $port != 80)) {
            $this->port = $port;
        }
        
        return $this;
    }

    /**
     * Set request Url
     *
     * @param string $url
     * @return $this
     */
    public function setRequestUrl($url = '')
    {
        if ($url != '') {
            $this->requestSetting['url'] = $url;
        }

        return $this;    
    }

    /**
     * Set request method
     *
     * @param string $method
     * @return $this
     */
    public function setRequestMethod($method = 'GET')
    {
        $this->requestSetting['method'] = $method;

        return $this;
    }

    /**
     * Add query data for GET request
     *
     * @param mixed $query
     * @return $this
     */
    public function addQueryData($query)
    {
        $query = (is_array($query)) ? http_build_query($query) : $query;
        if (! empty($this->requestSetting['query'])) {
            $this->requestSetting['query'] .= '&' . $query;
        } else {
            $this->requestSetting['query'] = $query;
        }

        return $this;
    }

    /**
     * Add post fields
     *
     * @param mixed $fields
     * @return $this
     */
    public function addPostFields($fields)
    {
        if (is_array($fields)) {
            $this->requestSetting['params'] = $fields;
            $fields = http_build_query($fields);
            $this->requestSetting['postfields'] = $fields;
        }

        $this->options[CURLOPT_CUSTOMREQUEST] = 'POST';
        $this->options[CURLOPT_POSTFIELDS] = $fields;

        return $this;
    }

    /**
     * Add post file
     *
     * @param mixed $file
     * @return $this
     */
    public function addPostFile($file)
    {
        // TO DO: post/upload file
    }

    /**
     * Add HTTP header to existing request headers
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addRequestHeader($name, $value)
    {
        if (isset($name)) {
            $header = strtolower($name) . ': ' . $value;
            $this->requestSetting['header'][] = trim($header);
        }

        return $this;
    }

    /**
     * Add HTTP request headers
     *
     * @param array $headers
     * @return $this
     */
    public function addRequestHeaders($headers)
    {
        if (is_array($headers)) {
            // overwrite existing headers
            $this->requestSetting['header'] = $headers;
        }

        return $this;
    }

    /**
     * Set HTTP basic authentication
     *
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $this->options[CURLOPT_USERPWD] = $username . ':' . $password;

        return $this;
    }

    /**
     * Set HTTP user agent
     *
     * @param string $useragent
     * @return $this
     */
    public function setUserAgent($useragent='')
    {
        if ($useragent != '') {
            $this->requestSetting['useragent'] = $useragent;
        }

        return $this;
    }

    /**
     * Set HTTP referer
     *
     * @param string $referer
     * @return $this
     */
    public function setReferer($referer='')
    {
        if ($referer != '') {
            $this->requestSetting['referer'] = $referer;
        }

        return $this;
    }

    /**
     * Set SSL verify peer
     * For HTTP request using SSL connection, this should be set to true
     *
     * @param bool $verifypeer
     * @return $this
     */
    public function setSslVerifypeer($verifypeer = true)
    {
        if ($verifypeer) {
            $this->options[CURLOPT_SSL_VERIFYPEER] = true;
            $this->options[CURLOPT_SSL_VERIFYHOST] = 2;

            $this->useSsl = true;
        }

        return $this;
    }

    /**
     * Set SSL certificate
     * For HTTP request using SSL connection, this should be set to your cacert.pem file.
     * you can get the latest cacert.pem from here http://curl.haxx.se/ca/cacert.pem
     * if you're getting HTTP 0 responses, check cacert.pem exists and is readable
     * without it curl won't be able to create an SSL connection.
     *
     * @param string $cainfo
     * @param string $capath
     * @return $this
     */
    public function setSslCertificate($cainfo = '', $capath = '')
    {
        if ($cainfo == '') {
            $cainfo = __DIR__ . DIRECTORY_SEPARATOR . 'Certificate' . DIRECTORY_SEPARATOR . 'cacert.pem';
        }

        if (file_exists($cainfo)) {
            if ($capath == '') {
                $capath = dirname($cainfo);
            }

            if ($this->useSsl) {
                $this->options[CURLOPT_CAINFO] = $cainfo;
                $this->options[CURLOPT_CAPATH] = $capath;
            }
        }

        return $this;
    }

    /**
     * Set response header out
     * Include curl response header into curl response
     *
     * @param bool $enable
     * @return $this
     */
    public function setHeaderOut($enable = true)
    {
        if ($enable) {
            $this->options[CURLOPT_HEADER] = true;
            $this->options[CURLINFO_HEADER_OUT] = true;
        }
    
        return $this;
    }

    /**
     * Set response no body
     * Exclude response body from curl response
     *
     * @param bool $enable
     * @return $this
     */
    public function setNoBody($enable = true)
    {
        if ($enable) {
            $this->options[CURLOPT_NOBODY] = true;
        }
        
        return $this;
    }

    /**
     * Reset HTTP request
     * Reset HTTP request setting, clean response, re-use request (curl) instance
     *
     * @return $this
     */
    public function resetRequest()
    {
        $this->setRequestSetting();
        $this->rawResponse = '';
        $this->response = [];

        return $this;
    }

    /**
     * Set default request setting
     *
     * @param array $settings request settings
     * @return void value is stored to the requestSetting array class variable
     */
    protected function setRequestSetting($settings = [])
    {
        // Set default and overwrite http request settings.
        $this->requestSetting = array_merge(
            [
                'url'       => '',
                'method'    => 'GET',
                'query'     => '',
                'multipart' => false,
                'header'    => [],
                'useragent' => 'HttpAdapter ' . self::VERSION . (($this->useSsl) ? '+' : '-') 
                                . 'SSL - //github.com/joglomedia/httpadapter'
            ],
            $settings
        );
    }

    /**
     * Prepares the URL for use in the base string by parsing it apart and reconstructing it
     *
     * Ref: 3.4.1.2
     *
     * @return void value isvalue is stored to the requestSetting array class variable
     */
    protected function prepareRequestUrl()
    {
        // parse request url.
        $parts = parse_url($this->requestSetting['url']);
        
        if ($this->port != '' && $this->port > 0) {
            $port = $this->port;
        } else {
            $port = isset($parts['port']) ? $parts['port'] : false;
        }

        $scheme    = $parts['scheme'];
        $host    = $parts['host'];
        $path   = isset($parts['path']) ? $parts['path'] : '';
        $query    = isset($parts['query']) ? $parts['query'] : '';

        $port or $port = ($scheme == 'https') ? '443' : '80';

        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";

            // reassign port
            $this->port = $port;
        }

        // HTTP scheme and host MUST be lowercase
        $this->requestSetting['url'] = strtolower("$scheme://$host");
        // but not the path
        $this->requestSetting['url'] .= $path;

        // Save query strings.
        if (! empty($this->requestSetting['query'])) {
            $this->requestSetting['query'] = (empty($query) ? '' : $query . '&') . $this->requestSetting['query'];
        } else {
            $this->requestSetting['query'] = $query;
        }
    }

    /**
     * Prepare HTTP request
     *
     * @return void value is stored to the requestSetting array class variable
     */
    protected function prepareRequest()
    {
        $this->prepareRequestUrl();

        // request url
        $this->requestSetting['url'] = empty($this->requestSetting['query']) ?:    
            $this->requestSetting['url'] . '?' . $this->requestSetting['query'];

        $this->options[CURLOPT_URL] = $this->requestSetting['url'];

        // referer
        if (isset($this->requestSetting['referer'])) {
            $this->options[CURLOPT_REFERER] = $this->requestSetting['referer'];
        }

        // re-assign useragent
        if (! empty($this->requestSetting['useragent'])) {
            $this->options[CURLOPT_USERAGENT] = $this->requestSetting['useragent'];
        }

        // request header
        if (! empty($this->requestSetting['header'])) {
            $this->options[CURLOPT_HTTPHEADER] = $this->requestSetting['header'];
        }

        // alternative port to connect.
        if (($this->port != '80' && $this->port != '443')) {
            $this->options[CURLOPT_PORT] = $this->port;
        }
    }

    /**
     * Send HTTP request, curl exec
     *
     * @return $this value is stored to the response array class variable
     */
    public function send()
    {
        // prepare request
        $this->prepareRequest();

        // curl options
        if (! empty($this->options)) {
            curl_setopt_array($this->ch, $this->options);
        }

        $this->rawResponse = curl_exec($this->ch);
        $this->response['info'] = curl_getinfo($this->ch);
        $this->response['error'] = curl_error($this->ch);

        // parse response
        $this->parseResponse();

        return $this;
    }

    /**
     * Parse HTTP response header and body
     *
     * @return void value is stored to the response array class variable
     */
    protected function parseResponse()
    {
        if ($this->response['error']) {
            $this->response['body'] = "cURL Error #:" . $this->response['error'];
        } else {
            if (isset($this->options[CURLINFO_HEADER_OUT])) {
                $headerLength = $this->response['info']['header_size'];
                $this->response['header'] = substr($this->rawResponse, 0, $headerLength);
                $this->response['body'] = substr($this->rawResponse, $headerLength);
            } else {
                $this->response['body'] = $this->rawResponse;
            }
        }
    }

    /**
     * Close (curl) request
     *
     * @return void
     */
    public function close()
    {
        curl_close($this->ch);
    }

    /**
     * Get HTTP request (curl) options
     *
     * @return array options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get HTTP request setting
     *
     * @return array requestSetting
     */
    public function getRequestSetting()
    {
        return $this->requestSetting;
    }

    /**
     * Get HTTP raw response
     *
     * @return string rawResponse
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Get HTTP parsed response
     *
     * @return array response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get HTTP response header
     *
     * @return mixed response header
     */
    public function getResponseHeader($arrayReturn = false)
    {
        return $header = isset($this->response['header']) ? $this->response['header'] : '';
    }

    /**
     * Get HTTP response body
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $body = isset($this->response['body']) ? $this->response['body'] : '';
    }

    /**
     * Get HTTP response cookie
     *
     * @return string
     */
    public function getResponseCookie()
    {
        $cookie = '';
        
        if (isset($this->response['header'])) {
            $cookie = HttpHelper::getCookie($this->response['header']);
        }
        
        return $cookie;
    }
}
