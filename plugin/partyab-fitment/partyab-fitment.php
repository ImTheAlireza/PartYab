<?php
/**
 * Plugin Name:       PartYab Fitment — سازگاری قطعه و خودرو
 * Plugin URI:        https://github.com/ImTheAlireza/PartYab
 * Description:       کاتالوگ خودروهای ایرانی و وارداتی، جدول سازگاری قطعه، پنج حالت سازگاری، فیلتر «فقط سازگار» در فروشگاه و ایمپورت CSV.
 * Version:           1.1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            PartYab
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       partyab
 * Domain Path:       /languages
 * WC requires at least: 7.0
 * WC tested up to:   9.4
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_FITMENT_VERSION', '1.1.0' );
define( 'PARTYAB_FITMENT_FILE', __FILE__ );
define( 'PARTYAB_FITMENT_DIR', plugin_dir_path( __FILE__ ) );
define( 'PARTYAB_FITMENT_URL', plugin_dir_url( __FILE__ ) );
define( 'PARTYAB_COOKIE', 'partyab_vehicle' );

require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-vehicles.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-db.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-fitment.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-admin.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-rest.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-csv.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-query.php';
require_once PARTYAB_FITMENT_DIR . 'includes/class-partyab-setup.php';

/**
 * Boot the plugin once WooCommerce (or at least WordPress) is ready.
 */
function partyab_fitment_boot() {
	load_plugin_textdomain( 'partyab', false, dirname( plugin_basename( PARTYAB_FITMENT_FILE ) ) . '/languages' );

	Partyab_DB::maybe_upgrade();

	Partyab_Admin::init();
	Partyab_REST::init();
	Partyab_CSV::init();
	Partyab_Query::init();
	Partyab_Setup::init();
}
add_action( 'plugins_loaded', 'partyab_fitment_boot' );

/**
 * Declare compatibility with WooCommerce HPOS (custom order tables).
 */
function partyab_fitment_declare_compat() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', PARTYAB_FITMENT_FILE, true );
	}
}
add_action( 'before_woocommerce_init', 'partyab_fitment_declare_compat' );

/**
 * Activation: create the fitment table and seed defaults.
 */
function partyab_fitment_activate() {
	Partyab_DB::install();
	add_option( 'partyab_fitment_db', Partyab_DB::VERSION );
}
register_activation_hook( __FILE__, 'partyab_fitment_activate' );

/**
 * Warn (quietly) when WooCommerce is missing — the theme still works, products just do not exist.
 */
function partyab_fitment_admin_notice() {
	if ( class_exists( 'WooCommerce' ) ) {
		return;
	}
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	esc_html_e( 'افزونهٔ «پارت‌یاب — سازگاری قطعه و خودرو» برای کامل‌شدن به ووکامرس نیاز دارد. جدول سازگاری ساخته می‌شود، ولی محصولی برای اتصال وجود ندارد.', 'partyab' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'partyab_fitment_admin_notice' );

/* -------------------------------------------------------------------------
 * Template helpers — used by the theme (and safe to call from any template).
 * ---------------------------------------------------------------------- */

/**
 * Current visitor vehicle (validated against the catalog).
 *
 * @return array|null
 */
function partyab_current_vehicle() {
	return Partyab_Vehicles::current();
}

/**
 * Fitment state for a product.
 *
 * @param int        $product_id Product ID.
 * @param array|null $vehicle    Vehicle array; defaults to the visitor's vehicle.
 * @return array{state:string,note:string,range:array|null,out_of_range:array|null}
 */
function partyab_fit_state( $product_id, $vehicle = null ) {
	if ( null === $vehicle ) {
		$vehicle = partyab_current_vehicle();
	}
	return Partyab_Fitment::state( (int) $product_id, $vehicle );
}

/**
 * Persian digits helper (works with any latin number).
 *
 * @param string|int $value Value.
 * @return string
 */
function partyab_fa( $value ) {
	return Partyab_Vehicles::fa_digits( $value );
}

/**
 * Format a price with Persian digits and thousands separator.
 *
 * @param float|int $amount Amount.
 * @return string
 */
function partyab_money( $amount ) {
	return partyab_fa( number_format( (float) $amount, 0, '.', '٬' ) );
}
