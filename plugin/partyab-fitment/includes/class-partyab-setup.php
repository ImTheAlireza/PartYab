<?php
/**
 * One-click setup: activate the theme, import sample data, build pages and menus,
 * configure WooCommerce, then verify. Idempotent — safe to run again.
 *
 * Front-ends:
 *   wp partyab install [--currency=IRR] [--with-images] [--force] [--install-woo]
 *   Tools → «نصب سریع پارت‌یاب»
 *
 * @package Partyab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Setup
 */
class Partyab_Setup {

	/**
	 * Tools page slug.
	 */
	const PAGE_SLUG = 'partyab-setup';

	/**
	 * Collected report rows.
	 *
	 * @var array
	 */
	protected static $report = array();

	/**
	 * Category map: slug => (name, icon, description).
	 *
	 * @return array
	 */
	public static function categories_map() {
		return array(
			'engine'     => array( 'موتور و انتقال قدرت', 'engine', 'قطعات موتور، تسمه تایم، شمع و کلاچ.' ),
			'filter'     => array( 'فیلترها و روغن', 'filter', 'فیلتر روغن، هوا، کابین و روغن موتور.' ),
			'brake'      => array( 'ترمز و ABS', 'brake', 'لنت، دیسک، کالیپر و روغن ترمز.' ),
			'suspension' => array( 'جلوبندی و تعلیق', 'shock', 'کمک فنر، طبق، سیبک و بوش.' ),
			'electrical' => array( 'برق و روشنایی', 'light', 'چراغ، باتری، دینام و سنسور.' ),
			'body'       => array( 'بدنه و آینه', 'car', 'سپر، آینه، گلگیر و لوازم بدنه.' ),
			'tire'       => array( 'لاستیک و رینگ', 'tire', 'لاستیک، رینگ و تجهیزات مرتبط.' ),
			'accessory'  => array( 'لوازم جانبی', 'accessory', 'برف‌پاک‌کن، کف‌پوش و لوازم مصرفی.' ),
		);
	}

	/**
	 * Demo product images, keyed by SKU (they live inside the theme).
	 *
	 * @return array
	 */
	public static function image_map() {
		return array(
			'BRK-206-4415'   => 'p-brake.jpg',
			'OIL-MF-7700'    => 'p-oilfilter.jpg',
			'AIR-BS-1402'    => 'p-airfilter.jpg',
			'SPK-NGK-ILZ'    => 'p-sparkplug.jpg',
			'SHK-KYB-3390'   => 'p-shock.jpg',
			'LGT-HL-206R'    => 'p-headlight.jpg',
			'WPR-BS-A555'    => 'p-wiper.jpg',
			'TBK-GT-K015603' => 'p-timingbelt.jpg',
			'DSC-TRW-2401'   => 'p-brake.jpg',
			'CAB-VL-1102'    => 'p-airfilter.jpg',
			'OIL-TT-5W30'    => 'p-oilfilter.jpg',
			'SHK-MN-7721'    => 'p-shock.jpg',
			'FLU-BS-DOT4'    => 'p-brakefluid.jpg',
		);
	}

	/**
	 * Hook the admin page and the WP-CLI command.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_post_partyab_setup_run', array( __CLASS__, 'handle_post' ) );

		add_action( 'admin_notices', array( __CLASS__, 'setup_notice' ) );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'partyab install', array( __CLASS__, 'cli' ) );
		}
	}

	/* ---------------------------------------------------------------------
	 * Entry points
	 * ------------------------------------------------------------------ */

	/**
	 * Add the Tools page.
	 */
	public static function admin_menu() {
		add_management_page(
			__( 'نصب سریع پارت‌یاب', 'partyab' ),
			__( 'نصب سریع پارت‌یاب', 'partyab' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'admin_page' )
		);
	}

	/**
	 * Nudge the admin towards the one-click setup until it has run once.
	 */
	public static function setup_notice() {
		if ( get_option( 'partyab_last_setup' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( self::PAGE_SLUG === $page ) {
			return;
		}

		printf(
			'<div class="notice notice-info is-dismissible"><p><strong>%1$s</strong> %2$s <a class="button button-primary" href="%3$s">%4$s</a></p></div>',
			esc_html__( 'پارت‌یاب آمادهٔ راه‌اندازی است.', 'partyab' ),
			esc_html__( 'با یک کلیک: دسته‌ها، محصولات نمونه، جدول سازگاری، صفحه‌ها، منوها و تنظیمات ووکامرس ساخته می‌شوند.', 'partyab' ),
			esc_url( admin_url( 'tools.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'اجرای نصب سریع', 'partyab' )
		);
	}

	/**
	 * Run the installer from the dashboard.
	 */
	public static function handle_post() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی لازم برای این کار را ندارید.', 'partyab' ) );
		}

		check_admin_referer( 'partyab_setup_run' );

		$args = array(
			'currency'    => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'IRR',
			'with-images' => ! empty( $_POST['with_images'] ),
			'force'       => ! empty( $_POST['force'] ),
			'install-woo' => ! empty( $_POST['install_woo'] ),
		);

		$report = self::run( $args );

		set_transient( 'partyab_setup_report_' . get_current_user_id(), $report, 5 * MINUTE_IN_SECONDS );

		wp_safe_redirect( admin_url( 'tools.php?page=' . self::PAGE_SLUG . '&done=1' ) );
		exit;
	}

	/**
	 * WP-CLI entry point.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Flags.
	 */
	public static function cli( $args, $assoc_args ) {
		$assoc_args = is_array( $assoc_args ) ? $assoc_args : array();

		$report = self::run(
			array(
				'currency'    => isset( $assoc_args['currency'] ) ? $assoc_args['currency'] : 'IRR',
				'with-images' => ! isset( $assoc_args['no-images'] ),
				'force'       => isset( $assoc_args['force'] ),
				'install-woo' => isset( $assoc_args['install-woo'] ),
			)
		);

		$failed = 0;
		$warned = 0;

		foreach ( $report as $row ) {
			\WP_CLI::log( sprintf( '[%s] %s — %s', strtoupper( $row['status'] ), $row['step'], $row['message'] ) );

			if ( 'fail' === $row['status'] ) {
				$failed++;
			}
			if ( 'warn' === $row['status'] ) {
				$warned++;
			}
		}

		if ( $failed ) {
			\WP_CLI::error(
				sprintf(
					/* translators: 1: failed count, 2: warning count */
					__( '%1$s مورد خطا و %2$s هشدار — خروجی بالا را بخوانید و پیش‌نیازها را درست کنید، بعد دوباره اجرا کنید.', 'partyab' ),
					$failed,
					$warned
				)
			);
		}

		if ( $warned ) {
			\WP_CLI::warning( __( 'نصب تمام شد، ولی موارد «هشدار» بالا کار باقی‌مانده دارند (مثلاً استثنا کردن کش صفحه).', 'partyab' ) );
		}

		\WP_CLI::success(
			sprintf(
				/* translators: %s: site url */
				__( 'نصب خودکار تمام شد. سایت را در پنجرهٔ ناشناس باز کنید: %s', 'partyab' ),
				home_url( '/' )
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Main routine
	 * ------------------------------------------------------------------ */

	/**
	 * Run every step. Never throws: failures are reported, not fatal.
	 *
	 * @param array $args Options.
	 * @return array
	 */
	public static function run( $args = array() ) {
		$args = array_merge(
			array(
				'currency'    => 'IRR',
				'with-images' => true,
				'force'       => false,
				'install-woo' => false,
			),
			is_array( $args ) ? $args : array()
		);

		self::$report = array();

		@set_time_limit( 240 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'admin' );
		}

		$ready = self::step_environment( $args );

		if ( ! $ready ) {
			return self::$report;
		}

		self::step_theme( $args );
		self::step_site( $args );
		self::step_permalinks();
		self::step_woocommerce( $args );
		self::step_shipping();
		self::step_categories();
		self::step_products( $args );
		self::step_images( $args );
		self::step_fitment();
		self::step_pages( $args );
		self::step_menus( $args );
		self::step_finalize( $args );

		update_option(
			'partyab_last_setup',
			array(
				'time' => current_time( 'mysql' ),
				'args' => $args,
			)
		);

		return self::$report;
	}

	/**
	 * Append one row to the report.
	 *
	 * @param string $step    Step name.
	 * @param string $status  ok|skip|warn|fail.
	 * @param string $message Human message.
	 */
	protected static function add( $step, $status, $message ) {
		self::$report[] = array(
			'step'    => $step,
			'status'  => $status,
			'message' => $message,
		);
	}

	/* ---------------------------------------------------------------------
	 * 1. Environment
	 * ------------------------------------------------------------------ */

	/**
	 * Check requirements and, when asked, install WooCommerce.
	 *
	 * @param array $args Options.
	 * @return bool True when the rest of the steps can run.
	 */
	protected static function step_environment( $args ) {
		$ok = true;

		$wp = get_bloginfo( 'version' );
		if ( version_compare( $wp, '6.2', '>=' ) ) {
			self::add( 'وردپرس', 'ok', sprintf( 'نسخهٔ %s', $wp ) );
		} else {
			self::add( 'وردپرس', 'fail', sprintf( 'نسخهٔ %s — دست‌کم ۶.۲ لازم است.', $wp ) );
			$ok = false;
		}

		if ( version_compare( PHP_VERSION, '7.4', '>=' ) ) {
			self::add( 'PHP', 'ok', PHP_VERSION );
		} else {
			self::add( 'PHP', 'fail', PHP_VERSION . ' — دست‌کم ۷.۴ لازم است.' );
			$ok = false;
		}

		if ( function_exists( 'mb_strtolower' ) ) {
			self::add( 'mbstring', 'ok', __( 'نصب است', 'partyab' ) );
		} else {
			self::add( 'mbstring', 'fail', __( 'افزونهٔ mbstring نصب نیست؛ جست‌وجوی مدل خودرو کار نمی‌کند.', 'partyab' ) );
			$ok = false;
		}

		if ( ! class_exists( 'WooCommerce' ) && ! empty( $args['install-woo'] ) && defined( 'WP_CLI' ) && WP_CLI ) {
			self::add( 'ووکامرس', 'warn', __( 'در حال نصب ووکامرس…', 'partyab' ) );
			try {
				\WP_CLI::runcommand( 'plugin install woocommerce --activate', array( 'exit_error' => false ) );
			} catch ( \Exception $e ) {
				self::add( 'ووکامرس', 'warn', $e->getMessage() );
			}
		}

		if ( class_exists( 'WooCommerce' ) ) {
			self::add( 'ووکامرس', 'ok', defined( 'WC_VERSION' ) ? WC_VERSION : __( 'فعال', 'partyab' ) );
		} else {
			$installed = self::plugin_dir_exists( 'woocommerce/woocommerce.php' );

			self::add(
				'ووکامرس',
				'fail',
				$installed
					? __( 'ووکامرس نصب شده ولی در این اجرا بارگذاری نشد؛ همین نصب را یک‌بار دیگر اجرا کنید.', 'partyab' )
					: __( 'ووکامرس فعال نیست. اول آن را نصب/فعال کنید (یا با ‎--install-woo‎ اجرا کنید).', 'partyab' )
			);
			$ok = false;
		}

		$theme = wp_get_theme( 'partyab' );
		if ( $theme->exists() ) {
			self::add( 'قالب', 'ok', __( 'قالب پارت‌یاب نصب است.', 'partyab' ) );
		} else {
			self::add( 'قالب', 'fail', __( 'پوشهٔ قالب partyab در wp-content/themes پیدا نشد.', 'partyab' ) );
			$ok = false;
		}

		if ( class_exists( 'Partyab_DB' ) ) {
			Partyab_DB::install();
			self::add( 'جدول سازگاری', 'ok', Partyab_DB::table() );
		} else {
			self::add( 'جدول سازگاری', 'fail', __( 'کلاس Partyab_DB پیدا نشد؛ افزونه ناقص است.', 'partyab' ) );
			$ok = false;
		}

		if ( ! $ok ) {
			self::add( 'نتیجه', 'fail', __( 'پیش‌نیازها کامل نیست؛ مراحل بعدی اجرا نشد.', 'partyab' ) );
		}

		return $ok;
	}

	/**
	 * Is a plugin folder present on disk?
	 *
	 * @param string $relative Relative plugin path.
	 * @return bool
	 */
	protected static function plugin_dir_exists( $relative ) {
		if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
			return false;
		}

		return file_exists( WP_PLUGIN_DIR . '/' . $relative );
	}

	/* ---------------------------------------------------------------------
	 * 2. Theme + site identity
	 * ------------------------------------------------------------------ */

	/**
	 * Activate the theme.
	 *
	 * @param array $args Options.
	 */
	protected static function step_theme( $args ) {
		if ( 'partyab' === get_template() ) {
			self::add( 'فعال‌سازی قالب', 'skip', __( 'قالب پارت‌یاب از قبل فعال است.', 'partyab' ) );
			return;
		}

		switch_theme( 'partyab' );

		if ( 'partyab' === get_template() ) {
			self::add( 'فعال‌سازی قالب', 'ok', __( 'قالب پارت‌یاب فعال شد.', 'partyab' ) );
		} else {
			self::add( 'فعال‌سازی قالب', 'warn', __( 'فعال‌سازی خودکار نشد؛ از «نمایش → پوسته‌ها» فعال کنید.', 'partyab' ) );
		}
	}

	/**
	 * Name, timezone and date format.
	 *
	 * @param array $args Options.
	 */
	protected static function step_site( $args ) {
		$name = (string) get_option( 'blogname' );
		$defaults = array( '', 'My WordPress Blog', 'My Blog', 'WordPress', 'سایت من', 'وبلاگ من' );

		if ( $args['force'] || in_array( $name, $defaults, true ) ) {
			update_option( 'blogname', 'پارت‌یاب' );
			self::add( 'نام سایت', 'ok', 'پارت‌یاب' );
		} else {
			self::add( 'نام سایت', 'skip', sprintf( '«%s» دست نخورد (برای تغییر: ‎--force‎).', $name ) );
		}

		$desc = (string) get_option( 'blogdescription' );
		if ( $args['force'] || '' === $desc || false !== strpos( $desc, 'Just another' ) ) {
			update_option( 'blogdescription', 'قطعهٔ درست، برای خودروی درست' );
			self::add( 'توضیح سایت', 'ok', 'قطعهٔ درست، برای خودروی درست' );
		}

		update_option( 'timezone_string', 'Asia/Tehran' );
		update_option( 'date_format', 'Y/m/d' );
		update_option( 'start_of_week', 6 );
		self::add( 'زمان و تقویم', 'ok', __( 'تهران، شروع هفته شنبه، تاریخ ۱۴۰۳/۰۱/۰۱‌نما', 'partyab' ) );
	}

	/**
	 * Pretty permalinks (required by the REST API).
	 */
	protected static function step_permalinks() {
		if ( '/%postname%/' === get_option( 'permalink_structure' ) ) {
			flush_rewrite_rules( false );
			self::add( 'پیوند یکتا', 'skip', '/%postname%/' );
			return;
		}

		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
		self::add( 'پیوند یکتا', 'ok', __( 'روی «نام نوشته» تنظیم شد.', 'partyab' ) );
	}

	/* ---------------------------------------------------------------------
	 * 3. WooCommerce settings
	 * ------------------------------------------------------------------ */

	/**
	 * Currency, country, checkout and payment basics.
	 *
	 * @param array $args Options.
	 */
	protected static function step_woocommerce( $args ) {
		$currency = strtoupper( (string) $args['currency'] );

		if ( 'IRT' !== $currency ) {
			$currency = 'IRR';
		}

		update_option( 'woocommerce_currency', $currency );
		update_option( 'woocommerce_price_num_decimals', 0 );
		update_option( 'woocommerce_price_thousand_sep', '٬' );
		update_option( 'woocommerce_price_decimal_sep', '.' );
		update_option( 'woocommerce_currency_pos', 'right_space' );
		update_option( 'woocommerce_default_country', 'IR:THR' );
		update_option( 'woocommerce_allowed_countries', 'specific' );
		update_option( 'woocommerce_specific_allowed_countries', array( 'IR' ) );
		update_option( 'woocommerce_ship_to_countries', '' );
		update_option( 'woocommerce_calc_taxes', 'no' );
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );
		update_option( 'woocommerce_cart_redirect_after_add', 'no' );
		update_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );
		update_option( 'woocommerce_manage_stock', 'yes' );
		update_option( 'woocommerce_hold_stock_minutes', 30 );

		self::add(
			'ووکامرس',
			'ok',
			sprintf(
				/* translators: %s: currency code */
				__( 'واحد پول %s، کشور ایران، بدون اعشار، ارقام فارسی از سمت قالب.', 'partyab' ),
				$currency
			)
		);

		if ( 'IRT' === $currency ) {
			self::add( 'واحد پول', 'warn', __( 'کد IRT در ووکامرس پیش‌فرض نیست؛ اگر برچسب «تومان» نمی‌بینید افزونهٔ تومان نصب کنید یا با IRR کار کنید.', 'partyab' ) );
		}

		$cod = get_option( 'woocommerce_cod_settings', array() );
		if ( empty( $cod['enabled'] ) || 'yes' !== $cod['enabled'] ) {
			update_option(
				'woocommerce_cod_settings',
				array(
					'enabled'      => 'yes',
					'title'        => 'پرداخت در محل',
					'description'  => 'هنگام تحویل، مبلغ سفارش را به پیک پرداخت می‌کنید.',
					'instructions' => 'لطفاً هنگام تحویل، مبلغ دقیق سفارش را آماده داشته باشید.',
				)
			);
			self::add( 'پرداخت در محل', 'ok', __( 'روشن شد (برای تست بدون درگاه).', 'partyab' ) );
		} else {
			self::add( 'پرداخت در محل', 'skip', __( 'از قبل فعال بود.', 'partyab' ) );
		}
	}

	/**
	 * A single Iran shipping zone with three methods.
	 */
	protected static function step_shipping() {
		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			self::add( 'ارسال', 'warn', __( 'کلاس ارسال ووکامرس پیدا نشد.', 'partyab' ) );
			return;
		}

		$zones = WC_Shipping_Zones::get_zones();
		foreach ( $zones as $zone_data ) {
			if ( isset( $zone_data['zone_name'] ) && 'ایران' === $zone_data['zone_name'] ) {
				self::add( 'ارسال', 'skip', __( 'منطقهٔ «ایران» از قبل وجود دارد.', 'partyab' ) );
				return;
			}
		}

		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( 'ایران' );
		$zone->add_location( 'IR', 'country' );
		$zone->save();

		$methods = array(
			array( 'flat_rate', 'پیک موتوری (تهران)', '70000' ),
			array( 'flat_rate', 'پست پیشتاز', '120000' ),
			array( 'local_pickup', 'تحویل حضوری', '0' ),
		);

		$created = 0;
		foreach ( $methods as $method ) {
			$instance_id = $zone->add_shipping_method( $method[0] );
			if ( ! $instance_id ) {
				continue;
			}

			$key = ( 'local_pickup' === $method[0] ) ? 'woocommerce_local_pickup_' . $instance_id . '_settings' : 'woocommerce_' . $method[0] . '_' . $instance_id . '_settings';

			update_option(
				$key,
				array(
					'title'      => $method[1],
					'tax_status' => 'none',
					'cost'       => $method[2],
				)
			);
			$created++;
		}

		self::add( 'ارسال', 'ok', sprintf( __( 'منطقهٔ «ایران» با %s روش ارسال ساخته شد.', 'partyab' ), self::fa( $created ) ) );
	}

	/* ---------------------------------------------------------------------
	 * 4. Categories
	 * ------------------------------------------------------------------ */

	/**
	 * Create the eight storefront categories with their icons.
	 */
	protected static function step_categories() {
		$created = 0;
		$skipped = 0;

		foreach ( self::categories_map() as $slug => $data ) {
			$existing = get_term_by( 'slug', $slug, 'product_cat' );

			if ( $existing ) {
				$term_id = (int) $existing->term_id;
				$skipped++;
			} else {
				$result = wp_insert_term(
					$data[0],
					'product_cat',
					array(
						'slug'        => $slug,
						'description' => $data[2],
					)
				);

				if ( is_wp_error( $result ) ) {
					$by_name = get_term_by( 'name', $data[0], 'product_cat' );
					if ( ! $by_name ) {
						continue;
					}
					$term_id = (int) $by_name->term_id;
				} else {
					$term_id = (int) $result['term_id'];
					$created++;
				}
			}

			update_term_meta( $term_id, 'partyab_icon', $data[1] );
		}

		self::add( 'دسته‌بندی‌ها', 'ok', sprintf( __( '%1$s دسته ساخته شد، %2$s از قبل بود.', 'partyab' ), self::fa( $created ), self::fa( $skipped ) ) );
	}

	/* ---------------------------------------------------------------------
	 * 5. Products
	 * ------------------------------------------------------------------ */

	/**
	 * Import the bundled sample products.
	 *
	 * @param array $args Options.
	 */
	protected static function step_products( $args ) {
		$file = PARTYAB_FITMENT_DIR . 'sample/products.csv';

		if ( ! file_exists( $file ) ) {
			self::add( 'محصولات', 'warn', __( 'فایل sample/products.csv پیدا نشد.', 'partyab' ) );
			return;
		}

		$result = self::import_products( $file, $args );

		if ( $result['total'] < 1 ) {
			self::add( 'محصولات', 'warn', __( 'هیچ ردیفی در فایل نبود.', 'partyab' ) );
			return;
		}

		self::add(
			'محصولات',
			$result['failed'] ? 'warn' : 'ok',
			sprintf(
				/* translators: 1: created, 2: updated, 3: skipped, 4: failed */
				__( '%1$s محصول ساخته شد، %2$s به‌روزرسانی شد، %3$s دست‌نخورده ماند، %4$s خطا.', 'partyab' ),
				self::fa( $result['created'] ),
				self::fa( $result['updated'] ),
				self::fa( $result['skipped'] ),
				self::fa( $result['failed'] )
			)
		);
	}

	/**
	 * Parse and import the products CSV.
	 *
	 * @param string $file CSV path.
	 * @param array  $args Options.
	 * @return array
	 */
	protected static function import_products( $file, $args ) {
		$out = array(
			'total'   => 0,
			'created' => 0,
			'updated' => 0,
			'skipped' => 0,
			'failed'  => 0,
		);

		$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! $handle ) {
			return $out;
		}

		$header = fgetcsv( $handle );

		if ( ! is_array( $header ) ) {
			fclose( $handle );
			return $out;
		}

		$header = self::clean_header( $header );

		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			if ( ! is_array( $row ) || empty( array_filter( $row ) ) ) {
				continue;
			}

			$out['total']++;

			$row = array_pad( $row, count( $header ), '' );
			$map = array_combine( $header, array_slice( $row, 0, count( $header ) ) );

			if ( ! $map ) {
				$out['failed']++;
				continue;
			}

			$sku = isset( $map['sku'] ) ? trim( (string) $map['sku'] ) : '';
			if ( '' === $sku ) {
				$out['failed']++;
				continue;
			}

			$existing_id = wc_get_product_id_by_sku( $sku );

			if ( $existing_id ) {
				if ( empty( $args['force'] ) ) {
					$out['skipped']++;
					continue;
				}
				$product = wc_get_product( $existing_id );
				$out['updated']++;
			} else {
				$product = new WC_Product_Simple();
				$out['created']++;
			}

			if ( ! $product ) {
				$product = new WC_Product_Simple();
			}

			try {
				$product->set_name( isset( $map['name'] ) ? (string) $map['name'] : $sku );
				$product->set_sku( $sku );
				$product->set_status( ( isset( $map['published'] ) && '0' === (string) $map['published'] ) ? 'draft' : 'publish' );
				$product->set_catalog_visibility( 'visible' );
				$product->set_featured( isset( $map['is featured?'] ) && '1' === (string) $map['is featured?'] );
				$product->set_description( isset( $map['description'] ) ? (string) $map['description'] : '' );
				$product->set_short_description( isset( $map['short description'] ) ? (string) $map['short description'] : '' );

				$regular = isset( $map['regular price'] ) ? self::price( $map['regular price'] ) : '';
				if ( '' !== $regular ) {
					$product->set_regular_price( $regular );
				}

				$sale = isset( $map['sale price'] ) ? self::price( $map['sale price'] ) : '';
				if ( '' !== $sale ) {
					$product->set_sale_price( $sale );
				}

				if ( isset( $map['stock'] ) && '' !== trim( (string) $map['stock'] ) ) {
					$product->set_manage_stock( true );
					$product->set_stock_quantity( (int) self::latin( $map['stock'] ) );
				}

				$in_stock = ! isset( $map['in stock?'] ) || '1' === (string) $map['in stock?'] || '1' === trim( (string) $map['in stock?'] );
				$product->set_stock_status( $in_stock ? 'instock' : 'outofstock' );

				$product->save();
			} catch ( \Exception $e ) {
				$out['failed']++;
				continue;
			}

			$product_id = $product->get_id();

			// Categories.
			if ( ! empty( $map['categories'] ) ) {
				$names = array_filter( array_map( 'trim', explode( ',', (string) $map['categories'] ) ) );
				$ids   = array();

				foreach ( $names as $name ) {
					$term = get_term_by( 'name', $name, 'product_cat' );
					if ( ! $term ) {
						$new = wp_insert_term( $name, 'product_cat' );
						if ( is_wp_error( $new ) ) {
							continue;
						}
						$ids[] = (int) $new['term_id'];
					} else {
						$ids[] = (int) $term->term_id;
					}
				}

				if ( $ids ) {
					wp_set_object_terms( $product_id, $ids, 'product_cat' );
				}
			}

			// Custom meta columns (Meta: _partyab_*).
			foreach ( $map as $key => $value ) {
				if ( 0 !== strpos( $key, 'meta: ' ) ) {
					continue;
				}
				$meta_key = trim( substr( $key, 6 ) );
				if ( '' === $meta_key ) {
					continue;
				}
				update_post_meta( $product_id, $meta_key, trim( (string) $value ) );
			}
		}

		fclose( $handle );

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		return $out;
	}

	/* ---------------------------------------------------------------------
	 * 6. Demo images
	 * ------------------------------------------------------------------ */

	/**
	 * Copy the theme's demo photos into the media library and attach them.
	 *
	 * @param array $args Options.
	 */
	protected static function step_images( $args ) {
		if ( empty( $args['with-images'] ) ) {
			self::add( 'تصاویر نمونه', 'skip', __( 'با ‎--no-images‎ خاموش شده است.', 'partyab' ) );
			return;
		}

		$theme_dir = get_template_directory() . '/assets/img/';

		if ( ! is_dir( $theme_dir ) ) {
			self::add( 'تصاویر نمونه', 'warn', __( 'پوشهٔ assets/img در قالب پیدا نشد.', 'partyab' ) );
			return;
		}

		$attached = 0;

		foreach ( self::image_map() as $sku => $filename ) {
			$product_id = wc_get_product_id_by_sku( $sku );
			if ( ! $product_id ) {
				continue;
			}

			$product = wc_get_product( $product_id );
			if ( ! $product || $product->get_image_id() ) {
				continue;
			}

			$attachment_id = self::attach_image( $theme_dir . $filename, $filename );

			if ( $attachment_id ) {
				$product->set_image_id( $attachment_id );
				$product->save();
				$attached++;
			}
		}

		self::add( 'تصاویر نمونه', $attached ? 'ok' : 'skip', sprintf( __( '%s تصویر به محصولات وصل شد.', 'partyab' ), self::fa( $attached ) ) );
	}

	/**
	 * Import one file into the media library (reuses an earlier import of the same file).
	 *
	 * @param string $path     Absolute path.
	 * @param string $filename Base name.
	 * @return int Attachment ID or 0.
	 */
	protected static function attach_image( $path, $filename ) {
		if ( ! file_exists( $path ) ) {
			return 0;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_partyab_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => $filename, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);

		if ( ! empty( $existing ) ) {
			return (int) $existing[0];
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( false === $contents ) {
			return 0;
		}

		$upload = wp_upload_bits( $filename, null, $contents );

		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return 0;
		}

		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $metadata );
		update_post_meta( $attachment_id, '_partyab_demo_image', $filename );

		return (int) $attachment_id;
	}

	/* ---------------------------------------------------------------------
	 * 7. Fitment
	 * ------------------------------------------------------------------ */

	/**
	 * Import the bundled fitment CSV.
	 */
	protected static function step_fitment() {
		$file = PARTYAB_FITMENT_DIR . 'sample/fitment.csv';

		if ( ! file_exists( $file ) ) {
			self::add( 'سازگاری', 'warn', __( 'فایل sample/fitment.csv پیدا نشد.', 'partyab' ) );
			return;
		}

		$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! $handle ) {
			self::add( 'سازگاری', 'warn', __( 'فایل سازگاری باز نشد.', 'partyab' ) );
			return;
		}

		$header = self::clean_header( fgetcsv( $handle ) );
		$by_sku = array();

		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			if ( ! is_array( $row ) || empty( array_filter( $row ) ) ) {
				continue;
			}
			$row = array_pad( $row, count( $header ), '' );
			$map = array_combine( $header, array_slice( $row, 0, count( $header ) ) );

			if ( ! $map || empty( $map['sku'] ) ) {
				continue;
			}

			$sku = trim( (string) $map['sku'] );

			$by_sku[ $sku ][] = array(
				'model_id'  => isset( $map['model_id'] ) ? $map['model_id'] : '',
				'year_from' => isset( $map['year_from'] ) ? $map['year_from'] : '',
				'year_to'   => isset( $map['year_to'] ) ? $map['year_to'] : '',
				'status'    => isset( $map['status'] ) ? $map['status'] : 'fit',
				'note'      => isset( $map['note'] ) ? $map['note'] : '',
			);
		}

		fclose( $handle );

		$saved   = 0;
		$unknown = 0;
		$missing = 0;

		foreach ( $by_sku as $sku => $rows ) {
			$product_id = wc_get_product_id_by_sku( $sku );

			if ( ! $product_id ) {
				$unknown += count( $rows );
				continue;
			}

			$written = Partyab_DB::replace_for( $product_id, $rows );
			$saved  += $written;
			$missing += ( count( $rows ) - $written );
		}

		$status = ( $saved > 0 ) ? 'ok' : 'warn';
		self::add(
			'سازگاری',
			$status,
			sprintf(
				/* translators: 1: rows saved, 2: unknown sku rows, 3: invalid rows */
				__( '%1$s ردیف روی محصولات ذخیره شد، %2$s ردیف SKU ناشناس، %3$s ردیف مدل نامعتبر.', 'partyab' ),
				self::fa( $saved ),
				self::fa( $unknown ),
				self::fa( $missing )
			)
		);

		if ( $unknown ) {
			self::add( 'سازگاری', 'warn', __( 'SKU ناشناس یعنی محصول آن SKU وجود ندارد؛ اول محصولات را ایمپورت کنید.', 'partyab' ) );
		}
	}

	/* ---------------------------------------------------------------------
	 * 8. Pages
	 * ------------------------------------------------------------------ */

	/**
	 * Create the storefront pages and wire WooCommerce + reading settings.
	 *
	 * @param array $args Options.
	 */
	protected static function step_pages( $args ) {
		$home    = self::ensure_page( 'home', 'خانه', '', '' );
		$garage  = self::ensure_page( 'garage', 'گاراژ من', '', 'page-garage.php' );
		$guides  = self::ensure_page( 'guides', 'راهنما', '', '' );
		$about   = self::ensure_page( 'about-partyab', 'دربارهٔ پارت‌یاب', self::about_content(), '' );
		$code    = self::ensure_page( 'code-finder', 'جست‌وجوی کد فنی', '', 'page-code.php' );
		$trust   = self::ensure_page( 'trust', 'ضمانت و بازگشت کالا', '', 'page-trust.php' );

		if ( $code ) {
			self::add( 'قطعه‌یاب کد فنی', 'ok', __( 'برگهٔ «جست‌وجوی کد فنی» ساخته شد.', 'partyab' ) );
		}

		if ( $trust ) {
			self::add( 'ضمانت و بازگشت', 'ok', __( 'برگهٔ «ضمانت و بازگشت کالا» ساخته شد.', 'partyab' ) );
		}

		if ( $home ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home );
		}

		if ( $guides ) {
			update_option( 'page_for_posts', $guides );
		}

		// WooCommerce pages (created by WooCommerce on activation; recreate if missing).
		$shop    = self::ensure_wc_page( 'shop', 'فروشگاه', 'shop', '' );
		$cart    = self::ensure_wc_page( 'cart', 'سبد خرید', 'cart', '[woocommerce_cart]' );
		$checkout = self::ensure_wc_page( 'checkout', 'تسویه حساب', 'checkout', '[woocommerce_checkout]' );
		$account = self::ensure_wc_page( 'myaccount', 'حساب کاربری', 'myaccount', '[woocommerce_my_account]' );

		self::add(
			'صفحه‌ها',
			'ok',
			sprintf(
				/* translators: 1: home, 2: garage, 3: shop, 4: cart, 5: checkout */
				__( 'خانه %1$s، گاراژ %2$s، فروشگاه %3$s، سبد %4$s، تسویه %5$s.', 'partyab' ),
				self::fa( $home ),
				self::fa( $garage ),
				self::fa( $shop ),
				self::fa( $cart ),
				self::fa( $checkout )
			)
		);

		// Classic cart/checkout shortcodes: the theme styles those, not the blocks.
		self::classic_shortcode( $cart, '[woocommerce_cart]' );
		self::classic_shortcode( $checkout, '[woocommerce_checkout]' );

		if ( $about ) {
			self::add( 'دربارهٔ ما', 'ok', __( 'برگهٔ «دربارهٔ پارت‌یاب» ساخته شد.', 'partyab' ) );
		}

		if ( $account && ! get_option( 'woocommerce_myaccount_page_id' ) ) {
			update_option( 'woocommerce_myaccount_page_id', $account );
		}
	}

	/**
	 * Ensure a page exists (by slug) and has the right template.
	 *
	 * @param string $slug     Slug.
	 * @param string $title    Title.
	 * @param string $content  Content.
	 * @param string $template Page template file.
	 * @return int Page ID.
	 */
	protected static function ensure_page( $slug, $title, $content, $template ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page ) {
			$page_id = (int) $page->ID;
		} else {
			$page_id = (int) wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $content,
				)
			);
		}

		if ( $page_id && $template && get_page_template_slug( $page_id ) !== $template ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}

		return $page_id;
	}

	/**
	 * Ensure a WooCommerce page exists and is linked to its option.
	 *
	 * @param string $key      WooCommerce page key.
	 * @param string $title    Title.
	 * @param string $slug     Slug.
	 * @param string $content  Content (shortcode).
	 * @return int Page ID.
	 */
	protected static function ensure_wc_page( $key, $title, $slug, $content ) {
		$option = 'woocommerce_' . $key . '_page_id';
		$page_id = (int) get_option( $option );

		if ( $page_id > 0 && get_post( $page_id ) ) {
			return $page_id;
		}

		$page_id = self::ensure_page( $slug, $title, $content, '' );

		if ( $page_id ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}

	/**
	 * Replace block-based cart/checkout content with the classic shortcode.
	 *
	 * @param int    $page_id   Page ID.
	 * @param string $shortcode Shortcode.
	 */
	protected static function classic_shortcode( $page_id, $shortcode ) {
		if ( ! $page_id ) {
			return;
		}

		$page = get_post( $page_id );
		if ( ! $page ) {
			return;
		}

		$content = (string) $page->post_content;

		if ( false !== strpos( $content, $shortcode ) ) {
			return;
		}

		if ( '' !== trim( $content ) && false === strpos( $content, 'wp:woocommerce' ) ) {
			return; // Custom content: leave it alone.
		}

		wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => $shortcode,
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * 9. Menus
	 * ------------------------------------------------------------------ */

	/**
	 * Build the four menus and assign their locations.
	 *
	 * @param array $args Options.
	 */
	protected static function step_menus( $args ) {
		$shop_id  = (int) get_option( 'woocommerce_shop_page_id' );
		$cart_id  = (int) get_option( 'woocommerce_cart_page_id' );
		$chk_id   = (int) get_option( 'woocommerce_checkout_page_id' );
		$acc_id   = (int) get_option( 'woocommerce_myaccount_page_id' );
		$front_id = (int) get_option( 'page_on_front' );
		$blog_id  = (int) get_option( 'page_for_posts' );
		$garage   = get_page_by_path( 'garage' );
		$about    = get_page_by_path( 'about-partyab' );
		$garage_id = $garage ? (int) $garage->ID : 0;
		$about_id  = $about ? (int) $about->ID : 0;

		$cats = array();
		foreach ( self::categories_map() as $slug => $data ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term ) {
				$cats[ $slug ] = array( 'id' => (int) $term->term_id, 'name' => $data[0] );
			}
		}

		// Primary.
		$primary = self::menu( 'منوی اصلی' );
		if ( $primary && empty( wp_get_nav_menu_items( $primary ) ) ) {
			self::menu_page( $primary, $front_id, 'خانه' );
			self::menu_page( $primary, $shop_id, 'فروشگاه' );
			foreach ( array( 'brake', 'filter', 'suspension', 'electrical' ) as $slug ) {
				if ( isset( $cats[ $slug ] ) ) {
					self::menu_term( $primary, $cats[ $slug ]['id'], $cats[ $slug ]['name'] );
				}
			}
			self::menu_page( $primary, $garage_id, 'گاراژ من' );
			self::menu_page( $primary, $blog_id, 'راهنما' );
		}

		// Footer: categories.
		$footer_shop = self::menu( 'فوتر — دسته‌بندی‌ها' );
		if ( $footer_shop && empty( wp_get_nav_menu_items( $footer_shop ) ) ) {
			$i = 0;
			foreach ( $cats as $cat ) {
				self::menu_term( $footer_shop, $cat['id'], $cat['name'] );
				$i++;
				if ( $i >= 5 ) {
					break;
				}
			}
		}

		// Footer: help.
		$footer_help = self::menu( 'فوتر — راهنما' );
		if ( $footer_help && empty( wp_get_nav_menu_items( $footer_help ) ) ) {
			self::menu_page( $footer_help, $garage_id, 'گاراژ من' );
			self::menu_page( $footer_help, $cart_id, 'سبد خرید' );
			self::menu_page( $footer_help, $chk_id, 'تسویه حساب' );
			self::menu_page( $footer_help, $acc_id, 'حساب کاربری' );
			self::menu_page( $footer_help, $blog_id, 'راهنما' );
		}

		// Footer: about.
		$footer_about = self::menu( 'فوتر — پارت‌یاب' );
		if ( $footer_about && empty( wp_get_nav_menu_items( $footer_about ) ) ) {
			self::menu_page( $footer_about, $about_id, 'دربارهٔ پارت‌یاب' );
			self::menu_page( $footer_about, $blog_id, 'راهنما و مقاله' );
			self::menu_page( $footer_about, $shop_id, 'همهٔ قطعات' );
		}

		$locations = get_theme_mod( 'nav_menu_locations' );
		$locations = is_array( $locations ) ? $locations : array();

		$map = array(
			'primary'      => $primary,
			'footer-shop'  => $footer_shop,
			'footer-help'  => $footer_help,
			'footer-about' => $footer_about,
		);

		foreach ( $map as $location => $menu_id ) {
			if ( $menu_id ) {
				$locations[ $location ] = $menu_id;
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );

		self::add( 'منوها', 'ok', __( 'منوی اصلی و سه منوی فوتر ساخته و به جایگاه‌ها وصل شدند.', 'partyab' ) );
	}

	/**
	 * Get or create a menu by name.
	 *
	 * @param string $name Menu name.
	 * @return int Menu ID.
	 */
	protected static function menu( $name ) {
		$menu = wp_get_nav_menu_object( $name );

		if ( $menu ) {
			return (int) $menu->term_id;
		}

		$id = wp_create_nav_menu( $name );

		return is_wp_error( $id ) ? 0 : (int) $id;
	}

	/**
	 * Add a page to a menu.
	 *
	 * @param int    $menu_id Menu ID.
	 * @param int    $page_id Page ID.
	 * @param string $title   Label.
	 */
	protected static function menu_page( $menu_id, $page_id, $title ) {
		if ( ! $menu_id || ! $page_id ) {
			return;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $title,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}

	/**
	 * Add a product category to a menu.
	 *
	 * @param int    $menu_id Menu ID.
	 * @param int    $term_id Term ID.
	 * @param string $title   Label.
	 */
	protected static function menu_term( $menu_id, $term_id, $title ) {
		if ( ! $menu_id || ! $term_id ) {
			return;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $title,
				'menu-item-object'    => 'product_cat',
				'menu-item-object-id' => $term_id,
				'menu-item-type'      => 'taxonomy',
				'menu-item-status'    => 'publish',
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * 10. Finalize + verification
	 * ------------------------------------------------------------------ */

	/**
	 * Flush, verify and warn about the two real risks (page cache, block checkout).
	 *
	 * @param array $args Options.
	 */
	protected static function step_finalize( $args ) {
		flush_rewrite_rules( false );

		$products = wp_count_posts( 'product' );
		$published = isset( $products->publish ) ? (int) $products->publish : 0;

		self::add( 'شمارش محصولات', $published > 0 ? 'ok' : 'warn', sprintf( __( '%s محصول منتشرشده', 'partyab' ), self::fa( $published ) ) );

		$fitted = class_exists( 'Partyab_DB' ) ? Partyab_DB::fitted_product_count() : 0;
		self::add(
			'سازگاری محصولات',
			$fitted > 0 ? 'ok' : 'warn',
			sprintf( __( '%1$s از %2$s محصول جدول سازگاری دارند.', 'partyab' ), self::fa( $fitted ), self::fa( $published ) )
		);

		$cache = self::active_cache_plugins();
		if ( $cache ) {
			self::add(
				'کش صفحه',
				'warn',
				sprintf(
					/* translators: %s: plugin names */
					__( 'افزونهٔ کش فعال است: %s — مسیرهای /shop/، /product/، /product-category/ و صفحهٔ گاراژ را از کش استثنا کنید یا کش را با کوکی partyab_vehicle متغیر کنید.', 'partyab' ),
					implode( ', ', $cache )
				)
			);
		} else {
			self::add( 'کش صفحه', 'ok', __( 'افزونهٔ کش شناخته‌شده‌ای فعال نیست.', 'partyab' ) );
		}

		if ( self::has_block_checkout() ) {
			self::add( 'سبد و تسویه', 'warn', __( 'برگهٔ سبد/تسویه هنوز بلوکی است؛ برای استایل کامل قالب از شورت‌کد کلاسیک استفاده کنید.', 'partyab' ) );
		} else {
			self::add( 'سبد و تسویه', 'ok', __( 'حالت کلاسیک (شورت‌کد) فعال است و استایل قالب اعمال می‌شود.', 'partyab' ) );
		}

		self::add( 'آدرس سایت', 'ok', home_url( '/' ) );
		self::add(
			'بعدی',
			'ok',
			__( 'یک پنجرهٔ ناشناس باز کنید: انتخاب خودرو ۲۰۶ ← فروشگاه «فقط سازگار» ← صفحهٔ محصول ← سبد و تسویه با پرداخت در محل.', 'partyab' )
		);
	}

	/**
	 * Known page-cache plugins.
	 *
	 * @return array
	 */
	protected static function active_cache_plugins() {
		$known = array(
			'litespeed-cache/litespeed-cache.php'          => 'LiteSpeed Cache',
			'wp-rocket/wp-rocket.php'                      => 'WP Rocket',
			'w3-total-cache/w3-total-cache.php'            => 'W3 Total Cache',
			'wp-super-cache/wp-cache.php'                  => 'WP Super Cache',
			'cache-enabler/cache-enabler.php'              => 'Cache Enabler',
			'nginx-helper/nginx-helper.php'                => 'Nginx Helper',
			'cloudflare/cloudflare.php'                    => 'Cloudflare',
			'autoptimize/autoptimize.php'                  => 'Autoptimize',
			'sg-cachepress/sg-cachepress.php'              => 'SiteGround Optimizer',
			'hostinger-cache/hostinger-cache.php'          => 'Hostinger Cache',
		);

		$active = (array) get_option( 'active_plugins', array() );
		$found  = array();

		foreach ( $known as $file => $label ) {
			if ( in_array( $file, $active, true ) ) {
				$found[] = $label;
			}
		}

		return $found;
	}

	/**
	 * Are cart/checkout pages still block-based?
	 *
	 * @return bool
	 */
	protected static function has_block_checkout() {
		foreach ( array( 'cart', 'checkout' ) as $key ) {
			$page_id = (int) get_option( 'woocommerce_' . $key . '_page_id' );
			if ( ! $page_id ) {
				continue;
			}
			$page = get_post( $page_id );
			if ( $page && false !== strpos( (string) $page->post_content, 'wp:woocommerce' ) ) {
				return true;
			}
		}

		return false;
	}

	/* ---------------------------------------------------------------------
	 * Helpers
	 * ------------------------------------------------------------------ */

	/**
	 * Normalise CSV headers (strip BOM, spaces, lowercase).
	 *
	 * @param array|false $header Raw header row.
	 * @return array
	 */
	protected static function clean_header( $header ) {
		if ( ! is_array( $header ) ) {
			return array();
		}

		$out = array();

		foreach ( $header as $column ) {
			$column = str_replace( "\xEF\xBB\xBF", '', (string) $column );
			$out[]  = strtolower( trim( $column ) );
		}

		return $out;
	}

	/**
	 * Latin digits from Persian/Arabic digits.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	protected static function latin( $value ) {
		$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		$ar = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );

		$value = str_replace( $fa, array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), (string) $value );
		$value = str_replace( $ar, array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $value );

		return $value;
	}

	/**
	 * Clean a price cell.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	protected static function price( $value ) {
		$value = self::latin( $value );
		$value = str_replace( array( '٬', ',', ' ', "\xc2\xa0" ), '', $value );

		if ( ! is_numeric( $value ) ) {
			return '';
		}

		return (string) (float) $value;
	}

	/**
	 * Persian digits for report strings.
	 *
	 * @param string|int $value Value.
	 * @return string
	 */
	protected static function fa( $value ) {
		if ( function_exists( 'partyab_fa' ) ) {
			return partyab_fa( $value );
		}

		return str_replace( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), (string) $value );
	}

	/**
	 * Short about-page copy.
	 *
	 * @return string
	 */
	protected static function about_content() {
		return '<p>پارت‌یاب فروشگاه قطعات خودرو است: خودروی خود را انتخاب می‌کنید و فقط قطعاتی را می‌بینید که سازگاری‌شان با مدل و سال ساخت شما در فروشگاه ثبت شده است.</p>'
			. '<p>هر قطعه یکی از پنج وضعیت را دارد: سازگار، سازگار با شرط، ناسازگار، ثبت‌نشده و عمومی. «ثبت‌نشده» یعنی اطلاعاتی نداریم — نه این‌که قطعه ناسازگار است.</p>'
			. '<p>پرداخت در محل، ارسال با پیک یا پست، و بازگشت ۷ روزه.</p>';
	}

	/* ---------------------------------------------------------------------
	 * Admin screen
	 * ------------------------------------------------------------------ */

	/**
	 * Tools → «نصب سریع پارت‌یاب».
	 */
	public static function admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$report = get_transient( 'partyab_setup_report_' . get_current_user_id() );

		if ( $report ) {
			delete_transient( 'partyab_setup_report_' . get_current_user_id() );
		}

		$last = get_option( 'partyab_last_setup', array() );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'نصب سریع پارت‌یاب', 'partyab' ); ?></h1>

			<p style="max-width:70ch;line-height:2">
				<?php esc_html_e( 'این صفحه همان کاری را می‌کند که «راهنمای نصب بی‌خطر» به‌صورت دستی توضیح می‌دهد: فعال‌سازی قالب، ساخت جدول سازگاری، ساخت ۸ دسته، ایمپورت ۱۳ محصول نمونه و ۳۶ ردیف سازگاری، ساخت صفحهٔ گاراژ، تنظیم صفحهٔ اصلی، ساخت منوها و تنظیم واحد پول، ارسال و پرداخت در محل.', 'partyab' ); ?>
				<br>
				<strong><?php esc_html_e( 'اجرای دوباره بی‌خطر است:', 'partyab' ); ?></strong>
				<?php esc_html_e( 'محصولات موجود و صفحه‌های ساخته‌شده بازنویسی نمی‌شوند مگر گزینهٔ «بازنویسی» را بزنید.', 'partyab' ); ?>
			</p>

			<?php if ( is_array( $report ) && $report ) : ?>
				<h2><?php esc_html_e( 'گزارش اجرا', 'partyab' ); ?></h2>
				<table class="widefat striped" style="max-width:900px">
					<thead>
						<tr>
							<th style="width:170px"><?php esc_html_e( 'مرحله', 'partyab' ); ?></th>
							<th style="width:90px"><?php esc_html_e( 'وضعیت', 'partyab' ); ?></th>
							<th><?php esc_html_e( 'نتیجه', 'partyab' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $report as $row ) : ?>
							<?php
							$colors = array(
								'ok'   => '#15803D',
								'skip' => '#475569',
								'warn' => '#B45309',
								'fail' => '#B91C1C',
							);
							$labels = array(
								'ok'   => __( 'انجام شد', 'partyab' ),
								'skip' => __( 'رد شد', 'partyab' ),
								'warn' => __( 'هشدار', 'partyab' ),
								'fail' => __( 'خطا', 'partyab' ),
							);
							$status = isset( $row['status'] ) ? $row['status'] : 'ok';
							$color  = isset( $colors[ $status ] ) ? $colors[ $status ] : '#475569';
							?>
							<tr>
								<td><strong><?php echo esc_html( $row['step'] ); ?></strong></td>
								<td><span style="color:<?php echo esc_attr( $color ); ?>;font-weight:600"><?php echo esc_html( isset( $labels[ $status ] ) ? $labels[ $status ] : $status ); ?></span></td>
								<td><?php echo esc_html( $row['message'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php elseif ( is_array( $last ) && ! empty( $last['time'] ) ) : ?>
				<p class="description">
					<?php
					printf(
						/* translators: %s: date */
						esc_html__( 'آخرین اجرای موفق: %s', 'partyab' ),
						esc_html( partyab_fa( $last['time'] ) )
					);
					?>
				</p>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:22px;max-width:760px">
				<?php wp_nonce_field( 'partyab_setup_run' ); ?>
				<input type="hidden" name="action" value="partyab_setup_run">

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="partyab-currency"><?php esc_html_e( 'واحد پول', 'partyab' ); ?></label></th>
						<td>
							<select id="partyab-currency" name="currency">
								<option value="IRR"><?php esc_html_e( 'ریال ایران (IRR)', 'partyab' ); ?></option>
								<option value="IRT"><?php esc_html_e( 'تومان (IRT)', 'partyab' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'ووکامرس به‌صورت پیش‌فرض ریال دارد؛ تومان نیاز به افزونهٔ تومان دارد.', 'partyab' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'تصاویر نمونه', 'partyab' ); ?></th>
						<td>
							<label><input type="checkbox" name="with_images" value="1" checked> <?php esc_html_e( 'تصاویر دمو را از قالب به کتابخانهٔ رسانه اضافه کن', 'partyab' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'بازنویسی', 'partyab' ); ?></th>
						<td>
							<label><input type="checkbox" name="force" value="1"> <?php esc_html_e( 'محصولات موجود با همان SKU را به‌روزرسانی کن (روی سایت واقعی نزن)', 'partyab' ); ?></label>
						</td>
					</tr>
					<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
						<tr>
							<th scope="row"><?php esc_html_e( 'ووکامرس', 'partyab' ); ?></th>
							<td>
								<label><input type="checkbox" name="install_woo" value="1"> <?php esc_html_e( 'نصب و فعال‌سازی ووکامرس هم انجام شود (نیاز به دسترسی WP-CLI)', 'partyab' ); ?></label>
							</td>
						</tr>
					<?php endif; ?>
				</table>

				<p>
					<button type="submit" class="button button-primary button-hero">
						<?php esc_html_e( 'اجرای نصب خودکار', 'partyab' ); ?>
					</button>
				</p>
			</form>

			<hr style="margin:26px 0">

			<h2><?php esc_html_e( 'بعد از نصب', 'partyab' ); ?></h2>
			<ol style="line-height:2;max-width:80ch">
				<li><?php esc_html_e( 'یک پنجرهٔ ناشناس باز کنید و صفحهٔ اصلی را ببینید.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'خودرو ۲۰۶ مدل ۱۳۹۵ را انتخاب کنید؛ باید به فروشگاه با فیلتر «فقط سازگار» بروید.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'یک محصول را به سبد اضافه کنید و تسویه را با «پرداخت در محل» تا آخر ببرید.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'اگر افزونهٔ کش فعال دارید، مسیرهای /shop/ و /product/ را از کش استثنا کنید (گزارش بالاتر هشدار می‌دهد).', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'برای دادهٔ واقعی: جدول سازگاری را با CSV خودتان ایمپورت کنید و تصاویر محصولات را جایگزین کنید.', 'partyab' ); ?></li>
			</ol>
		</div>
		<?php
	}
}
