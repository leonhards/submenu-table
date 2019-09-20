<?php
/**
 * Plugin Name: Submenu Table
 * Plugin URI: 
 * Description: Add submenu page with dynamic CRUD for defined table
 * Version: 1.0
 * Author: Leonhard Sinaga
 * Author URI:
 * License: Copyright 2019
 * Text Domain: submenutable
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Cheatin\' uh?' );
}

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'STBL_DB_VERSION', '1.0' );
define( 'STBL_TEXT_DOMAIN', 'submenutable' );

/**
 * HOW TO USE THIS PLUGIN:
 * 1. Define custom variable for 'tables' and 'post_type' below.
 * 2. Create table(s) in /includes/db_setup.php by using variable name for table defined on number 1.
*/

/** == Start: Custom variable for 'tables' and 'post_type' == */
// Custom variables for 'tables
define( 'STBL_VEHICLE_MAKE_TABLE', 'vehicle_make' );
define( 'STBL_VEHICLE_MODEL_TABLE', 'vehicle_model' );

// Custom variables for 'post_type' you want this plugin to be assigned
define( 'STBL_POST_TYPE', 'vehicledt' );
/** == End: Custom variable here == */

// Define static variable for server filesystem like PHP.
define( 'STBL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Define static variable for client filesystem like load images, JS, and the like.
define( 'STBL__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Install table on plugin activation
register_activation_hook( __FILE__, 'stbl_plugin_activation' );
function stbl_plugin_activation() {
    require_once( STBL__PLUGIN_DIR . "includes/db_setup.php" );
    stbl_plugin_install_db();
}

// include all required files
require_once( STBL__PLUGIN_DIR . 'includes/class-stbl-page.php' );
require_once( STBL__PLUGIN_DIR . 'includes/class-stbl-loader.php' );
require_once( STBL__PLUGIN_DIR . 'includes/class-stbl-db.php' );