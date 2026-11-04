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
		home_url( '/' )         => 'خانه',
		home_url( '/shop' )     => 'فروشگاه',
		home_url( '/my-car' )   => 'خودروی من',
		home_url( '/about' )    => 'درباره ما',
		home_url( '/faq' )      => 'سؤالات متداول',
		home_url( '/returns' )  => 'شرایط مرجوعی',
		home_url( '/contact' )  => 'تماس با ما',
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
	if ( is_front_page() || ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) ) {
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

/**
 * آیکن دسته‌ها برای صفحه اصلی (SVG درون‌خطی، بدون درخواست شبکه).
 *
 * @param int $index شماره دسته.
 * @return string HTML آیکن.
 */
function partyab_category_icon( int $index = 0 ): string {
	$icons = array(
		// دیسک ترمز.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2.4"/><path d="M12 4v3M12 17v3M4 12h3M17 12h3"/></svg>',
		// فیلتر.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M7 4h10l1 5H6z"/><path d="M8 9v11h8V9"/><path d="M10 13h4M10 16h4"/></svg>',
		// باتری.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><rect x="3" y="7" width="18" height="11" rx="2"/><path d="M7 7V5h3v2M14 7V5h3v2"/><path d="M8 12h3l-1.5 3"/><path d="M15 11v4M13 13h4"/></svg>',
		// فنر/کمک فنر.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M12 3v3"/><path d="M8 6h8"/><path d="M9 9h6M9 12h6M9 15h6M9 18h6"/><path d="M12 20v2"/></svg>',
		// چراغ.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 8h7a5 5 0 0 1 0 10H4z"/><path d="M16 9l3-2M16 15l3 2M17 12h4"/></svg>',
		// روغن.
		'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M9 3h6l1 4H8z"/><path d="M8 7h8v12a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2z"/><path d="M10 13h4"/></svg>',
	);

	return $icons[ $index % count( $icons ) ];
}
