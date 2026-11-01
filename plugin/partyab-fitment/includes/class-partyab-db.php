<?php
/**
 * Fitment table (create / read / write).
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_DB
 */
class Partyab_DB {

	/**
	 * Schema version — bump to force a dbDelta upgrade.
	 */
	const VERSION = '1.0.0';

	/**
	 * Table name.
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'partyab_fitment';
	}

	/**
	 * Create or upgrade the table.
	 */
	public static function install() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = self::table();
		$collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id BIGINT UNSIGNED NOT NULL,
			make_id VARCHAR(32) NOT NULL DEFAULT '',
			model_id VARCHAR(32) NOT NULL DEFAULT '',
			year_from SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			year_to SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(16) NOT NULL DEFAULT 'fit',
			note TEXT NULL,
			updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY product_id (product_id),
			KEY model_id (model_id),
			KEY years (year_from, year_to)
		) {$collate};";

		dbDelta( $sql );

		update_option( 'partyab_fitment_db', self::VERSION );
	}

	/**
	 * Run install() when the stored schema version is stale.
	 */
	public static function maybe_upgrade() {
		if ( get_option( 'partyab_fitment_db' ) !== self::VERSION ) {
			self::install();
		}
	}

	/**
	 * All rows for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return array
	 */
	public static function rows_for( $product_id ) {
		global $wpdb;

		$table = self::table();
		$rows  = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare( "SELECT make_id, model_id, year_from, year_to, status, note FROM {$table} WHERE product_id = %d ORDER BY model_id ASC, year_from ASC", (int) $product_id ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Replace all rows of a product.
	 *
	 * @param int   $product_id Product ID.
	 * @param array $rows       Rows: model_id, year_from, year_to, status, note.
	 * @return int Number of rows written.
	 */
	public static function replace_for( $product_id, $rows ) {
		global $wpdb;

		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return 0;
		}

		$table = self::table();
		$wpdb->delete( $table, array( 'product_id' => $product_id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		$written = 0;
		foreach ( (array) $rows as $row ) {
			$model_id = isset( $row['model_id'] ) ? sanitize_key( $row['model_id'] ) : '';
			$model    = Partyab_Vehicles::find_model( $model_id );
			if ( ! $model ) {
				continue;
			}

			$from = isset( $row['year_from'] ) ? (int) $row['year_from'] : (int) $model['years'][0];
			$to   = isset( $row['year_to'] ) ? (int) $row['year_to'] : (int) $model['years'][1];
			if ( $from > $to ) {
				$tmp  = $from;
				$from = $to;
				$to   = $tmp;
			}

			$status = isset( $row['status'] ) && 'conditional' === $row['status'] ? 'conditional' : 'fit';

			$ok = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table,
				array(
					'product_id' => $product_id,
					'make_id'    => $model['make_id'],
					'model_id'   => $model_id,
					'year_from'  => $from,
					'year_to'    => $to,
					'status'     => $status,
					'note'       => isset( $row['note'] ) ? sanitize_textarea_field( $row['note'] ) : '',
					'updated_at' => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s' )
			);

			if ( $ok ) {
				$written++;
			}
		}

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients( $product_id );
		}

		/**
		 * Fires after a product fitment list was replaced.
		 *
		 * @param int   $product_id Product ID.
		 * @param array $rows       Saved rows.
		 */
		do_action( 'partyab_fitment_saved', $product_id, $rows );

		return $written;
	}

	/**
	 * Product IDs compatible with a vehicle.
	 *
	 * @param array $vehicle  Normalized vehicle.
	 * @param bool  $with_conditional Include conditional rows.
	 * @return int[]
	 */
	public static function product_ids_for( $vehicle, $with_conditional = true ) {
		global $wpdb;

		if ( empty( $vehicle['model_id'] ) ) {
			return array();
		}

		$table  = self::table();
		$year   = (int) $vehicle['year'];
		$status = $with_conditional ? "IN ('fit','conditional')" : "= 'fit'";

		$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT DISTINCT product_id FROM {$table} WHERE model_id = %s AND %d BETWEEN year_from AND year_to AND status {$status}", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$vehicle['model_id'],
				$year
			)
		);

		return array_map( 'intval', is_array( $ids ) ? $ids : array() );
	}

	/**
	 * Number of products with at least one fitment row.
	 *
	 * @return int
	 */
	public static function fitted_product_count() {
		global $wpdb;
		$table = self::table();
		return (int) $wpdb->get_var( "SELECT COUNT(DISTINCT product_id) FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Drop everything (used by the uninstall file).
	 */
	public static function uninstall() {
		global $wpdb;
		$table = self::table();
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB
		delete_option( 'partyab_fitment_db' );
	}
}
