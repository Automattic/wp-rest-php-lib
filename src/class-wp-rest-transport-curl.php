<?php

class WP_REST_Transport_Curl extends WP_REST_Transport {

	public function send_request( WP_REST_Request $request ) {
		$curl = curl_init( $request->get_url() );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $request->get_method() );

		if ( $request->has_post_data() ) {
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $request->get_post_data() );
		}

		curl_setopt( $curl, CURLOPT_HTTPHEADER, $request->get_processed_headers() );

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


