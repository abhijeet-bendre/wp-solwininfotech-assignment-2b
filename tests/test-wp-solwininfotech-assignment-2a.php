<?php
/**
 * Class Wp_Solwininfotech_Assignment_2b_Test
 *
 * @package Wp_Solwininfotech_Assignment_2b
 */

/**
 * Solwininfotech Assignment 2b test case.
 */
class Wp_Solwininfotech_Assignment_2b_Test extends WP_UnitTestCase {

	/**
	 * Test if plugin is active.
	 */
	function test_is_plugin_active() {
		$this->assertTrue( is_plugin_active( WPSA_PLUGIN_NAME . '/' . WPSA_PLUGIN_NAME. 'php' ) );
	}


}
