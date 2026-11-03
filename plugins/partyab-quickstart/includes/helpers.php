<?php
/**
 * ابزارهای مشترک نصب‌کننده.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * مسیر پوشه بسته‌های همراه (vendor).
 *
 * @return string
 */
function partyab_qs_vendor_dir(): string {
	return PARTYAB_QS_DIR . 'vendor/';
}

/**
 * پیدا کردن اولین فایل ZIP مطابق یک الگو در vendor.
 *
 * @param string $pattern الگو (مثل elementor-pro*).
 * @return string مسیر فایل یا رشته خالی.
 */
function partyab_qs_find_zip( string $pattern ): string {
	$files = glob( partyab_qs_vendor_dir() . '*.zip' );

	if ( ! $files ) {
		return '';
	}

	// مقایسه بدون حساسیت به حروف بزرگ/کوچک — روی لینوکس نام Test.zip و test.zip یکی نیستند.
	$regex = '/^' . str_replace( '\*', '.*', preg_quote( strtolower( $pattern ), '/' ) ) . '\.zip$/';

	foreach ( $files as $file ) {
		if ( preg_match( $regex, strtolower( basename( $file ) ) ) ) {
			return $file;
		}
	}

	return '';
}

/**
 * فایل افزونه فعال‌شده را بر اساس پوشه پیدا می‌کند.
 *
 * @param string $slug نام پوشه افزونه.
 * @return string مسیر نسبی افزونه یا رشته خالی.
 */
function partyab_qs_plugin_file( string $slug ): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	foreach ( array_keys( get_plugins() ) as $file ) {
		if ( 0 === strpos( $file, $slug . '/' ) ) {
			return $file;
		}
	}

	return '';
}

/**
 * آیا افزونه نصب و فعال است؟
 *
 * @param string $slug نام پوشه.
 * @return bool
 */
function partyab_qs_is_active( string $slug ): bool {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$file = partyab_qs_plugin_file( $slug );

	return $file && is_plugin_active( $file );
}

/**
 * نصب افزونه از فایل ZIP محلی.
 *
 * @param string $zip مسیر فایل.
 * @return array{ok:bool,msg:string}
 */
function partyab_qs_install_plugin_zip( string $zip ): array {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( ! file_exists( $zip ) ) {
		return array( 'ok' => false, 'msg' => 'فایل بسته پیدا نشد: ' . basename( $zip ) );
	}

	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $zip );

	if ( is_wp_error( $result ) ) {
		return array( 'ok' => false, 'msg' => $result->get_error_message() );
	}

	return array( 'ok' => true, 'msg' => 'نصب شد: ' . basename( $zip ) );
}

/**
 * نصب افزونه از مخزن وردپرس.
 *
 * @param string $slug نام افزونه.
 * @return array{ok:bool,msg:string}
 */
function partyab_qs_install_plugin_wporg( string $slug ): array {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$url      = 'https://downloads.wordpress.org/plugin/' . $slug . '.latest-stable.zip';
	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $url );

	if ( is_wp_error( $result ) ) {
		return array( 'ok' => false, 'msg' => $result->get_error_message() );
	}

	return array( 'ok' => true, 'msg' => 'از مخزن وردپرس نصب شد: ' . $slug );
}

/**
 * فعال‌سازی افزونه.
 *
 * @param string $slug نام پوشه.
 * @return array{ok:bool,msg:string}
 */
function partyab_qs_activate_plugin( string $slug ): array {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$file = partyab_qs_plugin_file( $slug );

	if ( ! $file ) {
		return array( 'ok' => false, 'msg' => 'افزونه نصب نشد: ' . $slug );
	}

	if ( is_plugin_active( $file ) ) {
		return array( 'ok' => true, 'msg' => 'از قبل فعال بود: ' . $slug );
	}

	$result = activate_plugin( $file );

	if ( is_wp_error( $result ) ) {
		return array( 'ok' => false, 'msg' => $result->get_error_message() );
	}

	return array( 'ok' => true, 'msg' => 'فعال شد: ' . $slug );
}

/**
 * نصب قالب از ZIP.
 *
 * @param string $zip مسیر فایل.
 * @return array{ok:bool,msg:string}
 */
function partyab_qs_install_theme_zip( string $zip ): array {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme.php';

	if ( ! file_exists( $zip ) ) {
		return array( 'ok' => false, 'msg' => 'فایل قالب پیدا نشد: ' . basename( $zip ) );
	}

	$upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $zip );

	if ( is_wp_error( $result ) ) {
		return array( 'ok' => false, 'msg' => $result->get_error_message() );
	}

	return array( 'ok' => true, 'msg' => 'قالب نصب شد: ' . basename( $zip ) );
}

/**
 * افزودن یک ردیف به گزارش.
 *
 * @param array  $report گزارش (با ارجاع).
 * @param string $group  نام مرحله.
 * @param string $label  عنوان.
 * @param bool   $ok     موفق بودن.
 * @param string $msg    پیام.
 */
function partyab_qs_log( array &$report, string $group, string $label, bool $ok, string $msg = '' ): void {
	$report[] = array(
		'group' => $group,
		'label' => $label,
		'ok'    => $ok,
		'msg'   => $msg,
	);
}
