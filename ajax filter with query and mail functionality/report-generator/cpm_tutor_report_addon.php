<?php
/** 

* @package Akismet 

*/
/* 

Plugin Name: Tutor Report Generator Addon

Plugin URI: http://codepixelzmedia.com.np// 

Description: Use to calculate the report generator monthly. 

Version: 1.0.0

Author: Codepixelzmedia
*/


/* Main Plugin File */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// define( 'PLUGIN_ROOT_DIR', plugin_dir_path( __FILE__ ) );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


if ( is_plugin_active( 'tutor/tutor.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "report-generator" . DIRECTORY_SEPARATOR  ."cpm_tutor_report_loader.php";
	require_once $init_file;

} else {
	if ( ! function_exists( 'tutor_addon_notification' ) ) {
		function tutor_addon_notification() {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Please install and activate Tutor LMS plugin to use Tutor LMS Addon .', 'tutor-addon' ); ?></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'tutor_addon_notification' );
}

