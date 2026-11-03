<?php
/**
 * سئو و سرعت: متا، Open Graph، داده ساختاریافته و پاک‌سازی هدر.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * کوتاه‌کردن متن برای توضیح متا، با حفظ مرز کلمه.
 *
 * @param string $text  متن خام (ممکن است HTML باشد).
 * @param int    $limit سقف کاراکتر.
 * @return string
 */
function partyab_seo_description( string $text, int $limit = 160 ): string {
	$text = wp_strip_all_tags( $text );
	$text = trim( (string) preg_replace( '/\s+/u', ' ', $text ) );

	$len = function_exists( 'partyab_text_len' ) ? partyab_text_len( $text ) : strlen( $text );

	if ( $len <= $limit ) {
		return $text;
	}

	$cut   = mb_substr( $text, 0, $limit );
	$space = mb_strrpos( $cut, ' ' );

	if ( false !== $space && $space > (int) ( $limit * 0.6 ) ) {
		$cut = mb_substr( $cut, 0, $space );
	}

	return rtrim( $cut, " \t\n\r\0\x0B.,،" ) . '…';
}

/**
 * اسکیمای محصول (Product) — خالص و قابل تست.
 *
 * @param array<string, mixed> $data name, sku, description, image, brand, price, currency, availability, url.
 * @return array<string, mixed>
 */
function partyab_product_schema( array $data ): array {
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => (string) ( $data['name'] ?? '' ),
		'description' => (string) ( $data['description'] ?? '' ),
	);

	if ( ! empty( $data['sku'] ) ) {
		$schema['sku'] = (string) $data['sku'];
	}

	if ( ! empty( $data['image'] ) ) {
		$schema['image'] = (string) $data['image'];
	}

	if ( ! empty( $data['brand'] ) ) {
		$schema['brand'] = array(
			'@type' => 'Brand',
			'name'  => (string) $data['brand'],
		);
	}

	$schema['offers'] = array(
		'@type'         => 'Offer',
		'url'           => (string) ( $data['url'] ?? '' ),
		'priceCurrency' => (string) ( $data['currency'] ?? 'IRR' ),
		'price'         => (string) ( $data['price'] ?? '0' ),
		'availability'  => (string) ( $data['availability'] ?? 'https://schema.org/InStock' ),
	);

	return $schema;
}

/**
 * اسکیمای مسیر (BreadcrumbList) — خالص و قابل تست.
 *
 * @param array<int, array{name:string,url:string}> $items آیتم‌ها.
 * @return array<string, mixed>
 */
function partyab_breadcrumb_schema( array $items ): array {
	$list = array();

	foreach ( array_values( $items ) as $index => $item ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => (string) $item['name'],
			'item'     => (string) $item['url'],
		);
	}

	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
}

/**
 * اسکیمای پرسش‌های متداول (FAQPage) — خالص و قابل تست.
 *
 * @param array<int, array{0:string,1:string}> $items پرسش/پاسخ.
 * @return array<string, mixed>
 */
function partyab_faq_schema( array $items ): array {
	$list = array();

	foreach ( $items as $item ) {
		$list[] = array(
			'@type'          => 'Question',
			'name'           => (string) $item[0],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => (string) $item[1],
			),
		);
	}

	return array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $list,
	);
}

/**
 * پاک‌سازی هدر وردپرس برای سرعت و امنیت.
 */
function partyab_seo_cleanup(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	add_filter( 'emoji_svg_url', '__return_false' );
	add_filter( 'embed_oembed_discover', '__return_false' );
}
add_action( 'init', 'partyab_seo_cleanup' );

add_action( 'wp_footer', 'partyab_dequeue_embed_script', 1 );

/**
 * حذف اسکریپت wp-embed از فرانت.
 */
function partyab_dequeue_embed_script(): void {
	wp_deregister_script( 'wp-embed' );
}

/* ============ سرعت تصاویر و ایندکس ============ */

add_filter( 'wp_get_attachment_image_attributes', 'partyab_image_priorities', 10, 3 );

/**
 * تصویر شاخص محصول با اولویت بالا بارگذاری شود (بهبود LCP).
 *
 * @param array<string, string> $attr       ویژگی‌های تصویر.
 * @param WP_Post               $attachment پیوست.
 * @param string|int[]          $size       اندازه.
 * @return array<string, string>
 */
function partyab_image_priorities( $attr, $attachment, $size ): array {
	$attr = is_array( $attr ) ? $attr : array();

	if ( function_exists( 'is_product' ) && is_product() && in_the_loop() ) {
		$attr['fetchpriority'] = 'high';
		$attr['loading']       = 'eager';
	}

	return $attr;
}

add_filter( 'wp_robots', 'partyab_robots' );

/**
 * صفحه‌های بی‌ارزش برای گوگل noindex شوند (سبد، تسویه، حساب، جستجو).
 *
 * @param array<string, mixed> $robots قواعد.
 * @return array<string, mixed>
 */
function partyab_robots( $robots ): array {
	$robots = is_array( $robots ) ? $robots : array();

	$private = is_search();

	if ( function_exists( 'is_cart' ) ) {
		$private = $private || is_cart() || is_checkout() || is_account_page();
	}

	if ( $private ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}

add_filter( 'wp_sitemaps_add_provider', 'partyab_sitemap_providers', 10, 2 );

/**
 * حذف نقشه سایت کاربران (حریم خصوصی، بدون ارزش سئو).
 *
 * @param mixed  $provider ارائه‌دهنده.
 * @param string $name     نام.
 * @return mixed
 */
function partyab_sitemap_providers( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}
