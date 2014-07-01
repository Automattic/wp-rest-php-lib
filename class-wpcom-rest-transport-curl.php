<?php

class WPCOM_REST_Transport_Curl extends WPCOM_REST_Transport {

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


