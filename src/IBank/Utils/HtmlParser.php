<?php
/**
 * This file is part of the IBank library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBank\Utils;

class HtmlParser
{
	/* DOM document instance */
	private $dom = null;
	
	/* DOM xpath instance */
	private $xpath = null;
	
	private $html = '';
	
	public function __construct($html = '')
	{
		if ($html != '') {
			$this->initParser($html);
		}
	}
	
	public function initParser($html = '')
	{
		$html = ! empty($this->html) ? $this->html : $html;

		$previous_value = libxml_use_internal_errors(true);

		$this->dom = new \DomDocument();
		$this->dom->loadHTML($html);

		$this->xpath = new \DomXpath($this->dom);

		libxml_clear_errors();
		libxml_use_internal_errors($previous_value);
		
		// make chainable
		return $this;
	}
	
	public function query($query='')
	{
		$entries = $this->xpath->query($query);

		return $entries;
	}
	
	public function getInnerHTML($elements = null)
	{
		$html = '';

		if (! is_null($elements)) {
			if (count($elements) > 0) {
				foreach($elements as $element) {
					$html .= $this->dom->saveXML($element) . "\r\n";
				}
			}
		}
		
		return $html;
	}
	
	public function setHTML($html = '')
	{
		$this->html = $html;
		
		return $this;
	}
	
	public function setHTMLFile($file = '')
	{
		try {
			if (file_exists($file)) {
				$this->html = file_get_contents($file);
			} else {
				throw new Exception('setHTMLFile failed, file does not exist.');
			}
		} catch(Exception $e) {
			echo 'Caught exception: ' .  $e->getMessage();
		}
		
		return $this;
	}
}
