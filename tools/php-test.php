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
function add_filter( ...$a ) {}
function remove_action( ...$a ) {}
function remove_filter( ...$a ) {}
function apply_filters( $tag, $value ) { return $value; }
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
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d ); }
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
function get_terms( $a = array() ) {
	return array(
		(object) array( 'term_id' => 1, 'name' => 'سیستم ترمز', 'slug' => 'brake-system', 'count' => 3, 'taxonomy' => 'product_cat' ),
		(object) array( 'term_id' => 2, 'name' => 'فیلترها', 'slug' => 'filters', 'count' => 2, 'taxonomy' => 'product_cat' ),
	);
}
function add_shortcode( $tag, $cb ) {}
function shortcode_exists( $t ) { return false; }
function do_shortcode( $c ) { return ''; }
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

/* ---------- ۵) ورودی سال: ارقام فارسی ---------- */
check( 'سال با ارقام فارسی نمایش داده شد', strpos( $html, 'value="۱۳۹۵"' ) !== false );

/* ---------- ۶) رندر صفحه اصلی دمو ۱ ---------- */
ob_start();
include PARTYAB_DIR . '/front-page.php';
$front = ob_get_clean();

foreach ( array( 'pb-hero', 'pb-cats', 'pb-cat__icon', 'pb-brands', 'pb-quote', 'pb-steps' ) as $section ) {
	check( 'بخش ' . $section, strpos( $front, $section ) !== false );
}

check( 'مسیر المنتور فعال نشد (چون المنتور نصب نیست)', strpos( $front, 'ELEMENTOR_CONTENT' ) === false );
check( 'صفحه اصلی هدر و فوتر دارد', strpos( $front, 'HEADER' ) !== false && strpos( $front, 'FOOTER' ) !== false );

echo $GLOBALS['fails'] === 0 ? "\nهمه تست‌ها سبز\n" : "\n" . $GLOBALS['fails'] . " تست شکست خورد\n";
exit( $GLOBALS['fails'] > 0 ? 1 : 0 );
