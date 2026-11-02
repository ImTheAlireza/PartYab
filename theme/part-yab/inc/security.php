<?php
/**
 * سخت‌سازی پایه امنیتی.
 *
 * هر مورد عمداً محافظه‌کارانه است: هیچ چیزی جلوی کارکرد افزونه‌های لازم را نمی‌گیرد.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * خاموش کردن XML-RPC (بزرگ‌ترین سطح حمله رمزعبور).
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

add_filter( 'wp_headers', 'partyab_remove_pingback_header' );

/**
 * حذف هدر X-Pingback.
 *
 * @param array $headers هدرها.
 * @return array
 */
function partyab_remove_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );

	return $headers;
}

/**
 * جلوگیری از شمارش کاربران از طریق REST API و آرشیو نویسنده.
 */
add_filter( 'rest_endpoints', 'partyab_restrict_user_endpoints' );

/**
 * حذف اندپوینت عمومی کاربران.
 *
 * @param array $endpoints اندپوینت‌های REST.
 * @return array
 */
function partyab_restrict_user_endpoints( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}

/**
 * خاموش کردن ویرایشگر فایل در پیشخوان (در صورت نبود ثابت در wp-config).
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * محدود کردن بارگذاری SVG به مدیر کل — جلوگیری از آپلود SVG آلوده توسط نقش‌های پایین‌تر.
 */
add_filter( 'upload_mimes', 'partyab_restrict_svg_upload' );

/**
 * فیلتر MIME برای SVG.
 *
 * @param array $mimes انواع مجاز.
 * @return array
 */
function partyab_restrict_svg_upload( array $mimes ): array {
	if ( ! current_user_can( 'manage_options' ) ) {
		unset( $mimes['svg'] );
	}

	return $mimes;
}

/**
 * نمایش خطای مبهم در ورود (بدون لو دادن نام کاربری موجود).
 */
add_filter( 'login_errors', 'partyab_generic_login_error' );

/**
 * پیام خطای ورود عمومی.
 *
 * @param string $error پیام پیش‌فرض.
 * @return string
 */
function partyab_generic_login_error( string $error ): string {
	return 'نام کاربری یا گذرواژه درست نیست.';
}
