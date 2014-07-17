<?php

class WP_REST_Exception extends Exception {
	public function __construct( $message, $code = 0, Exception $previous = null ) {
		// Mandatory message
        	parent::__construct( $message, $code, $previous );
	}
}
