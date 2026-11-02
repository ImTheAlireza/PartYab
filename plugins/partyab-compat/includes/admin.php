<?php
/**
 * صفحه مدیریت: وضعیت جدول + ایمپورت CSV + نمونه فایل.
 *
 * @package PartYab_Compat
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'partyab_compat_admin_menu' );

/**
 * افزودن منو.
 */
function partyab_compat_admin_menu(): void {
	add_menu_page(
		'سازگاری پارتیاب',
		'سازگاری',
		'manage_woocommerce',
		'partyab-compat',
		'partyab_compat_admin_page',
		'dashicons-car',
		56
	);
}

/**
 * رندر صفحه.
 */
function partyab_compat_admin_page(): void {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$rows     = partyab_compat_row_count();
	$products = partyab_compat_product_count();
	$last     = get_option( 'partyab_compat_last_import', array() );
	$brands   = partyab_compat_brands();

	echo '<div class="wrap">';
	echo '<h1>موتور سازگاری قطعه با خودرو</h1>';

	// پیام‌ها.
	if ( isset( $_GET['pb_inserted'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		printf(
			'<div class="notice notice-success is-dismissible"><p>ایمپورت انجام شد: %s ردیف درج شد، %s ردیف رد شد.</p></div>',
			esc_html( (string) (int) $_GET['pb_inserted'] ), // phpcs:ignore WordPress.Security.NonceVerification
			esc_html( (string) (int) ( $_GET['pb_skipped'] ?? 0 ) ) // phpcs:ignore WordPress.Security.NonceVerification
		);
	}

	if ( isset( $_GET['pb_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice-error"><p>' . esc_html( wp_unslash( $_GET['pb_error'] ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification
	}

	// کارت وضعیت.
	echo '<table class="widefat striped" style="max-width:640px;margin:16px 0">';
	echo '<tbody>';
	printf( '<tr><td>تعداد ردیف سازگاری</td><td><strong>%s</strong></td></tr>', esc_html( number_format_i18n( $rows ) ) );
	printf( '<tr><td>تعداد محصول دارای داده</td><td><strong>%s</strong></td></tr>', esc_html( number_format_i18n( $products ) ) );

	if ( ! empty( $last['time'] ) ) {
		printf(
			'<tr><td>آخرین ایمپورت</td><td>%s — %s ردیف (%s)</td></tr>',
			esc_html( $last['time'] ),
			esc_html( number_format_i18n( (int) $last['inserted'] ) ),
			esc_html( 'replace' === $last['mode'] ? 'جایگزینی' : 'افزودن' )
		);
	}

	echo '</tbody></table>';

	// فرم آپلود.
	echo '<h2>ایمپورت CSV</h2>';
	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'partyab_compat_import' );
	echo '<input type="hidden" name="action" value="partyab_compat_import">';
	echo '<p><input type="file" name="csv" accept=".csv" required></p>';
	echo '<p><label><input type="radio" name="mode" value="append" checked> افزودن به داده‌های موجود</label><br>';
	echo '<label><input type="radio" name="mode" value="replace"> <strong>جایگزینی کامل</strong> (جدول خالی می‌شود)</label></p>';
	submit_button( 'شروع ایمپورت' );
	echo '</form>';

	// نمونه و راهنما.
	echo '<h2>قالب فایل</h2>';
	echo '<p>ستون‌های لازم: <code>sku</code> (یا <code>product_id</code>)، <code>car_brand</code>، <code>car_model</code>، <code>year_from</code>، <code>year_to</code> — اختیاری: <code>engine</code>، <code>note</code></p>';
	echo '<p>نمونه آماده در مخزن: <code>data/compat-sample.csv</code></p>';

	// برندها.
	if ( $brands ) {
		echo '<h2>برندهای ثبت‌شده</h2>';
		echo '<table class="widefat striped" style="max-width:640px"><thead><tr><th>برند</th><th>تعداد ردیف</th></tr></thead><tbody>';
		foreach ( array_slice( $brands, 0, 15 ) as $brand ) {
			printf(
				'<tr><td>%s</td><td>%s</td></tr>',
				esc_html( $brand->car_brand ),
				esc_html( number_format_i18n( (int) $brand->total ) )
			);
		}
		echo '</tbody></table>';
	}

	echo '</div>';
}
