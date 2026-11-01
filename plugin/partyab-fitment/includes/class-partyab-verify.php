<?php
/**
 * Verification suite: theme/plugin state, data, pages, REST and the fitment matrix.
 *
 *   wp partyab verify
 *
 * The same code also backs bin/smoke-test.php so LocalWP, wp-env, Docker and a live
 * server all verify identically. Exit code 1 when something fails.
 *
 * @package Partyab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Verify
 */
class Partyab_Verify {

	/**
	 * Counters.
	 *
	 * @var int
	 */
	protected static $pass = 0;

	/**
	 * @var int
	 */
	protected static $warn = 0;

	/**
	 * @var int
	 */
	protected static $fail = 0;

	/**
	 * Register the WP-CLI command.
	 */
	public static function init() {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'partyab verify', array( __CLASS__, 'cli' ) );
		}
	}

	/**
	 * CLI entry point.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Flags.
	 */
	public static function cli( $args = array(), $assoc_args = array() ) {
		$ok = self::run();

		if ( ! $ok ) {
			\WP_CLI::halt( 1 );
		}
	}

	/**
	 * Print one line through WP-CLI (or echo outside of it).
	 *
	 * @param string $message Text.
	 */
	protected static function line( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::log( $message );
			return;
		}

		echo $message . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Record one check.
	 *
	 * @param string $label  What is checked.
	 * @param bool   $ok     Passed?
	 * @param string $detail Detail line.
	 * @param bool   $soft   When true, a failure is only a warning.
	 */
	protected static function check( $label, $ok, $detail = '', $soft = false ) {
		if ( $ok ) {
			self::$pass++;
			self::line( '  PASS  ' . $label . ( $detail ? '  — ' . $detail : '' ) );
			return;
		}

		if ( $soft ) {
			self::$warn++;
			self::line( '  WARN  ' . $label . ( $detail ? '  — ' . $detail : '' ) );
			return;
		}

		self::$fail++;
		self::line( '  FAIL  ' . $label . ( $detail ? '  — ' . $detail : '' ) );
	}

	/**
	 * Run every check.
	 *
	 * @return bool True when there is no failure.
	 */
	public static function run() {
		self::$pass = 0;
		self::$warn = 0;
		self::$fail = 0;

		self::line( '' );
		self::line( 'پارت‌یاب — تست سلامت نصب' );
		self::line( 'سایت: ' . home_url( '/' ) );
		self::line( str_repeat( '-', 60 ) );

	/* ------------------------------------------------------------------ core */

	self::line( 'پایه:' );

	self::check( 'قالب پارت‌یاب فعال است', 'partyab' === get_template(), get_template() );

	self::check(
		'افزونهٔ سازگاری فعال است',
		class_exists( 'Partyab_Setup' ) && class_exists( 'Partyab_Fitment' ),
		class_exists( 'Partyab_Setup' ) ? 'Partyab_Setup بارگذاری شد' : 'کلاس‌ها پیدا نشدند'
	);

	self::check( 'ووکامرس فعال است', class_exists( 'WooCommerce' ), defined( 'WC_VERSION' ) ? WC_VERSION : '' );

	$structure = (string) get_option( 'permalink_structure' );
	self::check( 'پیوند یکتا روی «نام نوشته» است', '/%postname%/' === $structure, $structure ? $structure : 'ساده' );

	$theme_dir = get_template_directory();
	self::check( 'فایل‌های قالب موجودند', file_exists( $theme_dir . '/assets/js/partyab.js' ) && file_exists( $theme_dir . '/assets/css/style.css' ), $theme_dir );

	$fonts = glob( $theme_dir . '/assets/fonts/*.woff2' );
	self::check( 'فونت‌های محلی موجودند', is_array( $fonts ) && count( $fonts ) >= 10, count( (array) $fonts ) . ' فایل' );

	/* --------------------------------------------------------------- database */

	self::line( 'داده:' );

	global $wpdb;
	$table      = Partyab_DB::table();
	$table_name = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB
	self::check( 'جدول سازگاری ساخته شده', $table_name === $table, $table );

	if ( $table_name === $table ) {
		$rows = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB
		self::check( 'ردیف سازگاری وجود دارد', $rows > 0, $rows . ' ردیف' );

		$fitted = Partyab_DB::fitted_product_count();
		self::check( 'محصول دارای جدول سازگاری وجود دارد', $fitted > 0, $fitted . ' محصول' );
	}

	$published = (int) wp_count_posts( 'product' )->publish;
	self::check( 'محصول منتشرشده وجود دارد', $published > 0, $published . ' محصول' );

	$catalog_models = 0;
	foreach ( Partyab_Vehicles::makes() as $make ) {
		$catalog_models += count( (array) $make['models'] );
	}
	self::check( 'کاتالوگ خودرو بارگذاری شد', $catalog_models >= 30, $catalog_models . ' مدل' );

	/* ------------------------------------------------------------------ pages */

	self::line( 'صفحه‌ها و ناوبری:' );

	$front = (int) get_option( 'page_on_front' );
	self::check( 'صفحهٔ اصلی تنظیم شده', 'page' === get_option( 'show_on_front' ) && $front > 0, $front ? get_permalink( $front ) : 'تنظیم نشده' );

	$garage = get_page_by_path( 'garage' );
	self::check( 'برگهٔ گاراژ ساخته شده', (bool) $garage, $garage ? get_permalink( $garage ) : 'پیدا نشد' );

	if ( $garage ) {
		$template = get_page_template_slug( $garage->ID );
		self::check( 'الگوی «گاراژ پارت‌یاب» روی برگه است', 'page-garage.php' === $template, $template ? $template : 'پیش‌فرض' );
	}

	foreach ( array( 'shop' => 'فروشگاه', 'cart' => 'سبد خرید', 'checkout' => 'تسویه', 'myaccount' => 'حساب کاربری' ) as $key => $label ) {
		$page_id = (int) get_option( 'woocommerce_' . $key . '_page_id' );
		$exists  = $page_id > 0 && get_post( $page_id );
		self::check( 'برگهٔ ' . $label . ' وصل است', (bool) $exists, $page_id ? (string) $page_id : 'تنظیم نشده' );
	}

	$cart_id     = (int) get_option( 'woocommerce_cart_page_id' );
	$checkout_id = (int) get_option( 'woocommerce_checkout_page_id' );
	$cart_post   = $cart_id ? get_post( $cart_id ) : null;
	$checkout    = $checkout_id ? get_post( $checkout_id ) : null;

	self::check(
		'سبد خرید کلاسیک (شورت‌کد) است',
		$cart_post && false !== strpos( (string) $cart_post->post_content, '[woocommerce_cart]' ),
		$cart_post && false !== strpos( (string) $cart_post->post_content, 'wp:woocommerce' ) ? 'بلوکی است — استایل قالب اعمال نمی‌شود' : ''
	);

	self::check(
		'تسویه حساب کلاسیک (شورت‌کد) است',
		$checkout && false !== strpos( (string) $checkout->post_content, '[woocommerce_checkout]' ),
		$checkout && false !== strpos( (string) $checkout->post_content, 'wp:woocommerce' ) ? 'بلوکی است — استایل قالب اعمال نمی‌شود' : ''
	);

	$locations = get_nav_menu_locations();
	$missing   = array();

	foreach ( array( 'primary', 'footer-shop', 'footer-help', 'footer-about' ) as $location ) {
		if ( empty( $locations[ $location ] ) ) {
			$missing[] = $location;
			continue;
		}
		$items = wp_get_nav_menu_items( (int) $locations[ $location ] );
		if ( empty( $items ) ) {
			$missing[] = $location;
		}
	}

	self::check( 'چهار منو به جایگاه‌ها وصل‌اند', empty( $missing ), $missing ? implode( ', ', $missing ) : 'همه وصل‌اند' );

	/* ------------------------------------------------------------------- rest */

	self::line( 'REST و انتخاب‌گر خودرو:' );

	$routes = rest_get_server()->get_routes();
	$need   = array( '/partyab/v1/vehicles', '/partyab/v1/search', '/partyab/v1/state', '/partyab/v1/vehicle', '/partyab/v1/coverage' );
	$absent = array();

	foreach ( $need as $route ) {
		if ( ! isset( $routes[ $route ] ) ) {
			$absent[] = $route;
		}
	}

	self::check( 'پنج مسیر REST ثبت شده', empty( $absent ), $absent ? implode( ', ', $absent ) : '' );

	$request  = new WP_REST_Request( 'GET', '/partyab/v1/vehicles' );
	$response = rest_do_request( $request );
	$data     = $response->get_data();
	$makes    = ( is_array( $data ) && isset( $data['makes'] ) && is_array( $data['makes'] ) ) ? count( $data['makes'] ) : 0;

	self::check( 'REST کاتالوگ پاسخ می‌دهد', 200 === $response->get_status() && $makes > 0, $makes . ' سازنده' );

	$search = new WP_REST_Request( 'GET', '/partyab/v1/search' );
	$search->set_param( 'q', '206' );
	$found = rest_do_request( $search )->get_data();

	self::check(
		'جست‌وجوی مدل «۲۰۶» نتیجه دارد',
		is_array( $found ) && ! empty( $found['results'] ),
		is_array( $found ) && ! empty( $found['results'] ) ? count( $found['results'] ) . ' نتیجه' : 'خالی'
	);

	/* --------------------------------------------------- fitment state matrix */

	self::line( 'ماتریس پنج حالت سازگاری:' );

	$vehicle_206 = Partyab_Vehicles::sanitize(
		array(
			'model_id' => '206',
			'year'     => 1395,
		)
	);

	self::check( 'ساخت خودروی آزمایشی (۲۰۶ ۱۳۹۵)', is_array( $vehicle_206 ), is_array( $vehicle_206 ) ? $vehicle_206['key'] : 'نامعتبر' );

	if ( is_array( $vehicle_206 ) ) {
		$cases = array(
			array( 'BRK-206-4415', 'conditional', 'لنت ترمز ۲۰۶ + ۲۰۶ ۱۳۹۵' ),
			array( 'SPK-NGK-ILZ', 'fit', 'شمع NGK + ۲۰۶ ۱۳۹۵' ),
			array( 'CAB-VL-1102', 'no', 'فیلتر کابین پراید + ۲۰۶ ۱۳۹۵' ),
			array( 'TBK-GT-K015603', 'unknown', 'تسمه تایم بدون ردیف سازگاری' ),
		);

		foreach ( $cases as $case ) {
			$product_id = wc_get_product_id_by_sku( $case[0] );

			if ( ! $product_id ) {
				self::check( $case[2], false, 'محصول ' . $case[0] . ' نیست', true );
				continue;
			}

			$state = Partyab_Fitment::state( (int) $product_id, $vehicle_206 );
			self::check( $case[2], $case[1] === $state['state'], 'وضعیت: ' . $state['state'] );
		}
	}

	$universal_id = wc_get_product_id_by_sku( 'WPR-BS-A555' );
	if ( $universal_id ) {
		$universal = Partyab_Fitment::state( (int) $universal_id, null );
		self::check( 'قطعهٔ عمومی بدون خودرو', 'universal' === $universal['state'], 'وضعیت: ' . $universal['state'] );
	}

	$ask_id = wc_get_product_id_by_sku( 'BRK-206-4415' );
	if ( $ask_id ) {
		$ask = Partyab_Fitment::state( (int) $ask_id, null );
		self::check( 'بدون خودرو حالت «انتخاب خودرو»', 'ask' === $ask['state'], 'وضعیت: ' . $ask['state'] );
	}

	/* ---------------------------------------------------------------- shop cfg */

	self::line( 'تنظیمات فروشگاه:' );

	$currency = (string) get_option( 'woocommerce_currency' );
	self::check( 'واحد پول تنظیم شده', in_array( $currency, array( 'IRR', 'IRT' ), true ), $currency );

	$cod = get_option( 'woocommerce_cod_settings', array() );
	self::check( 'پرداخت در محل فعال است', is_array( $cod ) && isset( $cod['enabled'] ) && 'yes' === $cod['enabled'], 'برای تست بدون درگاه لازم است', true );

	$zones = class_exists( 'WC_Shipping_Zones' ) ? WC_Shipping_Zones::get_zones() : array();
	$has_ir = false;
	foreach ( (array) $zones as $zone ) {
		if ( isset( $zone['zone_name'] ) && 'ایران' === $zone['zone_name'] ) {
			$has_ir = true;
		}
	}
	self::check( 'منطقهٔ ارسال «ایران» وجود دارد', $has_ir, count( (array) $zones ) . ' منطقه', true );

	/* ---------------------------------------------------------------- summary */

	self::line( str_repeat( '-', 60 ) );
	self::line(
		sprintf(
			'نتیجه: %d موفق، %d هشدار، %d خطا',
			self::$pass,
			self::$warn,
			self::$fail
		)
	);

	if ( self::$fail > 0 ) {
		self::line( 'چند مورد خطا دارد؛ خروجی بالا دقیقاً می‌گوید کدام مرحله ناقص است (اجرای دوبارهٔ نصب خودکار معمولاً درستش می‌کند).' );
		return false;
	}

	self::line( '' );
	self::line( 'حالا مرورگر را باز کنید و این شش سناریو را ببینید:' );
	self::line( '  ۱) صفحهٔ اصلی → هیرو و نقشهٔ خودرو' );
	self::line( '  ۲) انتخاب خودرو ۲۰۶ ۱۳۹۵ → انتقال به فروشگاه با ?compat=1' );
	self::line( '  ۳) صفحهٔ محصول لنت ترمز → «سازگار با شرط» با متن شرط' );
	self::line( '  ۴) تب «خودروهای سازگار» → ردیف ۲۰۶ هایلایت کهربایی' );
	self::line( '  ۵) افزودن به سبد → شمارندهٔ هدر + تسویه با پرداخت در محل' );
	self::line( '  ۶) موبایل ۳۶۰px → بدون اسکرول افقی و نوار پایین فعال' );

	self::line( 'سلامت نصب تأیید شد.' );

		return true;
	}
}
