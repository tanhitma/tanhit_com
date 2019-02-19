<?php
/**
 * Template for displaying search forms
 *
 * @package Tanhit
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo pll_e( 'Search for:', 'tanhit' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo pll_e( 'Search &hellip;', 'tanhit' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo pll_e( 'Search for:', 'tanhit' ); ?>" />
	</label>
	<button type="submit" class="search-submit"><span class="screen-reader-text"><?php echo pll_e( 'Search', 'tanhit' ); ?></span></button>
</form>
