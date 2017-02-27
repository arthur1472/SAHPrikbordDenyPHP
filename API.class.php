<?php
/**
 * Created by PhpStorm.
 * User: Arthur
 * Date: 13-1-2017
 * Time: 14:01
 */

class API {
	private $web;
	private $token;

	function __construct($web, $token = null)
	{
		$this->web = $web;

		if ($token != null) {
			$this->token = $token;
		}
	}

	public function login($username, $password) {
		$url = "https://sah.ownprojects.info/login";

		$variables = array("post" => "email=$username&password=$password");
		$data = json_decode($this->web->post($url, $variables));

		if (isset($data->token)) {
			$this->setToken($data->token);
			return true;
		}

		return false;
	}

	public function loggedIn() {
		$url = "https://sah.ownprojects.info/ingelogd?token=".$this->getToken();
		$response = $this->web->get($url);
		$loggedIn = json_decode($response);
		if (isset($loggedIn->ingelogd)) {
			return ($loggedIn->ingelogd == "false") ? false : true;
		} else {
			return false;
		}
	}

	public function getPrikbordItems() {
		$url = "https://sah.ownprojects.info/prikbord_actueel?token=".$this->getToken();
		$response = $this->web->get($url);
		$prikbordItems = json_decode($response);
		return $prikbordItems;
	}

	public function denyPrikbordItem($id) {
		$url = "https://sah.ownprojects.info/prikbord_kan_niet?token=".$this->getToken()."&id=$id";
		$response = $this->web->get($url);
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setToken($token) {
		$this->token = $token;
	}


}