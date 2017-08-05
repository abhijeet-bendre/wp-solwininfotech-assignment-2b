<?php
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

class Wp_Solwininfotech_Assignment_2b
{
	private static $_tblname                = 'assignment_2b_ticket_booking';
	private $ticket_checkbox_name           = 'wpsa_cf7_addon';
	private $ticket_checkbox_option_name    = "wpsa_cf7_checkbox_field_";
	private $ticket_addon_table_primary_key = 1;

	public function __construct(  )
	{
		add_action("wp_enqueue_scripts",array($this, 'wpsa_init_assets'));
		add_filter( 'wpcf7_form_elements',  array( $this, 'enable_custom_wpcf7_shortcodes' ));
		add_shortcode( 'ticket_book_cf7', array( $this,'ticket_book_cf7' ));
		add_action( 'wpcf7_submit', array( $this, 'action_wpcf7_submit'));
		add_filter( 'wpcf7_load_js', '__return_false' );
	}

	function wpsa_init_assets(  ) {
		wp_register_style( 'wpsa_assignment_2b_main', plugin_dir_url( __FILE__ ).'assets/css/wpsa_main.css',null );
		wp_enqueue_style( 'wpsa_assignment_2b_main' );
	}

	function enable_custom_wpcf7_shortcodes( $form ) {
		$form = do_shortcode( $form );
		return $form;
	}

	function ticket_book_cf7(  ) {

		$ticket_book_cf7_short_code = "";
		$saved_ticket_checkboxes = $this->get_saved_ticket_checkboxes();
		if( $saved_ticket_checkboxes !== "" )
		{
			for ( $i=1; $i <= 10 ; $i++ ) {
				$checkbox_name = "$this->ticket_checkbox_name[$this->ticket_checkbox_option_name$i]";
				$ticket_book_cf7_short_code .= "<div class='wpsa_cf7_checkbox'>";
				$ticket_book_cf7_short_code .= "<label for='$checkbox_name'> ticket number {$i}</label>";
				$ticket_book_cf7_short_code .= "<input type='checkbox' name='$checkbox_name' ";
				if(in_array( $i, $saved_ticket_checkboxes) ) {
					$ticket_book_cf7_short_code .= "checked=checked ";
					$ticket_book_cf7_short_code .= "disabled=disabled";
				}
				$ticket_book_cf7_short_code .= " value='1'>";
				$ticket_book_cf7_short_code .= "</div>";
			}
			return $ticket_book_cf7_short_code;
		} else {
			$error = '<div class="error notice">';
			$error .= __( 'An error occoured. Please contact Administrator! ', 'wp_solwininfotech_assignment_2b' );
			$error .= '</div>';
			echo $error;
		}

	}

	function action_wpcf7_submit( $instance, $result ) {
		global $table_prefix, $wpdb;
		$columns_to_update = array(  );
		$field_id          = "";

		$ticket_checkboxes_selected = $_POST[$this->ticket_checkbox_name];
		if( !empty($ticket_checkboxes_selected) ) {
			foreach ($ticket_checkboxes_selected as $ticket_checkbox_key => $ticket_checkbox_value ) {
				$field_id = strchr( $ticket_checkbox_key , $this->ticket_checkbox_option_name );
				$field_id = explode( "_" , $ticket_checkbox_key );
				$columns_to_update[ "field_".(int)$field_id[4] ] = 1;
			}
			$wpdb->update( $table_prefix."".self::$_tblname, $columns_to_update,  array( 'ID' => $this->ticket_addon_table_primary_key ),"%s" )	;
			}
		}

	  public function get_saved_ticket_checkboxes( ) {
			global $table_prefix, $wpdb;
			$saved_ticket_checkboxes = array();
			$saved_ticket_checkboxes_sql = "";
			$wp_track_table = $table_prefix.self::$_tblname;

			//chek if table is exists
			$table_exists = $wpdb->get_var( "show tables like '$wp_track_table'" );

			if( $table_exists != "")
			{
				$saved_ticket_checkboxes_sql = "SELECT * from $table_prefix".self::$_tblname." where id = {$this->ticket_addon_table_primary_key}";
				$results = $wpdb->get_results ( $saved_ticket_checkboxes_sql, ARRAY_N  );
				if( !empty( $results ))
				{
					$saved_ticket_checkboxes = array_keys( $results[0], "1");
				}
			} else {
				$saved_ticket_checkboxes = "";
			}
			return $saved_ticket_checkboxes;
		}

		public static function create_plugin_database_tables( )
		{
			global $table_prefix, $wpdb, $wpsa_2b_db_version;

			$wp_track_table = $table_prefix.self::$_tblname;
			$charset_collate = $wpdb->get_charset_collate();
			$columns_to_insert  = "";

			#Check to see if the table exists already, if not, then create it
			if( $wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table)
			{
				$create_sql = "CREATE TABLE `".$wp_track_table."` (";
				$create_sql .= "`id` INT(11) NOT NULL AUTO_INCREMENT ,";
				for ($i = 1; $i <=100 ; $i++) {
					$create_sql .= "`field_".$i."` CHAR(1) NOT NULL DEFAULT '0' ,";
				}
				$create_sql .= "PRIMARY KEY (`id`)) ".$charset_collate;
				require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

				dbDelta( $create_sql );

				for ($i = 1; $i <=100 ; $i++) {
					$columns_to_insert[ "field_{$i}" ] = 0;
				}
				$wpdb->insert( $wp_track_table, $columns_to_insert, array( '%s' )	);
				add_option( 'wpsa_2b_db_version', $wpsa_2b_db_version );
			}

		}
	}

	new Wp_Solwininfotech_Assignment_2b( );

	global $wpsa_2b_db_version;
	global $columns_to_update, $columns_to_update_format;
	$wpsa_2b_db_version = "1.0";
	//Wp_Solwininfotech_Assignment_2b::create_plugin_database_tables();
	register_activation_hook( __FILE__, array('Wp_Solwininfotech_Assignment_2b', 'create_plugin_database_tables' ));
