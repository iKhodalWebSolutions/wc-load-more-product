<?php 
/*
  Plugin Name: Ajax wordpress product listing plugin for woocommerce
  Description: Products list and grid view of the woocommerce
  Author: iKhodal Web Solution
  Plugin URI: https://www.ikhodal.com/ajax-wordpress-product-listing-plugin-for-woocommerce
  Author URI: https://www.ikhodal.com
  Version: 2.1
  Text Domain: richproductslistandgrid
*/ 
  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  


/**
*  Assets of the plugin
*/
$wplg_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'wplg_media', $wplg_plugins_url ); 

/**
*  Plugin DIR
*/
$wplg_plugin_dir = plugin_basename(dirname(__FILE__));

define( 'wplg_plugin_dir', $wplg_plugin_dir );  

 
/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';


///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Register custom product type for shortcode
 */ 
require_once 'include/shortcode.php';

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Category and Product View on frontent pages
 */
require_once 'include/richproductslistandgrid.php'; 

/**
 * Clean data on activation / deactivation
 */
require_once 'include/activation_deactivation.php';  
 