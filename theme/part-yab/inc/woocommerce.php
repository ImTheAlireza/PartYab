<?php
/**
 * تنظیمات فروشگاه (حلقه، آرشیو، اجزای پیش‌فرض).
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * تعداد ستون حلقه بر اساس بریک‌پوینت قفل‌شده.
 *
 * @return int
 */
function partyab_loop_columns(): int {
	return 4;
}
add_filter( 'loop_shop_columns', 'partyab_loop_columns', 20 );

/**
 * تعداد محصول در هر صفحه آرشیو.
 *
 * @return int
 */
function partyab_products_per_page(): int {
	return 12;
}
add_filter( 'loop_shop_per_page', 'partyab_products_per_page', 20 );

/**
 * ترتیب پیش‌فرض: موجودی‌دارها اول، سپس پرفروش‌ها.
 *
 * @param array $args آرگومان‌های کوئری.
 * @return array
 */
function partyab_archive_order( array $args ): array {
	if ( isset( $args['orderby'] ) && '' !== $args['orderby'] ) {
		return $args;
	}

	$args['orderby']  = 'meta_value';
	$args['meta_key'] = '_stock_status';
	$args['order']    = 'ASC';

	return $args;
}

/**
 * حذف پیام «نتیجه‌ای پیدا نشد» پیش‌فرض با نسخه راهنما‌دار.
 */
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );

add_action( 'woocommerce_no_products_found', 'partyab_no_products_found' );

/**
 * حالت خالی آرشیو — هدایت به انتخاب خودرو به‌جای بن‌بست.
 */
function partyab_no_products_found(): void {
	echo '<div class="partyab-empty">';
	echo '<p class="partyab-empty__title">محصولی با این فیلترها پیدا نشد.</p>';
	echo '<p>خودروی خود را انتخاب کنید تا قطعات سازگار را ببینید، یا فیلترها را ساده‌تر کنید.</p>';
	echo '<a class="partyab-btn" href="' . esc_url( home_url( '/my-car' ) ) . '">انتخاب خودروی من</a>';
	echo '</div>';
}

/**
 * حذف تب‌های اضافه محصول در نسخه ۱؟ نه — فقط حذف تب «نظرات» تا فاز ۹.
 * با یک فیلتر قابل برگشت.
 */
add_filter( 'woocommerce_product_tabs', 'partyab_product_tabs', 20 );

/**
 * ترتیب و حذف تب‌های محصول.
 *
 * @param array $tabs تب‌ها.
 * @return array
 */
function partyab_product_tabs( array $tabs ): array {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = 'مشخصات و توضیحات';
		$tabs['description']['priority'] = 10;
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = 'جدول مشخصات';
		$tabs['additional_information']['priority'] = 15;
	}

	return $tabs;
}

/* ============================================================
 * چیدمان فروشگاه: سایدبار فیلتر + استایل ووکامرس
 * ============================================================ */

/**
 * بارگذاری استایل اختصاصی ووکامرس (جایگزین رنگ پیش‌فرض بنفش ووکامرس).
 */
function partyab_enqueue_woo_style(): void {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return;
	}

	if ( ! ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		return;
	}

	wp_enqueue_style(
		'partyab-woo',
		PARTYAB_URI . '/assets/css/woo.css',
		array( 'partyab-base', 'partyab-layout' ),
		partyab_asset_version( 'assets/css/woo.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'partyab_enqueue_woo_style', 25 );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'partyab_shop_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'partyab_shop_wrapper_end', 10 );

/**
 * آیا این صفحه، آرشیو محصولات است (نه صفحه تک محصول)؟
 */
function partyab_is_product_archive(): bool {
	return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
}

/**
 * باز کردن قالب فروشگاه + سایدبار فیلتر در آرشیوها.
 */
function partyab_shop_wrapper_start(): void {
	echo '<div class="pb-container">';

	if ( partyab_is_product_archive() ) {
		echo '<div class="pb-shop">';
		partyab_shop_sidebar();
		echo '<div class="pb-shop__main">';
	}
}

/**
 * بستن قالب فروشگاه.
 */
function partyab_shop_wrapper_end(): void {
	if ( partyab_is_product_archive() ) {
		echo '</div></div>';
	}

	echo '</div>';
}

/**
 * سایدبار فیلتر آرشیو: خودرو، دسته، قیمت، موجودی.
 */
function partyab_shop_sidebar(): void {
	$in_stock = isset( $_GET['pb_in_stock'] ) ? '1' === (string) $_GET['pb_in_stock'] : false; // phpcs:ignore WordPress.Security.NonceVerification
	$min      = isset( $_GET['min_price'] ) ? (int) $_GET['min_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$max      = isset( $_GET['max_price'] ) ? (int) $_GET['max_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$base     = partyab_is_product_archive() && is_shop() ? wc_get_page_permalink( 'shop' ) : get_term_link( get_queried_object() );

	if ( is_wp_error( $base ) ) {
		$base = home_url( '/shop' );
	}

	echo '<aside class="pb-shop__side">';

	// ۱) خودروی من.
	echo '<div class="pb-side-card pb-side-card--car">';
	echo '<h3 class="pb-side-card__title">خودروی من</h3>';

	if ( function_exists( 'partyab_has_car' ) && partyab_has_car() ) {
		$car = partyab_current_car();
		$label = trim( ( $car['brand'] ?? '' ) . ' ' . ( $car['model'] ?? '' ) . ' ' . ( $car['year'] ?? '' ) );
		echo '<p class="pb-side-car">' . esc_html( $label ) . '</p>';
		echo '<a class="pb-side-link" href="' . esc_url( add_query_arg( 'pb_car_clear', '1', $base ) ) . '">پاک کردن خودرو</a> ';
	} else {
		echo '<p class="pb-side-car pb-side-car--empty">هنوز خودرویی انتخاب نشده؛ فقط قطعات سازگار را ببین.</p>';
	}

	echo '<a class="pb-btn pb-btn--sm" href="' . esc_url( home_url( '/my-car' ) ) . '">انتخاب خودرو</a>';
	echo '</div>';

	// ۲) دسته‌ها.
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
	)	);

	if ( ! is_wp_error( $terms ) && $terms ) {
		$current = is_product_taxonomy() ? get_queried_object_id() : 0;

		echo '<div class="pb-side-card">';
		echo '<h3 class="pb-side-card__title">دسته‌بندی</h3>';
		echo '<ul class="pb-side-list">';

		foreach ( $terms as $term ) {
			if ( 'uncategorized' === $term->slug ) {
				continue;
			}

			printf(
				'<li class="%s"><a href="%s">%s <span>%s</span></a></li>',
				(int) $current === (int) $term->term_id ? 'is-active' : '',
				esc_url( (string) get_term_link( $term ) ),
				esc_html( $term->name ),
				esc_html( number_format_i18n( $term->count ) )
			);
		}

		echo '</ul></div>';
	}

	// ۳) قیمت.
	echo '<div class="pb-side-card">';
	echo '<h3 class="pb-side-card__title">محدوده قیمت (تومان)</h3>';
	echo '<form class="pb-side-price" method="get" action="' . esc_url( $base ) . '">';
	echo '<input type="number" name="min_price" placeholder="از" min="0" step="10000" value="' . esc_attr( $min ? (string) $min : '' ) . '">';
	echo '<input type="number" name="max_price" placeholder="تا" min="0" step="10000" value="' . esc_attr( $max ? (string) $max : '' ) . '">';
	echo '<button type="submit" class="pb-btn pb-btn--sm">اعمال</button>';
	echo '</form></div>';

	// ۴) موجودی.
	echo '<div class="pb-side-card">';
	echo '<h3 class="pb-side-card__title">موجودی</h3>';
	printf(
		'<a class="pb-side-toggle %s" href="%s">%s فقط کالاهای موجود</a>',
		$in_stock ? 'is-active' : '',
		esc_url( $in_stock ? $base : add_query_arg( 'pb_in_stock', '1', $base ) ),
		$in_stock ? '✓' : '○'
	);
	echo '</div>';

	echo '</aside>';
}

/**
 * اعمال فیلتر «فقط موجود» روی کوئری آرشیو.
 *
 * @param WP_Query $query کوئری.
 */
function partyab_filter_in_stock( $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( empty( $_GET['pb_in_stock'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	if ( ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) ) {
		return;
	}

	$meta = (array) $query->get( 'meta_query' );
	$meta[] = array(
		'key'   => '_stock_status',
		'value' => 'instock',
	);

	$query->set( 'meta_query', $meta );
}
add_action( 'pre_get_posts', 'partyab_filter_in_stock', 20 );

/**
 * در صفحه محصول: اگر خودرو انتخاب نشده، دعوت به انتخاب خودرو.
 */
function partyab_single_car_prompt(): void {
	if ( ! function_exists( 'partyab_has_car' ) || partyab_has_car() ) {
		return;
	}

	echo '<div class="pb-car-prompt">';
	echo '<strong>این قطعه به خودروی شما می‌خورد؟</strong>';
	echo '<span>خودرویت را انتخاب کن تا سازگاری همین قطعه را ببینی.</span>';
	echo '<a href="' . esc_url( home_url( '/my-car' ) ) . '">انتخاب خودرو →</a>';
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'partyab_single_car_prompt', 4 );
