<?php

class WPCOM_REST_Object_Me extends WPCOM_REST_Object {
	protected function __construct( WPCOM_REST_Client $client ) {
		parent::__construct( $client );
	}

	public static function withAuth( WPCOM_REST_Client $client ) {
		return new self( $client );
	}

	public function get() {
		$url = 'v1/me';
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}
}
