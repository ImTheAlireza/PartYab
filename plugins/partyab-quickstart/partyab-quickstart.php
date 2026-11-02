<?php
/**
 * Plugin Name:       PartYab Quick Start
 * Description:       نصب‌کننده یک‌کلیکی پارتیاب: نصب قالب و افزونه‌ها، تنظیمات، محصولات تست با عکس، داده سازگاری.
 * Version:           1.1.1
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            PartYab
 * License:           GPL v3 or later
 * Text Domain:       partyab-quickstart
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

// محافظ کپی تکراری: اگر نسخه دومی از افزونه فعال شود، به‌جای خطای «redeclare» بی‌صدا خارج می‌شود.
if ( defined( 'PARTYAB_QS_LOADED' ) ) {
	return;
}

define( 'PARTYAB_QS_LOADED', true );

define( 'PARTYAB_QS_VERSION', '1.1.1' );
define( 'PARTYAB_QS_FILE', __FILE__ );
define( 'PARTYAB_QS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PARTYAB_QS_URI', plugin_dir_url( __FILE__ ) );

/**
 * خطاهای هنگام بارگذاری ماژول‌ها — به‌جای مرگ سایت نمایش داده می‌شوند.
 *
 * @var array<int,string>
 */
$GLOBALS['partyab_qs_boot_errors'] = array();

/**
 * بارگذاری امن یک ماژول.
 *
 * @param string $module نام فایل بدون .php.
 */
function partyab_qs_load( string $module ): void {
	$file = PARTYAB_QS_DIR . 'includes/' . $module . '.php';

	if ( ! is_readable( $file ) ) {
		$GLOBALS['partyab_qs_boot_errors'][] = 'فایل ماژول پیدا نشد: includes/' . $module . '.php';

		return;
	}

	try {
		require_once $file;
	} catch ( \Throwable $error ) {
		$GLOBALS['partyab_qs_boot_errors'][] = $module . ': ' . $error->getMessage();
	}
}

// حداقل نسخه PHP — اگر قدیمی بود، بی‌صدا خارج شو تا سایت سفید نشود.
if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error"><p><strong>نصب سریع پارتیاب:</strong> این افزونه به PHP 8.0 به بالا نیاز دارد. نسخه فعلی: ' . esc_html( PHP_VERSION ) . '</p></div>';
	} );

	return;
}

partyab_qs_load( 'helpers' );
partyab_qs_load( 'steps-plugins' );
partyab_qs_load( 'steps-settings' );
partyab_qs_load( 'steps-content' );
partyab_qs_load( 'runner' );
partyab_qs_load( 'uploader' );
partyab_qs_load( 'admin' );

/**
 * نمایش خطاهای بارگذاری در پیشخوان (به‌جای خطای مرگبار).
 */
add_action( 'admin_notices', function () {
	if ( empty( $GLOBALS['partyab_qs_boot_errors'] ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p><strong>نصب سریع پارتیاب — خطای بارگذاری:</strong></p><ul style="list-style:disc;padding-inline-start:20px">';

	foreach ( $GLOBALS['partyab_qs_boot_errors'] as $error ) {
		echo '<li><code>' . esc_html( $error ) . '</code></li>';
	}

	echo '</ul></div>';
} );

/**
 * لاگ اختیاری برای عیب‌یابی.
 *
 * @param string $message پیام.
 */
function partyab_qs_debug( string $message ): void {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( '[partyab-quickstart] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}
}
