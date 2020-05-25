<?php
/**
 * Class TestStorage
 *
 * @since   1.0.0
 * @package Awesome9\Notifications
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Notifications\Test;

use Awesome9\Notifications\Storage;

/**
 * Notifications test case.
 */
class TestStorage extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->storage = new Storage( 'awesome9_plugin_notifications' );
	}

	/**
	 * @expectedException ArgumentCountError
	 */
	public function test_should_throw_if_config_not_set_exception() {
		( new Storage() )->hooks();
	}

	public function test_notification_not_exists() {
		$this->assertNull( $this->storage->get_by_id( 'test' ) );
	}

	public function test_add_notification() {
		$this->storage->add( 'Test message.', [ 'id' => 'test' ] );
		$this->assertNotNull( $this->storage->get_by_id( 'test' ) );

		$this->assertArrayEquals(
			$this->storage->get_notifications(),
			[ [ 'message' => 'Test message.' ] ]
		);
	}

	public function test_unable_to_add_notification_save_id() {
		$this->storage->add( 'Test message.', [ 'id' => 'test' ] );
		$this->assertNull( $this->storage->add( 'Test message.', [ 'id' => 'test' ] ) );
	}

	public function test_remove_notification() {
		$this->storage->add( 'Test message.', [ 'id' => 'test' ] );
		$this->storage->remove( 'test' );
		$this->assertNotNull( $this->storage->get_by_id( 'test' ) );
	}

	public function assertArrayEquals( $array1, $array2 ) {
		$this->assertEquals( json_encode( $array1 ), json_encode( $array2 ) );
	}

	public function getPrivate( $obj, $attribute ) {
		$getter = function() use ( $attribute ) {
			return $this->$attribute;
		};
		$get = \Closure::bind( $getter, $obj, get_class( $obj ) );
		return $get();
	}
}
