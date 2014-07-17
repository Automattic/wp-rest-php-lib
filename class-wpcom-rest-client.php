<?php

// TODO: replace with spl-autoload?
$base_dir =  dirname( __FILE__ );
require_once( $base_dir . '/class-wp-rest-client.php' );
require_once( $base_dir . '/class-wp-rest-request.php' );
require_once( $base_dir . '/class-wpcom-rest-exception.php' );
require_once( $base_dir . '/class-wp-rest-transport.php' );
require_once( $base_dir . '/class-wp-rest-transport-curl.php' );
require_once( $base_dir . '/class-wp-rest-object.php' );
require_once( $base_dir . '/class-wpcom-rest-object-site.php' );
require_once( $base_dir . '/class-wpcom-rest-object-post.php' );
require_once( $base_dir . '/class-wpcom-rest-object-me.php' );
unset( $base_dir );

class WPCOM_Rest_Client extends WP_REST_Client {
	const OAUTH_ACCESS_TOKEN_ENDPOINT = '/token';
	const OAUTH_AUTHORIZE_ENDPOINT = '/authorize';
	const OAUTH_AUTHENTICATE_URL = '/authenticate';

	const DEFAULT_API_BASE_URL = 'https://public-api.wordpress.com/rest';
	const DEFAULT_OAUTH_BASE_URL = 'https://public-api.wordpress.com/oauth2';

	protected $request_methods = array( self::REQUEST_METHOD_GET, self::REQUEST_METHOD_POST );

	private $oauth_base_url = self::DEFAULT_OAUTH_BASE_URL;
	protected $api_base_url = self::DEFAULT_API_BASE_URL;

	private $auth_key;
	private $auth_secret;
	private $auth_token;

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

	protected function authenticate_request( WP_REST_Request &$request ) {
		if ( $this->auth_token ) { 
			$request->add_header( 'Authorization', sprintf( 'Bearer %s', $this->auth_token ) );
		}
	}

	public function request_access_token( $authorization_code, $redirect_uri ) {
		$post_data = array(
			'client_id'     => $this->auth_key,
			'client_secret' => $this->auth_secret,
			'redirect_uri'  => $redirect_uri,
			'code'          => $authorization_code,
			'grant_type'    => 'authorization_code'
		);

		$request = new WP_REST_Request( $this->oauth_base_url, OAUTH_ACCESS_TOKEN_ENDPOINT, self::REQUEST_METHOD_POST, null, $post_data );

		return $this->send_request( $request );
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
