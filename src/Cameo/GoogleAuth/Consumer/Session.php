<?php

namespace Cameo\GoogleAuth\Consumer;

use Guzzle\Http\Client;

class Session {

	private $_access_token;

	private $_refresh_token;

	private $_expires_in;

	private $_profile;

	public function __construct($access_token, $refresh_token, $expires_in) {
		$this->_access_token = $access_token;
		$this->_refresh_token = $refresh_token;
		$this->_expires_in = $expires_in;
	}

	public function getAccessToken() {
		return $this->_access_token;
	}

	public function getRefreshToken() {
		return $this->_refresh_token;
	}

	public function getExpiresIn() {
		return $this->_expires_in;
	}

	public function refresh($access_token, $expires_in) {
		$this->_access_token = $access_token;
		$this->_expires_in = $expires_in;
	}

	public function getProfile() {
		if (is_null($this->_profile)) {
			$client = new Client("https://www.googleapis.com/oauth2/v2/userinfo");
			$request = $client->get("", array(
				"Authorization" => "Bearer " . $this->getAccessToken()
			));

			$response = $request->send();
			$this->_profile = $response->json();
		}

		return $this->_profile;
	}

}