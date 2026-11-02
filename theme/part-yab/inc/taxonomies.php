<?php
/**
 * ساختار برند و خودرو.
 *
 * دو تاکسونومی روی محصول:
 *  - pb_brand : برند قطعه (ایساکو، بوش، …) — تخت.
 *  - pb_car   : خودرو، سلسله‌مراتبی؛ سطح اول برند خودرو (پراید)، فرزند مدل (۱۳۱).
 *
 * چرا تاکسونومی و نه فیلد متنی؟ چون فیلتر، آرشیو و شمارش خودکار می‌خواهیم
 * و نقشه سایت/SEO هم روی همین مسیرها ساخته می‌شود: /car/پراید/ و /brand/ایساکو/.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * ثبت تاکسونومی‌ها.
 */
function partyab_register_taxonomies(): void {
	register_taxonomy(
		'pb_brand',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => 'برند قطعه',
				'singular_name' => 'برند قطعه',
				'search_items'  => 'جستجوی برند',
				'all_items'     => 'همه برندها',
				'edit_item'     => 'ویرایش برند',
				'add_new_item'  => 'افزودن برند',
				'menu_name'     => 'برند قطعه',
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'brand',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'pb_car',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => 'خودروهای سازگار',
				'singular_name' => 'خودرو',
				'search_items'  => 'جستجوی خودرو',
				'all_items'     => 'همه خودروها',
				'parent_item'   => 'برند خودرو',
				'edit_item'     => 'ویرایش خودرو',
				'add_new_item'  => 'افزودن خودرو',
				'menu_name'     => 'خودروها',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'car',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'partyab_register_taxonomies', 5 );

/**
 * پیدا کردن ترم خودرو بر اساس نام برند و نام مدل.
 *
 * @param string $brand نام برند خودرو (پراید).
 * @param string $model نام مدل (۱۳۱). خالی = خود برند.
 * @return WP_Term|null
 */
function partyab_find_car_term( string $brand, string $model = '' ): ?WP_Term {
	$parent = get_term_by( 'name', $brand, 'pb_car' );

	if ( ! $parent ) {
		return null;
	}

	if ( '' === $model ) {
		return $parent;
	}

	$child = get_term_by( 'name', $model, 'pb_car' );

	if ( $child && (int) $child->parent === (int) $parent->term_id ) {
		return $child;
	}

	// اگر ترم دقیق نبود، فرزندان برند را بگرد.
	$children = get_terms( array(
		'taxonomy'   => 'pb_car',
		'parent'     => (int) $parent->term_id,
		'hide_empty' => false,
	) );

	if ( ! is_wp_error( $children ) ) {
		foreach ( $children as $term ) {
			if ( $term->name === $model || $term->slug === sanitize_title( $model ) ) {
				return $term;
			}
		}
	}

	return null;
}

/**
 * درخت خودروها: برند → مدل‌ها (برای انتخابگر و فیلترها).
 *
 * @return array<int,array{term:WP_Term,models:WP_Term[]}>
 */
function partyab_car_tree(): array {
	$tree = array();

	$brands = get_terms( array(
		'taxonomy'   => 'pb_car',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $brands ) || ! $brands ) {
		return $tree;
	}

	foreach ( $brands as $brand ) {
		$models = get_terms( array(
			'taxonomy'   => 'pb_car',
			'parent'     => (int) $brand->term_id,
			'hide_empty' => false,
			'orderby'    => 'name',
		) );

		$tree[] = array(
			'term'   => $brand,
			'models' => is_wp_error( $models ) ? array() : $models,
		);
	}

	return $tree;
}

/**
 * برندهای قطعه با تعداد محصول.
 *
 * @return WP_Term[]
 */
function partyab_brand_terms(): array {
	$terms = get_terms( array(
		'taxonomy'   => 'pb_brand',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * نمایش برند و خودروهای سازگار در متای صفحه محصول.
 */
function partyab_product_terms_pills(): void {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$brands = get_the_terms( $product->get_id(), 'pb_brand' );
	$cars   = get_the_terms( $product->get_id(), 'pb_car' );

	if ( ( ! $brands || is_wp_error( $brands ) ) && ( ! $cars || is_wp_error( $cars ) ) ) {
		return;
	}

	echo '<div class="pb-product-terms">';

	if ( $brands && ! is_wp_error( $brands ) ) {
		echo '<span class="pb-product-terms__label">برند قطعه:</span>';
		foreach ( $brands as $brand ) {
			printf(
				'<a class="pb-chip" href="%s">%s</a>',
				esc_url( (string) get_term_link( $brand ) ),
				esc_html( $brand->name )
			);
		}
	}

	if ( $cars && ! is_wp_error( $cars ) ) {
		$labels = array();

		foreach ( $cars as $car ) {
			$label = $car->name;

			if ( $car->parent ) {
				$parent = get_term( (int) $car->parent, 'pb_car' );

				if ( $parent && ! is_wp_error( $parent ) ) {
					$label = $parent->name . ' ' . $car->name;
				}
			}

			$labels[] = sprintf(
				'<a class="pb-chip pb-chip--car" href="%s">%s</a>',
				esc_url( (string) get_term_link( $car ) ),
				esc_html( $label )
			);
		}

		echo '<span class="pb-product-terms__label">سازگار با:</span>';
		echo wp_kses_post( implode( '', array_slice( $labels, 0, 8 ) ) );

		if ( count( $labels ) > 8 ) {
			echo '<span class="pb-chip pb-chip--more">+' . esc_html( (string) ( count( $labels ) - 8 ) ) . '</span>';
		}
	}

	echo '</div>';
}
add_action( 'woocommerce_product_meta_start', 'partyab_product_terms_pills', 5 );

/**
 * ستون برند و خودرو در فهرست محصولات ادمین، با لینک فیلتر.
 *
 * @param array $columns ستون‌ها.
 * @return array
 */
function partyab_admin_product_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'name' === $key ) {
			$new['pb_brand'] = 'برند';
			$new['pb_car']   = 'خودرو';
		}
	}

	return $new;
}
add_filter( 'manage_product_posts_columns', 'partyab_admin_product_columns' );
