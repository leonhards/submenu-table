<?php
/**
 * Class Vehicle Loader is used to load url and form
 * 
 * @package SubMenu_Table
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Class_SubMenuTable_Loader {

    private $table;
    private $_post;
    private $_args;

    public function __construct( $args = array() ) {
        $args = wp_parse_args(
                $args,
                array(
                    'id'     => '',
                    'page'   => '',
                    'action' => '',
                    'post_type'   => '',
                    'text_domain' => ''
                )
        );

        // Define columns to modify from the table
        $this->column['vehicle_make'] = array( 'make'=>'text', 'country'=>'text', 'description'=>'text' );
        $this->column['vehicle_model'] = array( 'model'=>'text', 'vehicle_id'=>self::table_lookup('vehicle_make', 'make'), 'model'=> 'text', 'description'=>'text' );

        // Declare public variable
        $this->_args = $args;

        // Load all data for index page
        self::load_page();
    }

    /**
     * Set notice warning based on ID
     * 
     * @param $id
     */
    public function set_notice( $id ) {
        if ( false === get_option( 'submenu_table_admin_notice' ) ) {
            add_option( 'submenu_table_admin_notice', '0' );
        } else {
            update_option( 'submenu_table_admin_notice', $id );
        }
    }

    /**
     * Get notice error or message of any action
     * 
     * @return string $message
     */
    public function get_notice() {
        switch( get_option('submenu_table_admin_notice') ) {
            case '0':
                $class = '';
                $message = '';
                break;
            case '1':
                $class = 'updated';
                $message = __('Data telah berhasil disimpan.', $this->_args['post_type']);
                break;
            case '2':
                $class = 'error';
                $message = __('Data telah berhasil dihapus.', $this->_args['post_type']);
                break;
            case '3':
                $class = 'error';
                $message = __('Error! Data yang Anda input salah. '. get_option('submenu_table_error_msg'), $this->_args['post_type']);
                break;
        }

        return '<div class="'. $class .'"><p>'. $message .'</p></div>';
    }

    /**
     * Set notice wmessage earning based on ID
     * 
     * @param $id
     */
    public function set_error_msg( $err_msg ) {
        if ( false === get_option( 'submenu_table_error_msg' ) ) {
            add_option( 'submenu_table_error_msg', $err_msg );
        } else {
            update_option( 'submenu_table_error_msg', $err_msg );
        }
    }

    /**
     * Load the page based on actions on page load
     * Using $_POST['save_form'] to check if form has been submitted
     * 
     * @param $args
     */
    public function load_page() {
        switch ( $this->_args['action'] ) :
            // If no action, load all data
            case '':
                self::set_notice(0);
                self::load_view();
                break;

            case 'new':
                if( isset( $_POST['save_form'] ) ) {
                    $qry = new Class_SubMenuTable_DB( 'create' );
                    if( $qry->saved ) {
                        self::set_notice(1);
                        wp_safe_redirect( esc_url_raw( self::get_admin_url( 'edit', $qry->insert_id ) ) );
                        exit();
                    } else {
                        self::set_notice(3);
                        self::set_error_msg( $qry->error );
                        wp_safe_redirect( esc_url_raw( self::get_admin_url( 'new' ) ) );
                        exit();
                    }
                } else {
                    self::load_form();
                }
                break;
            
            case 'edit':
                if( isset( $_POST['save_form'] ) ) {
                    $qry = new Class_SubMenuTable_DB( 'update' );
                    if( $qry->updated ) {
                        self::set_notice(1);  
                        self::load_form();
                    } else {
                        self::set_notice(3);
                        self::set_error_msg( $qry->error );
                        self::load_form();
                    }
                } else {
                    self::load_form();
                }
                break;

            case 'delete':
                $qry = new Class_SubMenuTable_DB( 'delete' );
                if( $qry->deleted ) {
                    self::load_view();
                }
                break;
        endswitch;
    }

    /**
     * Customize admin_url path with args
     */
	public function get_admin_url( $action = '', $id = '', $notice = '' ) {

        if( $notice === '' ) {
            $notice = get_option('submenu_table_admin_notice');
            self::set_notice($notice);
        } else {
            self::set_notice($notice);
        }

		$action = ( $action != '' ) ? $action : null;
		$id = ( $id != '' ) ? $id : null;

        $admin_url = get_admin_url( get_current_blog_id(), 'admin.php' );
        $url = add_query_arg( array( 'page' => $this->_args['page'], 'action' => $action, 'id' => $id ), $admin_url );
		return $url;
    }

    /**
     * Get admin url for base custom page
     */
    public function get_custom_page_admin_url( $action = '', $id = '' ) {
        $action = ( $action != '' ) ? $action : NULL;
        $id = ( $id != '' ) ? $id : NULL;
        $admin_url = get_admin_url( get_current_blog_id(), 'edit.php' );
        $url = add_query_arg( array( 'post_type' => $this->_args['post_type'], 'page' => $this->_args['page'], 'action' => $action, 'id' => $id ), $admin_url );

        return $url;
    }
    
    /**
     * Load view for index table page
     */
    protected function load_view() {

        // Load all data
        $load = new Class_SubMenuTable_DB();
        $list = $load->_list;

        ?>
        <div class="wrap">
            <h2 class="left"><?php echo ucwords( str_replace( '_', ' ', $this->_args['page'] ) ); ?> <a class="page-title-action" href="<?php echo esc_url( self::get_admin_url( 'new', '', '0' ) ); ?>">+ <?php _e( 'Add New', $this->_args['text_domain'] ); ?></a></h2>
            <div class="clear"></div>
            <hr />

            <?php echo self::get_notice(); ?>
      
            <table id="mytable" class="widefat display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php
                        foreach( $this->column[$this->_args['page']] as $col => $type ) :
                            echo '<th>'. ucfirst( $col ) .'</th>';
                        endforeach;
                        ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach( $list as $key => $value ) : ?>
                    <tr>
                        <td></td>
                        <?php foreach( $this->column[$this->_args['page']] as $col => $type ) : ?>
                            <td><?php echo $value->{$col}; ?></td>
                        <?php endforeach; ?>
                        <td>
                            <a href="<?php echo esc_url( self::get_admin_url( 'edit', $value->id, '0' ) ); ?>" title="Edit">
                                <span class="dashicons dashicons-edit"></span>Edit
                            </a>&nbsp;
                            <a href="<?php echo esc_url( wp_nonce_url( self::get_custom_page_admin_url( 'delete', $value->id ), 'nonce_delete_data' ) ); ?>" style="color: #ED4337;" title="Edit" onclick="return confirm('Anda yakin ingin menghapus data ini?')">
                                <span class="dashicons dashicons-no"></span>Delete
                            </a>&nbsp;&nbsp;
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <?php foreach( $this->column[$this->_args['page']] as $col => $type ) :
                            echo '<th>'. ucfirst( $col ) .'</th>';
                        endforeach; ?>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php
    }

    /**
     * Load form to create or edit data
     * For the form submit URL (new), add 'noheader=true' to prevent 'headers already sent...' error
     */
    protected function load_form() {
        global $wpdb;

        // No need to send header when $action = 'new'
        $id = ( $this->_args['action'] == 'new' ) ? '&noheader=true' : '&id='.$this->_args['id'];

        $action = ( $this->_args['action'] == 'edit' ) ? (int) $this->_args['id'] : 'new';
        $data = new Class_SubMenuTable_DB( $action );

        ?>
        <div class="wrap">
            <h2 class="left"><?php echo ucwords( str_replace( '_', ' ', $this->_args['page'] ) ); ?></h2>
            <div class="clear"></div>
            <hr />

            <?php echo self::get_notice(); ?>

            <form method="post" action="<?php echo esc_url( self::get_admin_url( $this->_args['action'] ).$id ); ?>">
                <table class="form-table">
                    <tbody>
                        <?php foreach( $this->column[$this->_args['page']] as $column => $type ) : ?>
                            <?php $value = ( ! $data->new ) ? $data->{$column} : ''; ?>
                            <tr>
                                <th scope="row"><?php echo ucfirst( $column ); ?></th>
                                <td><?php echo self::get_element( $type, $column, $value ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="id" value="<?php echo $this->_args['id']; ?>" />
                <input type="hidden" name="save_form" value="<?php echo $this->_args['action']; ?>" />
                <input type="hidden" name="columns" value="<?php echo implode(',', array_keys($this->column[$this->_args['page']]) ); ?>" />

                <div>
                    <p>
                        <?php wp_nonce_field( 'nonce_action_crud_form', 'nonce_field_submenu' ); ?>
                        <?php $button = ( $this->_args['action'] == 'new' ) ? 'Add New' : 'Save'; ?>
                        <input class="button-primary" class="left" type="submit" value="<?php echo $button; ?>" />&nbsp;
                        <a class="button" href="<?php echo self::get_custom_page_admin_url(); ?>">&lsaquo; Back</a>&nbsp;
                    </p>
                </div>
            </form>
        </div>
    <?php
    }

    /**
     * Create element form
     */
    protected function get_element( $type, $columnName, $value ) {
        switch( $type ) {
            case 'text':
                $element = '<input name="'. $columnName .'" type="text" class="regular-text" autocomplete="off" value="'. $value .'" />';
                break;
            case 'select':
                $option = '';
                foreach( $this->lookup as $key => $value ) {
                    $option .= '<option value='. $value->id .'>'. $value->make .'</option>';
                }
                $element = '<select name="'. $columnName .'">'. $option .'</select>';
                break;
        }

        return $element;
    }

    protected function table_lookup( $table, $column ) {
        global $wpdb;
        $table = $wpdb->prefix . $table;

        $this->lookup = $wpdb->get_results( "SELECT id, {$column} FROM {$table}" );

        return 'select';
    }
}