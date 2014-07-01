<?php

class WpcomRestClientTest extends PHPUnit_Framework_TestCase {
	public function testInstanceHasCurlTransportAsDefault() {
		$client = new WPCOM_REST_Client;
		$this->assertTrue( $client->get_api_transport() instanceof WPCOM_REST_Transport_Curl );
	}

	public function testSetAuthKey() {
		$client = new WPCOM_REST_Client;
		$this->assertNull( $client->get_auth_key() );
		$this->assertNull( $client->get_auth_key() );

		$client->set_auth_key( 'foo', 'bar' );
		$this->assertEquals( 'foo', $client->get_auth_key() );
		$this->assertEquals( 'bar', $client->get_auth_secret() );
	}
}
