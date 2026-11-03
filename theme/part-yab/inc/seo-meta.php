<?php
/**
 * متا، Open Graph و داده ساختاریافته صفحه — خروجی HTML.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * توضیح متای صفحه جاری.
 *
 * @return string
 */
function partyab_current_description(): string {
	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );

		if ( $product ) {
			$text = $product->get_short_description() ?: $product->get_description();

			if ( '' === trim( wp_strip_all_tags( (string) $text ) ) ) {
				$text = sprintf(
					'%s — خرید با ضمانت اصالت کالا، ارسال سریع و مرجوعی ۷ روزه از پارتی‌یاب.',
					$product->get_name()
				);
			}

			return partyab_seo_description( (string) $text );
		}
	}

	if ( is_singular() ) {
		$post = get_post( get_queried_object_id() );

		if ( $post ) {
			$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;

			if ( '' !== trim( wp_strip_all_tags( (string) $text ) ) ) {
				return partyab_seo_description( (string) $text );
			}
		}
	}

	if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$text = '' !== trim( (string) $term->description )
				? $term->description
				: sprintf( 'خرید %s با فیلتر سازگاری خودرو، ضمانت اصالت کالا و ارسال سریع.', $term->name );

			return partyab_seo_description( $text );
		}
	}

	return partyab_seo_description( (string) get_bloginfo( 'description' ) );
}

/**
 * تصویر اشتراک‌گذاری صفحه جاری.
 *
 * @return string
 */
function partyab_current_share_image(): string {
	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );

		if ( $product ) {
			$image = wp_get_attachment_image_url( (int) $product->get_image_id(), 'large' );

			if ( $image ) {
				return (string) $image;
			}
		}
	}

	$thumb = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );

	if ( $thumb ) {
		return (string) $thumb;
	}

	return (string) get_site_icon_url( 512 );
}

/**
 * عنوان صفحه جاری برای اشتراک‌گذاری.
 *
 * @return string
 */
function partyab_current_title(): string {
	if ( is_singular() || ( function_exists( 'is_product' ) && is_product() ) ) {
		return (string) get_the_title( get_queried_object_id() );
	}

	if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			return (string) $term->name;
		}
	}

	return (string) get_bloginfo( 'name' );
}

/**
 * چاپ متا تگ‌های توضیح، Open Graph و Twitter.
 */
function partyab_seo_meta(): void {
	$title       = partyab_current_title();
	$description = partyab_current_description();
	$image       = partyab_current_share_image();
	$url         = is_singular() ? (string) get_permalink( get_queried_object_id() ) : home_url( add_query_arg( array() ) );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:locale" content="fa_IR">' . "\n" );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( (string) get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	} else {
		printf( '<meta name="twitter:card" content="summary">' . "\n" );
	}

	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
}
add_action( 'wp_head', 'partyab_seo_meta', 5 );

/**
 * چاپ داده ساختاریافته JSON-LD.
 */
function partyab_seo_jsonld(): void {
	$blocks = array();

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );

		if ( $product ) {
			$blocks[] = partyab_product_schema(
				array(
					'name'         => $product->get_name(),
					'sku'          => $product->get_sku(),
					'description'  => partyab_seo_description( (string) ( $product->get_short_description() ?: $product->get_description() ), 300 ),
					'image'        => (string) wp_get_attachment_image_url( (int) $product->get_image_id(), 'large' ),
					'brand'        => partyab_product_brand_name( (int) $product->get_id() ),
					'price'        => (string) $product->get_price(),
					'currency'     => 'IRR',
					'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
					'url'          => (string) get_permalink( (int) $product->get_id() ),
				)
			);
		}
	}

	if ( is_front_page() ) {
		$blocks[] = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => (string) get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	if ( is_page( 'faq' ) && function_exists( 'partyab_faq_items' ) ) {
		$blocks[] = partyab_faq_schema( partyab_faq_items() );
	}

	$breadcrumbs = partyab_breadcrumb_items();

	if ( $breadcrumbs ) {
		$blocks[] = partyab_breadcrumb_schema( $breadcrumbs );
	}

	foreach ( $blocks as $block ) {
		$json = (string) wp_json_encode( $block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'partyab_seo_jsonld', 20 );

/**
 * زنجیره مسیر صفحه جاری.
 *
 * @return array<int, array{name:string,url:string}>
 */
function partyab_breadcrumb_items(): array {
	$items = array(
		array(
			'name' => 'خانه',
			'url'  => home_url( '/' ),
		),
	);

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$items[] = array( 'name' => 'فروشگاه', 'url' => (string) wc_get_page_permalink( 'shop' ) );

		return $items;
	}

	if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$items[] = array( 'name' => (string) $term->name, 'url' => (string) get_term_link( $term ) );
		}

		return $items;
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );

		if ( $product ) {
			$terms = get_the_terms( (int) $product->get_id(), 'product_cat' );

			if ( is_array( $terms ) && $terms ) {
				$items[] = array( 'name' => (string) $terms[0]->name, 'url' => (string) get_term_link( $terms[0] ) );
			}

			$items[] = array( 'name' => $product->get_name(), 'url' => (string) get_permalink( (int) $product->get_id() ) );
		}

		return $items;
	}

	if ( is_singular() ) {
		$items[] = array( 'name' => (string) get_the_title( get_queried_object_id() ), 'url' => (string) get_permalink( get_queried_object_id() ) );
	}

	return $items;
}

/**
 * نام برند قطعه برای اسکیما.
 *
 * @param int $product_id شناسه محصول.
 * @return string
 */
function partyab_product_brand_name( int $product_id ): string {
	$terms = get_the_terms( $product_id, 'pb_brand' );

	return is_array( $terms ) && $terms ? (string) $terms[0]->name : '';
}
