<?php

abstract class WPCOM_REST_Transport {
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


