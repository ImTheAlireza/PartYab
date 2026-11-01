<?php
/**
 * Shop / category archive.
 *
 * Keeps the WooCommerce hooks so plugins keep working, but swaps the grid for
 * the theme's `.p-grid` and adds the vehicle bar + toolbar.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * woocommerce_before_main_content.
 *
 * @hooked partyab_wc_open_container - 10
 */
do_action( 'woocommerce_before_main_content' );

$partyab_term = is_product_taxonomy() ? get_queried_object() : null;
?>
<header class="shop-head">
	<nav class="breadcrumb">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'partyab' ); ?></a><span class="sep">/</span>
		<a href="<?php echo esc_url( partyab_shop_url() ); ?>"><?php esc_html_e( 'فروشگاه', 'partyab' ); ?></a>
		<?php if ( $partyab_term ) : ?>
			<span class="sep">/</span><span class="current"><?php echo esc_html( $partyab_term->name ); ?></span>
		<?php endif; ?>
	</nav>
	<h1 id="pageHeading">
		<?php
		if ( $partyab_term ) {
			echo esc_html( $partyab_term->name );
		} elseif ( is_search() ) {
			printf( esc_html__( 'نتایج جست‌وجو برای «%s»', 'partyab' ), esc_html( get_search_query() ) );
		} else {
			esc_html_e( 'همهٔ قطعات خودرو', 'partyab' );
		}
		?>
	</h1>
	<?php if ( $partyab_term && $partyab_term->description ) : ?>
		<p class="shop-desc"><?php echo esc_html( $partyab_term->description ); ?></p>
	<?php endif; ?>
</header>

<?php
if ( woocommerce_product_loop() ) {

	/**
	 * woocommerce_before_shop_loop.
	 *
	 * @hooked partyab_wc_vehicle_bar - 5
	 * @hooked woocommerce_result_count - 20
	 * @hooked partyab_wc_toolbar - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	do_action( 'woocommerce_no_products_found' );
}

/**
 * woocommerce_after_main_content.
 *
 * @hooked partyab_wc_close_container - 10
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
