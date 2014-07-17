<?php

abstract class WP_REST_Client {
	const REQUEST_METHOD_GET = 'GET';
	const REQUEST_METHOD_POST = 'POST';
	const REQUEST_METHOD_HEAD = 'HEAD';
	const REQUEST_METHOD_PUT = 'PUT';
	const REQUEST_METHOD_DELETE = 'DELETE';
	const REQUEST_METHOD_PATCH = 'PATCH';

	protected $api_transport;
	protected $api_base_url;
	protected $request_methods;

	public function __construct() {
		$this->set_api_transport( new WPCOM_REST_Transport_Curl ); 
	}

	protected abstract function authenticate_request( WP_REST_Request &$request );	

	public function send_api_request( $path, $method, $params = array(), $post_data = array(), $headers = array(), $is_multipart = false ) {
		$url = $this->build_url( $this->api_base_url, $path, $params );

		$request = new WP_REST_Request( $url, $method, $post_data, $headers, $is_multipart );
		$this->authenticate_request( $request );

		return $this->send_request( $request );
	}

	protected function send_request( WP_REST_Request $request ) {
		// TODO: move this to WP_REST_Request?
		if ( ! $this->is_valid_request_method( $request->get_method() ) ) {
			throw new DomainException( sprintf( 'Invalid request $method: %s; should be one of %s', $method, implode( ',', $this->get_valid_request_methods() ) ) );
		}

		// TODO: set UA to identify requests

		return $this->api_transport->send_request( $request );
	}

	// TODO: move this to WP_REST_Request?
	protected function build_url( $url, $path, $params ) {
		if ( $path ) {
			$url = sprintf( '%s/%s', rtrim( $url, '/\\' ), ltrim( $path, '/\\' ) );
		}

		if ( ! parse_url( $url, PHP_URL_QUERY ) && ! empty( $params ) ) {
			$url .= '?';
		}

		$url .= http_build_query( $params );

		return $url; 
	}

	protected function is_valid_request_method( $method ) {
		return in_array( $method, $this->get_valid_request_methods() );
	}

	protected function get_valid_request_methods() {
		return $this->request_methods;
	}

	public function set_api_transport( WPCOM_REST_Transport $transport ) {
		$this->api_transport = $transport;
	}

	public function get_api_transport() {
		return $this->api_transport;
	}

	public function set_api_base_url( $url ) {
		$this->api_base_url = $url;
	}

	public function get_api_base_url() {
		return $this->api_base_url;
	}
}
