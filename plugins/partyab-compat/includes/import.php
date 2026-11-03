<?php
/**
 * ایمپورت CSV سازگاری — هم از پیشخوان، هم با WP-CLI.
 *
 * ستون‌های لازم: sku یا product_id + car_brand + car_model + year_from + year_to
 * ستون‌های اختیاری: engine, note
 *
 * @package PartYab_Compat
 */

defined( 'ABSPATH' ) || exit;

/**
 * خواندن و درج یک فایل CSV.
 *
 * @param string $file مسیر فایل.
 * @param string $mode append|replace.
 * @return array{inserted:int,skipped:int,errors:string[]}
 */
function partyab_compat_import_csv( string $file, string $mode = 'append' ): array {
	$report = array(
		'inserted' => 0,
		'skipped'  => 0,
		'errors'   => array(),
	);

	if ( ! is_readable( $file ) ) {
		$report['errors'][] = 'فایل خواندنی نیست.';

		return $report;
	}

	$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! $handle ) {
		$report['errors'][] = 'باز کردن فایل ممکن نشد.';

		return $report;
	}

	// حذف BOM اکسل.
	$first = fgets( $handle );
	$first = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $first );
	$map   = array_map( 'trim', str_getcsv( $first, ',' ) );

	if ( ! in_array( 'car_brand', $map, true ) || ! in_array( 'car_model', $map, true ) ) {
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$report['errors'][] = 'ردیف عنوان (header) پیدا نشد یا ستون car_brand/car_model ندارد.';

		return $report;
	}

	if ( 'replace' === $mode ) {
		partyab_compat_truncate();
	}

	$line = 1;

	while ( ( $row = fgetcsv( $handle, 0, ',' ) ) !== false ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
		++$line;

		if ( ! is_array( $row ) || count( array_filter( $row ) ) === 0 ) {
			continue;
		}

		$data = array();
		foreach ( $map as $index => $key ) {
			$data[ $key ] = trim( (string) ( $row[ $index ] ?? '' ) );
		}

		$product_id = 0;

		if ( ! empty( $data['product_id'] ) ) {
			$product_id = (int) $data['product_id'];
		} elseif ( ! empty( $data['sku'] ) && function_exists( 'wc_get_product_id_by_sku' ) ) {
			$product_id = (int) wc_get_product_id_by_sku( $data['sku'] );
		}

		if ( ! $product_id ) {
			++$report['skipped'];
			if ( count( $report['errors'] ) < 15 ) {
				$report['errors'][] = "خط {$line}: محصول پیدا نشد (sku: " . ( $data['sku'] ?? '-' ) . ').';
			}
			continue;
		}

		$brand = partyab_compat_normalize( $data['car_brand'] ?? '' );
		$model = partyab_compat_normalize( $data['car_model'] ?? '' );
		$from  = (int) ( $data['year_from'] ?? 0 );
		$to    = (int) ( $data['year_to'] ?? 0 );

		if ( '' === $brand || '' === $model || $from <= 0 || $to <= 0 ) {
			++$report['skipped'];
			if ( count( $report['errors'] ) < 15 ) {
				$report['errors'][] = "خط {$line}: برند/مدل/سال ناقص است.";
			}
			continue;
		}

		if ( $from > $to ) {
			++$report['skipped'];
			if ( count( $report['errors'] ) < 15 ) {
				$report['errors'][] = "خط {$line}: سال شروع بزرگ‌تر از سال پایان است.";
			}
			continue;
		}

		partyab_compat_insert_row( array(
			'product_id' => $product_id,
			'car_brand'  => $brand,
			'car_model'  => $model,
			'year_from'  => $from,
			'year_to'    => $to,
			'engine'     => $data['engine'] ?? '',
			'note'       => $data['note'] ?? '',
		) );

		++$report['inserted'];
	}

	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	partyab_compat_flush_cache();
	update_option( 'partyab_compat_last_import', array(
		'time'     => current_time( 'mysql' ),
		'inserted' => $report['inserted'],
		'skipped'  => $report['skipped'],
		'mode'     => $mode,
	) );

	return $report;
}

add_action( 'admin_post_partyab_compat_import', 'partyab_compat_handle_upload' );

/**
 * پردازش فرم آپلود CSV از پیشخوان.
 */
function partyab_compat_handle_upload(): void {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'دسترسی ندارید.' );
	}

	check_admin_referer( 'partyab_compat_import' );

	$mode = ( isset( $_POST['mode'] ) && 'replace' === $_POST['mode'] ) ? 'replace' : 'append';

	if ( empty( $_FILES['csv']['tmp_name'] ) || ! is_uploaded_file( $_FILES['csv']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		partyab_compat_redirect( array( 'pb_error' => 'فایلی انتخاب نشده است.' ) );
	}

	$tmp = $_FILES['csv']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$report = partyab_compat_import_csv( $tmp, $mode );

	partyab_compat_redirect( array(
		'pb_inserted' => $report['inserted'],
		'pb_skipped'  => $report['skipped'],
	) );
}

/**
 * بازگشت به صفحه تنظیمات با پیام.
 *
 * @param array $args پارامترهای URL.
 */
function partyab_compat_redirect( array $args ): void {
	$url = add_query_arg( array_merge( array( 'page' => 'partyab-compat' ), $args ), admin_url( 'admin.php' ) );
	wp_safe_redirect( $url );
	exit;
}

// ---------------------------------------------------------------------------
// WP-CLI
// ---------------------------------------------------------------------------
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * ایمپورت سازگاری از خط فرمان.
	 *
	 * ## EXAMPLES
	 *     wp partyab import data/compat-sample.csv --replace
	 */
	WP_CLI::add_command( 'partyab import', function ( $args, $assoc_args ) {
		$file = $args[0] ?? '';
		$mode = isset( $assoc_args['replace'] ) ? 'replace' : 'append';
		$report = partyab_compat_import_csv( $file, $mode );

		WP_CLI::log( 'درج‌شده: ' . $report['inserted'] . ' | رد‌شده: ' . $report['skipped'] );

		foreach ( $report['errors'] as $error ) {
			WP_CLI::warning( $error );
		}

		WP_CLI::success( 'ایمپورت تمام شد.' );
	} );
}
