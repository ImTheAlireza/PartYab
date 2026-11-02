<?php
/**
 * تنظیمات چیدمان: منوهای جایگزین، عنوان صفحه، کلاس‌های body.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * منوی جایگزین وقتی منویی به جایگاه هدر وصل نشده است.
 */
function partyab_nav_fallback(): void {
	$links = array(
		home_url( '/' )                  => 'خانه',
		home_url( '/shop' )              => 'فروشگاه',
		home_url( '/my-car' )            => 'خودروی من',
		home_url( '/contact' )           => 'تماس با ما',
	);

	echo '<ul class="pb-nav__menu">';

	foreach ( $links as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}

	// اگر ووکامرس هست، دسته‌های اصلی را هم بیاور.
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 4,
		) );

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( (string) get_term_link( $term ) ),
					esc_html( $term->name )
				);
			}
		}
	}

	echo '</ul>';
}

/**
 * منوی جایگزین فوتر.
 */
function partyab_footer_fallback(): void {
	$links = array(
		home_url( '/' )        => 'خانه',
		home_url( '/shop' )    => 'فروشگاه',
		home_url( '/my-car' )  => 'خودروی من',
		home_url( '/contact' ) => 'تماس با ما',
	);

	echo '<ul class="pb-footer__menu">';

	foreach ( $links as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}

	echo '</ul>';
}

/**
 * در صفحه اصلی، عنوان صفحه تکراری نمایش داده نشود.
 *
 * @param bool $show وضعیت پیش‌فرض Hello Elementor.
 * @return bool
 */
function partyab_hide_page_title( $show ) {
	if ( is_front_page() || ( function_exists( 'is_shop' ) && is_shop() ) ) {
		return false;
	}

	return $show;
}

add_filter( 'hello_elementor_page_title', 'partyab_hide_page_title' );

add_filter( 'body_class', 'partyab_body_class' );

/**
 * کلاس‌های کمکی روی body.
 *
 * @param array $classes کلاس‌ها.
 * @return array
 */
function partyab_body_class( array $classes ): array {
	$classes[] = 'partyab';

	if ( function_exists( 'partyab_has_car' ) && partyab_has_car() ) {
		$classes[] = 'has-car';
	}

	return $classes;
}

add_filter( 'excerpt_length', 'partyab_excerpt_length' );

/**
 * طول خلاصه.
 *
 * @return int
 */
function partyab_excerpt_length(): int {
	return 22;
}
