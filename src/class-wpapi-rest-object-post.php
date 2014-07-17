<?php

class WPAPI_REST_Object_Post extends WP_REST_Object {
	private $post_id;

	protected function __construct( $post_id, WP_REST_Client $client ) {
		parent::__construct( $client );
		$this->post_id = $post_id;
	}

	public static function withId( $post_id, WP_REST_Client $client ) {
		return new self( $post_id, $client );
	}

	public static function asNew( $post_data, WP_REST_Client $client ) {
		$url = 'posts';
		$response = $client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_POST, null, $post_data );

		return self::withId( $response->ID, $client );
	}

	public function get() {
		$url = sprintf( 'posts/%d', $this->post_id );
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET );
	}

	public function update( $post_data ) {
		$url = sprintf( 'posts/%d', $this->post_id );
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_PUT, null, $post_data );
	}

	public function delete() {
		$url = sprintf( 'posts/%d', $this->post_id );
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_DELETE );
	}

	public function get_revisions() {
		$url = sprintf( 'posts/%d/revisions', $this->post_id );
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_comments() {
		$url = sprintf( 'posts/%d/comments', $this->post_id );
		return $this->client->send_api_request( $url, WP_REST_Client::REQUEST_METHOD_GET );
	}
}
