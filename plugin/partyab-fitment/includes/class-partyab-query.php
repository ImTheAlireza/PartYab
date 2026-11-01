<?php
/**
 * Archive filtering: "only compatible parts" for the active vehicle.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Query
 */
class Partyab_Query {

	/**
	 * Hook.
	 */
	public static function init() {
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_archive' ) );
		add_shortcode( 'partyab_compat_toggle', array( __CLASS__, 'compat_toggle_shortcode' ) );
	}

	/**
	 * Is the current request a WooCommerce product archive?
	 *
	 * @return bool
	 */
	public static function is_product_archive() {
		if ( is_admin() ) {
			return false;
		}
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return true;
		}
		if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			return true;
		}
		if ( is_post_type_archive( 'product' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Should the archive be limited to compatible products?
	 *
	 * @return bool
	 */
	public static function compat_requested() {
		return isset( $_GET['compat'] ) && in_array( wp_unslash( $_GET['compat'] ), array( '1', 'yes', 'true' ), true ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Apply the filter.
	 *
	 * @param WP_Query $query Query.
	 */
	public static function filter_archive( $query ) {
		if ( ! $query instanceof WP_Query || ! $query->is_main_query() ) {
			return;
		}
		if ( ! self::is_product_archive() || ! self::compat_requested() ) {
			return;
		}

		$vehicle = Partyab_Vehicles::current();
		if ( ! $vehicle ) {
			return;
		}

		$ids = Partyab_Fitment::compatible_ids( $vehicle );

		$query->set( 'post__in', ! empty( $ids ) ? $ids : array( 0 ) );
		$query->set( 'ignore_sticky_posts', true );
	}

	/**
	 * Checkbox-style toggle for the shop toolbar.
	 *
	 * @return string
	 */
	public static function compat_toggle_shortcode() {
		$vehicle = Partyab_Vehicles::current();
		if ( ! $vehicle ) {
			return '';
		}

		$active = self::compat_requested();
		$url    = $active ? remove_query_arg( 'compat' ) : add_query_arg( 'compat', '1' );

		return sprintf(
			'<a class="shop-compat-toggle %1$s" href="%2$s">%3$s %4$s</a>',
			$active ? 'is-active' : '',
			esc_url( $url ),
			esc_html__( 'فقط سازگار با', 'partyab' ),
			esc_html( $vehicle['model_name'] )
		);
	}
}
