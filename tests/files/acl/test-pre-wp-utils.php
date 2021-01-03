<?php

namespace Automattic\VIP\Files\Acl\Pre_WP_Utils;

use WP_Error;

class VIP_Files_Acl_Pre_Wp_Utils_Test extends \WP_UnitTestCase {
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once( __DIR__ . '/../../files/acl/pre-wp-utils.php' );
	}

	public function get_data__validate_path__invalid() {
		return [
			'null-uri' => [
				null,
				'VIP Files ACL failed due to empty path',
			],
	
			'empty-uri' => [
				'',
				'VIP Files ACL failed due to empty path',
			],
	
			'path-no-wp-content' => [
				'/a/path/to/a/file.jpg',
				'VIP Files ACL failed due to invalid path (for /a/path/to/a/file.jpg)'
			],
	
			'relative-url-with-wp-content' => [
				'en/wp-content/uploads/file.png',
				'VIP Files ACL failed due to relative path (for en/wp-content/uploads/file.png)'
			],
		];
	}
	
	public function get_data__validate_path__valid() {
		return [
			'valid-path' => [
				'/wp-content/uploads/kittens.gif',
			],
	
			'valid-path-nested' => [
				'/wp-content/uploads/subfolder/2099/12/cats.jpg',
			],
	
			'valid-path-subsite' => [
				'/subsite/wp-content/uploads/puppies.png',
			],
	
			/* TODO: not supported yet
			'resized-image' => [
				'/wp-content/uploads/2021/01/dinos-100x100.jpg',
			],
			*/
		];
	}

	public function get_data__sanitize_path() {
		return [
			'valid-path' => [
				'/wp-content/uploads/kittens.gif',
				'kittens.gif',
			],

			'valid-path-nested' => [
				'/wp-content/uploads/subfolder/2099/12/cats.jpg',
				'subfolder/2099/12/cats.jpg',
			],

			'valid-path-subsite' => [
				'/subsite/wp-content/uploads/puppies.png',
				'puppies.png',
			],

			/* TODO: not supported yet
			'resized-image' => [
				'/wp-content/uploads/2021/01/dinos-100x100.jpg',
				'dinos.jpg',
			],
			*/
		];
	}

	/**
	 * @dataProvider get_data__validate_path__invalid
	 */
	public function test__validate_path__invalid( $file_path, $expected_warning ) {
		$this->expectException( \PHPUnit\Framework\Error\Warning::class );
		$this->expectExceptionMessage( $expected_warning );

		$actual_is_valid = validate_path( $file_path );
	
		$this->assertFalse( $actual_is_valid );
	}

	/**
	 * @dataProvider get_data__validate_path__valid
	 */
	public function test__validate_path__valid( $file_path ) {
		$actual_is_valid = validate_path( $file_path );
	
		$this->assertTrue( $actual_is_valid );
	}

	/**
	 * @dataProvider get_data__sanitize_path
	 */
	public function test__sanitize_path( $file_path, $expected_path ) {
		$actual_path = sanitize_path( $file_path );

		$this->assertEquals( $actual_path, $expected_path );
	}
}