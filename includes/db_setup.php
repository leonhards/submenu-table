<?php
/**
 * Setup database tables on plugin activation.
 * upgrade.php must be included
 * 
 * @package SubMenu_Table
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function stbl_plugin_install_db() {
    global $wpdb, $stbl_db_version;
    
    $sql = array();
    $charset_collate = $wpdb->get_charset_collate();
    
    require( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql[] = "CREATE TABLE {$wpdb->prefix}". STBL_VEHICLE_MAKE_TABLE ." (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                make VARCHAR(50) NOT NULL DEFAULT '',
                country VARCHAR(50) NOT NULL DEFAULT '',
                description LONGTEXT NOT NULL DEFAULT '',
                UNIQUE KEY make (make)
            ) {$charset_collate};";
            
    $sql[] = "CREATE TABLE {$wpdb->prefix}". STBL_VEHICLE_MODEL_TABLE ." (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                vehicle_id BIGINT(20) NOT NULL DEFAULT '0',
                model VARCHAR(50) NOT NULL DEFAULT '',
                description LONGTEXT NOT NULL DEFAULT '',
                UNIQUE KEY model (model)
			) {$charset_collate};";

	dbDelta( $sql );

	add_option( "stbl_db_version", STBL_DB_VERSION );
}