<?php

// TODO: simplify this using autoload?
$base_dir =  dirname( __FILE__ );
require_once( $base_dir . '/class-wp-rest-client.php' );
require_once( $base_dir . '/class-wp-rest-request.php' );
require_once( $base_dir . '/class-wp-rest-exception.php' );
require_once( $base_dir . '/class-wp-rest-transport.php' );
require_once( $base_dir . '/class-wp-rest-transport-curl.php' );
require_once( $base_dir . '/class-wp-rest-object.php' );
require_once( $base_dir . '/class-wpcom-rest-object-site.php' );
require_once( $base_dir . '/class-wpcom-rest-object-post.php' );
require_once( $base_dir . '/class-wpcom-rest-object-me.php' );
require_once( $base_dir . '/class-wpcom-rest-client.php' );
unset( $base_dir );