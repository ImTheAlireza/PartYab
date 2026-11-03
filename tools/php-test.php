<?php
/**
 * تست دودی قالب بدون وردپرس: توابع خالص و رندر شورت‌کدها با استاب.
 * اجرا: node tools/php-run.mjs tools/php-test.php
 */
define( 'ABSPATH', '/app/' );
define( 'PARTYAB_DIR', '/app/theme/part-yab' );
define( 'PARTYAB_URI', 'http://example.test/theme' );
define( 'PARTYAB_VERSION', '0.1.0' );

$GLOBALS['fails'] = 0;

function check( $label, $ok, $note = '' ) {
	if ( ! $ok ) {
		$GLOBALS['fails']++;
	}
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . ( '' !== $note ? ' — ' . $note : '' ) . "\n";
}

/* ---------- استاب‌های حداقلی وردپرس ---------- */
function add_action( ...$a ) {}

$GLOBALS['pb_filters'] = array();

function add_filter( $tag, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['pb_filters'][ $tag ][ $priority ][] = array( $callback, $args );
	return true;
}
function remove_action( ...$a ) {}
function remove_filter( ...$a ) {}
function apply_filters( $tag, $value, ...$rest ) {
	if ( empty( $GLOBALS['pb_filters'][ $tag ] ) ) {
		return $value;
	}

	ksort( $GLOBALS['pb_filters'][ $tag ] );

	foreach ( $GLOBALS['pb_filters'][ $tag ] as $callbacks ) {
		foreach ( $callbacks as $entry ) {
			list( $callback, $args ) = $entry;
			$extra = array_slice( $rest, 0, max( 0, $args - 1 ) );
			$value = call_user_func_array( $callback, array_merge( array( $value ), $extra ) );
		}
	}

	return $value;
}
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function esc_attr( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function esc_url( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function esc_url_raw( $t ) { return (string) $t; }
function esc_textarea( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES ); }
function wp_kses_post( $t ) { return (string) $t; }
function home_url( $p = '/' ) { return 'http://example.test' . $p; }
function site_url( $p = '/' ) { return home_url( $p ); }
function admin_url( $p = '' ) { return 'http://example.test/wp-admin/' . $p; }
function is_admin() { return false; }
function is_ssl() { return false; }
function sanitize_title( $t ) { return preg_replace( '/\s+/u', '-', trim( (string) $t ) ); }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function absint( $v ) { return abs( (int) $v ); }
function number_format_i18n( $n, $d = 0 ) { return apply_filters( 'number_format_i18n', number_format( (float) $n, $d ), $n, $d ); }
function selected( $a, $b, $echo = true ) { $r = ( (string) $a === (string) $b ) ? ' selected' : ''; if ( $echo ) { echo $r; } return $r; }
function get_option( $k, $d = false ) { return $d; }
function update_option( $k, $v ) { return true; }
function add_query_arg( ...$args ) {
	if ( count( $args ) === 2 && is_array( $args[1] ) ) {
		$url = $args[0];
		return $url . ( strpos( $url, '?' ) === false ? '?' : '&' ) . http_build_query( $args[1] );
	}
	if ( count( $args ) === 2 ) {
		return $args[1] . ( strpos( $args[1], '?' ) === false ? '?' : '&' ) . $args[0] . '=1';
	}
	return (string) ( $args[3] ?? '' );
}
function remove_query_arg( $keys, $url = '' ) { return '' !== $url ? $url : home_url( '/shop' ); }
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function wp_localize_script() {}
function wp_script_is() { return false; }
function wp_add_inline_style() {}
function partyab_asset_version( $f = '' ) { return '1'; }
function wc_get_page_permalink( $p ) { return home_url( '/' . $p ); }
function is_shop() { return true; }
function is_woocommerce() { return true; }
function is_product_taxonomy() { return false; }
function is_cart() { return false; }
function is_checkout() { return false; }
function is_account_page() { return false; }
function is_front_page() { return false; }
function is_home() { return false; }
function is_tax() { return false; }
function is_product() { return false; }
function taxonomy_exists( $t ) { return in_array( $t, array( 'pb_car', 'pb_brand' ), true ); }
function is_wp_error( $x ) { return false; }
function get_queried_object_id() { return 0; }
function get_object_taxonomies( $t ) { return array( 'product_cat' ); }
function has_shortcode( $c, $s ) { return false; }
function flush_rewrite_rules( $h = true ) {}
function get_header() { echo "<!--HEADER-->\n"; }
function get_footer() { echo "<!--FOOTER-->\n"; }
function have_posts() { return false; }
function the_post() {}
function the_content() { echo '<!--ELEMENTOR_CONTENT-->'; }
function wc_get_products( $a = array() ) { return array(); }
function wc_get_product_ids_on_sale() { return array(); }
function wc_get_account_endpoint_url( $e ) { return home_url( '/account/' . $e ); }
function wc_get_account_menu_items() { return array( 'dashboard' => 'پیشخوان' ); }
function wc_get_account_menu_item_classes( $e ) { return 'woocommerce-MyAccount-navigation-link'; }
function partyab_brand_terms() {
	return array(
		(object) array( 'term_id' => 1, 'name' => 'ایساکو', 'count' => 2, 'slug' => 'isaco' ),
		(object) array( 'term_id' => 2, 'name' => 'بوش', 'count' => 1, 'slug' => 'bosch' ),
	);
}
function get_term_by( $field, $value, $tax ) {
	return (object) array( 'term_id' => 77, 'name' => 'سیستم ترمز', 'slug' => $value, 'count' => 3, 'taxonomy' => 'product_cat' );
}
function get_the_terms( $post_id, $tax ) {
	if ( 'pb_brand' === $tax ) {
		return array( (object) array( 'term_id' => 1, 'name' => 'ایساکو', 'parent' => 0 ) );
	}
	if ( 'pb_car' === $tax ) {
		return array(
			(object) array( 'term_id' => 10, 'name' => 'پراید', 'parent' => 0 ),
			(object) array( 'term_id' => 11, 'name' => '131', 'parent' => 10 ),
			(object) array( 'term_id' => 12, 'name' => '111', 'parent' => 10 ),
		);
	}
	return array();
}
function get_terms( $a = array() ) {
	return array(
		(object) array( 'term_id' => 1, 'name' => 'سیستم ترمز', 'slug' => 'brake-system', 'count' => 3, 'taxonomy' => 'product_cat' ),
		(object) array( 'term_id' => 2, 'name' => 'فیلترها', 'slug' => 'filters', 'count' => 2, 'taxonomy' => 'product_cat' ),
	);
}
function add_shortcode( $tag, $cb ) {}
function shortcode_exists( $t ) { return in_array( $t, array( 'partyab_my_car', 'products' ), true ); }
function do_shortcode( $c ) { return '<div class="mock-shortcode">' . $c . '</div>'; }
function get_term_link( $t ) { return home_url( '/term' ); }
function get_term( $id, $tax ) { return null; }
function get_bloginfo( $k = '' ) { return 'PartYab'; }
function wc_get_cart_url() { return home_url( '/cart' ); }
function is_rtl() { return true; }
function wp_create_nonce( $a ) { return 'nonce'; }
function wp_unslash( $v ) { return is_string( $v ) ? stripslashes( $v ) : $v; }
function wp_parse_args( $a, $d = array() ) { return array_merge( $d, (array) $a ); }
function get_query_var( $k, $d = '' ) { return $d; }
function wp_json_encode( $v ) { return json_encode( $v ); }

// درخت خودرو (همان چیزی که تاکسونومی می‌سازد).
function partyab_car_tree() {
	return array(
		array(
			'term'   => (object) array( 'term_id' => 10, 'name' => 'پراید', 'count' => 2, 'parent' => 0 ),
			'models' => array(
				(object) array( 'term_id' => 11, 'name' => '131', 'count' => 1, 'parent' => 10 ),
				(object) array( 'term_id' => 12, 'name' => '111', 'count' => 1, 'parent' => 10 ),
			),
		),
	);
}

class WC_Cart_Stub {
	public $subtotal = 3200000;
	public function get_cart_contents_count() { return 2; }
	public function get_subtotal() { return $this->subtotal; }
}

class WC_Stub {
	public $cart;
	public function __construct() { $this->cart = new WC_Cart_Stub(); }
}

function WC() {
	static $instance = null;
	if ( null === $instance ) {
		$instance = new WC_Stub();
	}
	return $instance;
}

class WC_Product {
	public $regular = '0';
	public $sale    = '0';
	public function get_regular_price() { return $this->regular; }
	public function get_sale_price() { return $this->sale; }
	public function get_id() { return 1; }
}

require PARTYAB_DIR . '/inc/compat-ui.php';
require PARTYAB_DIR . '/inc/woocommerce.php';
require PARTYAB_DIR . '/inc/layout.php';

/* ---------- ۱) نگاشت خودرو از تاکسونومی ---------- */
$map = partyab_cars_map();
check( 'نگاشت خودرو از تاکسونومی', isset( $map['پراید'] ) && in_array( '131', $map['پراید'], true ), json_encode( $map, JSON_UNESCAPED_UNICODE ) );

/* ---------- ۲) رندر شورت‌کد با خودروی ذخیره‌شده ---------- */
$_COOKIE['partyab_car'] = rawurlencode( 'پراید|111|1395' );
$car = partyab_current_car();
check( 'خواندن کوکی خودرو', 'پراید' === $car['brand'] && '111' === $car['model'] && '1395' === (string) $car['year'], json_encode( $car, JSON_UNESCAPED_UNICODE ) );

$html = partyab_car_selector_shortcode();
check( 'گزینه برند پراید', strpos( $html, 'value="پراید"' ) !== false );
check( 'گزینه مدل 131 رندر شده', strpos( $html, '>131</option>' ) !== false );
check( 'مدل ذخیره‌شده انتخاب است', (bool) preg_match( '/value="111"\s+selected/', $html ) );
check( 'باکس خودرو کلاس درست دارد', strpos( $html, 'class="pb-car"' ) !== false );

/* ---------- ۳) ارقام فارسی ---------- */
check( 'ارقام فارسی', '۱,۲۵۰,۰۰۰' === partyab_fa_digits( '1,250,000' ), partyab_fa_digits( '1,250,000' ) );

/* ---------- ۴) درصد تخفیف ---------- */
$product       = new WC_Product();
$product->regular = '850000';
$product->sale    = '690000';
$badge = partyab_sale_percent_badge( '<span class="onsale">تخفیف</span>', null, $product );
check( 'درصد تخفیف محاسبه شد', strpos( $badge, '۱۹٪' ) !== false, $badge );

/* ---------- ۴٫۵) خط برند/خودرو روی کارت ---------- */
ob_start();
$partyab_product = new WC_Product();
partyab_loop_terms_line();
$card_meta = ob_get_clean();
check( 'چیپ برند روی کارت', strpos( $card_meta, 'ایساکو' ) !== false, trim( $card_meta ) );
check( 'خودروهای سازگار روی کارت', strpos( $card_meta, 'سازگار با 131' ) !== false, trim( $card_meta ) );

/* ---------- ۴٫۶) سایدبار با base صریح (صفحه جستجو) ---------- */
ob_start();
partyab_shop_sidebar( 'http://example.test/shop' );
$sidebar = ob_get_clean();
check( 'سایدبار روی صفحه جستجو هم رندر می‌شود', strpos( $sidebar, 'pb-side-card' ) !== false && strlen( $sidebar ) > 500, strlen( $sidebar ) . ' بایت' );

/* ---------- ۵) ورودی سال: ارقام فارسی ---------- */
check( 'سال با ارقام فارسی نمایش داده شد', strpos( $html, 'value="۱۳۹۵"' ) !== false );

/* ---------- ۵٫۵) محافظ صفحه گاراژ خالی ---------- */
function is_page( $p = '' ) { return true; }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function get_the_title( $id = 0 ) { return 'گاراژ من'; }
function get_the_ID() { return 42; }
function get_post_field( $f, $id ) { return 'garage'; }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function mock_car_marker( $c ) { return $c; }

check( 'صفحه گاراژ خالی خودکار باکس خودرو می‌گیرد', strpos( partyab_auto_car_selector( '' ), 'partyab_my_car' ) !== false );
check( 'صفحه‌ای که محتوا دارد دست نمی‌خورد', 'محتوای من' === partyab_auto_car_selector( 'محتوای من' ) );

/* ---------- ۵٫۶) سبد و تسویه ---------- */
ob_start();
partyab_free_shipping_progress();
$ship = ob_get_clean();
check( 'نوار ارسال رایگان: مبلغ باقی‌مانده', strpos( $ship, '۱,۸۰۰,۰۰۰' ) !== false, trim( strip_tags( $ship ) ) );
check( 'نوار ارسال رایگان: درصد پیشرفت', strpos( $ship, 'width:64%' ) !== false );
check( 'نوار ارسال رایگان: شریط ARIA', strpos( $ship, 'role="progressbar"' ) !== false );

ob_start();
partyab_checkout_trust();
$trust = ob_get_clean();
check( 'ردیف اعتماد تسویه', strpos( $trust, 'پرداخت امن' ) !== false && strpos( $trust, 'مرجوعی' ) !== false );

$fields = partyab_checkout_fields( array(
	'billing' => array(
		'billing_company'  => array( 'label' => 'شرکت' ),
		'billing_address_2' => array( 'label' => 'آدرس ۲' ),
		'billing_first_name' => array( 'label' => 'نام' ),
	),
	'order'   => array( 'order_comments' => array( 'label' => 'یادداشت' ) ),
) );
check( 'حذف فیلدهای اضافی تسویه', ! isset( $fields['billing']['billing_company'] ) && ! isset( $fields['billing']['billing_address_2'] ) );
check( 'برچسب فارسی یادداشت سفارش', 'یادداشت سفارش (اختیاری)' === $fields['order']['order_comments']['label'] );

/* ---------- ۶) رندر صفحه اصلی دمو ۱ ---------- */
ob_start();
include PARTYAB_DIR . '/front-page.php';
$front = ob_get_clean();

foreach ( array( 'pb-hero', 'pb-cats', 'pb-cat__icon', 'pb-banner', 'pb-brands', 'pb-quote', 'pb-steps' ) as $section ) {
	check( 'بخش ' . $section, strpos( $front, $section ) !== false );
}

check( 'مسیر المنتور فعال نشد (چون المنتور نصب نیست)', strpos( $front, 'ELEMENTOR_CONTENT' ) === false );
check( 'صفحه اصلی هدر و فوتر دارد', strpos( $front, 'HEADER' ) !== false && strpos( $front, 'FOOTER' ) !== false );

echo $GLOBALS['fails'] === 0 ? "\nهمه تست‌ها سبز\n" : "\n" . $GLOBALS['fails'] . " تست شکست خورد\n";
exit( $GLOBALS['fails'] > 0 ? 1 : 0 );
