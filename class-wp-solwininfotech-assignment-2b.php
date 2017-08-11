<?php
/**
 * Assignment-2b: Ticket booking add-on for Contact Form 7 plugin
 *
 * @package Solwin Infotech Plugin Assignment-2b
 * @version 0.1
 */

/*
Plugin Name: Solwin Infotech Plugin Assignment-2b
Plugin URI:  http://tymescripts.com/solwininfotech
Description: Assignment-2b: Ticket booking add-on for Contact Form 7 plugin
Version:     0.1
Author:      Abhijeet Bendre
Author URI:  http://tymescripts.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: wp_solwininfotech_assignment_2b
*/

	namespace  Wp_Solwininfotech_Assignment_2b;

	// Exit if accessed directly.
	defined( 'ABSPATH' ) || exit;

	/* Global variables and constants */
	global $wpsa_2b_db_version;
	$wpsa_2b_db_version = '1.0';

	define( 'WPSA_PLUGIN_NAME', 'wp-solwininfotech-assignment-2b' );
	define( 'WPSA_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Solwininfotech Assignment 2b Class.
 *
 * @category Class
 *
 * @since 0.1
 */
class Wp_Solwininfotech_Assignment_2b {

	 /**
	  * Tablename for ticket booking addon.
	  *
	  * @var static
	  */
	public static $_tblname = 'assignment_2b_ticket_booking';

	 /**
	  * Checkbox name for ticket booking addon.
	  *
	  * @var string
	  */
	private $ticket_checkbox_name = 'wpsa_cf7_addon';

	 /**
	  * Checkbox option name for ticket booking addon.
	  *
	  * @var string
	  */
	private $ticket_checkbox_option_name = 'wpsa_cf7_checkbox_field_';

	 /**
	 * Primary key for table tablename.
	 *
	 * @var const $ticket_addon_table_primary_key
	 */
	 const TICKET_ADDON_TABLE_PKEY = 1;

	 /**
	 * Primary key for table tablename.
	 *
	 * @var const NO_OF_CHECKBOXES
	 */
	 const NO_OF_CHECKBOXES = 100;

	 /**
	  * Constructor for this class
	  *
	  * @since 0.1
	  */
	public function __construct() {
		 add_action( 'wp_enqueue_scripts', array( $this, 'wpsa_init_assets' ) );
		 add_filter( 'wpcf7_form_elements', array( $this, 'enable_custom_wpcf7_shortcodes' ) );
		 add_shortcode( 'ticket_book_cf7', array( $this, 'ticket_book_cf7' ) );
		 add_action( 'wpcf7_submit', array( $this, 'action_wpcf7_submit' ) );
		 add_filter( 'wpcf7_load_js', '__return_false' );
	}

	 /**
	  * Init assets such as JS/CSS, required by plugin
	  *
	  * @since 0.1
	  */
	public function wpsa_init_assets() {
		 wp_register_style( 'wpsa_assignment_2b_main', plugin_dir_url( __FILE__ ) . 'assets/css/wpsa_main.css', null );
		 wp_enqueue_style( 'wpsa_assignment_2b_main' );
	}

	 /**
	  * Render third party shortcodes (such as [ticket_book_cf7]) in cf7 forms (which doesn't render by default).
	  *
	  * @param string $form cf7 form.
	  * @since 0.1
	  */
	public function enable_custom_wpcf7_shortcodes( $form ) {
		 $form = do_shortcode( $form );
		 return $form;
	}

	 /**
	  * Call back function for [ticket_book_cf7] shortcode
	  *
	  * @since 0.1
	  */
	public function ticket_book_cf7() {
		$ticket_book_cf7_short_code = '';
		$saved_ticket_checkboxes = $this->get_saved_ticket_checkboxes();
		for ( $i = 1 ; $i <= self::NO_OF_CHECKBOXES ; $i++ ) {
			$checkbox_name = $this->ticket_checkbox_name . '[' . $this->ticket_checkbox_option_name . $i . ']';
			$ticket_book_cf7_short_code .= "<div class='wpsa_cf7_checkbox'>";
			$ticket_book_cf7_short_code .= "<label for='$checkbox_name'> ticket number {$i}</label>";
			$ticket_book_cf7_short_code .= "<input type='checkbox' name='$checkbox_name' ";
			if ( '' !== $saved_ticket_checkboxes && in_array( $i, $saved_ticket_checkboxes, true ) ) {
				 $ticket_book_cf7_short_code .= 'checked=checked ';
				 $ticket_book_cf7_short_code .= 'disabled=disabled';
			}
			$ticket_book_cf7_short_code .= " value='1'>";
			$ticket_book_cf7_short_code .= '</div>';
		}
		return $ticket_book_cf7_short_code;
	}

	 /**
	  * Hook fired after submmitiing wpcf7 form.
	  *
	  * @since 0.1
	  */
	function action_wpcf7_submit() {
		global $table_prefix, $wpdb;
		$columns_to_update = array();
		$field_id          = '';

		// wpcf7_verify_nonce functon is used instaead of inbuil wp_verify_nonce function.
		// @codingStandardsIgnoreLine
		$wpcf7_nonce   = isset( $_POST['_wpcf7_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpcf7_nonce'] ) ) : ''; // Input var okay; sanitization okay
		// @codingStandardsIgnoreLine
		$wpcf7_form_id = isset( $_POST['_wpcf7'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpcf7'] ) ) : ''; // Input var okay; sanitization okay.

		// @codingStandardsIgnoreLine
		if ( ! wpcf7_verify_nonce( $wpcf7_nonce, $wpcf7_form_id ) ) {
			 wp_die( esc_html( __( 'An error occoured. Please contact Administrator! ', 'wp_solwininfotech_assignment_2b' ) ) );
		}

		// check if wpsa_cf7_addon checkbox field is not empty
		// @codingStandardsIgnoreLine
		if ( ! empty( $_POST[ $this->ticket_checkbox_name ] ) ) { // Input var okay.
			// @codingStandardsIgnoreLine
			$ticket_checkboxes_selected = wp_unslash( $_POST[ $this->ticket_checkbox_name ] ); // Input var okay.

			foreach ( $ticket_checkboxes_selected as $ticket_checkbox_key => $ticket_checkbox_value ) {

				 $field_id = explode( '_', $ticket_checkbox_key );
				 $columns_to_update[ 'field_' . (int) $field_id[4] ] = 1;
			}

			$wpdb->update(
				$table_prefix . '' . self::$_tblname,
				$columns_to_update,
				array(
					'ID' => self::TICKET_ADDON_TABLE_PKEY,
				),
				'%s'
			); // db call ok; no-cache ok.
		}
	}

	 /**
	  * Function for retireving saved ticket checkboxes from database.
	  *
	  * @since 0.1
	  */
	public function get_saved_ticket_checkboxes() {
		 global $table_prefix, $wpdb;
		 $saved_ticket_checkboxes     = array();
		 $saved_ticket_checkboxes_sql = '';
		 $wp_track_table              = $table_prefix . self::$_tblname;

		 // Check if table is exists.
		 $table_exists = $wpdb->get_var( $wpdb->prepare( "show tables like '%s'", $wp_track_table ) ); // db call ok; no-cache ok.

		if ( '' !== $table_exists ) {
			// @codingStandardsIgnoreLine
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT * from `$wp_track_table` where id = %d", self::TICKET_ADDON_TABLE_PKEY ), ARRAY_N ); // db call ok; no-cache ok.
			if ( ! empty( $results ) ) {
				 $saved_ticket_checkboxes = array_keys( $results[0], '1', true );
			}
		} else {
			 $error = '<div class="error notice">';
			 $error .= __( 'An error occoured. Please contact Administrator! ', 'wp_solwininfotech_assignment_2b' );
			 $error .= '</div>';
			 $error .= '<br/>';
			 echo esc_html( $error );
		}
		 return $saved_ticket_checkboxes;
	}

	 /**
	  * A static function for creating plugin tables
	  *
	  * @since 0.1
	  */
	public static function create_plugin_database_tables() {
		 global $table_prefix, $wpdb, $wpsa_2b_db_version;

		$wp_track_table = $table_prefix . self::$_tblname;
		$charset_collate = $wpdb->get_charset_collate();
		$columns_to_insert  = '';

		 // Check to see if the table exists already, if not, then create it.
		$table_exists = $wpdb->get_var( $wpdb->prepare( "show tables like '%s'", $wp_track_table ) ); // db call ok; no-cache ok.
		if ( $table_exists !== $wp_track_table ) {
				$create_sql = 'CREATE TABLE `' . $wp_track_table . '` (';
				$create_sql .= '`id` INT(11) NOT NULL AUTO_INCREMENT ,';
			for ( $i = 1; $i <= self::NO_OF_CHECKBOXES ; $i++ ) {
						$create_sql .= '`field_' . $i . '` CHAR(1) NOT NULL DEFAULT "0" ,';
			}
			$create_sql .= ' PRIMARY KEY (`id`)) ' . $charset_collate;

			include_once ABSPATH . '/wp-admin/includes/upgrade.php';
			$dbdelta = dbDelta( $create_sql );

			for ( $i = 1; $i <= self::NO_OF_CHECKBOXES ; $i++ ) {
					$columns_to_insert[ "field_{$i}" ] = 0;
			}
				$wpdb->insert( $wp_track_table, $columns_to_insert, array( '%s' ) );  // db call ok; no-cache ok.
				add_option( 'wpsa_2b_db_version', $wpsa_2b_db_version );
		}
	}
}

new Wp_Solwininfotech_Assignment_2b();
register_activation_hook( __FILE__, array( 'Wp_Solwininfotech_Assignment_2b', 'create_plugin_database_tables' ) );
