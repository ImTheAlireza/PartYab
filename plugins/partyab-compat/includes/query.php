<?php
/**
 * پرس‌وجوهای سازگاری + وضعیت سه‌حالته.
 *
 * قاعده مهم: نبود ردیف یعنی «نامشخص» (unknown)، نه «ناسازگار» (no).
 *
 * @package PartYab_Compat
 */

defined( 'ABSPATH' ) || exit;

/**
 * یکسان‌سازی متن فارسی برای تطبیق بهتر (ك عربی، ی، نیم‌فاصله، فاصله اضافه).
 *
 * @param string $text متن ورودی.
 * @return string
 */
function partyab_compat_normalize( string $text ): string {
	$text = str_replace( array( 'ك', 'ي', 'ۀ', 'ة', "\u{200c}", '‌' ), array( 'ک', 'ی', 'ه', 'ه', ' ', ' ' ), $text );
	$text = preg_replace( '/\s+/u', ' ', $text );

	return trim( mb_strtolower( $text ) );
}

/**
 * شناسه محصولات سازگار با خودرو.
 *
 * @param array $args {
 *     @type string $brand برند خودرو.
 *     @type string $model مدل خودرو.
 *     @type int    $year  سال ساخت (شمسی یا میلادی — هر چه در داده است).
 * }
 * @return int[]
 */
function partyab_compat_product_ids( array $args ): array {
	global $wpdb;

	$brand = partyab_compat_normalize( (string) ( $args['brand'] ?? '' ) );
	$model = partyab_compat_normalize( (string) ( $args['model'] ?? '' ) );
	$year  = isset( $args['year'] ) ? (int) $args['year'] : 0;

	if ( '' === $brand ) {
		return array();
	}

	$cache_key = 'ids_' . md5( wp_json_encode( array( $brand, $model, $year ) ) );
	$cached    = wp_cache_get( $cache_key, 'partyab_compat' );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$table = partyab_compat_table();
	$where = array( 'car_brand = %s' );
	$vals  = array( $brand );

	if ( '' !== $model ) {
		$where[] = 'car_model = %s';
		$vals[]  = $model;
	}

	if ( $year > 0 ) {
		$where[] = 'year_from <= %d';
		$where[] = 'year_to >= %d';
		$vals[]  = $year;
		$vals[]  = $year;
	}

	$sql = "SELECT DISTINCT product_id FROM {$table} WHERE " . implode( ' AND ', $where );

	// phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery
	$ids = $wpdb->get_col( $wpdb->prepare( $sql, $vals ) );
	$ids = array_map( 'intval', $ids );

	wp_cache_set( $cache_key, $ids, 'partyab_compat', 300 );

	return $ids;
}

/**
 * وضعیت سازگاری یک محصول با خودروی انتخاب‌شده.
 *
 * @param int   $product_id شناسه محصول.
 * @param array $args       همان آرگومان‌های partyab_compat_product_ids.
 * @return string yes|no|unknown
 */
function partyab_compat_status( int $product_id, array $args ): string {
	global $wpdb;

	$table = partyab_compat_table();

	// آیا اصلاً داده‌ای برای این محصول داریم؟
	$has_data = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE product_id = %d", $product_id )
	);

	if ( 0 === $has_data ) {
		return 'unknown';
	}

	$ids = partyab_compat_product_ids( $args );

	return in_array( $product_id, $ids, true ) ? 'yes' : 'no';
}

/**
 * همه ردیف‌های سازگاری یک محصول.
 *
 * @param int $product_id شناسه محصول.
 * @return array<int,array<string,mixed>>
 */
function partyab_compat_rows( int $product_id ): array {
	global $wpdb;

	$table = partyab_compat_table();

	// phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery
	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT car_brand, car_model, year_from, year_to, engine, note
			 FROM {$table} WHERE product_id = %d
			 ORDER BY car_brand ASC, car_model ASC, year_from ASC",
			$product_id
		),
		ARRAY_A
	);
}

/**
 * فهرست برندهای خودرو با تعداد ردیف (برای ساخت فیلتر).
 *
 * @return array<int,object>
 */
function partyab_compat_brands(): array {
	global $wpdb;

	$table = partyab_compat_table();
	$rows  = $wpdb->get_results( "SELECT car_brand, COUNT(*) AS total FROM {$table} GROUP BY car_brand ORDER BY total DESC" ); // phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery

	return (array) $rows;
}

/**
 * مدل‌های یک برند.
 *
 * @param string $brand برند خودرو.
 * @return string[]
 */
function partyab_compat_models( string $brand ): array {
	global $wpdb;

	$table = partyab_compat_table();
	$sql   = "SELECT DISTINCT car_model FROM {$table} WHERE car_brand = %s ORDER BY car_model ASC";

	// phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery
	return array_map( 'strval', (array) $wpdb->get_col( $wpdb->prepare( $sql, partyab_compat_normalize( $brand ) ) ) );
}

/**
 * پاک کردن کش پس از ایمپورت.
 */
function partyab_compat_flush_cache(): void {
	wp_cache_flush_group( 'partyab_compat' );
}
