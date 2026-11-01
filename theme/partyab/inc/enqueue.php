<?php
/**
 * Assets.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue styles and scripts.
 */
function partyab_assets() {
	$css_file = PARTYAB_THEME_DIR . '/assets/css/style.css';
	$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : PARTYAB_THEME_VERSION;

	// Theme header (style.css) — WordPress expects it registered.
	wp_enqueue_style( 'partyab-header', get_stylesheet_uri(), array(), PARTYAB_THEME_VERSION );

	// The design system itself.
	wp_enqueue_style( 'partyab-design', PARTYAB_THEME_URI . '/assets/css/style.css', array( 'partyab-header' ), $css_ver );

	// WordPress-only layout (blog, 404, garage, toasts).
	$wp_file = PARTYAB_THEME_DIR . '/assets/css/wordpress.css';
	$wp_ver  = file_exists( $wp_file ) ? (string) filemtime( $wp_file ) : PARTYAB_THEME_VERSION;
	wp_enqueue_style( 'partyab-wordpress', PARTYAB_THEME_URI . '/assets/css/wordpress.css', array( 'partyab-design' ), $wp_ver );

	// WooCommerce glue (buttons, inputs, cart/checkout tables).
	if ( class_exists( 'WooCommerce' ) ) {
		$wc_file = PARTYAB_THEME_DIR . '/assets/css/woocommerce.css';
		$wc_ver  = file_exists( $wc_file ) ? (string) filemtime( $wc_file ) : PARTYAB_THEME_VERSION;
		wp_enqueue_style( 'partyab-woocommerce', PARTYAB_THEME_URI . '/assets/css/woocommerce.css', array( 'partyab-design' ), $wc_ver );
	}

	$js_file = PARTYAB_THEME_DIR . '/assets/js/partyab.js';
	$js_ver  = file_exists( $js_file ) ? (string) filemtime( $js_file ) : PARTYAB_THEME_VERSION;

	wp_enqueue_script( 'partyab-app', PARTYAB_THEME_URI . '/assets/js/partyab.js', array(), $js_ver, true );

	wp_localize_script(
		'partyab-app',
		'PartYab',
		array(
			'restUrl'  => esc_url_raw( rest_url( 'partyab/v1/' ) ),
			'home'     => esc_url_raw( home_url( '/' ) ),
			'shopUrl'  => function_exists( 'wc_get_page_permalink' ) ? esc_url_raw( wc_get_page_permalink( 'shop' ) ) : esc_url_raw( home_url( '/' ) ),
			'cookie'   => defined( 'PARTYAB_COOKIE' ) ? PARTYAB_COOKIE : 'partyab_vehicle',
			'cookieTtl' => 60 * 60 * 24 * 180,
			'garageUrl' => esc_url_raw( partyab_garage_url() ),
			'cartUrl'   => function_exists( 'wc_get_cart_url' ) ? esc_url_raw( wc_get_cart_url() ) : '',
			'icons'     => partyab_icon_paths(),
			'strings'  => array(
				'close'       => __( 'بستن', 'partyab' ),
				'noResults'   => __( 'مدلی پیدا نشد؛ املای دیگری را امتحان کنید.', 'partyab' ),
				'picked'      => __( 'خودروی شما ثبت شد', 'partyab' ),
				'remove'      => __( 'حذف خودرو', 'partyab' ),
				'selectCar'   => __( 'خودرو را انتخاب کنید', 'partyab' ),
				'confirm'     => __( 'تأیید و مشاهدهٔ قطعات', 'partyab' ),
				'loading'     => __( 'در حال جست‌وجو…', 'partyab' ),
				'added'       => __( 'به سبد خرید اضافه شد', 'partyab' ),
				'error'       => __( 'ارتباط برقرار نشد؛ دوباره تلاش کنید.', 'partyab' ),
				'maxCars'     => __( 'حداکثر ۳ خودرو در گاراژ', 'partyab' ),
				'emptyGarage' => __( 'هنوز خودرویی ثبت نکرده‌اید', 'partyab' ),
				'letsGo'      => __( 'ثبت خودرو', 'partyab' ),
				'searchCar'   => __( 'نام مدل خودرو را بنویسید', 'partyab' ),
				'yearFirst'   => __( 'اول مدل را انتخاب کنید', 'partyab' ),
				'noCompat'    => __( 'برای این خودرو قطعهٔ تأییدشده‌ای ثبت نشده است.', 'partyab' ),
				'coverage'    => __( 'پوشش کاتالوگ', 'partyab' ),
				'removeCar'   => __( 'حذف خودرو', 'partyab' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'partyab_assets' );

/**
 * Preload the two most used font files (they are referenced from the CSS).
 */
function partyab_preload_fonts() {
	$fonts = array(
		'vazirmatn-arabic-400-normal.woff2',
		'vazirmatn-arabic-700-normal.woff2',
	);
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( PARTYAB_THEME_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'partyab_preload_fonts', 2 );

/**
 * Inline the design tokens early so the page does not flash white.
 */
function partyab_inline_head() {
	echo '<meta name="theme-color" content="#132238">' . "\n";
}
add_action( 'wp_head', 'partyab_inline_head', 1 );
