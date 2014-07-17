<?php

class WpcomRestClientTest extends PHPUnit_Framework_TestCase {
	private $client;

	protected function setUp() {
		$this->client = new WPCOM_REST_Client;
	}
	protected function tearDown() {
		$this->client = null;
	}

	public function testInstanceHasCurlTransportAsDefault() {
		$this->assertTrue( $this->client->get_api_transport() instanceof WP_REST_Transport_Curl );
	}

	public function testSetTransport() {
		$transport = new WP_REST_Transport_Mock;
		$this->client->set_api_transport( $transport ); 
		$this->assertEquals( $transport, $this->client->get_api_transport() );
	}

	public function testSetAuthKey() {
		$this->assertNull( $this->client->get_auth_key() );
		$this->assertNull( $this->client->get_auth_key() );

		$this->client->set_auth_key( 'foo', 'bar' );
		$this->assertEquals( 'foo', $this->client->get_auth_key() );
		$this->assertEquals( 'bar', $this->client->get_auth_secret() );
	}

	public function testSetAuthToken() {
		$this->assertNull( $this->client->get_auth_token() );

		$this->client->set_auth_token( 'foo' );
		$this->assertEquals( 'foo', $this->client->get_auth_token() );
	}

	public function testSetBaseApiUrl() {
		$this->assertEquals( WPCOM_REST_Client::DEFAULT_API_BASE_URL, $this->client->get_api_base_url() );
		$new_api_base_url = 'http://example.com'; 
		$this->client->set_api_base_url( $new_api_base_url );
		$this->assertEquals( $new_api_base_url, $this->client->get_api_base_url() );

	}

	public function testSetOauthUrl() {
		$this->assertEquals( WPCOM_REST_Client::DEFAULT_OAUTH_BASE_URL, $this->client->get_oauth_base_url() );
		$new_oauth_base_url = 'http://example.com'; 
		$this->client->set_oauth_base_url( $new_oauth_base_url );
		$this->assertEquals( $new_oauth_base_url, $this->client->get_oauth_base_url() );
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testGetBlogAuthUrlWithoutKey() {
		$auth_url = $this->client->get_blog_auth_url( 'example.com', 'example.org' );
	}

	public function testGetBlogAuthUrl() {
		$this->client->set_auth_key( 'foo', 'bar' );
		$auth_url = $this->client->get_blog_auth_url( 'example.com', 'example.org' );
		$this->assertEquals( 'https://public-api.wordpress.com/oauth2/authorize?blog=example.com&client_id=foo&redirect_uri=example.org&response_type=code', $auth_url );
	}
}
