<?php

class WPCOM_REST_Object_Site extends WPCOM_REST_Object {
	private $client;
	private $site_id;

	private function __construct( $site_id, WPCOM_REST_Client $client ) {
		$this->site_id = $site_id;
		$this->client = $client;
	}

	public function withId( $site_id, WPCOM_REST_Client $client ) {
		return new self( $site_id, $client );
	}

	public function get() {
		$url = sprintf( 'v1/sites/%s', $this->site_id );

		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_posts( $params ) {
		$url = sprintf( 'v1/sites/%s/posts', $this->site_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET, $params );
	}

	public function get_post( $post_id ) {
		return WPCOM_REST_Object_Post::withId( $post_id, $this->site_id, $this->client );
	}

	public function add_post( $post_data ) {
		return WPCOM_REST_Object_Post::asNew( $post_data, $this->site_id, $this->client );
	}
}
