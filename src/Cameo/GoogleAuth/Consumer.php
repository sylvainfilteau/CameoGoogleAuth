<?php

namespace Cameo\GoogleAuth;

use Guzzle\Http\Client;

class Consumer {

	private $_url;

	private $_config = array();

	private $_config_default = array(
		"url" => "https://accounts.google.com/o/oauth2/auth",
		"access_type" => "offline",
		"approval_prompt" => "force",
		"scope" => array(
			"https://www.googleapis.com/auth/userinfo.email",
			"https://www.googleapis.com/auth/userinfo.profile"
		),
		"response_type" => "code"
	);

	public function __construct($config = array()) {
		$this->_config = array_merge($this->_config_default, $config);
		$this->_url = $this->_config["url"];
	}

	public function getConsent() {
		$params = array_intersect_key(
			$this->_config,
			array_flip(
				array(
					"response_type",
					"client_id",
					"redirect_uri",
					"access_type",
					"approval_prompt",
					"scope"
				)
			)
		);

		if (isset($this->_config["hd"])) {
			$params["hd"] = $this->_config["hd"];
		}

		if (is_array($this->_config["scope"])) {
			$params["scope"] = implode(" ", $this->_config["scope"]);
		}

		$redirect_to = $this->_url . "?" . http_build_query($params);
		header("Location: $redirect_to");
	}

	public function isCodeSent() {
		return isset($_GET["code"]);
	}

	/**
	 * @return Consumer\Session
	 * @throws \Exception
	 */
	public function getSession() {
		if (!isset($_GET['code'])) {
			throw new \Exception("There is no code in the request");
		}

		$token = $this->getToken($_GET['code']);
		$session = new Consumer\Session($token["access_token"], $token["refresh_token"], $token["expires_in"]);

		return $session;
	}

	public function revoke(Consumer\Session $session) {
		$client = new Client("https://accounts.google.com/o/oauth2/revoke?token=" . $session->getAccessToken());
		return $client->get("")->send();
	}

	public function refresh(Consumer\Session $session) {
		$request = $this->getAccessTokenRequest();
		$request->addPostFields(array(
			"refresh_token" => $session->getRefreshToken(),
			"grant_type" => "refresh_token"
		));

		/**
		 * @var $response \Guzzle\Http\Message\Response
		 */
		$response = $request->send();
		$tokens = $response->json();

		$session->refresh($tokens["access_token"], $tokens["expires_in"]);
	}

	private function getToken($code) {
		$request = $this->getAccessTokenRequest();
		$request->addPostFields(array(
			"code" => $code,
			"grant_type" => "authorization_code"
		));

		/**
		 * @var $response \Guzzle\Http\Message\Response
		 */
		$response = $request->send();
		return $response->json();
	}

	/**
	 * @return \Guzzle\Http\Message\Request
	 */
	private function getAccessTokenRequest() {
		$url = 'https://accounts.google.com/o/oauth2/token';

		$params = array_intersect_key(
			$this->_config,
			array_flip(
				array(
					"client_id",
					"client_secret",
					"redirect_uri"
				)
			)
		);

		$client = new Client($url);
		return $client->post("")->addPostFields($params);
	}

}