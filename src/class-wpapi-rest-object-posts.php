<?php

class WPAPI_REST_Object_Posts extends WP_REST_Object {

	protected function __construct( WP_REST_Client $client ) {
		parent::__construct( $client );
	}

	public static function init( WP_REST_Client $client ) {
		return new self( $client );
	}

	public function get( $params ) {
		$url = 'posts';
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET, $params );
	}

	public function get_post_types() {
		$url = 'posts/types';
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_post_type( $type ) {
		$route = sprintf( 'posts/types/%s', $type );
		return $this->client->send_api_request( $route, WP_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_post_statuses() {
		$url = 'posts/statuses';
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET );
	}
}
