<?php

class WpcomRestObjectSiteTest extends PHPUnit_Framework_TestCase {
	const TEST_SITE_ID = 4;
	const TEST_SITE_HOST = 'matt.wordpress.com';
	private $client;

	protected function setUp() {
		$this->client = new WPCOM_REST_Client;
	}

	protected function tearDown() {
		$this->client = null;
	}

	public function testInstantiateWithId() {
		$site = WPCOM_REST_Object_Site::withId( self::TEST_SITE_ID, $this->client );
		$this->assertTrue( is_a( $site, 'WPCOM_REST_Object_Site' ) );
	}

	public function testInstantiateWithUrl() {
		$site = WPCOM_REST_Object_Site::withId( self::TEST_SITE_HOST, $this->client );
		$this->assertTrue( is_a( $site, 'WPCOM_REST_Object_Site' ) );
	}

	public function testGet() {
		$site = WPCOM_REST_Object_Site::withId( self::TEST_SITE_ID, $this->client );
		$details = $site->get();
		$this->assertObjectHasAttribute( 'ID', $details );
		$this->assertEquals( self::TEST_SITE_ID, $details->ID );
	}
}