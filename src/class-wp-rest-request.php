<?php

class WP_REST_Request {
	private $url;
	private $method;
	private $params = array();
	private $post_data = array();
	private $headers = array();
	private $is_multipart = false;

	public function __construct( $url, $method, $post_data = array(), $headers = array(), $is_multipart = false ) {
		$this->url = $url;
		$this->method = $method;
		$this->set_headers( $headers );
		$this->set_post_data( $post_data );
		$this->set_is_multipart( $is_multipart );
	}

	public function get_url() {
		return $this->url;
	}

	public function get_method() {
		return $this->method;
	}

	public function set_post_data( $post_data ) {
		$this->post_data = $post_data;
	}

	public function has_post_data() {
		return ! empty( $this->post_data );
	}

	public function get_post_data() {
		return $this->post_data;
	}

	public function set_headers( $headers ) {
		$this->headers = array();
		foreach ( (array) $headers as $name => $content ) {
			$this->add_header( $name, $content );
		}
	}

	public function add_header( $name, $content ) {
		$this->headers[ $name ] = $content;
	}

	public function get_headers() {
		return $this->headers;
	}

	public function get_processed_headers() {
		$processed_headers = array();

		foreach ( $this->headers as $name => $content ) {
			$processed_headers[] = sprintf( '%s: %s', $name, $content );
		}

		return $processed_headers;
	}

	public function set_is_multipart( $is_multipart ) {
		$this->is_multipart = $is_multipart;
		$this->add_header( 'Content-Type', 'multipart/form-data' );
	}

	public function is_multipart() {
		return $this->is_multipart;
	}
}
