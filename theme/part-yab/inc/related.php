<?php
/**
 * مرتبط‌ها: محصولات مشابه، مکمل خرید و نوارهای محصول.
 *
 * قاعده: «مرتبط» در این قالب یعنی «همان خودرو» اول، دسته دوم.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * نگاشت دسته → دسته‌های مکمل (برای «مکمل خرید»).
 *
 * @return array<string, array<int, string>>
 */
function partyab_complement_map(): array {
	return array(
		'brake-system' => array( 'filters', 'suspension' ),
		'filters'      => array( 'brake-system', 'electrical' ),
		'electrical'   => array( 'filters', 'body-glass' ),
		'suspension'   => array( 'brake-system', 'body-glass' ),
		'body-glass'   => array( 'electrical', 'filters' ),
	);
}

/**
 * دسته‌های مکمل یک محصول بر اساس اسلاگ دسته‌هایش.
 *
 * @param array<int, string> $slugs اسلاگ دسته‌ها.
 * @return array<int, string>
 */
function partyab_complement_slugs( array $slugs ): array {
	$map = partyab_complement_map();
	$out = array();

	foreach ( $slugs as $slug ) {
		foreach ( $map[ $slug ] ?? array() as $candidate ) {
			if ( ! in_array( $candidate, $slugs, true ) && ! in_array( $candidate, $out, true ) ) {
				$out[] = $candidate;
			}
		}
	}

	return $out;
}

/**
 * شناسه محصولات هم‌خودرو.
 *
 * @param int $product_id شناسه محصول.
 * @param int $limit      تعداد.
 * @return array<int, int>
 */
function partyab_same_car_ids( int $product_id, int $limit = 4 ): array {
	$terms = wp_get_post_terms( $product_id, 'pb_car', array( 'fields' => 'ids' ) );

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'post__not_in'   => array( $product_id ),
			'orderby'        => 'rand', // phpcs:ignore WordPress.DB.SlowDBQuery
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'pb_car',
					'field'    => 'term_id',
					'terms'    => $terms,
				),
			),
		)
	);

	return array_map( 'absint', (array) $ids );
}

/**
 * شناسه محصولات مرتبط: هم‌خودرو → هم‌دسته → پرکردن با محصولات تصادفی.
 *
 * @param int $product_id شناسه محصول.
 * @param int $limit      تعداد.
 * @return array<int, int>
 */
function partyab_related_ids( int $product_id, int $limit = 4 ): array {
	$ids = partyab_same_car_ids( $product_id, $limit );

	if ( count( $ids ) < $limit ) {
		$cats = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		if ( ! is_wp_error( $cats ) && $cats ) {
			$same_cat = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => $limit,
					'fields'         => 'ids',
					'post__not_in'   => array_merge( array( $product_id ), $ids ),
					'orderby'        => 'rand', // phpcs:ignore WordPress.DB.SlowDBQuery
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $cats,
						),
					),
				)
			);

			$ids = array_merge( $ids, array_map( 'absint', (array) $same_cat ) );
		}
	}

	$ids = array_values( array_unique( array_filter( $ids ) ) );

	return array_slice( $ids, 0, $limit );
}

/**
 * جایگزینی محصولات مرتبط پیش‌فرض ووکامرس با نسخه «هم‌خودرو اول».
 *
 * @param array<int, int> $related شناسه‌های پیش‌فرض.
 * @return array<int, int>
 */
function partyab_filter_related_ids( $related ): array {
	$product_id = get_the_ID();

	if ( ! $product_id ) {
		return (array) $related;
	}

	$ids = partyab_related_ids( (int) $product_id, 4 );

	return $ids ? $ids : (array) $related;
}
add_filter( 'woocommerce_related_products', 'partyab_filter_related_ids' );

/**
 * شناسه محصولات «مکمل خرید» بر اساس دسته‌های مکمل.
 *
 * @param int $product_id شناسه محصول.
 * @param int $limit      تعداد.
 * @return array<int, int>
 */
function partyab_complement_ids( int $product_id, int $limit = 4 ): array {
	$terms = wp_get_post_terms( $product_id, 'product_cat' );

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$slugs = array();

	foreach ( $terms as $term ) {
		$slugs[] = $term->slug;
	}

	$complement = partyab_complement_slugs( $slugs );

	if ( ! $complement ) {
		return array();
	}

	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'post__not_in'   => array( $product_id ),
			'orderby'        => 'rand', // phpcs:ignore WordPress.DB.SlowDBQuery
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $complement,
				),
			),
		)
	);

	return array_map( 'absint', (array) $ids );
}

/**
 * رندر یک نوار محصول با کارت‌های استاندارد ووکامرس.
 *
 * @param array<int, int> $ids   شناسه محصولات.
 * @param string          $title عنوان.
 * @param string          $note  زیرنویس.
 * @param string          $mod   کلاس تغییردهنده.
 */
function partyab_render_products_strip( array $ids, string $title, string $note = '', string $mod = '' ): void {
	$ids = array_values( array_filter( array_map( 'absint', $ids ) ) );

	if ( ! $ids || ! function_exists( 'wc_get_template_part' ) ) {
		return;
	}

	printf(
		'<section class="pb-strip %s"><header class="pb-strip__head"><h2>%s</h2>%s</header><ul class="products columns-4 pb-strip__grid">',
		esc_attr( $mod ),
		esc_html( $title ),
		$note ? '<p>' . esc_html( $note ) . '</p>' : ''
	);

	foreach ( $ids as $id ) {
		$post_object = get_post( $id );

		if ( ! $post_object ) {
			continue;
		}

		$GLOBALS['post'] = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post_object );
		wc_get_template_part( 'content', 'product' );
	}

	wp_reset_postdata();
	echo '</ul></section>';
}

/**
 * نوار «همین خودرو را دارد» + «مکمل خرید».
 */
function partyab_render_related_strips(): void {
	$product_id = get_the_ID();

	if ( ! $product_id ) {
		return;
	}

	$same_car = partyab_same_car_ids( (int) $product_id, 4 );

	if ( count( $same_car ) >= 2 ) {
		partyab_render_products_strip(
			$same_car,
			'همین خودرو را دارد',
			'قطعاتی که برای همان برند و مدل انتخاب شده‌اند',
			'pb-strip--car'
		);
	}

	$complement = partyab_complement_ids( (int) $product_id, 4 );

	if ( $complement ) {
		partyab_render_products_strip(
			$complement,
			'مکمل خرید',
			'با هم خریداری می‌شوند',
			'pb-strip--complement'
		);
	}
}
add_action( 'woocommerce_after_single_product_summary', 'partyab_render_related_strips', 22 );
