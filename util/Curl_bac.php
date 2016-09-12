<?php
namespace tomcao\tools\util;

class Curl_bac
{
	public $response;
	public $responseCode;
	public $requestHead;

	private $_options = [];
	private $_params = [];
	private $_cookies = [];

	private $methods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD'];

	private $_defaultOptions = [
		CURLOPT_USERAGENT => 'TOMCAO HTTP CLIENT',
		CURLOPT_TIMEOUT => 60, // seconds to allow cRUL functions to execute.
		CURLOPT_CONNECTTIMEOUT => 5, // seconds to wait while trying to connect.  0 means indefinitely.
		CURLOPT_RETURNTRANSFER => true, // to return the transfer as a string of the return value
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HEADER => false, 
		CURLOPT_NOBODY => false,
	];

	public function __construct($options = null)
	{
		if (!extension_loaded('curl'))
			throw new \Exception('You should add extension curl');
	}

	// $raw = 1为json转换数组，10为获取request报文，其他为报文
	private function __httpRequest($url, $raw, $method)
	{
		$this->setOptions(CURLOPT_CUSTOMREQUEST, $method);
		if ($methos === 'HEAD') {
			$this->_defaultOptions[CURLOPT_NOBODY] = true;
			$this->_defaultOptions[CURLOPT_HEADER] = true;
		}

		if ($this->_params) {
			if($method === 'HEAD' || $method === 'GET'){
				$this->_defaultOptions[CURLOPT_HTTPHEADER] = $this->_defaultOptions[CURLOPT_HTTPHEADER]
					?? ['content-type:application/x-www-form-urlencoded'];
				$url = $url . '?' .  http_build_query($this->_params);
			}else{
				$this->defaultOptions[CURLOPT_HTTPHEADER] = $this->defaultOptions[CURLOPT_HTTPHEADER]
					?? ['content-type:multipart/form-data'];
				$this->setOptions(CURLOPT_POSTFIELDS, $this->_params);				
			}
		}

		if ($this->_cookies) {
			$cookies = http_build_query(cookie);
			$this-> setOptions(CURLOPT_COOKIE, $cookies);
		}
	}

	public function test($url)
	{
		if ($ch = curl_init($url)) {
			foreach ($this->_defaultOptions as $key => $option) {
				//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true); 
				curl_setopt($ch, $key, $option);
			}
			curl_exec($ch);
			echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		}
	}
}