<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_CSV_Import
 *
 * Import license codes in batch mode. Used to insert multiple license codes
 */
class WC_LD_CSV_Import {

	public $page_id;
	public $product_id;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_items' ), 9 );

	}

	/**
	 * Import is starting.
	 */
	private function import_start() {
		if ( function_exists( 'gc_enable' ) ) {
			gc_enable();
		}
		if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ),
				'set_time_limit' ) && ! ini_get( 'safe_mode' )
		) {
			@set_time_limit( 0 );
		}
		@ob_flush();
		@flush();
		@ini_set( 'auto_detect_line_endings', '1' );
	}


	public function add_menu_items() {

		// submenu: Import from csv
		$page_id = add_submenu_page( 'license_codes',
			__( 'CSV Upload', 'highthemes' ),
			__( 'CSV Upload', 'highthemes' ),
			'manage_woocommerce',
			'license_code_csv_upload',
			array( $this, 'handle_upload' )
		);

		$this->page_id = $page_id;

		add_action( "load-$page_id", array( $this, 'add_meta_boxes' ) );
		add_action( "admin_footer-{$page_id}", array( $this, 'enqueue_footer_scripts' ) );


	}

	public function enqueue_footer_scripts() {
		$screen = get_current_screen();
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				// toggle
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				<?php if($screen->id == $this->page_id):?>
				postboxes.add_postbox_toggles('<?php echo $this->page_id; ?>');
				<?php endif;?>
			});
			//]]>
		</script>
		<?php
	}

	/**
	 * add metaboxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'license_code_upload_metabox',
			__( 'Upload a CSV file', 'highthemes' ),
			array( $this, 'license_code_upload_form' ),
			$this->page_id, 'normal', 'default'
		);

	}

	public function handle_upload() {

		global $wpdb;

		// succuss messages
		$message = '';

		// error or warning messages
		$notice = '';

		// here we are verifying does this request is post back and have correct nonce
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__ ) ) ) {

			if ( ! empty( $_REQUEST['do_import'] ) && $_REQUEST['do_import'] == 1 && ! empty( $_REQUEST['product_id'] ) && ! empty( $_REQUEST['csv_data'] ) ) {

				$product_id  = absint( $_REQUEST['product_id'] );
				$import_data = unserialize( stripslashes( $_REQUEST['csv_data'] ) );

				if ( ! empty( $import_data ) ) {

					$values        = array();
					$place_holders = array();
					$current_time  = current_time( 'mysql' );

					$query = "INSERT INTO {$wpdb->wc_ld_license_codes} (product_id, license_code1, license_code2, license_code3, license_code4, creation_date) VALUES ";

					$skip_count = 0;
					foreach ( $import_data as $key => $value ) {
						$trimmed = trim( $value[0] );
						if ( $trimmed == '' ) {
							$skip_count ++;
							continue;
						}

						$value[0] = isset( $value[0] ) ? trim( $value[0] ) : '';
						$value[1] = isset( $value[1] ) ? trim( $value[1] ) : '';
						$value[2] = isset( $value[2] ) ? trim( $value[2] ) : '';
						$value[3] = isset( $value[3] ) ? trim( $value[3] ) : '';

						array_push( $values, $product_id, $value[0], $value[1], $value[2], $value[3], $current_time );
						$place_holders[] = "(%d, %s, %s, %s, %s, %s)";
					}

					$query .= implode( ', ', $place_holders );
					$sql    = $wpdb->prepare( $query, $values );
					$result = $wpdb->query( $sql );

					if ( $result ) {
						$message = ( count( $import_data ) - $skip_count ) . ' ' . __( 'from',
								'highthemes' ) . ' ' . count( $import_data ) . ' ' . __( 'inserted successfully',
								'highthemes' );
						do_action( 'wc_ld_license_code_inserted', array( 'id' => $product_id ) );
					} else {
						$notice = __( 'Database Error, Please contact the administrator.', 'highthemes' );
					}


				} else {
					$notice = __( "Please check the data file, and try again.", 'highthemes' );
				}


			} else {
				// validate product ID
				if ( ! empty( $_REQUEST['product_id'] ) && is_numeric( $_REQUEST['product_id'] ) ) {
					$product_id = $_REQUEST['product_id'];
					if ( isset( $_FILES['csv_file'] ) && ( $_FILES['csv_file']['size'] > 0 ) && $_FILES['csv_file']['error'] == 0 ) {

						$csv_data = '';

						// Get the type of the uploaded file. This is returned as "type/extension"
						$arr_file_type      = wp_check_filetype( basename( $_FILES['csv_file']['name'] ) );
						$uploaded_file_type = $arr_file_type['type'];

						// Set an array containing a list of acceptable formats
						$allowed_file_types = array( 'text/csv', 'application/csv' );

						// If the uploaded file is the right format
						if ( in_array( $uploaded_file_type, $allowed_file_types ) ) {

							$csv_file = $_FILES['csv_file']['tmp_name'];

							$this->import_start();

							$csv_data = array();
							if ( ( $handle = fopen( $csv_file, "r" ) ) !== false ) {
								while ( ( $data = fgetcsv( $handle, null, "," ) ) !== false ) {

									if ( count( $data ) > 4 || count( $data ) < 1 || ! isset( $data[0] ) || trim( $data[0] ) == '' ) {
										continue;
									}
									$csv_data[] = $data;
								}
								fclose( $handle );
							}

							$this->product_id = $product_id;
							unlink( $_FILES['csv_file']['tmp_name'] );

						} else { // wrong file type
							$notice = __( 'Only CSV files are allowed!', 'highthemes' );
						}

					}
				} else {
					$notice = __( 'Please select a product.', 'highthemes' );
				} // end check product id
			}

		}

		?>
		<div class="wrap">
			<h2>
				<?php _e( 'Upload CSV File', 'highthemes' ) ?>
			</h2>

			<?php if ( ! empty( $notice ) ): ?>
				<div id="notice" class="error"><p><?php echo $notice ?></p></div>
			<?php endif; ?>
			<?php if ( ! empty( $message ) ): ?>
				<div id="message" class="updated"><p><?php echo $message ?></p></div>
			<?php endif; ?>
			<?php if ( isset( $csv_data ) && is_array( $csv_data ) ): ?>
				<form id="form" method="POST">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ) ?>"/>
					<input type="hidden" name="do_import" value="1"/>
					<input type="hidden" name="product_id" value="<?php echo absint($this->product_id); ?>"/>
					<input type="hidden" name="csv_data"
					       value="<?php echo htmlentities( serialize( $csv_data ) ); ?>">
					<?php $this->display_csv_preview( $csv_data ); ?>
					<div class="tablenav bottom">
						<div class="alignleft  bulkactions">
							<input class="button-primary" type="submit" name="submit"
							       value="<?php _e( 'Insert License Codes to Database', 'highthemes' ); ?>"/>
						</div>
						<div class="alignleft actions">
						</div>
						<br class="clear"/>
					</div>

				</form>

			<?php else: ?>

				<form id="form" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ) ?>"/>
					<div style="max-width: 100%;" class="card ">
						<h2><?php _e("Batch Upload ", "highthemes");?></h2>
						<p><?php _e('Here you can upload your license codes in bulk using csv format.', 'highthemes');?> </p>
						<p><?php _e('As each product license code can contain up to 4 fields, you must provide at least 1 and maximum
							4 columns of data in each row. The first column must be always filled with data.', 'highthemes');?></p>
						<h4><?php _e('Download Sample CSV File', 'highthemes');?></h4>
						<p><a href="<?php echo WooCommerce_License_Delivery::get_plugin_url();?>/dummy-data/dummy-licenses.csv"><? _e('Click here to download a sample file', 'highthemes');?></a></p>
					</div>

					<div class="metabox-holder" id="poststuff">
						<div id="post-body">
							<div id="post-body-content">
								<?php do_meta_boxes( $this->page_id, 'normal', null ); ?>
								<input type="submit" value="<?php _e( 'Upload File and Preview', 'highthemes' ) ?>"
								       id="submit"
								       class="button-primary" name="submit">
							</div>
						</div>
					</div>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	public function display_csv_preview( $item ) {
		?>
		<div id="message" class="notice notice-info"><p>
				<?php
				printf(
					__( 'There are <strong>%d</strong> items that can be inserted. You are viewing up to 10 items for verification of data.',
						'highthemes' ),
					count( $item )
				)

				?>
			</p></div>
		<div id="message" class="updated"><p><?php _e( 'Selected Product :', 'highthemes' ); ?>
				<strong><?php echo edit_post_link( get_the_title( $this->product_id ), '', '',
						$this->product_id ); ?></strong>.</p></div>
		<table class="widefat">
			<thead>
			<tr>
				<th><strong><?php _e( 'Field #1', 'highthemes' ); ?></strong></th>
				<th><strong><?php _e( 'Field #2', 'highthemes' ); ?></strong></th>
				<th><strong><?php _e( 'Field #3', 'highthemes' ); ?></strong></th>
				<th><strong><?php _e( 'Field #4', 'highthemes' ); ?></strong></th>
			</tr>
			</thead>

			<tbody>
			<?php $i = 0;
			foreach ( $item as $value ): ?>

				<tr>
					<td scope="row"><?php echo isset( $value[0] ) ? esc_html($value[0]) : ''; ?></td>
					<td scope="row"><?php echo isset( $value[1] ) ? esc_html($value[1]) : ''; ?></td>
					<td scope="row"><?php echo isset( $value[2] ) ? esc_html($value[2]) : ''; ?></td>
					<td scope="row"><?php echo isset( $value[3] ) ? esc_html($value[3]) : ''; ?></td>

				</tr>
				<?php if ( $i == 10 ) {
					break;
				}
				$i ++; endforeach; ?>

			</tbody>
		</table>
		<?php
	}

	public function license_code_upload_form( $item ) {

		$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size  = size_format( $bytes );
		?>

		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="product_id"><?php _e( 'Product ID', 'highthemes' ) ?></label>
				</th>
				<td>

					<?php
					$args     = array(
						'post_type'      => 'product',
						'posts_per_page' => - 1,
						'meta_key'       => '_wc_ld_license_code',
						'meta_value'     => 'yes'
					);
					$products = get_posts( $args );
					echo '<select class="select2" id="product_id" name="product_id" class="code">';
					echo '<option value="">' . __( 'Select a product', 'highthemes' ) . '</option>';
					foreach ( $products as $index => $product ) {
						$selected = selected( $item['product_id'], $product->ID, false );
						echo "<option $selected value='{$product->ID}'>{$product->post_title}</option>";
					}
					echo '</select>';
					?>
				</td>
			</tr>

			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="csv_file"><?php _e( 'CSV File', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="csv_file" name="csv_file" type="file">
					<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>"/>
					<small><?php printf( __( 'Maximum size: %s', 'highthemes' ), $size ); ?></small>
				</td>
			</tr>

			</tbody>
		</table>
		<?php
	}

}