<?php
/**
 * آپلود بسته‌های نصب از خود صفحه + عیب‌یابی محیط.
 *
 * چرا: بسته کامل ۲۷ مگابایتی روی بعضی محیط‌ها آپلود نمی‌شود.
 * با این بخش، افزونه سبک (۶۴۰KB) کافی است و بسته‌های سنگین از همین صفحه اضافه می‌شوند.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * فهرست بسته‌هایی که نصب‌کننده دنبالشان می‌گردد.
 *
 * @return array<string,array<string,mixed>>
 */
function partyab_qs_packages(): array {
	return array(
		'part-yab'       => array(
			'label'    => 'چایلد تم پارتیاب',
			'pattern'  => 'part-yab',
			'needed'   => true,
			'hint'     => 'همراه خود نصب‌کننده است؛ اگر نیست، از releases/part-yab.zip بگیر',
		),
		'partyab-compat' => array(
			'label'    => 'افزونه سازگاری قطعه-خودرو',
			'pattern'  => 'partyab-compat',
			'needed'   => true,
			'hint'     => 'همراه خود نصب‌کننده است',
		),
		'elementor'      => array(
			'label'    => 'Elementor (رایگان)',
			'pattern'  => 'elementor',
			'needed'   => false,
			'hint'     => 'اگر نبود، نصب‌کننده از مخزن وردپرس می‌گیرد',
		),
		'elementor-pro'  => array(
			'label'    => 'Elementor Pro',
			'pattern'  => 'elementor-pro*',
			'needed'   => false,
			'hint'     => 'بدون این فایل، قالب‌های صفحه و WooCommerce Builder نداریم',
		),
		'woocommerce'    => array(
			'label'    => 'WooCommerce',
			'pattern'  => 'woocommerce*',
			'needed'   => false,
			'hint'     => 'اگر نبود، از مخزن وردپرس نصب می‌شود',
		),
	);
}

/**
 * وضعیت هر بسته.
 *
 * @return array<int,array<string,mixed>>
 */
function partyab_qs_packages_status(): array {
	$status = array();

	foreach ( partyab_qs_packages() as $key => $package ) {
		$file = partyab_qs_find_zip( $package['pattern'] );

		$status[] = array(
			'key'     => $key,
			'label'   => $package['label'],
			'found'   => (bool) $file,
			'file'    => $file ? basename( $file ) : '',
			'size'    => $file ? size_format( (int) filesize( $file ) ) : '',
			'needed'  => $package['needed'],
			'hint'    => $package['hint'],
		);
	}

	return $status;
}

/**
 * نام فایل استاندارد برای هر بسته.
 *
 * @param string $key کلید بسته.
 * @return string
 */
function partyab_qs_canonical_name( string $key ): string {
	$map = array(
		'part-yab'       => 'part-yab.zip',
		'partyab-compat' => 'partyab-compat.zip',
		'elementor'      => 'elementor.zip',
		'elementor-pro'  => 'elementor-pro.zip',
		'woocommerce'    => 'woocommerce.zip',
	);

	return $map[ $key ] ?? '';
}

add_action( 'admin_post_partyab_qs_upload', 'partyab_qs_handle_upload' );

/**
 * ذخیره فایل ZIP آپلودشده در پوشه vendor.
 */
function partyab_qs_handle_upload(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'دسترسی ندارید.' );
	}

	check_admin_referer( 'partyab_qs_upload' );

	$key   = isset( $_POST['pb_package'] ) ? sanitize_key( wp_unslash( $_POST['pb_package'] ) ) : '';
	$name  = partyab_qs_canonical_name( $key );
	$error = '';

	if ( '' === $name ) {
		$error = 'نوع بسته نامعتبر است.';
	} elseif ( empty( $_FILES['pb_zip']['tmp_name'] ) || ! is_uploaded_file( $_FILES['pb_zip']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$error = 'فایلی انتخاب نشده یا آپلود ناموفق بود.';
	} elseif ( ! class_exists( 'ZipArchive' ) ) {
		$error = 'افزونه ZipArchive روی سرور فعال نیست.';
	} else {
		$tmp  = $_FILES['pb_zip']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$zip  = new ZipArchive();
		$open = $zip->open( $tmp );

		if ( true !== $open || 0 === $zip->numFiles ) {
			$error = 'فایل ZIP سالم نیست.';
		} else {
			$zip->close();

			if ( ! wp_mkdir_p( partyab_qs_vendor_dir() ) ) {
				$error = 'پوشه vendor ساخته نشد.';
			} elseif ( ! is_writable( partyab_qs_vendor_dir() ) ) {
				$error = 'پوشه vendor قابل نوشتن نیست (دسترسی فایل).';
			} elseif ( ! move_uploaded_file( $tmp, partyab_qs_vendor_dir() . $name ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
				$error = 'انتقال فایل به vendor ممکن نشد.';
			}
		}
	}

	$args = array( 'page' => 'partyab-quickstart' );

	if ( '' !== $error ) {
		$args['pb_up_error'] = rawurlencode( $error );
	} else {
		$args['pb_uploaded'] = rawurlencode( $name );
	}

	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}

/**
 * گزارش عیب‌یابی محیط.
 *
 * @return array<int,array{label:string,value:string,ok:bool}>
 */
function partyab_qs_diagnostics(): array {
	$vendor = partyab_qs_vendor_dir();

	return array(
		array(
			'label' => 'نسخه PHP',
			'value' => PHP_VERSION,
			'ok'    => version_compare( PHP_VERSION, '8.0', '>=' ),
		),
		array(
			'label' => 'نسخه وردپرس',
			'value' => get_bloginfo( 'version' ),
			'ok'    => version_compare( get_bloginfo( 'version' ), '6.0', '>=' ),
		),
		array(
			'label' => 'افزونه ZipArchive',
			'value' => class_exists( 'ZipArchive' ) ? 'فعال' : 'غیرفعال',
			'ok'    => class_exists( 'ZipArchive' ),
		),
		array(
			'label' => 'حافظه مجاز (memory_limit)',
			'value' => (string) ini_get( 'memory_limit' ),
			'ok'    => wp_convert_hr_to_bytes( (string) ini_get( 'memory_limit' ) ) >= 134217728,
		),
		array(
			'label' => 'حداکثر حجم آپلود',
			'value' => (string) ini_get( 'upload_max_filesize' ),
			'ok'    => wp_convert_hr_to_bytes( (string) ini_get( 'upload_max_filesize' ) ) >= 33554432,
		),
		array(
			'label' => 'حداکثر حجم POST',
			'value' => (string) ini_get( 'post_max_size' ),
			'ok'    => wp_convert_hr_to_bytes( (string) ini_get( 'post_max_size' ) ) >= 33554432,
		),
		array(
			'label' => 'حداکثر زمان اجرا',
			'value' => (string) ini_get( 'max_execution_time' ),
			'ok'    => (int) ini_get( 'max_execution_time' ) >= 60 || 0 === (int) ini_get( 'max_execution_time' ),
		),
		array(
			'label' => 'پوشه vendor قابل نوشتن',
			'value' => is_writable( $vendor ) ? 'بله' : ( file_exists( $vendor ) ? 'خیر' : 'وجود ندارد' ),
			'ok'    => is_writable( $vendor ),
		),
		array(
			'label' => 'حق نصب افزونه/قالب',
			'value' => current_user_can( 'install_plugins' ) ? 'دارد' : 'ندارد',
			'ok'    => current_user_can( 'install_plugins' ),
		),
	);
}
