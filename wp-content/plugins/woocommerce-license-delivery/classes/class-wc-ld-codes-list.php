<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Codes_List
 *
 * structures license codes into native WordPress list
 */
class WC_LD_Codes_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'License Code', 'highthemes' ),
			'plural'   => __( 'License Codes', 'highthemes' ),
			'ajax'     => false

		) );

	}

	public function no_items() {
		_e( 'No  license code avaliable.', 'highthemes' );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'license_status':
			case 'product_id':
				return $item[ $column_name ];
				break;
			case 'sold_date':
				$time = strtotime( $item[ $column_name ] );
				if ( ! empty( $time ) ) {
					$relative = date_i18n( get_option( 'date_format' ), strtotime( $item[ $column_name ] ) );

					return $relative;
				}
				break;

			case 'creation_date':
				$relative = date_i18n( get_option( 'date_format' ), strtotime( $item[ $column_name ] ) );

				return $relative;
				break;
			case 'order_id':
				if ( ! empty( $item['order_id'] ) ) {
					return edit_post_link( __( 'Order #', 'highthemes' ) . $item['order_id'], '', '', $item['order_id'] );
				}

			default:
				return '';
		}
	}

	function column_license_code1( $item ) {

		$delete_nonce    = wp_create_nonce( 'wt_delete_code' );
		$confirm_message = __( 'You are about to delete this item permanently, are you sure?', 'highthemes' );

		// if the license code has been assigned to an order
		$assignment_status = ( $item['order_id'] > 0 ) ? true : false;

		if ( $assignment_status === true ) {
			$confirm_message = __( 'This code has been assigned to order #' . $item['order_id'] . '. You cannot delete it before removing the order.',
				'highthemes' );
		}

		// action links
		$actions = array(
			'edit'   => sprintf( '<a href="?page=license_code_edit&id=%s">%s</a>', $item['id'],
				__( 'Edit', 'highthemes' ) ),
			'delete' => sprintf( '<a onclick="return confirm(\'' . $confirm_message . '\');" href="?page=%s&action=delete&id=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'], $item['id'], $delete_nonce,
				__( 'Delete', 'highthemes' ) ),
		);

		return sprintf( '<strong><a href="?page=license_code_edit&id=%s">%s</a></strong> %s',
			$item['id'],
			 esc_html($item['license_code1']),
			$this->row_actions( $actions )
		);
	}

	// show the product
	function column_product_id( $item ) {

		$product_name = get_the_title( $item['product_id'] );
		$link         = '<a href="' . get_edit_post_link( $item['product_id'] ) . '">' . $product_name . '</a>';

		return $link;

	}

	// the status of the license code
	function column_license_status( $item ) {

		switch ( $item['license_status'] ) {
			case '1': // sold - assigned to an order
				return '<span class="dashicons dashicons-lock"></span>';
			case '0': // free
				return '<span class="dashicons dashicons-yes"></span>';
		}

	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	public function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'license_code1'     => __( 'License Code', 'highthemes' ),
			'creation_date' => __( 'Creation Date', 'highthemes' ),
			'sold_date'     => __( 'Sold Date', 'highthemes' ),
			'license_status'    => __( 'Status', 'highthemes' ),
			'order_id'      => __( 'Order ID', 'highthemes' ),
			'product_id'    => __( 'Product', 'highthemes' )
		);

		return $columns;
	}


	public function get_sortable_columns() {
		$sortable_columns = array(
			'license_code1'     => array( 'license_code1', false ),
			'creation_date' => array( 'creation_date', true ),
			'license_status'    => array( 'license_status', false ),
			'product_id'    => array( 'product_id', false ),
		);

		return $sortable_columns;
	}


	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete'
		);

		return $actions;
	}


	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'codes_per_page', 30 );
		$current_page = $this->get_pagenum();
		$total_items  = WC_LD_Model::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );


		$this->items = WC_LD_Model::get_codes( $per_page, $current_page );
	}

	public function process_bulk_action() {


		//Detect when a delet action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'wt_delete_code' ) ) {
				die( 'Go get a life script kiddies' );
			} else {

				// get the ID
				$license_id      = absint( $_GET['id'] );
				$license_details = WC_LD_Model::get_codes_by_id( $license_id );

				if ( ! empty( $license_details ) ) {
					if ( ! empty( $license_details[0]['order_id'] ) ) {
						echo '<div id="notice" class="error"><p>' . __( "It's not possible to delete a code that is assigned to an existing order. You need to delete the order before removing the license code.",
								"highthemes" ) . '</p></div>';
					} else {
						WC_LD_Model::delete_code( absint( $_GET['id'] ) );
						echo '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d',
								'highthemes' ), count( $_REQUEST['id'] ) ) . '</p></div>';
					}

				}

			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {

			// license ids
			$delete_ids = (empty($_POST['bulk-delete'])) ? '' : esc_sql( $_POST['bulk-delete'] );

			if ( ! empty( $delete_ids ) ) {

				// loop over the array of record IDs and delete them
				$i = 0;
				foreach ( $delete_ids as $id ) {
					$delete = WC_LD_Model::delete_code( $id );
					if ( $delete === true ) {
						$i++;
					}


				}
				if($i === 0) {
					$message = '<div class="notice notice-error " id="error"><p>' .
					           __( 'It\'s not possible to delete a code that is assigned to an existing order. You need to delete the order before removing the license code.',
						           'highthemes' ) . '</p></div>';
				} else {
					$message = '<div class="updated below-h2" id="message"><p>' .
					           __( 'Items deleted: ',
						           'highthemes' ) . $i. '</p></div>';
				}
				echo $message;

			}


		}
	}

	public function extra_tablenav( $which ) {
		$move_on_url = '&product-filter=';
		if ( $which == "top" ) {
			?>
			<div class="alignleft actions bulkactions">
				<?php
				$args     = array( 'post_type' => 'product', 'posts_per_page' => - 1, 'meta_key'=>'_wc_ld_license_code', 'meta_value' => 'yes' );
				$products = get_posts( $args );
				echo '<select class="select2-product-filter" id="wt-product-filter" name="product-filter" class="wt-product-filter">';
				echo '<option value="">' . __( "Filter by Product", "highthemes" ) . '</option>';
				foreach ( $products as $index => $product ) {
					$selected = '';
					if ( $_GET['product-filter'] == $product->ID ) {
						$selected = ' selected = "selected"';
					}
					echo "<option $selected value='" . $move_on_url . $product->ID . "'>{$product->post_title}</option>";
				}
				echo '</select>';
				?>
				<input type="button" name="filter_action" id="code-query-submit" class="button"
				       value="<?php _e( "Filter", "highthemes" ); ?>">
			</div>
			<?php
		}
		if ( $which == "bottom" ) {

		}
	}
}