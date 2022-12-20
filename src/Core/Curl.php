<?php

namespace Kaufland\Core;


abstract class Curl{
	const DEFAULT_TIMEOUT = 30;
	
	/**
	 * @return mixed
	 */
	private static function arrayMergeKeepKeys(){
		$arg_list = func_get_args();
		$Zoo = null;
		foreach((array)$arg_list as $arg){
			foreach((array)$arg as $K => $V){
				$Zoo[$K] = $V;
			}
		}
		return $Zoo;
	}
	
	/**
	 * @param $url
	 * @param array $curl_option
	 * @throws HttpException
	 * @return resource
	 */
	private static function getCurlInstance($url, $curl_option = array()){
		if(!$url){
			throw new HttpException('CURL URL NEEDED');
		}
		
		//use ssl
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		
		$opt = array(
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
		);
		
		if($ssl){
			$opt[CURLOPT_SSL_VERIFYPEER] = 0;
			$opt[CURLOPT_SSL_VERIFYHOST] = 1;
		}
		
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$a[CURLOPT_URL] = $url;
		foreach($curl_option as $k => $val){
			$a[$k] = $val;
			curl_setopt($curl, $k, $val);
		}
		return $curl;
	}
	
	/**
	 * @param string $url
	 * @param array $curl_option
	 * @throws HttpException
	 * @return array
	 */
	public static function execute($url, $curl_option = array()){
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		if($curl_errno>0){
			throw new HttpException($curl_errno);
		}
		
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		$response_headers = substr($content, 0, $header_size);
		$response_body = substr($content, $header_size);
		curl_close($curl);
		return [$response_body,$response_code,$response_headers];
	}
}