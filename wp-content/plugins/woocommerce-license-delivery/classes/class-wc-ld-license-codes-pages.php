<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class WC_LD_License_Codes_Pages
 *
 * displays the license code management pages
 */
class WC_LD_License_Codes_Pages {

	/**
	 * @var $instance used for instantiate the object
	 */
	static $instance;

	/**
	 * @var $license_list_object an instance of License_Code_list object
	 */
	public $license_list_object;

	/**
	 * @var $product_id save product id for forms
	 */
	protected $product_id;

	/**
	 * @var $code_edit_page_id saves edit/insert page id
	 */
	protected $code_edit_page_id;


	/**
	 * License_Codes constructor
	 */
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'add_menu_items' ), 9 );

		// add batch upload and export sub pages
		new WC_LD_CSV_Import();
		new WC_LD_CSV_Export();
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Number of License Codes per page', 'highthemes' ),
			'default' => 30,
			'option'  => 'codes_per_page'
		);

		add_screen_option( $option, $args );

		$this->license_list_object = new WC_LD_Codes_List();
	}

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * add menu to WP admin
	 */
	public function add_menu_items() {

		// add main menu item
		$license_code_page = add_menu_page(
			__( 'License Codes', 'highthemes' ),
			__( 'License Codes', 'highthemes' ),
			'manage_woocommerce',
			'license_codes',
			array( $this, 'display_license_codes' ),
			'dashicons-lock',
			2
		);

		// submenu: New Code
		$code_edit_page_id = add_submenu_page( 'license_codes',
			__( 'Add new', 'highthemes' ),
			__( 'Add new', 'highthemes' ),
			'manage_woocommerce',
			'license_code_edit',
			array( $this, 'license_code_editor_handler' )
		);

		$this->code_edit_page_id = $code_edit_page_id;

		add_action( "load-$code_edit_page_id", array( $this, 'add_meta_boxes' ) );
		add_action( "load-$license_code_page", array( $this, 'screen_option' ) );
		add_action( "admin_footer-{$code_edit_page_id}", array( $this, 'enqueue_footer_scripts' ) );

	}


	function enqueue_footer_scripts() {
		$screen = get_current_screen();
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				// toggle
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

				<?php if($screen->id == $this->code_edit_page_id):?>
				postboxes.add_postbox_toggles('<?php echo $this->code_edit_page_id; ?>');
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

		// here we adding our custom meta box
		add_meta_box( 'license_code_insert_update_metabox', __( 'License(s)', 'highthemes' ),
			array( $this, 'license_code_insert_update_form' ),
			$this->code_edit_page_id, 'normal', 'default' );
	}

	public function display_license_codes() {

		?>
		<div class="wrap">
			<h2><?php _e( 'License Codes', 'highthemes' ) ?>

				<a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(),
					'admin.php?page=license_code_edit' ); ?>"><?php _e( 'Add New', 'highthemes' ) ?></a>
			</h2>
			<div>
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->license_list_object->prepare_items();
								$this->license_list_object->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}


	function license_code_editor_handler() {

		global $wpdb;

		$message = '';
		$notice  = '';

		// this is default $item which will be used for new records
		$default = array(
			'id'             => 0,
			'license_code1'  => '',
			'license_code2'  => '',
			'license_code3'  => '',
			'license_code4'  => '',
			'license_code5'  => '',
			'product_id'     => '',
			'license_status' => 0,
			'creation_date'  => current_time( 'mysql' )
		);

		// here we are verifying does this request is post back and have correct nonce
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__ ) ) ) {

			// combine our default item with request params
			$item = shortcode_atts( $default, $_REQUEST );

			$item_valid = $this->license_code_validate_field( $item );

			$item = stripslashes_deep( $item );

			if ( $item_valid === true ) {
				if ( $item['id'] == 0 ) {

					$import_data   = array();
					$values        = array();
					$place_holders = array();
					$current_time  = current_time( 'mysql' );


					if ( ! empty( $item['license_code1'] ) && ! empty( $item['license_code1'][0] ) ) {
						$import_data['code1'] = $item['license_code1'];
					}
					if ( ! empty( $item['license_code2'] ) && ! empty( $item['license_code2'][0] ) ) {
						$import_data['code2'] = $item['license_code2'];
					}
					if ( ! empty( $item['license_code3'] ) && ! empty( $item['license_code3'][0] ) ) {
						$import_data['code3'] = $item['license_code3'];
					}
					if ( ! empty( $item['license_code4'] ) && ! empty( $item['license_code4'][0] ) ) {
						$import_data['code4'] = $item['license_code4'];
					}
					if ( ! empty( $item['license_code5'] ) && ! empty( $item['license_code5'][0] ) ) {
						$import_data['code5'] = $item['license_code5'];
					}
					$query = "INSERT INTO {$wpdb->wc_ld_license_codes} (product_id,
							  license_code1, license_code2, license_code3, license_code4, creation_date) VALUES ";

					foreach ( $import_data as $key => $value ) {

						array_push( $values, absint( $item['product_id'] ), $value[0], $value[1], $value[2], $value[3],
							$current_time );
						$place_holders[] = "(%d, %s, %s, %s, %s, %s)";
					}

					$query .= implode( ', ', $place_holders );
					$sql    = $wpdb->prepare( $query, $values );
					$result = $wpdb->query( $sql );

					if ( $result ) {
						$message = __( 'Data inserted successfully', 'highthemes' );
						do_action( 'wc_ld_license_code_inserted', array( 'id' => $item['product_id'] ) );
					} else {
						$notice = __( 'There was an error while saving data.', 'highthemes' );
					}
				} else {

					$update_item = array(
						'license_code1'  => $item['license_code1'][0],
						'license_code2'  => $item['license_code1'][1],
						'license_code3'  => $item['license_code1'][2],
						'license_code4'  => $item['license_code1'][3],
						'product_id'     => $item['product_id'],
						'license_status' => $item['license_status'],
						'id'             => $item['id']
					);

					$item            = $update_item;
					$prev            = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->wc_ld_license_codes} WHERE id = %d",
						$item['id'] ), ARRAY_A );
					$last_product_id = $prev['product_id'];

					$result = $wpdb->update( $wpdb->wc_ld_license_codes, $item, array( 'id' => $item['id'] ) );
					if ( $result ) {
						$message = __( 'Item was successfully updated', 'highthemes' );
						do_action( 'wc_ld_license_code_updated', array( 'id' => $item['product_id'] ) );
						do_action( 'wc_ld_license_code_updated_previous', array( 'id' => $last_product_id ) );
					} else {
						$notice = __( 'No Update', 'highthemes' );
					}
				}
			} else {
				// if $item_valid not true it contains error message(s)
				$notice = $item_valid;
				$item   = $default;
				if ( isset( $_REQUEST['id'] ) ) {
					$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->wc_ld_license_codes} WHERE id = %d",
						absint( $_REQUEST['id'] ) ), ARRAY_A );
				}
			}
		} else {
			// if this is not post back we load item to edit or give new one to create
			$item = $default;
			if ( isset( $_REQUEST['id'] ) ) {
				$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->wc_ld_license_codes} WHERE id = %d",
					absint( $_REQUEST['id'] ) ), ARRAY_A );
				if ( ! $item ) {
					$item   = $default;
					$notice = __( 'Item not found', 'highthemes' );
				}
			}
		}
		?>
		<div class="wrap">
			<h2><?php
				$screen = get_current_screen();
				if ( $this->code_edit_page_id == $screen->id && ! empty( $_GET['id'] ) ) {
					$page_type = __( "Edit", 'highthemes' );
				} else {
					$page_type = __( 'Add New', 'highthemes' );
				}
				_e( $page_type . ' License Code', 'highthemes' );
				?></h2>
			<p>
				<?php
				if ( $page_type != 'Edit' ) {
					_e( "You can insert up to 5 license codes in a single submit by filling this form. The first field of each license code is required.",
						"highthemes" );
				}
				?>
			</p>

			<?php if ( ! empty( $notice ) ): ?>
				<div id="notice" class="error"><p><?php echo $notice ?></p></div>
			<?php endif; ?>
			<?php if ( ! empty( $message ) ): ?>
				<div id="message" class="updated"><p><?php echo $message ?></p></div>
			<?php endif; ?>

			<form id="form" method="POST">
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ) ?>"/>
				<input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

				<div class="metabox-holder" id="poststuff">
					<div id="post-body">
						<div id="post-body-content">
							<?php if ( $item['id'] == 0 ) {
								$temp_product_id    = $item['product_id'];
								$item               = $default;
								$item['product_id'] = $temp_product_id;
							} ?>
							<?php do_meta_boxes( $this->code_edit_page_id, 'normal', $item ); ?>
							<input type="submit" value="<?php _e( 'Save Data', 'highthemes' ) ?>" id="submit"
							       class="button-primary" name="submit">
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	function license_code_insert_update_form( $item ) {
		?>

		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">

					<label for="product_id"><?php _e( 'Product', 'highthemes' ) ?></label>
				</th>
				<td>
					<?php
					$args = array(
						'post_type'      => 'product',
						'posts_per_page' => - 1

					);
					if ( $item['id'] == 0 ) {
						$args['meta_key']   = '_wc_ld_license_code';
						$args['meta_value'] = 'yes';
					}
					$products = get_posts( $args );
					echo '<select class="select2" id="product_id" name="product_id" class="code" required>';
					echo '<option value="">' . __( 'Select a product', 'highthemes' ) . '</option>';
					foreach ( $products as $index => $product ) {
						$selected = selected( $item['product_id'], $product->ID, false );
						echo "<option $selected value='{$product->ID}'>{$product->post_title}</option>";
					}
					echo '</select>';
					?>
					<small><?php _e( "Select the product that the license code(s) blong to.", "highthemes" ); ?></small>
				</td>
			</tr>

			<tr>
				<td colspan="2"
				    style="background-color: #eee; font-weight: bold"><?php _e( "License Code #1",
						"highthemes" ); ?></td>
			</tr>

			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="license_code1"><?php _e( 'Main Field', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="license_code1" name="license_code1[]" type="text" style="width: 95%"
					       value="<?php echo esc_attr( $item['license_code1'] ); ?>" size="50" class="code"
					       placeholder="<?php _e( 'Main Field', 'highthemes' ) ?>" required>
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="license_code2"><?php _e( 'Field 2', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="license_code2" name="license_code1[]" type="text" style="width: 95%"
					       value="<?php echo esc_attr( $item['license_code2'] ) ?>"
					       size="50" class="code" placeholder="<?php _e( 'Field 2', 'highthemes' ) ?>">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="license_code3"><?php _e( 'Field 3', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="license_code3" name="license_code1[]" type="text" style="width: 95%"
					       value="<?php echo esc_attr( $item['license_code3'] ) ?>"
					       size="50" class="code" placeholder="<?php _e( 'Field 3', 'highthemes' ) ?>">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="license_code4"><?php _e( 'Field 4', 'highthemes' ) ?></label>
				</th>
				<td>
					<input id="license_code4" name="license_code1[]" type="text" style="width: 95%"
					       value="<?php echo esc_attr( $item['license_code4'] ) ?>"
					       size="50" class="code" placeholder="<?php _e( 'Field 4', 'highthemes' ) ?>">
				</td>
			</tr>

			<?php if ( isset( $item ) && $item['id'] > 0 ): ?>
				<tr class="form-field">
					<th scope="row"><?php _e( 'License Status' ) ?></th>
					<td>
						<fieldset>
							<?php
							$license_status_array = array( "0" => "Not Used", "1" => "Used" );

							foreach ( $license_status_array as $col => $value ) {
								$checked = checked( $item['license_status'], $col, false );
								echo "\t<label title='" . esc_attr( $value ) . "'><input type='radio' $checked  name='license_status' value='" . esc_attr( $col ) . "'";
								echo ' /> ' . $value . "</label><br />\n";
							}
							?>
						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( isset( $item ) && $item['id'] == 0 ): ?>
				<tr>
					<td colspan="2"
					    style="background-color: #eee; font-weight: bold"><?php _e( "License Code #2",
							"highthemes" ); ?></td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code1"><?php _e( 'Main Field', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code1" name="license_code2[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Main Field', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code2"><?php _e( 'Field 2', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code2" name="license_code2[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 2', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code3"><?php _e( 'Field 3', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code3" name="license_code2[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 3', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code4"><?php _e( 'Field 4', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code4" name="license_code2[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 4', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2"
					    style="background-color: #eee; font-weight: bold"><?php _e( "License Code #3",
							"highthemes" ); ?></td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code1"><?php _e( 'Main Field', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code1" name="license_code3[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Main Field', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code2"><?php _e( 'Field 2', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code2" name="license_code3[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 2', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code3"><?php _e( 'Field 3', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code3" name="license_code3[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 3', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code4"><?php _e( 'Field 4', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code4" name="license_code3[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 4', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2"
					    style="background-color: #eee; font-weight: bold"><?php _e( "License Code #4",
							"highthemes" ); ?></td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code1"><?php _e( 'Main Field', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code1" name="license_code4[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Main Field', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code2"><?php _e( 'Field 2', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code2" name="license_code4[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 2', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code3"><?php _e( 'Field 3', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code3" name="license_code4[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 3', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code4"><?php _e( 'Field 4', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code4" name="license_code4[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 4', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2"
					    style="background-color: #eee; font-weight: bold"><?php _e( "License Code #5",
							"highthemes" ); ?></td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code1"><?php _e( 'Main Field', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code1" name="license_code5[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Main Field', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code2"><?php _e( 'Field 2', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code2" name="license_code5[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 2', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code3"><?php _e( 'Field 3', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code3" name="license_code5[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 3', 'highthemes' ) ?>">
					</td>
				</tr>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="license_code4"><?php _e( 'Field 4', 'highthemes' ) ?></label>
					</th>
					<td>
						<input id="license_code4" name="license_code5[]" type="text" style="width: 95%"
						       size="50" class="code" placeholder="<?php _e( 'Field 4', 'highthemes' ) ?>">
					</td>
				</tr>

			<?php endif; ?>


			</tbody>
		</table>
		<?php
	}

	function license_code_validate_field( $item ) {
		$messages = array();

		if ( trim( $item['license_code1'][0] ) == '' ) {
			$messages[] = __( 'You must fill the first field of each code', 'highthemes' );
		}
		if ( empty( $item['product_id'] ) || ! is_numeric( $item['product_id'] ) ) {
			$messages[] = __( 'Please select a valid Product', 'highthemes' );
		}

		if ( empty( $messages ) ) {
			return true;
		}

		return implode( '<br />', $messages );
	}


	/** Singleton instance */
	public static function setup() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}