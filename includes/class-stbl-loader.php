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

        /**
         * Define columns to display and modify and what table to join.
         * 
         * @param $this->columnDef :: Define columns to modify on 'new' or 'edit' page.
         * @param $this->columnJoin :: Define column to join from '$this->columnDef' and display on index page (list)
         */
        switch( $args['page'] ) {
            case 'vehicle_make':
                $this->columnDef['vehicle_make'] = array( 'make' => 'text', 'country' => 'text', 'description' => 'text' );
                break;
            case 'vehicle_model':
                $this->columnDef['vehicle_model'] = array( 'vehicle_id' => self::table_lookup( 'vehicle_make', 'make' ), 'model'=> 'text', 'description' => 'text' );
                $this->columnJoin['vehicle_model'] = array( 'vehicle_id' => array( 'vehicle_make', 'make' ) );
                break;
            case 'gold_vendor':
                $this->columnDef['gold_vendor'] = array( 'vendor' => 'text', 'color' => 'text', 'image_url' => 'text' );
                break;
            case 'gold_vendor_data':
                $this->columnDef['gold_vendor_data'] = array( 'vendor_id' => self::table_lookup('gold_vendor', 'vendor'), 'buy_price' => 'text', 'sell_price' => 'text', 'date' => 'date');
                $this->columnJoin['gold_vendor_data'] = array( 'vendor_id' => array( 'gold_vendor', 'vendor' ) );
                break;
        }

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
                $message = __('Error! Data yang Anda input sudah ada atau mungkin tidak valid. '. get_option('submenu_table_error_msg'), $this->_args['post_type']);
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
                        wp_safe_redirect( esc_url_raw( self::get_admin_url( 'edit', $qry->insert_id, true ) ) );
                        exit();
                    } else {
                        self::set_error_msg( $qry->error );
                        wp_safe_redirect( esc_url_raw( self::get_admin_url( 'new', 'error' ) ) );
                        exit();
                    }
                } else {
                    if( $this->_args['id'] == 'error' )
                        self::set_notice(3);
                    else
                        self::set_notice(0);
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
                    if( isset( $_GET['redirect'] ) )
                        self::set_notice(1);
                    else
                        self::set_notice(0);
                    self::load_form();
                }
                break;

            case 'delete':
                $qry = new Class_SubMenuTable_DB( 'delete' );
                if( $qry->deleted ) {
                    self::set_notice(2);
                    self::load_view();
                }
                break;
        endswitch;
    }

    /**
     * Customize admin_url path with args
     * 
     * @param $action :: get action from URL
     * @param $id :: get id from URL
     * @param $redirect :: if it is redirection set value to 'true'. Default: 'false'.
     */
    public function get_admin_url( $action = '', $id = '', $redirect = false ) {

        $action = ( $action != '' ) ? $action : null;
        $id = ( $id != '' ) ? $id : null;
        $redirect = ( $redirect ) ? 'true' : null;

        $admin_url = get_admin_url( get_current_blog_id(), 'admin.php' );
        $url = add_query_arg( array( 'page' => $this->_args['page'], 'action' => $action, 'id' => $id, 'redirect' => $redirect ), $admin_url );

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
        global $wpdb;

        // Load all data
        $load = new Class_SubMenuTable_DB();
        $list = $load->_list;

        ?>
        <div class="wrap">
            <h2 class="left"><?php echo ucwords( str_replace( '_', ' ', $this->_args['page'] ) ); ?> <a class="page-title-action" href="<?php echo esc_url( self::get_admin_url( 'new' ) ); ?>">+ <?php _e( 'Add New', $this->_args['text_domain'] ); ?></a></h2>
            <div class="clear"></div>
            <hr />

            <?php echo self::get_notice(); ?>
      
            <table id="mytable" class="widefat display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php
                        foreach( $this->columnDef[$this->_args['page']] as $col => $type ) :
                            if( $type == 'select_lookup' ) {
                                $column_name = $this->columnJoin[$this->_args['page']][$col][1];
                            } else {
                                $column_name = $col;
                            }
                            echo '<th>'. ucwords( str_replace( '_', ' ', $column_name ) ) .'</th>';
                        endforeach;
                        ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach( $list as $key => $value ) : ?>
                    <tr>
                        <td></td>
                        <?php foreach( $this->columnDef[$this->_args['page']] as $col => $type ) : ?>
                            <?php
                            if( $type == 'select_lookup' ) {
                                $table_lookup = $wpdb->prefix . $this->columnJoin[$this->_args['page']][$col][0];
                                $column_lookup = $this->columnJoin[$this->_args['page']][$col][1];
                                $row_data =$wpdb->get_var( $wpdb->prepare( "SELECT {$column_lookup} from {$table_lookup} where id = %d", $value->{$col} ) );
                            } else {
                                $row_data = $value->{$col};
                            }
                            ?>
                            <td><?php echo $row_data; ?></td>
                        <?php endforeach; ?>
                        <td>
                            <a href="<?php echo esc_url( self::get_admin_url( 'edit', $value->id ) ); ?>" title="Edit">
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
                        <?php
                        foreach( $this->columnDef[$this->_args['page']] as $col => $type ) :
                            if( $type == 'select_lookup' ) {
                                $column_name = $this->columnJoin[$this->_args['page']][$col][1];
                            } else {
                                $column_name = $col;
                            }
                            echo '<th>'. ucwords( str_replace( '_', ' ', $column_name ) ) .'</th>';
                        endforeach;
                        ?>
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
        // No need to send header when $action = 'new'
        $id = ( $this->_args['action'] == 'new' ) ? '&noheader=true' : '&id='.$this->_args['id'];

        $action = ( $this->_args['action'] == 'edit' ) ? (int) $this->_args['id'] : 'new';
        $data = new Class_SubMenuTable_DB( $action );

        ?>
        <div class="wrap">
            <h2 class="left"><?php echo ucwords( str_replace( '_', ' ', $this->_args['page'] ) ); ?>
                <?php if( $this->_args['action'] == 'edit' ) : ?>
                    <a class="page-title-action" href="<?php echo esc_url( self::get_admin_url( 'new' ) ); ?>">+ <?php _e( 'Add New', $this->_args['text_domain'] ); ?></a>
                <?php endif; ?>
            </h2>
            <div class="clear"></div>
            <hr />

            <?php echo self::get_notice(); ?>

            <form method="post" action="<?php echo esc_url( self::get_admin_url( $this->_args['action'] ).$id ); ?>">
                <table class="form-table">
                    <tbody>
                        <?php foreach( $this->columnDef[$this->_args['page']] as $col => $type ) : ?>
                            <?php $value = ( ! $data->new ) ? $data->{$col} : ''; ?>
                            <tr>
                                <?php
                                if( $type == 'select_lookup' ) {
                                    $column_name = $this->columnJoin[$this->_args['page']][$col][1];
                                } else {
                                    $column_name = $col;
                                }
                                echo '<th scope="row">'. ucwords( str_replace( '_', ' ', $column_name ) ) .'</th>';
                                ?>
                                <td><?php echo self::get_element( $type, $col, $value ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="id" value="<?php echo $this->_args['id']; ?>" />
                <input type="hidden" name="save_form" value="<?php echo $this->_args['action']; ?>" />
                <input type="hidden" name="columns" value="<?php echo implode(',', array_keys($this->columnDef[$this->_args['page']]) ); ?>" />

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
            case 'select_lookup':
                $option = '';
                foreach( $this->lookup as $key => $data ) {
                    if( $data->id == $value )
                        $option .= '<option value='. $data->id .' selected>'. $data->{$this->columnLookup} .'</option>';
                    else
                        $option .= '<option value='. $data->id .'>'. $data->{$this->columnLookup} .'</option>';
                }
                $element = '<select name="'. $columnName .'">'. $option .'</select>';
                break;
            case 'date':
                $element = 
                    '<select id="calendarDay"></select>' .
                    '<select id="calendarMonth"></select>' .
                    '<select id="calendarYear"></select>' .
                    '<input type="hidden" class="hidden form-control calendarInput" name="'. $columnName .'" id="'. $columnName .'" value="'. $value .'">';
                break;
        }

        return $element;
    }

    protected function table_lookup( $table, $column ) {
        global $wpdb;
        $table = $wpdb->prefix . $table;

        $this->lookup = $wpdb->get_results( "SELECT id, {$column} FROM {$table}" );
        $this->columnLookup = $column;

        return 'select_lookup';
    }
}