<?php
/**
 * PartYab child theme bootstrap.
 *
 * قاعده پروژه: هیچ فایل بالای ۳۰۰ خط نمی‌رود و هر قابلیت یک ماژول در inc/ دارد.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_VERSION', '2.6.0' );
define( 'PARTYAB_DIR', get_stylesheet_directory() );
define( 'PARTYAB_URI', get_stylesheet_directory_uri() );

/**
 * نسخه‌گذاری بر اساس زمان تغییر فایل — کش مرورگر را دقیق می‌کند.
 *
 * @param string $relative_path مسیر نسبت به ریشه چایلد تم.
 * @return string
 */
function partyab_asset_version( string $relative_path ): string {
	$file = PARTYAB_DIR . '/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? (string) filemtime( $file ) : PARTYAB_VERSION;
}

$partyab_modules = array(
	'setup',       // ویژگی‌های قالب، منوها، اندازه تصاویر.
	'design',      // آیکن‌ها و اجزای مشترک بازطراحی.
	'motion-settings', // تنظیمات حرکت/صدا و پوشش اولیهٔ ترنزیشن.
	'enqueue',     // استایل/اسکریپت/فونت با نسخه‌گذاری.
	'security',    // سخت‌سازی پیش‌فرض وردپرس.
	'woocommerce', // تنظیمات حلقه و آرشیو فروشگاه.
	'design-commerce', // چیدمان صفحه‌های ووکامرس.
	'currency',    // نمایش واحد پول به تومان (تصمیم D15).
	'taxonomies',  // برند قطعه و خودرو (برند → مدل).
	'layout',      // منوهای جایگزین، عنوان صفحه، کلاس‌های body.
	'compat-ui',   // باکس «خودروی من»، بج سازگاری، فیلتر آرشیو.
	'compat-table', // تب «سازگاری با خودرو» در صفحه محصول.
	'related',     // مرتبط‌ها و مکمل خرید.
	'recent',      // بازدیدهای اخیر.
	'pages',       // شورت‌کدهای محتوایی: سؤالات متداول، تماس، راهنما.
	'contact-form', // فرم تماس سبک بدون افزونه.
	'search',       // جستجوی شماره فنی، ارقام فارسی و گزارش بی‌نتیجه.
	'search-suggest', // پیشنهاد زنده جستجو و گزارش پیشخوان.
	'seo',          // متا، Open Graph، داده ساختاریافته، پاک‌سازی هدر.
	'seo-meta',     // خروجی متا و JSON-LD در wp_head.
);

foreach ( $partyab_modules as $partyab_module ) {
	$partyab_file = PARTYAB_DIR . '/inc/' . $partyab_module . '.php';

	if ( is_readable( $partyab_file ) ) {
		require_once $partyab_file;
	}
}

unset( $partyab_modules, $partyab_module, $partyab_file );
