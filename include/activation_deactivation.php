<?php

/**
 * Clean data on activation / deactivation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
register_activation_hook( __FILE__, 'richproductslistandgrid_activation');

function richproductslistandgrid_activation() {

	if( ! current_user_can ( 'activate_plugins' ) ) {
		return;
	} 
	add_option( 'richproductslistandgrid_license_status', 'invalid' );
	add_option( 'richproductslistandgrid_license_key', '' ); 

}

register_uninstall_hook( __FILE__, 'richproductslistandgrid_uninstall');

function richproductslistandgrid_uninstall() {

	delete_option( 'richproductslistandgrid_license_status' );
	delete_option( 'richproductslistandgrid_license_key' ); 
	
}