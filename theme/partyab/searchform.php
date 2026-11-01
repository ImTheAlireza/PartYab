<?php
/**
 * Search form.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

$partyab_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $partyab_id ); ?>"><?php esc_html_e( 'جست‌وجو', 'partyab' ); ?></label>
	<div class="combo">
		<?php partyab_the_icon( 'search', 'combo-icon' ); ?>
		<input id="<?php echo esc_attr( $partyab_id ); ?>" type="search" class="search-field" name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'نام قطعه، کد فنی یا مدل خودرو…', 'partyab' ); ?>">
		<input type="hidden" name="post_type" value="product">
		<button type="submit" class="btn btn-amber btn-sm"><?php esc_html_e( 'جست‌وجو', 'partyab' ); ?></button>
	</div>
</form>
