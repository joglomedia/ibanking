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
	const VERSION = '1.0.0-dev';

	const METH_GET = 'GET';
	const METH_POST = 'POST';
	
	public $host = '';
	public $port = '';
	public $useSsl = false;
	public $options = []; // curl options

	protected $ch = null; // curl handler
	protected $requestSetting = [];
	protected $rawResponse = ''; // curl response
	protected $response = []; // parsed response

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
	
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
	
		// make chainable.
		return $this;
	}
	
	public function setOptions($options = array())
	{
		if (! is_array($options)) {
			return false;
		}

		// overwrite existing options
		$this->options = $options;

		return $this;
	}

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
	
	public function setRequestPort($port)
	{
		if (is_numeric($port) && ($port > 0 && $port != 80)) {
			$this->port = $port;
		}
		
		return $this;
	}
	
	public function setRequestUrl($url = '')
	{
		if ($url != '') {
			$this->requestSetting['url'] = $url;
		}

		return $this;	
	}
	
	public function setRequestMethod($method = 'GET')
	{
		$this->requestSetting['method'] = $method;

		return $this;
	}

	/**
	 * Query data string for GET request
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

	public function addPostFields($fields = array())
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

	public function addPostFile()
	{
		// TO DO: post/upload file
	}

	/**
	 * Add http header to existing request headers.
	 */
	public function addRequestHeader($name, $value='')
	{
		if (isset($name)) {
			$header = strtolower($name) . ': ' . $value;
			$this->requestSetting['header'][] = trim($header);
		}

		return $this;
	}

	public function addRequestHeaders($headers)
	{
		if (is_array($headers)) {
			// overwrite existing headers.
			$this->requestSetting['header'] = $headers;
		}

		return $this;
	}

	public function setBasicAuthentication($username, $password)
	{
		$this->options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		$this->options[CURLOPT_USERPWD] = $username . ':' . $password;

		return $this;
	}

	public function setUserAgent($useragent='')
	{
		if ($useragent != '') {
			$this->requestSetting['useragent'] = $useragent;
		}

		return $this;
	}

	public function setReferer($referer='')
	{
		if ($referer != '') {
			$this->requestSetting['referer'] = $referer;
		}

		return $this;
	}
	
	/**
	 * For HTTP request using SSL connection, this should be set to true.
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
	 * For HTTP request using SSL connection, this should be set to your cacert.pem file.
	 * you can get the latest cacert.pem from here http://curl.haxx.se/ca/cacert.pem
	 * if you're getting HTTP 0 responses, check cacert.pem exists and is readable
	 * without it curl won't be able to create an SSL connection.
	 */
	public function setSslCertificate($cainfo='', $capath='')
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
	 * Include response header
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
	 * Exclude response body
	 */
	public function setNoBody($enable = true)
	{
		if ($enable) {
			$this->options[CURLOPT_NOBODY] = true;
		}
		
		return $this;
	}
	
	public function resetRequest()
	{
		$this->setRequestSetting();
		$this->rawResponse = '';
		$this->response = [];

		return $this;
	}
	
	/**
	 * Set default request setting.
	 *
	 * @param array $settings request settings.
	 * @return void value is stored to the requestSetting array class variable.
	 */
	protected function setRequestSetting($settings = array())
	{
		// Set default and overwrite http request settings.
		$this->requestSetting = array_merge(
			[
				'url'		=> '',
				'method'	=> 'GET',
				'query'		=> '',
				'multipart' => false,
				'header'	=> [],
				'useragent'	=> 'HttpAdapter ' . self::VERSION . (($this->useSsl) ? '+' : '-') . 'SSL - //github.com/joglomedia/httpadapter'
			],
			$settings
		);
	}

	/**
	 * Prepares the URL for use in the base string by parsing it apart and reconstructing it.
	 *
	 * Ref: 3.4.1.2
	 *
	 * @return void value is stored to the class array variable '$this->requestSetting['url']'
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

		$scheme	= $parts['scheme'];
		$host	= $parts['host'];
		$path   = isset($parts['path']) ? $parts['path'] : '';
		$query	= isset($parts['query']) ? $parts['query'] : '';

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
	 * send request, process curl
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
	 * Parse response header and body
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
	 * close curl request
	 */
	public function close()
	{
		curl_close($this->ch);
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function getRequestSetting()
	{
		return $this->requestSetting;
	}

	public function getRawResponse()
	{
		return $this->rawResponse;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getResponseHeader($arrayReturn = false)
	{
		return $header = isset($this->response['header']) ? $this->response['header'] : '';
	}

	public function getResponseBody()
	{
		return $body = isset($this->response['body']) ? $this->response['body'] : '';
	}

	public function getResponseCookie()
	{
		$cookie = '';
		
		if (isset($this->response['header'])) {
			$cookie = HttpHelper::getCookie($this->response['header']);
		}
		
		return $cookie;
	}
}
