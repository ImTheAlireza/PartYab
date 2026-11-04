<?php
/**
 * Presentation hooks for native WooCommerce pages. Business logic is untouched.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/** Add a consistent page introduction without duplicating product headings. */
function partyab_commerce_intro(): void {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return;
	}
	if ( is_cart() ) {
		partyab_page_intro( 'سبد خرید تو', 'قطعات انتخابی‌ات را بررسی کن و با خیال راحت ادامه بده.', 'یک قدم تا ادامه مسیر' );
	} elseif ( is_checkout() ) {
		partyab_page_intro( 'تکمیل سفارش', 'اطلاعات ارسال را وارد کن؛ جزئیات سفارش در کنار توست.', 'آخرین قدم، انتخاب مطمئن' );
	} elseif ( is_account_page() ) {
		partyab_page_intro( is_user_logged_in() ? 'به گاراژ خودت خوش آمدی' : 'حساب کاربری پارتیاب', 'سفارش‌ها، آدرس‌ها و اطلاعات خودرو؛ همه در یک‌جا.', 'همراه تو، در هر کیلومتر' );
	} elseif ( is_shop() ) {
		partyab_page_intro( 'فروشگاه قطعات خودرو', 'خودرویت را انتخاب کن، فیلترها را تنظیم کن و قطعه مناسب را پیدا کن.', 'قطعه درست، انتخاب درست' );
	} elseif ( is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			partyab_page_intro( $term->name, wp_strip_all_tags( term_description( $term->term_id ) ), 'دسته‌بندی تخصصی پارتیاب' );
		}
	}
}
add_action( 'woocommerce_before_main_content', 'partyab_commerce_intro', 5 );

/** Open the result toolbar around native count and sorting controls. */
function partyab_shop_toolbar_start(): void {
	if ( ! function_exists( 'woocommerce_product_loop' ) || ! woocommerce_product_loop() ) {
		return;
	}
	echo '<div class="pb-shop-toolbar"><button class="pb-btn pb-btn--sm pb-btn--ghost pb-filter-toggle" type="button" data-filter-toggle aria-expanded="false">' . partyab_icon( 'gear' ) . 'فیلترها</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
function partyab_shop_toolbar_end(): void {
	if ( function_exists( 'woocommerce_product_loop' ) && woocommerce_product_loop() ) {
		echo '</div>';
	}
}
add_action( 'woocommerce_before_shop_loop', 'partyab_shop_toolbar_start', 12 );
add_action( 'woocommerce_before_shop_loop', 'partyab_shop_toolbar_end', 35 );

/** Two explicit columns keep the native cart and totals aligned. */
function partyab_cart_layout_start(): void {
	echo '<div class="pb-cart-layout"><div class="pb-cart-main">';
}
function partyab_cart_main_end(): void {
	echo '</div>';
}
function partyab_cart_layout_end(): void {
	echo '</div>';
}
add_action( 'woocommerce_before_cart', 'partyab_cart_layout_start', 5 );
add_action( 'woocommerce_after_cart', 'partyab_cart_main_end', 99 );
add_action( 'woocommerce_after_cart_collaterals', 'partyab_cart_layout_end', 99 );

/** Keep the custom header badge synchronized with WooCommerce fragments. */
function partyab_cart_count_fragment( array $fragments ): array {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$fragments['.pb-action__count'] = '<b class="pb-action__count">' . esc_html( number_format_i18n( WC()->cart->get_cart_contents_count() ) ) . '</b>';
	}
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'partyab_cart_count_fragment' );

/** Add a quiet SKU/brand line without changing the native product link. */
function partyab_loop_product_identity(): void {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$brand = function_exists( 'partyab_product_brand_name' ) ? partyab_product_brand_name( $product->get_id() ) : '';
	echo '<div class="pb-loop-identity"><span>' . esc_html( $brand ?: get_bloginfo( 'name' ) ) . '</span>';
	if ( $product->get_sku() ) {
		echo '<code dir="ltr">' . esc_html( $product->get_sku() ) . '</code>';
	}
	echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'partyab_loop_product_identity', 3 );

/** A real order-status summary, not a fabricated delivery timeline. */
function partyab_order_status_summary( int $order_id ): void {
	$order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : false;
	if ( ! $order ) {
		return;
	}
	echo '<div class="pb-order-status-summary" data-reveal>' . partyab_icon( 'verified' ) . '<div><strong>وضعیت سفارش: ' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</strong><p>جزئیات و تغییرات سفارش در حساب کاربری تو قابل پیگیری است.</p></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_thankyou', 'partyab_order_status_summary', 5 );

/** Static outer hit area + moving inner surface: no hover edge flicker. */
function partyab_loop_surface_start(): void {
	echo '<div class="pb-product-card__surface">';
}
function partyab_loop_surface_end(): void {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop_item', 'partyab_loop_surface_start', 1 );
add_action( 'woocommerce_after_shop_loop_item', 'partyab_loop_surface_end', 99 );
