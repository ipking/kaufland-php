<?php

namespace Kaufland\Core;

abstract class Client{
	
	
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	
	private static $success_codes = [200,201,204];
	
	protected static $callback;
	
	protected $method;
	
	protected $endpoint = 'https://sellerapi.kaufland.com';
	
	protected $url;
	
	protected $data;
	
	protected $client_response;
	
	protected $response_code;
	
	protected $client_id;
	
	protected $client_secret;
	
	/**
	 * @param $cb
	 */
	public static function setSendCallback($cb){
		self::$callback = $cb;
	}
	
	
	/**
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}
	
	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}
	
	/**
	 * @return string
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * @return string
	 */
	public function getResponse(){
		return $this->client_response;
	}
	
	/**
	 * @param string $endpoint 请求接入点域名(xx.[https://xxx.net)
	 */
	public function setEndpoint($endpoint)
	{
		$this->endpoint = $endpoint;
	}
	
	/**
	 * @param string $client_id
	 */
	public function setClientId($client_id){
		$this->client_id = $client_id;
	}
	
	/**
	 * @param string $client_secret
	 */
	public function setClientSecret($client_secret){
		$this->client_secret = $client_secret;
	}
	
	/**
	 * @param string $uri
	 * @param array $requestOptions
	 * @return array
	 * @throws HttpException|\Exception
	 */
	protected function send($uri, $requestOptions = []){
		$this->method = strtoupper($requestOptions['method']);
		$this->url = $this->endpoint.$uri;
		
		if (isset($requestOptions['query'])) {
			$this->url .= '?' . http_build_query($requestOptions['query']);
		}
		
		$timestamp = time();
		$user_agent = 'kaufland PHP API.';
		
		$string = implode("\n", [
			$this->method,
			$this->url,
			($requestOptions['json']?json_encode($requestOptions['json']):''),
			$timestamp,
		]);
		
		$signature = hash_hmac('sha256', $string, $this->client_secret);
		
		$header_arr = [];
		$header_arr[] = 'Accept: application/json';
		$header_arr[] = 'Shop-Timestamp: '.$timestamp;
		$header_arr[] = 'User-Agent: '.$user_agent;
		$header_arr[] = 'Shop-Client-Key: '.$this->client_id;
		$header_arr[] = 'Shop-Signature: '.$signature;
		
		
		switch($this->method){
			case self::METHOD_GET:
				$opt = array(
					CURLOPT_HTTPHEADER     => $header_arr,
				);
				
				return $this->execute($this->url,$opt);
			case self::METHOD_POST:
				$data = [];
				if($requestOptions['json']){
					$data = json_encode($requestOptions['json']);
					$header_arr[] = 'Content-Type: application/json';
				}
				$opt = array(
					CURLOPT_POST           => true,
					CURLOPT_HTTPHEADER     => $header_arr,
					CURLOPT_POSTFIELDS     => $data,
				);
				$this->data = json_encode($requestOptions['json']);
				return $this->execute($this->url,$opt);
			case self::METHOD_PUT:
			case self::METHOD_PATCH:
				$data = [];
				if($requestOptions['json']){
					$data = json_encode($requestOptions['json']);
					$header_arr[] = 'Content-Type: application/json';
				}
				$opt = array(
					CURLOPT_CUSTOMREQUEST  => $this->method,
					CURLOPT_HTTPHEADER     => $header_arr,
					CURLOPT_POSTFIELDS     => $data,
				);
				$this->data = json_encode($requestOptions['json']);
				return $this->execute($this->url,$opt);
			default :
				throw new \Exception('Not support method :'.$this->method);
		}
		
	}
	
	/**
	 * @param string $url
	 * @param array $opt
	 * @return array|mixed
	 * @throws HttpException
	 */
	public function execute($url, $opt){
		$this->response_code = '';
		$this->client_response = Curl::execute($url,$opt);
		list($response_body,$response_code) = $this->client_response;
		$this->response_code = $response_code;
		
		if(is_callable(self::$callback)){
			$callback = self::$callback;
			$callback($this);
		}
		
		return $response_body?json_decode($response_body, true):'';
	}
	
	/**
	 * @return bool
	 */
	public function isSuccess(){
		return in_array($this->response_code,self::$success_codes);
	}
}