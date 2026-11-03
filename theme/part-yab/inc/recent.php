<?php
/**
 * بازدیدهای اخیر — کوکی و نوار محصول.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

const PARTYAB_RECENT_COOKIE = 'partyab_recent';
const PARTYAB_RECENT_MAX    = 8;

/**
 * تجزیه کوکی بازدیدهای اخیر.
 *
 * @param string $raw     مقدار خام کوکی.
 * @param int    $exclude محصولی که نباید نمایش داده شود.
 * @param int    $limit   سقف تعداد.
 * @return array<int, int>
 */
function partyab_recent_parse( string $raw, int $exclude = 0, int $limit = PARTYAB_RECENT_MAX ): array {
	$ids = array();

	foreach ( explode( ',', $raw ) as $piece ) {
		$id = absint( trim( $piece ) );

		if ( $id > 0 && $id !== $exclude && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}

		if ( count( $ids ) >= $limit ) {
			break;
		}
	}

	return $ids;
}

/**
 * ثبت بازدید محصول جاری در کوکی.
 */
function partyab_track_recent_product(): void {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$product_id = get_queried_object_id();

	if ( $product_id <= 0 || headers_sent() ) {
		return;
	}

	$raw = isset( $_COOKIE[ PARTYAB_RECENT_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ PARTYAB_RECENT_COOKIE ] ) ) : '';
	$ids = partyab_recent_parse( $raw, (int) $product_id, PARTYAB_RECENT_MAX );
	array_unshift( $ids, (int) $product_id );

	$value = implode( ',', array_slice( $ids, 0, PARTYAB_RECENT_MAX ) );

	$_COOKIE[ PARTYAB_RECENT_COOKIE ] = $value;

	setcookie(
		PARTYAB_RECENT_COOKIE,
		$value,
		array(
			'expires'  => time() + MONTH_IN_SECONDS,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'secure'   => is_ssl(),
			'httponly' => false,
			'samesite' => 'Lax',
		)
	);
}
add_action( 'template_redirect', 'partyab_track_recent_product' );

/**
 * نوار «بازدیدهای اخیر».
 */
function partyab_render_recent_strip(): void {
	$exclude = function_exists( 'is_product' ) && is_product() ? (int) get_queried_object_id() : 0;
	$raw     = isset( $_COOKIE[ PARTYAB_RECENT_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ PARTYAB_RECENT_COOKIE ] ) ) : '';
	$ids     = partyab_recent_parse( $raw, $exclude, 4 );

	if ( count( $ids ) < 2 ) {
		return;
	}

	partyab_render_products_strip( $ids, 'بازدیدهای اخیر', 'ادامه از جایی که رها کردی', 'pb-strip--recent' );
}
add_action( 'woocommerce_after_single_product_summary', 'partyab_render_recent_strip', 24 );

/**
 * شورت‌کد «بازدیدهای اخیر» برای المنتور و برگه‌ها.
 *
 * @return string
 */
function partyab_recent_shortcode(): string {
	ob_start();
	partyab_render_recent_strip();

	return (string) ob_get_clean();
}
add_shortcode( 'partyab_recent_products', 'partyab_recent_shortcode' );
