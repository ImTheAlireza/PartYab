<?php
/**
 * ساخت جدول سازگاری.
 *
 * @package PartYab_Compat
 */

defined( 'ABSPATH' ) || exit;

/**
 * نام جدول.
 *
 * @return string
 */
function partyab_compat_table(): string {
	global $wpdb;

	return $wpdb->prefix . 'partyab_compat';
}

/**
 * ساخت جدول با dbDelta.
 */
function partyab_compat_install(): void {
	global $wpdb;

	$table   = partyab_compat_table();
	$collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		product_id BIGINT UNSIGNED NOT NULL,
		car_brand VARCHAR(64) NOT NULL,
		car_model VARCHAR(64) NOT NULL,
		year_from SMALLINT UNSIGNED NOT NULL,
		year_to SMALLINT UNSIGNED NOT NULL,
		engine VARCHAR(64) NOT NULL DEFAULT '',
		note VARCHAR(190) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY product_id (product_id),
		KEY car_lookup (car_brand, car_model, year_from, year_to)
	) {$collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'partyab_compat_db_version', PARTYAB_COMPAT_DB_VERSION );
}

/**
 * تعداد ردیف‌های جدول.
 *
 * @return int
 */
function partyab_compat_row_count(): int {
	global $wpdb;

	return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . partyab_compat_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * تعداد محصولات متمایز دارای داده سازگاری.
 *
 * @return int
 */
function partyab_compat_product_count(): int {
	global $wpdb;

	return (int) $wpdb->get_var( 'SELECT COUNT(DISTINCT product_id) FROM ' . partyab_compat_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * پاک کردن کل جدول (قبل از ایمپورت حالت replace).
 *
 * @return void
 */
function partyab_compat_truncate(): void {
	global $wpdb;

	$wpdb->query( 'TRUNCATE TABLE ' . partyab_compat_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * درج یک ردیف سازگاری.
 *
 * @param array $row داده ردیف.
 * @return int شناسه ردیف.
 */
function partyab_compat_insert_row( array $row ): int {
	global $wpdb;

	$wpdb->insert(
		partyab_compat_table(),
		array(
			'product_id' => (int) $row['product_id'],
			'car_brand'  => mb_substr( (string) $row['car_brand'], 0, 64 ),
			'car_model'  => mb_substr( (string) $row['car_model'], 0, 64 ),
			'year_from'  => (int) $row['year_from'],
			'year_to'    => (int) $row['year_to'],
			'engine'     => mb_substr( (string) ( $row['engine'] ?? '' ), 0, 64 ),
			'note'       => mb_substr( (string) ( $row['note'] ?? '' ), 0, 190 ),
			'created_at' => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s' )
	);

	return (int) $wpdb->insert_id;
}
