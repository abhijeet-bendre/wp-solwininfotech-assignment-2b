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
	private $tblname = 'assignment_2b_ticket_booking';

	public function __construct(  )
	{
		add_action("wp_enqueue_scripts",array($this, 'wpsa_init_assets'));
		add_filter( 'wpcf7_form_elements',  array( $this, 'enable_custom_wpcf7_shortcodes' ));
		add_shortcode( 'ticket_book_cf7', array( $this,'ticket_book_cf7' ));
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

		$short_code = "";
		for ( $i=0; $i < 100 ; $i++ ) {
			$checkbox_name = $this->checkbox_option_prefix.$i.$this->checkbox_option_postfix;
			$short_code .= "<div class='wpsa_cf7_checkbox'>";
			$short_code .= "<label for='$checkbox_name'> ticket number {$i}</label>";
			$short_code .= "<input type='checkbox' name='$checkbox_name' ";
			if(!empty($options['wpsa_checkbox_field_'.$i])) {
				$short_code .= checked( $options['wpsa_checkbox_field_'.$i], 1 );
			}
			$short_code .= " value='1'>";
			$short_code .= "</div>";
		}
		echo $short_code;
	}

	public static function create_plugin_database_tables()
	{
	    global $table_prefix, $wpdb, $wpsa_2b_db_version;

	    $wp_track_table = $table_prefix.$this>tblname;
			$charset_collate = $wpdb->get_charset_collate();

			#Check to see if the table exists already, if not, then create it
	    if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table)
	    {
					$sql = "CREATE TABLE `".$wp_track_table."` (";
					$sql .= "`id` INT(11) NOT NULL AUTO_INCREMENT ,";
					for ($i=0; $i <=100 ; $i++) {
						$sql .= "`field_".$i."` INT(1) NOT NULL DEFAULT '0' ,";
					}
					$sql .= "PRIMARY KEY (`id`)) ".$charset_collate;
	        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
					//echo $sql;
	        dbDelta($sql);
					add_option( 'wpsa_2b_db_version', $wpsa_2b_db_version );
	    }
	}
}

new Wp_Solwininfotech_Assignment_2b();

global $wpsa_2b_db_version;
$wpsa_2b_db_version = "1.0";
//Wp_Solwininfotech_Assignment_2b::create_plugin_database_tables();
register_activation_hook( __FILE__, array('Wp_Solwininfotech_Assignment_2b', 'create_plugin_database_tables' ));
