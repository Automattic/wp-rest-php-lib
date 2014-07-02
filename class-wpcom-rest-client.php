<?php

// TODO: replace with spl-autoload?
$base_dir =  dirname( __FILE__ );
require_once( $base_dir . '/class-wpcom-rest-exception.php' );
require_once( $base_dir . '/class-wpcom-rest-transport.php' );
require_once( $base_dir . '/class-wpcom-rest-transport-curl.php' );
require_once( $base_dir . '/class-wpcom-rest-transport-wp-http-api.php' );
require_once( $base_dir . '/class-wpcom-rest-object.php' );
require_once( $base_dir . '/class-wpcom-rest-object-site.php' );
require_once( $base_dir . '/class-wpcom-rest-object-post.php' );
require_once( $base_dir . '/class-wpcom-rest-object-me.php' );
unset( $base_dir );

class WPCOM_Rest_Client {
	const REQUEST_METHOD_GET = 'GET';
	const REQUEST_METHOD_POST = 'POST';

	const OAUTH_ACCESS_TOKEN_ENDPOINT = '/token';
	const OAUTH_AUTHORIZE_ENDPOINT = '/authorize';
	const OAUTH_AUTHENTICATE_URL = '/authenticate';

	const DEFAULT_API_BASE_URL = 'https://public-api.wordpress.com/rest';
	const DEFAULT_OAUTH_BASE_URL = 'https://public-api.wordpress.com/oauth2';

	private $request_methods = array( 'GET', 'POST' );

	private $api_transport;
	private $oauth_base_url = self::DEFAULT_OAUTH_BASE_URL;
	private $api_base_url = self::DEFAULT_API_BASE_URL;

	private $auth_key;
	private $auth_secret;
	private $auth_token;

	public function __construct() {
		$this->set_api_transport( new WPCOM_REST_Transport_Curl ); 
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

	public function set_oauth_base_url( $url ) {
		$this->oauth_base_url = $url;
	}
	public function get_oauth_base_url() {
		return $this->oauth_base_url;
	}

	public function set_auth_key( $key, $secret ) {
		$this->auth_key = $key;
		$this->auth_secret = $secret;
	}
	public function get_auth_key() {
		return $this->auth_key;
	}
	public function get_auth_secret() {
		return $this->auth_secret;
	}

	public function set_auth_token( $token ) {
		$this->auth_token = $token;
	}
	public function get_auth_token() {
		return $this->auth_token;
	}

	// TODO: remove this method; always send through send_api_request (to handle private sites)
	public function send_authorized_api_request( $path, $method, $params = array(), $post_data = array(), $headers = array(), $is_multipart = false ) {
		if ( ! is_array( $headers ) ) {
			$headers = array(); 
		}

		if ( $this->auth_token ) { 
			$headers[] = sprintf( 'Authorization: Bearer %s', $this->auth_token );
		}

		return $this->send_api_request( $path, $method, $params, $post_data, $headers, $is_multipart );
	}

	public function send_api_request( $path, $method, $params = array(), $post_data = array(), $headers = array(), $is_multipart = false ) {
		$url = $this->build_url( $this->api_base_url, $path, $params );
		return $this->send_request( $url, $method, $params, $post_data, $headers, $is_multipart );
	}

	private function send_request( $url, $method, $params = array(), $post_data = array(), $headers = array(), $is_multipart = false ) {

		if ( ! $this->is_valid_request_method( $method ) ) {
			throw new DomainException( sprintf( 'Invalid request $method: %s; should be one of %s', $method, implode( ',', $this->request_methods ) ) );
		}

		if ( ! is_array( $headers ) ) {
			$headers = array();
		}

		if ( $is_multipart ) {
			$headers[] = 'Content-Type: multipart/form-data';
		}

		// TODO: set UA to identify requests

		return $this->api_transport->send_request( $url, $method, $post_data, $headers );
	}

	private function build_url( $url, $path, $params ) {
		if ( $path ) {
			$url = sprintf( '%s/%s', rtrim( $url, '/\\' ), ltrim( $path, '/\\' ) );
		}

		if ( ! parse_url( $url, PHP_URL_QUERY ) ) {
			$url .= '?';
		}

		$url .= http_build_query( $params );

		return $url; 
	}

	private function is_valid_request_method( $method ) {
		return in_array( $method, $this->get_valid_request_methods() );
	}

	private function get_valid_request_methods() {
		return array( self::REQUEST_METHOD_GET, self::REQUEST_METHOD_POST );
	}

	public function request_access_token( $authorization_code, $redirect_uri ) {
		$post_data = array(
			'client_id'     => $this->auth_key,
			'client_secret' => $this->auth_secret,
			'redirect_uri'  => $redirect_uri,
			'code'          => $authorization_code,
			'grant_type'    => 'authorization_code'
		);

		return $this->send_request( $this->oauth_base_url, OAUTH_ACCESS_TOKEN_ENDPOINT, self::REQUEST_METHOD_POST, null, $post_data, false );
	}

	public function get_blog_auth_url( $blog_url, $redirect_uri ) {
		if ( empty( $this->auth_key ) ) {
			throw new BadMethodCallException( 'Please specify a valid auth_key.' );
		}

		return $this->build_url( $this->oauth_base_url, self::OAUTH_AUTHORIZE_ENDPOINT, array(
			'blog' => $blog_url,
			'client_id' => $this->auth_key,
			'redirect_uri' => $redirect_uri,
			'response_type' => 'code',
		) );
	}
}

