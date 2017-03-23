<?php

/**
 * Plugin Name: What's in my database?
 * Description: Gives a general outline of what's in your database.
 * Version: 1.1
 * Author: @tharsheblows
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'init', function(){
	// only activate the plugin if the current user can list users. This is admins only.
	if ( current_user_can( 'list_users' ) && is_admin() ) {
		require_once( plugin_dir_path( __FILE__ ) . 'class-mjj-whats-in-my-database.php' );
		MJJ_Whats_In_My_Database::get_instance();
	}
}, 10);
