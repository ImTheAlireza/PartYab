<?php
/**
 * بارگذاری استایل، اسکریپت و فونت با نسخه‌گذاری.
 *
 * قاعده: هر فایل ثبت‌شده باید با filemtime نسخه بگیرد تا کش قدیمی سرو نشود.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'partyab_enqueue_assets', 30 );

/**
 * صف‌بندی دارایی‌های فرانت.
 */
function partyab_enqueue_assets(): void {
	// استایل قالب والد (Hello Elementor).
	wp_enqueue_style(
		'partyab-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		PARTYAB_VERSION
	);

	// استایل اصلی ما — بعد از والد.
	wp_enqueue_style(
		'partyab-base',
		PARTYAB_URI . '/assets/css/base.css',
		array( 'partyab-parent' ),
		partyab_asset_version( 'assets/css/base.css' )
	);

	// اسکریپت کوچک تعاملات پایه (بدون jQuery).
	wp_enqueue_script(
		'partyab-main',
		PARTYAB_URI . '/assets/js/main.js',
		array(),
		partyab_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// استایل چیدمان: هدر، ناوبری، صفحه اصلی، فوتر.
	wp_enqueue_style(
		'partyab-layout',
		PARTYAB_URI . '/assets/css/layout.css',
		array( 'partyab-base' ),
		partyab_asset_version( 'assets/css/layout.css' )
	);

	// استایل و اسکریپت پیشنهاد زنده جستجو.
	wp_enqueue_style(
		'partyab-search',
		PARTYAB_URI . '/assets/css/search.css',
		array( 'partyab-base' ),
		partyab_asset_version( 'assets/css/search.css' )
	);

	wp_enqueue_script(
		'partyab-search',
		PARTYAB_URI . '/assets/js/search.js',
		array(),
		partyab_asset_version( 'assets/js/search.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// استایل صفحه‌های محتوایی (درباره، تماس، قوانین).
	wp_enqueue_style(
		'partyab-content',
		PARTYAB_URI . '/assets/css/content.css',
		array( 'partyab-base' ),
		partyab_asset_version( 'assets/css/content.css' )
	);

	// استایل رابط سازگاری خودرو (سبک، همیشه لازم است).
	wp_enqueue_style(
		'partyab-compat-ui',
		PARTYAB_URI . '/assets/css/compat-ui.css',
		array( 'partyab-base' ),
		partyab_asset_version( 'assets/css/compat-ui.css' )
	);

	// اگر افزونه سازگاری فعال است، انتخابگر خودرو را هم بارگذاری کن.
	if ( function_exists( 'partyab_compat_brands' ) && function_exists( 'partyab_cars_map' ) ) {
		wp_enqueue_script(
			'partyab-car-ui',
			PARTYAB_URI . '/assets/js/compat-ui.js',
			array(),
			partyab_asset_version( 'assets/js/compat-ui.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$partyab_car = function_exists( 'partyab_current_car' ) ? partyab_current_car() : array( 'model' => '' );

		wp_localize_script( 'partyab-car-ui', 'partyabCarData', array(
			'cars'         => partyab_cars_map(),
			'currentModel' => (string) ( $partyab_car['model'] ?? '' ),
		) );
	}

	// Shared redesign components load after legacy/plugin styles.
	wp_enqueue_style( 'partyab-redesign', PARTYAB_URI . '/assets/css/redesign.css', array( 'partyab-layout', 'partyab-content', 'partyab-compat-ui', 'partyab-search' ), partyab_asset_version( 'assets/css/redesign.css' ) );
	wp_enqueue_style( 'partyab-motion', PARTYAB_URI . '/assets/css/motion.css', array( 'partyab-redesign' ), partyab_asset_version( 'assets/css/motion.css' ) );
	wp_enqueue_style( 'partyab-interaction', PARTYAB_URI . '/assets/css/interaction.css', array( 'partyab-motion' ), partyab_asset_version( 'assets/css/interaction.css' ) );
	wp_enqueue_script( 'partyab-ui', PARTYAB_URI . '/assets/js/ui.js', array(), partyab_asset_version( 'assets/js/ui.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'partyab-motion', PARTYAB_URI . '/assets/js/motion.js', array( 'partyab-ui', 'partyab-main' ), partyab_asset_version( 'assets/js/motion.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'partyab-page-transition', PARTYAB_URI . '/assets/js/page-transition.js', array( 'partyab-motion' ), partyab_asset_version( 'assets/js/page-transition.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'partyab-interaction', PARTYAB_URI . '/assets/js/interaction.js', array( 'partyab-ui', 'partyab-motion' ), partyab_asset_version( 'assets/js/interaction.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

	// داده‌های لازم در جاوااسکریپت (بدون inline script پراکنده).
	wp_localize_script( 'partyab-main', 'partyabData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'partyab_nonce' ),
		'isRtl'   => is_rtl(),
	) );
}

add_action( 'wp_head', 'partyab_preload_font', 1 );

/**
 * preload فونت متغیر — جلوگیری از پرش متن (CLS).
 */
function partyab_preload_font(): void {
	$font = PARTYAB_URI . '/assets/fonts/Vazirmatn-Variable.woff2';

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( $font )
	);
}

add_action( 'wp_enqueue_scripts', 'partyab_dequeue_parent_fonts', 99 );

/**
 * حذف فونت‌های گوگل قالب والد — ما وزیرمتن را self-host می‌کنیم.
 */
function partyab_dequeue_parent_fonts(): void {
	foreach ( array( 'hello-elementor-fonts', 'hello-elementor-google-fonts' ) as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}
}
