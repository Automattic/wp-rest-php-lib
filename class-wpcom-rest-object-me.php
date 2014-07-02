<?php

class WPCOM_REST_Object_Me extends WPCOM_REST_Object {
	private $client;

	public function __construct( WPCOM_REST_Client $client ) {
		$this->client = $client;
	}

	public function get() {
		$url = 'v1/me';
		return $this->client->send_authorized_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}
}
