<?php

class WPCOM_Rest_Client {
	const REQUEST_METHOD_GET = 'GET';
	const REQUEST_METHOD_POST = 'POST';

	const OAUTH_ACCESS_TOKEN_ENDPOINT = '/token';
	const OAUTH_AUTHORIZE_ENDPOINT = '/authorize';
	const OAUTH_AUTHENTICATE_URL = '/authenticate';

	const DEFAULT_API_BASE_URL = 'https://public-api.wordpress.com/rest/';
	const DEFAULT_OAUTH_BASE_URL = 'https://public-api.wordpress.com/oauth2';

	private $request_methods = array( 'GET', 'POST' );

	private $api_transport;
	private $oauth_base_url = self::DEFAULT_OAUTH_BASE_URL;
	private $api_base_url = self::DEFAULT_API_BASE_URL;

	private $auth_key;
	private $auth_secret;
	private $auth_token;

	public function __construct() {
		$this->set_api_transport( new WPCOM_REST_API_Transport_Curl ); 
	}
	
	public function set_api_transport( WPCOM_REST_API_Transport $transport ) {
		$this->api_transport = $transport;
	}

	public function set_api_base_url( $url ) {
		$this->api_base_url = $url;
	}

	public function set_oauth_base_url( $url ) {
		$this->oauth_base_url = $url;
	}

	public function set_auth_key( $key, $secret ) {
		$this->auth_key = $key;
		$this->auth_secret = $secret;
	}

	public function set_auth_token( $token ) {
		$this->auth_token = $token;
	}

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

		if ( ! parse_url( $url, PHP_URL_PATH ) ) {
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

	public function get_posts( $site_id, $params ) {
		$url = sprintf( 'v1/sites/%s/posts', $site_id );
		return $this->send_api_request( $url, self::REQUEST_METHOD_GET, $params );
	}

	public function get_post( $site_id, $post_id_or_slug ) {
		if ( is_numeric( $post_id_or_slug ) ) {
			return $this->get_post_by_id( $site_id, $post_id_or_slug );
		} else {
			return $this->get_post_by_slug( $site_id, $post_id_or_slug );
		}
	}

	public function get_post_by_id( $site_id, $post_id ) {
		$url = sprintf( 'v1/sites/%s/posts/%d', $site_id, $post_id );
		return $this->send_api_request( $url, self::REQUEST_METHOD_GET );
	}

	public function get_post_by_slug( $site_id, $post_slug ) {
		$url = sprintf( 'v1/sites/%s/posts/%d', $site_id, $post_slug );

		return $this->send_api_request( $url, self::REQUEST_METHOD_GET );
	}

	public function new_post( $site_id, $post_data ) {	
		$url = sprintf( 'v1/sites/%s/posts/new', $site_id );

		return $this->send_authorized_api_request( $url, self::REQUEST_METHOD_POST, null, $post_data );
	}

	public function update_post( $site_id, $post_id, $post_data ) {
		$edit_post_url = sprintf( 'v1/sites/%s/posts/%d', $site_id, $post_id );

		return $this->send_authorized_api_request( $url, self::REQUEST_METHOD_POST, null, $post_data );
	}

	public function delete_post( $site_id, $post_id ) {
		$url = sprintf( 'v1/sites/%s/posts/%d/delete', $site_id, $post_id );

		return $this->send_authorized_api_request( $url, self::REQUEST_METHOD_POST );
	}

	public function get_user_details() {
		$url = '/me/';

		return $this->send_authorized_api_request( $url, self::REQUEST_METHOD_GET );
	}

	public function get_blog_details( $blog_id ) {
		$url = sprintf( 'v1/sites/%s', $blog_id );

		return $this->send_authorized_api_request( $url, self::REQUEST_METHOD_GET );
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

abstract class WPCOM_REST_API_Transport {
	private $response_codes = array( 200, 301, 302 );	

	abstract public function send_request( $url, $method, $post_data = array(), $headers = array() );

	protected function handle_success( $body ) {
		$decoded_body = json_decode( $body );

		if ( ! $decoded_body ) {
			throw new WPCOM_REST_Exception( 'Failed to decode data from endpoint', 'invalid-json' );
		}

		if ( isset( $decoded->error ) ) {
			if ( isset( $decoded_body->error_description ) ) {
				$error_message = $decoded_body->error_description;
			} elseif ( isset( $decoded_body->message ) ) {
				$error_message = $decoded_body->message;
			} else {
				$error_message = '';
			}
	
			return $this->handle_error( $error_message, $decoded_body->error );
		}

		return $decoded_body;
	}

	protected function handle_error( $message, $code ) {
		throw new WPCOM_REST_Exception( $message, $code );
	}

	protected function is_valid_response_code( $response_code ) {
		return in_array( $response_code, $this->response_codes );
	}
}

class WPCOM_REST_API_Transport_WP_HTTP_API extends WPCOM_REST_API_Transport {

	public function __construct() {
		if ( ! class_exists( 'WP_Http' ) ) {
			throw new BadMethodCallException( 'This transport requires the WordPress HTTP API.' );
		}
	}

	public function send_request( $url, $method, $post_data = array(), $headers = array() ) {
		$args = array(
			'body' => $post_data,
			'headers' => $headers,
		);

		if ( WPCOM_REST_Client::REQUEST_METHOD_GET === $method ) {
			$response = wp_remote_get( $url, $args );
		} elseif ( WPCOM_REST_Client::REQUEST_METHOD_POST === $method ) {
			$response = wp_remote_post( $url, $args );
		}

		$response_code = wp_remote_retrieve_response_code( $response ); 
		$response_body = wp_remote_retrieve_body( $response );
		if ( is_wp_error( $response ) ) {
			return $this->handle_error( $response->get_error_message(), $response->get_error_code() );
		} elseif ( ! $this->is_valid_response_code( $response_code ) ) {
			return $this->handle_error( $response_body, $response_code ); 
		}

		$body = $response_body;
		return $this->handle_success( $body );
	}
}	

class WPCOM_REST_API_Transport_Curl extends WPCOM_REST_API_Transport {

	public function send_request( $url, $method, $post_data = array(), $headers = array() ) {
		$curl = curl_init( $url );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );

		if ( ! empty( $post_data ) ) {
			curl_setopt( $curl, CURLOPT_POST, 1 );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
		}

		$response = curl_exec( $curl );
		$info     = curl_getinfo( $curl );
		$error    = curl_error( $curl );

		curl_close( $curl );

		$response_code = $this->get_response_code_from_request( $info );
		if ( ! $this->is_valid_response_code( $response_code ) ) {
			return $this->handle_error( sprintf( 'HTTP error for request; response: %s', $response ), $response_code );
		} elseif ( ! $response ) {
			return $this->handle_error( sprintf( 'Curl error: %s; info: %s', $error, var_export( $info, true ) ), 'curl-error' );
		}

		return json_decode( $response );
	}

	private function get_response_code_from_request( $info ) {
		if ( is_array( $info ) && isset( $info['http_code'] ) ) {
			return $info['http_code'];
		}

		return null;
	}
}

class WPCOM_REST_Exception extends Exception {
	public function __construct( $message, $code = 0, Exception $previous = null ) {
		// Mandatory message
        	parent::__construct( $message, $code, $previous );
	}
}
