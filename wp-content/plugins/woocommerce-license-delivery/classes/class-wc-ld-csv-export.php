<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_CSV_Export
 *
 * Exports the entire license codes table into CSV file, for backup.
 */
class WC_LD_CSV_Export {

	public $page_id;
	public $product_id;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_CSV' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_items' ), 9 );

	}

	public function array2csv( array &$array ) {
		if ( count( $array ) == 0 ) {
			return null;
		}
		ob_start();
		$df = fopen( "php://output", 'w' );
		fputcsv( $df, array_keys( reset( $array ) ) );
		foreach ( $array as $row ) {
			fputcsv( $df, $row );
		}
		fclose( $df );

		return ob_get_clean();
	}

	public function download_send_headers( $filename ) {
		// disable caching
		$now = gmdate( "D, d M Y H:i:s" );
		header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
		header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
		header( "Last-Modified: {$now} GMT" );

		// force download
		header( "Content-Type: application/force-download" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Type: application/download" );

		// disposition / encoding on response body
		header( "Content-Disposition: attachment;filename={$filename}" );
		header( "Content-Transfer-Encoding: binary" );
	}


	public function add_menu_items() {

		// submenu: Import from csv
		$page_id = add_submenu_page( 'license_codes',
			__( 'Export Data', 'highthemes' ),
			__( 'Export Data', 'highthemes' ),
			'manage_woocommerce',
			'csv_export',
			array( $this, 'export_csv' )
		);

		$this->page_id = $page_id;

		add_action( "load-$page_id", array( $this, 'add_meta_boxes' ) );
		add_action( "admin_footer-{$page_id}", array( $this, 'enqueue_footer_scripts' ) );


	}


	function enqueue_footer_scripts() {
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
			'license_code_export_metabox',
			__( 'Export', 'highthemes' ),
			array( $this, 'license_code_export_form' ),
			$this->page_id, 'normal', 'default'
		);

	}

	public function download_CSV() {
		global $wpdb;
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

			if ( isset( $_POST['product_id'] ) ) {

				$product_id = $_POST['product_id'];
				$exclude    = isset( $_POST['exclude'] ) ? true : false;

				$sql = "SELECT * FROM {$wpdb->wc_ld_license_codes}";

				if ( is_numeric( $product_id ) ) {
					$sql .= " WHERE product_id = $product_id";

					if ( $exclude === true ) {
						$sql .= " AND license_status = '0'";
					}

				} else {
					if ( $exclude === true ) {
						$sql .= " WHERE license_status = '0'";
					}
				}

				$result = $wpdb->get_results( $sql, ARRAY_A );
				$this->download_send_headers( "data_export_" . date( "Y-m-d" ) . ".csv" );
				echo $this->array2csv( $result );
				die();


			}
		}
	}

	public function export_csv() {


		// succuss messages
		$message = '';

		// error or warning messages
		$notice = '';
		?>
		<div class="wrap">
			<h2>
				<?php _e( 'Export Data to CSV', 'highthemes' ) ?>
				<a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(),
					'admin.php?page=license_codes' ); ?>"><?php _e( 'Back to code list', 'highthemes' ) ?></a>
			</h2>

			<?php if ( ! empty( $notice ) ): ?>
				<div id="notice" class="error"><p><?php echo $notice ?></p></div>
			<?php endif; ?>
			<?php if ( ! empty( $message ) ): ?>
				<div id="message" class="updated"><p><?php echo $message ?></p></div>
			<?php endif; ?>
			<form id="form" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ) ?>"/>
				<div style="max-width: 100%;" class="card ">
					<h2><?php _e('Download the license codes in CSV format', 'highthemes');?></h2>
					<p><?php _e('Here you can download all the license codes in csv file format. It is used for backup purposes. The csv file is a whole backup of the codes table based on your desire options.', 'highthemes');?></p>
				</div>

				<div class="metabox-holder" id="poststuff">
					<div id="post-body">
						<div id="post-body-content">
							<?php do_meta_boxes( $this->page_id, 'normal', null ); ?>
							<input type="submit" value="<?php _e( 'Download CSV File', 'highthemes' ) ?>"
							       id="submit"
							       class="button-primary" name="submit">
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}


	function license_code_export_form( $item ) {
		?>

		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="product_id"><?php _e( 'Product', 'highthemes' ) ?></label>
				</th>
				<td>

					<?php
					$args = array( 'post_type'      => 'product',
					               'posts_per_page' => - 1,
					               'meta_key'       => '_wc_ld_license_code',
					               'meta_value'     => 'yes'
					);
					$products = get_posts( $args );
					echo '<select class="select2" id="product_id" name="product_id" class="code">';
					echo '<option value="all">' . __( 'All Products', 'highthemes' ) . '</option>';
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
					<label for="exclude"><?php _e( 'Exclude Sold Licenses', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="exclude" name="exclude" type="checkbox" value="1">
				</td>
			</tr>

			</tbody>
		</table>
		<?php
	}

}