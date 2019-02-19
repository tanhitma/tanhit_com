<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$tanhit_permalinks 		= get_option( 'woocommerce_permalinks' );
$tanhit_category_base   = isset( $tanhit_permalinks[ 'category_base' ] ) ? $tanhit_permalinks[ 'category_base' ] : 'product-category';
$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
$tanhit_home   = trailingslashit( home_url() );

global $wp_query;
$tanhit_product_cat = '';
if ( ! empty( $wp_query->query[ 'product_cat' ] ) ) {
	$tanhit_product_cat = $wp_query->query[ 'product_cat' ];	
}	

?>
<form class="woocommerce-ordering" method="get">
	<div>
		<select onchange="tanhitShopSortByCat(this)" id="tanhit-select_cat" name="tanhit-select_cat">
			<option <?php if ( $tanhit_product_cat == '' ) echo 'selected'; ?> value="<?php echo $shop_page_url; ?>">Все</option>
			<option <?php if ( $tanhit_product_cat == 'seminar'  ) echo 'selected'; ?> value="<?php echo $tanhit_home . $tanhit_category_base . '/seminar' ; ?>">Семинары</option>
			<option <?php if ( $tanhit_product_cat == 'webinar'  ) echo 'selected'; ?> value="<?php echo $tanhit_home . $tanhit_category_base . '/webinar' ; ?>">Вебинары</option>
			<option <?php if ( $tanhit_product_cat == 'practice' ) echo 'selected'; ?> value="<?php echo $tanhit_home . $tanhit_category_base . '/practice' ; ?>">Практики</option>
		</select>		
	</div>
</form>
<script type="text/javascript">	
/* <![CDATA[ */
	function tanhitShopSortByCat( select ) { 
		document.location.href = select.value;
	}	
/* ]]> */
</script>
<?php		

/**
<form class="woocommerce-ordering" method="get">
	<select name="orderby" class="orderby">
		<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
		// Keep query string vars intact
		foreach ( $_GET as $key => $val ) {
			if ( 'orderby' === $key || 'submit' === $key ) {
				continue;
			}
			if ( is_array( $val ) ) {
				foreach( $val as $innerVal ) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
	?>
</form>
*/
