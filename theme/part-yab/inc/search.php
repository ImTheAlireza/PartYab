<?php
/**
 * جستجوی قطعه: شماره فنی (SKU)، ارقام فارسی و گزارش جستجوهای بی‌نتیجه.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * یکسان‌سازی عبارت جستجو.
 *
 * @param string $term عبارت خام.
 * @return string
 */
function partyab_search_normalize( string $term ): string {
	$term = function_exists( 'partyab_normalize_digits' ) ? partyab_normalize_digits( $term ) : $term;
	$term = str_replace( array( 'ي', 'ك', '‌' ), array( 'ی', 'ک', ' ' ), $term );

	return trim( (string) preg_replace( '/\s+/u', ' ', $term ) );
}

/**
 * حالت‌های نوشتاری شماره فنی: با خط تیره، بدون خط تیره، حروف بزرگ.
 *
 * @param string $term عبارت.
 * @return array<int, string>
 */
function partyab_sku_variants( string $term ): array {
	$base    = partyab_search_normalize( $term );
	$upper   = strtoupper( $base );
	$compact = (string) preg_replace( '/[\s\-_.\/]+/', '', $upper );

	$variants = array_values( array_unique( array_filter( array( $upper, $compact, $base ) ) ) );

	return array_slice( $variants, 0, 3 );
}

/**
 * افزودن گزارش یک جستجوی بی‌نتیجه به آرایه (خالص و قابل تست).
 *
 * @param array<string, int> $log گزارش فعلی.
 * @param string             $term عبارت.
 * @param int                $max  سقف تعداد کلیدها.
 * @return array<string, int>
 */
function partyab_zero_log_add( array $log, string $term, int $max = 100 ): array {
	$term = partyab_search_normalize( $term );

	if ( '' === $term || partyab_text_len( $term ) < 2 ) {
		return $log;
	}

	$log[ $term ] = (int) ( $log[ $term ] ?? 0 ) + 1;

	if ( count( $log ) > $max ) {
		arsort( $log );
		$log = array_slice( $log, 0, $max, true );
	}

	return $log;
}

/**
 * آیا این کوئری، جستجوی محصول در فرانت است؟
 *
 * @param WP_Query $query کوئری.
 * @return bool
 */
function partyab_is_product_search_query( $query ): bool {
	// درخواست AJAX پیشنهاد هم باید شماره فنی را جستجو کند (is_admin در AJAX true است).
	$is_ajax = function_exists( 'wp_doing_ajax' ) && wp_doing_ajax();

	if ( ( is_admin() && ! $is_ajax ) || ! $query instanceof WP_Query ) {
		return false;
	}

	if ( $query->get( 'partyab_suggest' ) ) {
		return true;
	}

	return $query->is_main_query() && $query->is_search() && 'product' === $query->get( 'post_type' );
}

/**
 * افزودن شرط جستجو در شماره فنی به کوئری محصولات.
 *
 * @param string   $search شرط جستجوی وردپرس.
 * @param WP_Query $query  کوئری.
 * @return string
 */
function partyab_search_by_sku( $search, $query ): string {
	$search = (string) $search;

	if ( ! partyab_is_product_search_query( $query ) ) {
		return $search;
	}

	$term = partyab_search_normalize( (string) $query->get( 's' ) );

	if ( '' === $term ) {
		return $search;
	}

	global $wpdb;

	$conditions = array();

	foreach ( partyab_sku_variants( $term ) as $variant ) {
		$conditions[] = $wpdb->prepare(
			"EXISTS (SELECT 1 FROM {$wpdb->postmeta} pb_sku WHERE pb_sku.post_id = {$wpdb->posts}.ID AND pb_sku.meta_key = '_sku' AND pb_sku.meta_value LIKE %s)",
			'%' . $wpdb->esc_like( $variant ) . '%'
		);
	}

	if ( ! $conditions ) {
		return $search;
	}

	$extra = '(' . implode( ' OR ', $conditions ) . ')';
	$inner = trim( $search );

	if ( '' === $inner ) {
		return ' AND ' . $extra;
	}

	if ( 0 === stripos( $inner, 'AND ' ) ) {
		$inner = trim( substr( $inner, 4 ) );
	}

	return ' AND ( ' . $inner . ' OR ' . $extra . ' )';
}
add_filter( 'posts_search', 'partyab_search_by_sku', 10, 2 );

/**
 * ثبت جستجوهای بی‌نتیجه.
 */
function partyab_log_zero_search(): void {
	if ( ! is_search() || ! function_exists( 'is_search' ) ) {
		return;
	}

	if ( 'product' !== get_query_var( 'post_type' ) || ! have_posts() ) {
		return;
	}

	$term = partyab_search_normalize( (string) get_search_query() );

	if ( '' === $term ) {
		return;
	}

	$log = get_option( 'partyab_zero_searches', array() );
	$log = is_array( $log ) ? $log : array();

	update_option( 'partyab_zero_searches', partyab_zero_log_add( $log, $term ), false );
}
add_action( 'wp', 'partyab_log_zero_search' );

/**
 * اگر عبارت دقیقاً یک شماره فنی باشد، مستقیم به صفحه محصول برو.
 */
function partyab_sku_direct_redirect(): void {
	if ( ! is_search() || 'product' !== get_query_var( 'post_type' ) ) {
		return;
	}

	$term = partyab_search_normalize( (string) get_search_query() );

	if ( partyab_text_len( $term ) < 5 || ! function_exists( 'wc_get_products' ) ) {
		return;
	}

	$variants = partyab_sku_variants( $term );
	$products = wc_get_products(
		array(
			'limit'        => 1,
			'status'       => 'publish',
			'stock_status' => 'instock',
			'sku'          => $term,
		)
	);

	if ( ! $products ) {
		$products = wc_get_products(
			array(
				'limit'  => 1,
				'status' => 'publish',
				'sku'    => strtoupper( $term ),
			)
		);
	}

	if ( ! $products ) {
		return;
	}

	$sku = strtoupper( (string) $products[0]->get_sku() );

	foreach ( $variants as $variant ) {
		if ( $variant === strtoupper( (string) preg_replace( '/[\s\-_.\/]+/', '', $sku ) ) ) {
			wp_safe_redirect( (string) get_permalink( (int) $products[0]->get_id() ), 302 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'partyab_sku_direct_redirect' );
