<?php

require( dirname( dirname( __FILE__ ) ) . '/class-wpcom-rest-client.php' );

class WPCOM_REST_Transport_Mock extends WPCOM_REST_Transport {
	public function send_request( $url, $method, $post_data = array(), $headers = array() ) {
	}
}
