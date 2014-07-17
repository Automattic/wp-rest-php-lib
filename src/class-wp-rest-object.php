<?php

abstract class WP_REST_Object {
	protected $client;

	protected function __construct( WP_Rest_Client $client ) {
	    $this->client = $client;
	}

	public function get_client() {
		return $this->client;
	}
}
