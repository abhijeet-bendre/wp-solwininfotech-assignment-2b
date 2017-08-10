<?php
/**
 * Class Wp_Solwininfotech_Assignment_2b_Test
 *
 * @package Wp_Solwininfotech_Assignment_2b
 */
use Wp_Solwininfotech_Assignment_2b\Wp_Solwininfotech_Assignment_2b;
/**
 * Solwininfotech Assignment 2b test case.
 */
class Wp_Solwininfotech_Assignment_2b_Test extends WP_UnitTestCase {
		static $wp_track_table;

		/**
		 * Setup of 'setUpBeforeClass' test fixture
		 */
		public static function setUpBeforeClass() {
			global $table_prefix, $wpdb, $wpsa_2b_db_version;
			$charset_collate = $wpdb->get_charset_collate();

			self::$wp_track_table = $table_prefix . Wp_Solwininfotech_Assignment_2b::$_tblname;
			$create_sql = 'CREATE TABLE `' . self::$wp_track_table . '` (';
			$create_sql .= '`id` INT(11) NOT NULL AUTO_INCREMENT ,';
			for ( $i = 1; $i <= Wp_Solwininfotech_Assignment_2b::NO_OF_CHECKBOXES ; $i++ ) {
					$create_sql .= '`field_' . $i . '` CHAR(1) NOT NULL DEFAULT "0" ,';
			}
			$create_sql .= ' PRIMARY KEY (`id`)) ' . $charset_collate;
			//echo $create_sql;
			$wpdb->query($create_sql);
		}

		/**
		* Test if plugin is active.
		*/
		function test_is_plugin_active() {
			$this->assertTrue( is_plugin_active( WPSA_PLUGIN_NAME . '/' . WPSA_PLUGIN_NAME. 'php' ) );
		}

		/**
		* Test if cf7 plugin is active.
		*/
		function test_is_cf7_plugin_active() {
			$this->assertTrue( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) );
		}

		/**
		 * Test if no of checkboxes are 100
		 */
		function test_if_no_of_checkboxes_are_100() {
			$this->assertEquals( 100, Wp_Solwininfotech_Assignment_2b::NO_OF_CHECKBOXES );
		}

		/**
		 * Test if hard coded ticket addon table primary_key is 1
		 */
		function test_ticket_addon_primary_key() {
			$this->assertEquals( 1, Wp_Solwininfotech_Assignment_2b::TICKET_ADDON_TABLE_PKEY );
		}

		/**
		* Setup of 'tearDownAfterClass' test fixture
		*/
		public static function tearDownAfterClass()
		{
			global $wpdb;
			$drop_sql = 'DROP TABLE `' . self::$wp_track_table . '`';
			$wpdb->query($drop_sql);
		}
}
