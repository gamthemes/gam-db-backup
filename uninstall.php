<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

wp_clear_scheduled_hook( 'auto_gam_db_backup_schedules' );
delete_option( 'gam_db_backup_schedules_options' );