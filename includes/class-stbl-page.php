<?php
/**
 * Create submenu page for tables.
 *
 * @package SubMenu_Table
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_SubMenuTable_Page {

    public $post_type;
    public $text_domain;

    public function __construct() {
        $this->post_type = STBL_POST_TYPE;
        $this->text_domain = STBL_TEXT_DOMAIN;

        // Use 'load_plugin_textdomain' for setting up plugin's text domain
        load_plugin_textdomain( $this->text_domain, false, STBL__PLUGIN_DIR . 'languages/' );

        // Enqueue datatables for admin screens
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_custom_page' ) );

        // Add submenu page for CPT
        add_action( 'admin_menu', array( $this, 'custom_submenu_page' ) );

    }

    /**
     * Enqueue for specific custom post type in Admin
     */
    public function enqueue_admin_custom_page() {
        global $current_screen;

        // If this is admin and post_type = '{$this->post_type}'
        if( is_admin() && is_object( $current_screen ) && $this->post_type == $current_screen->post_type )
        {
            wp_enqueue_style( 'stbl-dt-style', STBL__PLUGIN_URL . 'assets/css/jquery.dataTables.min.css', '', '' );
            wp_enqueue_style( 'stbl-style', STBL__PLUGIN_URL . 'assets/css/style.css', '', '' );

            wp_enqueue_script( 'stbl-dt-js', STBL__PLUGIN_URL . 'assets/js/jquery.dataTables.min.js', array(), '', true );
            wp_enqueue_script( 'stbl-js', STBL__PLUGIN_URL . 'assets/js/script.js', array(), '', true );
        }
    }

    /**
     * How to create custom submenu page for current CPT
     * 1. Add submenu page below
     * 2. Set 'page' value as table name variable that is defined on 'submenu-table.php'
     * 3. Define columns to modify from the table: Class_Vehicle_Loader::__construct();
     * 4. Refresh the page.
     */
    public function custom_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            __( 'Make / Brand', $this->text_domain ),
            __( 'Make / Brand', $this->text_domain ),
            'manage_options',
            STBL_VEHICLE_MAKE_TABLE,
            array( $this, 'custom_table_view' )
        );

        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            __( 'Model', $this->text_domain ),
            __( 'Model', $this->text_domain ),
            'manage_options',
            STBL_VEHICLE_MODEL_TABLE,
            array( $this, 'custom_table_view' )
        );
    }

    // Load custom table view of current page
    public function custom_table_view() {
        if ( !current_user_can( 'manage_options' ) )
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

        $args = array(
            'id'     => !empty( $_GET['id'] ) ? $_GET['id'] : '',
            'page'   => $_GET['page'],
            'action' => !empty( $_GET['action'] ) ? $_GET['action'] : '',
            'post_type'   => $this->post_type,
            'text_domain' => $this->text_domain
        );

        new Class_SubMenuTable_Loader( $args );
    }
}

new Class_SubMenuTable_Page;