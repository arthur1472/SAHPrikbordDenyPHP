<?php
/**
 * Created by PhpStorm.
 * User: Arthur
 * Date: 13-1-2017
 * Time: 14:01
 */

class web
{

	function __construct()
	{
	}

	public function get($url, $variables = null) {
		$ch =  curl_init();

		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		if(isset($variables['refer'])) {
			curl_setopt( $ch, CURLOPT_REFERER, $variables['refer']);
		}

		if (isset($variables['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $variables['headers']);
		}

		if (isset($variables['useragent'])) {
			curl_setopt($ch, CURLOPT_USERAGENT, $variables['useragent']);
		}

		//curl_setopt($ch, CURLOPT_HEADER, 1);

		$result = curl_exec($ch);

		//$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
		//echo $headerSent.PHP_EOL."<br>"."<br>";


		curl_close($ch);

		return $result;
	}

	public function post($url, $variables = null) {
		$ch =  curl_init();

		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $variables['post']);


		if(isset($variables['refer'])) {
			curl_setopt( $ch, CURLOPT_REFERER, $variables['refer']);
		}

		if (isset($variables['useragent'])) {
			curl_setopt($ch, CURLOPT_USERAGENT, $variables['useragent']);
		}

		if (isset($variables['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $variables['headers']);
		}

		//curl_setopt($ch, CURLOPT_HEADER, 1);

		$result = curl_exec($ch);

		//$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
		//echo $headerSent.PHP_EOL."<br>"."<br>";


		curl_close($ch);
		return $result;
	}
}
