<?php
/**
 * Class Vehicle DB for saving data
 * 
 * @package SubMenu_Table
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_SubMenuTable_DB {
	public function __construct( $action = '' ) {
		switch ( $action ) {
			case '':
				self::_list();
				break;
			case 'new':
			case is_numeric( $action ): // If $action = ID is sent, the load data based on the ID
				self::get_data();
				break;
			case 'create':
				self::create();
				break;
			case 'update':
				self::update();
				break;
			case 'delete':
				self::delete();
				break;
		}
	}

	public function _list() {
		global $wpdb;
		$table = $wpdb->prefix . $_GET['page'];

		$this->_list = $wpdb->get_results( "SELECT * FROM {$table}" );
		$this->column = $wpdb->get_col("DESC {$table}", 0);
		$this->_count = !empty( $this->_list ) ? count( $this->_list ) : 0;
	}

	public function get_data() {
		if( $_GET['action'] == 'new' ) {
			$this->new = true;

		// // $action = edit
		} else {
			global $wpdb;
			$table = $wpdb->prefix . $_GET['page'];

			$q = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $_GET['id'] ) );
			$q = stripslashes_deep( $q[0] );
			if( empty($q) ) return '';

			$attributes = get_object_vars( $q );
			foreach( $attributes as $attr => $val ) {
				$this->$attr = $val;
			}

			$this->new = false;
		}
	}

	public function create() {
		$this->saved = false;

		// Use 'check_admin_referer' to verify nonce inside the WP administration area. This will return false or die		
		if ( ! empty( $_POST ) && check_admin_referer( 'nonce_action_crud_form', 'nonce_field_submenu' ) ) {

			global $wpdb;
			$table = $wpdb->prefix . $_GET['page'];

			// Parse the column from $_POST['columns']
			$args = self::parse_column( $_POST );

			$this->saved = $wpdb->insert( $table, $args );
			$this->error = $wpdb->last_error;
			$this->insert_id = $wpdb->insert_id;

		}
	}

	public function update() {
		$this->updated = false;

		// Use 'check_admin_referer' to verify nonce inside the WP administration area. This will return false or die		
		if ( ! empty( $_POST ) && check_admin_referer( 'nonce_action_crud_form', 'nonce_field_submenu' ) ) {

			global $wpdb;
			$table = $wpdb->prefix . $_GET['page'];

			// Parse the column from $_POST['columns']
			$args = self::parse_column( $_POST );
				
			$this->updated = $wpdb->update( $table, $args, array('id' => $_GET['id']) );
			$this->error = $wpdb->last_error;

		}
	}

	public function delete() {
		$this->deleted = false;

		if( empty( $_GET ) || empty( $_GET['id'] ) ) return '';

        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'nonce_delete_data' ) )
			return false;

		global $wpdb;
		$table = $wpdb->prefix . $_GET['page'];

		$this->deleted = $wpdb->delete( $table, array('id' => $_GET['id']) );
	}

	private function parse_column( $post ) {
		$columns = explode( ',', $post['columns'] );
		$args = array();
		foreach( $columns as $key => $column ) {
			$args[$column] = '';
		}

		// Get data of specific columns that is going to insert
		$args = shortcode_atts( $args, $post );

		return $args;
	}
}