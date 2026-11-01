<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function partyab_setup() {
	load_theme_textdomain( 'partyab', PARTYAB_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'      => __( 'منوی اصلی (نوار پاسپورت)', 'partyab' ),
			'footer-shop'  => __( 'فوتر — دسته‌بندی‌ها', 'partyab' ),
			'footer-help'  => __( 'فوتر — راهنما', 'partyab' ),
			'footer-about' => __( 'فوتر — پارت‌یاب', 'partyab' ),
		)
	);

	add_image_size( 'partyab-card', 480, 360, true );
}
add_action( 'after_setup_theme', 'partyab_setup' );

/**
 * Content width.
 */
function partyab_content_width() {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'partyab_content_width', 0 );

/**
 * Widget areas (footer columns fall back to menus, but widgets are supported).
 */
function partyab_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'فوتر — ستون ۱', 'partyab' ),
			'id'            => 'footer-1',
			'before_widget' => '<div id="%1$s" class="footer-col %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<b>',
			'after_title'   => '</b>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'فوتر — ستون ۲', 'partyab' ),
			'id'            => 'footer-2',
			'before_widget' => '<div id="%1$s" class="footer-col %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<b>',
			'after_title'   => '</b>',
		)
	);
}
add_action( 'widgets_init', 'partyab_widgets' );

/**
 * Body classes used by the design system.
 *
 * @param array $classes Body classes.
 * @return array
 */
function partyab_body_class( $classes ) {
	$classes[] = 'partyab';

	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$classes[] = 'page-shop';
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		$classes[] = 'page-product';
	}
	if ( is_front_page() ) {
		$classes[] = 'page-home';
	}

	return $classes;
}
add_filter( 'body_class', 'partyab_body_class' );

/**
 * Allow SVG uploads for admins only (icons used by the storefront content).
 *
 * @param array $mimes Mime types.
 * @return array
 */
function partyab_allow_svg( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'partyab_allow_svg' );
