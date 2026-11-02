<?php
/**
 * ویژگی‌های قالب، منوها و اندازه تصاویر.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'partyab_setup' );

/**
 * ثبت قابلیت‌های پایه.
 */
function partyab_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// گالری محصول: زوم و لایت‌باکس بومی ووکامرس.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 1000,
		'product_grid'          => array(
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 5,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'partyab-primary' => 'منوی اصلی (هدر)',
		'partyab-topbar'  => 'منوی نواری بالا',
		'partyab-footer'  => 'منوی فوتر',
	) );

	// اندازه تصاویر اختصاصی — فقط همان اندازه‌هایی که واقعاً استفاده می‌شوند.
	add_image_size( 'partyab-product', 600, 600, true );
	add_image_size( 'partyab-thumb', 300, 300, true );
	add_image_size( 'partyab-blog', 800, 450, true );
}

add_action( 'after_setup_theme', 'partyab_content_width', 0 );

/**
 * عرض محتوا (پایه گرید ۱۲۴۰px).
 */
function partyab_content_width(): void {
	$GLOBALS['content_width'] = 1240;
}

add_filter( 'image_size_names_choose', 'partyab_custom_image_sizes' );

/**
 * نمایش اندازه‌های اختصاصی در پنجره درج تصویر.
 *
 * @param array $sizes اندازه‌های موجود.
 * @return array
 */
function partyab_custom_image_sizes( array $sizes ): array {
	return array_merge( $sizes, array(
		'partyab-product' => 'محصول (۶۰۰×۶۰۰)',
		'partyab-blog'    => 'مقاله (۸۰۰×۴۵۰)',
	) );
}

/**
 * پاک‌سازی سبک‌های اضافه خود وردپرس (سبک‌تر شدن صفحات).
 */
add_action( 'init', 'partyab_clean_head' );

function partyab_clean_head(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}

add_filter( 'emoji_svg_url', '__return_false' );
