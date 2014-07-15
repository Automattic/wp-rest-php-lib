<?php

class WPCOM_REST_Object_Post extends WPCOM_REST_Object {
	private $post_id;
	private $site_id;

	protected function __construct( $post_id, $site_id, WPCOM_REST_Client $client ) {
		parent::__construct( $client );
		$this->post_id = $post_id;
		$this->site_id = $site_id;
	}

	public static function withId( $post_id, $site_id, WPCOM_REST_Client $client ) {
		return new self( $post_id, $site_id, $client );
	}

	public static function asNew( $post_data, $site_id, WPCOM_REST_Client $client ) {
		$url = sprintf( 'v1/sites/%s/posts/new', $site_id );

		$response = $client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_POST, null, $post_data );
		return self::withId( $response->ID, $site_id, $client );
	}

	public function get() {
		$url = sprintf( 'v1/sites/%s/posts/%d', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

	public function update_post( $post_data ) {
		$url = sprintf( 'v1/sites/%s/posts/%d', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_POST, null, $post_data );
	}

	public function delete_post() {
		$url = sprintf( 'v1/sites/%s/posts/%d/delete', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_POST );
	}

	public function get_comments() {
		$url = sprintf( 'v1/sites/%s/posts/%d/replies', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_likes() {
		$url = sprintf( 'v1/sites/%s/posts/%d/likes', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_reblogs() {
		$url = sprintf( 'v1/sites/%s/posts/%d/reblogs', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

	public function get_related() {
		$url = sprintf( 'v1/sites/%s/posts/%d/related', $this->site_id, $this->post_id );
		return $this->client->send_api_request( $url, WPCOM_REST_Client::REQUEST_METHOD_GET );
	}

}
