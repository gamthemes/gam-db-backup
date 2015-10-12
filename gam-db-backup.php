<?php
/*
 Plugin Name: GAM DB Backup
 Plugin URI:  http://www.gamthemes.com/shop/gam-db-backup/
 Description: Help you to take instant backup. Also you can schedule daily, weekly or monthly backup.
 
 Author: GAM Themes
 Author URI: https://www.gamthemes.com/
 Text Domain: gam-db-backup
 Domain Path: /languages
 Version: 1.0.0
 Since: 1.0.0
 Requires WordPress Version at least: 4.1

 Copyright: 2015 GAM Themes
 License: GNU General Public License v3.0
 License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 
/**
 * GAM-DB_Backup class.
 */
class GAM_DB_Backup {

	/**
	 * Constructor - get the plugin hooked in and ready
	 */
	public function __construct() 
	{
		// Define constants
		define( 'GAM_DB_BACKUP_VERSION', '1.0.0' );
        	define( 'GAM_DB_BACKUP_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
        	define( 'GAM_DB_BACKUP_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		 // Actions
		add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );

		//register script				
		add_action( 'admin_enqueue_scripts',array($this,'backend_scripts') );
		
	   	//addd admin menu
	   	add_action('admin_menu', array($this,'add_plugin_menu'));   		
	
		
		//backup now
		//actoin for logged in user
        	add_action( 'wp_ajax_db_backup_now', 'db_backup_now');
        	
        	//backup scheduled
        	add_filter('cron_schedules', array($this,'cron_schedules_intervals'));
        	
        	
        	$selected_schedule_option = get_option('gam_db_backup_schedules_options');
        	        
        	if ( !empty($selected_schedule_option) && $selected_schedule_option !='none' )
        	{			
  		     wp_schedule_event( time(), $selected_schedule_option, 'auto_gam_db_backup_schedules' );
  		}
		
        	add_action( 'auto_gam_db_backup_schedules',  'db_backup_schedules');
        	
        	add_action( 'wp_ajax_save_selected_schedule_option', 'save_selected_schedule_option');
	}
		

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		$domain = 'gam-db-backup';       
        	$locale = apply_filters('plugin_locale', get_locale(), $domain);
		load_textdomain( $domain, WP_LANG_DIR . "/gam-db-backup/".$domain."-" .$locale. ".mo" );
		load_plugin_textdomain($domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load functions
	 */
	public function include_template_functions() {
		include( 'gam-db-backup-functions.php' );
		include( 'gam-db-backup-template.php' );
		$this->settings_page = new GAM_DB_Backup_Settings();

	}  
    

	
	/**
	 * Register and enqueue scripts and css
	 */
	public function backend_scripts() 
	{
		wp_register_style( 'gam-db-backup-backend-css',  GAM_DB_BACKUP_PLUGIN_URL.'/assets/css/backend.css');
	      	wp_enqueue_style('gam-db-backup-backend-css');	
		  
		//script admin.min.js 
		wp_enqueue_script( 'gam-db-backup-admin-js', GAM_DB_BACKUP_PLUGIN_URL. '/assets/js/backend.min.js', array( 'jquery'), GAM_DB_BACKUP_PLUGIN_URL, true );
          	wp_localize_script( 'gam-db-backup-admin-js', 'gam_db_backup_admin_js', 
								array(
									'ajax_url'      => admin_url( 'admin-ajax.php' ),									
									'loading_message' => __( 'Processing, Please wait...', 'gam-db-backup' )
								     )
		   					);	
	}
    
   	public function add_plugin_menu()
	{
		add_menu_page( 'GAM DB Backup', __( 'GAM DB Backup', 'gam-db-backup' ),7, 'gam-db-backup', array( $this->settings_page, 'output' ) );	

	}
	
	
	/**
	 * The filter accepts an array of arrays. The outer array has a key that is the name of the schedule or for example 'weekly'. 
	 * The value is an array with two keys, one is 'interval' and the other is 'display'.
	 * @access public
	 * @return void
	 */ 
	public function cron_schedules_intervals($schedules) 
	{
		$selected_schedule_option = get_option('gam_db_backup_schedules_options');	
		
		if($selected_schedule_option == 'daily')
		{
				$schedules['daily'] = array(
				'interval' => 86400,
				'display' =>__( 'Once Daily', 'gam-db-backup' )
				);	
		}else if($selected_schedule_option == 'weekly')
		{
				$schedules['weekly'] = array(
				'interval' => 604800,
				'display' =>__( 'Once Weekly', 'gam-db-backup' )
				);
		}
		else if($selected_schedule_option == 'monthly')
		{
				$schedules['monthly'] = array(
				'interval' => 2635200,
				'display' =>__('Once Monthly', 'gam-db-backup' ) 
				);
		}
		
		return $schedules;			
				
	}
	
	
}

$GLOBALS['db_backup'] = new GAM_DB_Backup();

?>