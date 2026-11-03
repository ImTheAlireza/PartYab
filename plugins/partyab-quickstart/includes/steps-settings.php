<?php
/**
 * مرحله ۳: تنظیمات وردپرس، ووکامرس و المنتور.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * اعمال تنظیمات پایه.
 *
 * @param array $report گزارش.
 */
function partyab_qs_step_settings( array &$report ): void {
	// ---- وردپرس ----
	update_option( 'timezone_string', 'Asia/Tehran' );
	update_option( 'start_of_week', 6 );
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'blogdescription', 'قطعه درست، همان بار اول' );

	// بسته زبان فارسی: اگر نصب نباشد تلاش می‌کنیم دانلود شود (نیاز به اینترنت).
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';

	$langs = function_exists( 'get_available_languages' ) ? get_available_languages() : array();

	if ( ! in_array( 'fa_IR', $langs, true ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$downloaded = wp_download_language_pack( 'fa_IR' );
		partyab_qs_log(
			$report,
			'تنظیمات',
			'بسته زبان فارسی',
			(bool) $downloaded,
			$downloaded ? 'دانلود و نصب شد' : 'دانلود نشد (اینترنت؟) — از پیشخوان ← پیشخوان ← به‌روزرسانی‌ها دستی نصب کن'
		);
	}

	update_option( 'WPLANG', 'fa_IR' );

	partyab_qs_log( $report, 'تنظیمات', 'پیوند یکتا و منطقه زمانی', true, 'نام نوشته + تهران + هفته از شنبه' );

	// ---- ووکامرس ----
	update_option( 'woocommerce_currency', 'IRR' );
	update_option( 'woocommerce_currency_symbol', 'تومان' );
	update_option( 'woocommerce_price_num_decimals', 0 );
	update_option( 'woocommerce_currency_pos', 'right_space' );
	update_option( 'woocommerce_price_thousand_sep', ',' );
	update_option( 'woocommerce_price_decimal_sep', '.' );
	update_option( 'woocommerce_weight_unit', 'kg' );
	update_option( 'woocommerce_dimension_unit', 'cm' );
	update_option( 'woocommerce_enable_guest_checkout', 'yes' );
	update_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );
	update_option( 'woocommerce_manage_stock', 'yes' );
	update_option( 'woocommerce_notify_low_stock_amount', 2 );
	update_option( 'woocommerce_default_country', 'IR:THR' );
	update_option( 'woocommerce_allow_tracking', 'no' );

	// واحد پول: عدد ذخیره‌شده = تومان، در حالی که کد ارز IRR است.
	partyab_qs_log( $report, 'تنظیمات', 'ووکامرس: تومان، کیلوگرم، سانتی‌متر', true, 'قیمت‌ها به تومان ذخیره می‌شوند' );

	// ---- المنتور ----
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	update_option( 'elementor_container_width', 1240 );
	update_option( 'elementor_experiment-container', 'active' );
	update_option( 'elementor_experiment-e_container', 'active' );
	update_option( 'elementor_page_title_selector', 'h1' );
	update_option( 'elementor_cpt_support', array( 'page', 'post', 'product' ) );
	update_option( 'elementor_unfiltered_files_upload', 1 );

	partyab_qs_log( $report, 'تنظیمات', 'المنتور: Container فعال، رنگ/فونت پیش‌فرض خاموش', true, 'عرض محتوا ۱۲۴۰px' );

	// ---- اندازه تصاویر فروشگاه ----
	update_option( 'woocommerce_thumbnail_image_width', 600 );
	update_option( 'woocommerce_single_image_width', 1000 );
	update_option( 'woocommerce_thumbnail_cropping', '1:1' );

	flush_rewrite_rules( false );
	partyab_qs_log( $report, 'تنظیمات', 'بازنویسی پیوندها', true, 'rewrite flush شد' );
}
