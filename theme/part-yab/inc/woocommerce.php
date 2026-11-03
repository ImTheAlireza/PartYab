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

	if ( ! ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_front_page() || is_home() || is_search() ) ) {
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
function partyab_shop_sidebar( string $base = '' ): void {
	$in_stock = isset( $_GET['pb_in_stock'] ) ? '1' === (string) $_GET['pb_in_stock'] : false; // phpcs:ignore WordPress.Security.NonceVerification
	$min      = isset( $_GET['min_price'] ) ? (int) $_GET['min_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$max      = isset( $_GET['max_price'] ) ? (int) $_GET['max_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	if ( '' === $base ) {
		if ( partyab_is_product_archive() && is_shop() ) {
			$base = wc_get_page_permalink( 'shop' );
		} elseif ( partyab_is_product_archive() ) {
			$term = get_queried_object();
			$base = $term instanceof WP_Term ? (string) get_term_link( $term ) : wc_get_page_permalink( 'shop' );
		} else {
			$base = wc_get_page_permalink( 'shop' );
		}
	}

	if ( ! is_string( $base ) || '' === $base ) {
		$base = wc_get_page_permalink( 'shop' );
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
		echo '<p class="pb-side-car pb-side-car--empty">خودرویت را انتخاب کن تا فقط قطعات سازگار را ببینی.</p>';
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

	// ۳) برند قطعه.
	$brands = function_exists( 'partyab_brand_terms' ) ? partyab_brand_terms() : array();

	if ( $brands ) {
		echo '<div class="pb-side-card">';
		echo '<h3 class="pb-side-card__title">برند قطعه</h3>';
		echo '<ul class="pb-side-list">';

		foreach ( array_slice( $brands, 0, 10 ) as $brand ) {
			printf(
				'<li class="%s"><a href="%s">%s <span>%s</span></a></li>',
				is_tax( 'pb_brand', $brand->term_id ) ? 'is-active' : '',
				esc_url( (string) get_term_link( $brand ) ),
				esc_html( $brand->name ),
				esc_html( number_format_i18n( $brand->count ) )
			);
		}

		echo '</ul></div>';
	}

	// ۴) خودرو: برند و مدل.
	$car_tree = function_exists( 'partyab_car_tree' ) ? partyab_car_tree() : array();

	if ( $car_tree ) {
		echo '<div class="pb-side-card">';
		echo '<h3 class="pb-side-card__title">خودرو</h3>';
		echo '<ul class="pb-side-list pb-side-list--cars">';

		foreach ( $car_tree as $node ) {
			$is_brand_active = is_tax( 'pb_car', $node['term']->term_id );

			printf(
				'<li class="%s"><a href="%s">%s <span>%s</span></a>',
				$is_brand_active ? 'is-active' : '',
				esc_url( (string) get_term_link( $node['term'] ) ),
				esc_html( $node['term']->name ),
				esc_html( number_format_i18n( $node['term']->count ) )
			);

			if ( $is_brand_active && $node['models'] ) {
				echo '<ul class="pb-side-sublist">';

				foreach ( $node['models'] as $model ) {
					printf(
						'<li class="%s"><a href="%s">%s</a></li>',
						is_tax( 'pb_car', $model->term_id ) ? 'is-active' : '',
						esc_url( (string) get_term_link( $model ) ),
						esc_html( $model->name )
					);
				}

				echo '</ul>';
			}

			echo '</li>';
		}

		echo '</ul></div>';
	}

	// ۵) قیمت.
	echo '<div class="pb-side-card">';
	echo '<h3 class="pb-side-card__title">محدوده قیمت (تومان)</h3>';
	echo '<form class="pb-side-price" method="get" action="' . esc_url( $base ) . '">';
	echo '<input type="number" name="min_price" placeholder="از" min="0" step="10000" value="' . esc_attr( $min ? (string) $min : '' ) . '">';
	echo '<input type="number" name="max_price" placeholder="تا" min="0" step="10000" value="' . esc_attr( $max ? (string) $max : '' ) . '">';
	echo '<button type="submit" class="pb-btn pb-btn--sm">اعمال</button>';
	echo '</form></div>';

	// ۶) موجودی.
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

	$is_product_search = $query->is_search() && 'product' === $query->get( 'post_type' );

	if ( ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) || $is_product_search ) ) {
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
add_action( 'woocommerce_single_product_summary', 'partyab_single_car_prompt', 18 );

/* ============================================================
 * جزئیات نمایش: درصد تخفیف، ارقام فارسی، صفحه‌بندی، فیلترهای فعال
 * ============================================================ */

/**
 * تبدیل ارقام لاتین به فارسی.
 *
 * @param string $text متن.
 * @return string
 */
function partyab_fa_digits( $text ) {
	if ( ! is_string( $text ) ) {
		return $text;
	}

	return str_replace(
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
		$text
	);
}

add_filter( 'wc_price', 'partyab_fa_digits', 20 );
add_filter( 'number_format_i18n', 'partyab_fa_digits', 20 );
add_filter( 'woocommerce_get_stock_html', 'partyab_fa_digits', 20 );

/**
 * بج تخفیف: «۲۵٪ تخفیف» به‌جای «تخفیف».
 *
 * @param string     $html    نشان پیش‌فرض.
 * @param WP_Post    $post    نوشته.
 * @param WC_Product $product محصول.
 * @return string
 */
function partyab_sale_percent_badge( $html, $post, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $html;
	}

	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();

	if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
		$percent = (int) round( ( ( $regular - $sale ) / $regular ) * 100 );

		return '<span class="onsale pb-sale">' . esc_html( partyab_fa_digits( (string) $percent ) ) . '٪ تخفیف</span>';
	}

	return $html;
}
add_filter( 'woocommerce_sale_flash', 'partyab_sale_percent_badge', 20, 3 );

/**
 * فلش‌های صفحه‌بندی فارسی شوند (‹ › در قالب نمایش داده نمی‌شد).
 *
 * @param array $args آرگومان‌ها.
 * @return array
 */
function partyab_pagination_args( array $args ): array {
	$args['prev_text'] = 'قبلی';
	$args['next_text'] = 'بعدی';

	return $args;
}
add_filter( 'woocommerce_pagination_args', 'partyab_pagination_args' );

/**
 * محصولات مرتبط: ۴ ستون.
 *
 * @param array $args آرگومان‌ها.
 * @return array
 */
function partyab_related_args( array $args ): array {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'partyab_related_args' );

/**
 * ردیف اعتماد زیر دکمه خرید.
 */
function partyab_product_trust(): void {
	echo '<ul class="pb-product-trust">';
	echo '<li>ضمانت اصالت کالا</li>';
	echo '<li>ارسال سریع</li>';
	echo '<li>مرجوعی ۷ روزه</li>';
	echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'partyab_product_trust', 40 );

/**
 * چیپ فیلترهای فعال بالای شبکه محصولات.
 */
function partyab_active_filter_chips(): void {
	if ( ! partyab_is_product_archive() ) {
		return;
	}

	$chips = array();
	$url   = wc_get_page_permalink( 'shop' );

	if ( is_tax() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$chips[] = array( 'label' => $term->name, 'remove' => $url );
		}
	}

	if ( function_exists( 'partyab_has_car' ) && partyab_has_car() ) {
		$car     = partyab_current_car();
		$chips[] = array(
			'label'  => trim( $car['brand'] . ' ' . $car['model'] ),
			'remove' => add_query_arg( 'pb_car_clear', '1' ),
		);
	}

	if ( ! empty( $_GET['pb_in_stock'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$chips[] = array(
			'label'  => 'فقط موجود',
			'remove' => remove_query_arg( 'pb_in_stock' ),
		);
	}

	$min = isset( $_GET['min_price'] ) ? (int) $_GET['min_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$max = isset( $_GET['max_price'] ) ? (int) $_GET['max_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification

	if ( $min || $max ) {
		$chips[] = array(
			'label'  => 'قیمت: ' . ( $min ? number_format_i18n( $min ) : '۰' ) . ' تا ' . ( $max ? number_format_i18n( $max ) : 'بی‌نهایت' ),
			'remove' => remove_query_arg( array( 'min_price', 'max_price' ) ),
		);
	}

	if ( ! $chips ) {
		return;
	}

	echo '<div class="pb-active-filters">';

	foreach ( $chips as $chip ) {
		printf(
			'<a class="pb-active-filter" href="%s">%s <span aria-hidden="true">×</span></a>',
			esc_url( $chip['remove'] ),
			esc_html( $chip['label'] )
		);
	}

	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'partyab_active_filter_chips', 15 );

/**
 * خط برند و خودروهای سازگار روی کارت محصول.
 */
function partyab_loop_terms_line(): void {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$brands = get_the_terms( $product->get_id(), 'pb_brand' );
	$cars   = get_the_terms( $product->get_id(), 'pb_car' );

	$brand_name = ( $brands && ! is_wp_error( $brands ) ) ? $brands[0]->name : '';
	$car_names  = array();

	if ( $cars && ! is_wp_error( $cars ) ) {
		foreach ( $cars as $car ) {
			// فقط مدل‌ها (فرزند) را بشمار، نه برندها.
			if ( $car->parent ) {
				$car_names[] = $car->name;
			}
		}
	}

	if ( '' === $brand_name && ! $car_names ) {
		return;
	}

	echo '<div class="pb-card-meta">';

	if ( '' !== $brand_name ) {
		printf( '<span class="pb-card-meta__brand">%s</span>', esc_html( $brand_name ) );
	}

	if ( $car_names ) {
		printf(
			'<span class="pb-card-meta__cars">%s</span>',
			esc_html( 'سازگار با ' . implode( '، ', array_slice( $car_names, 0, 2 ) ) . ( count( $car_names ) > 2 ? ' و ' . count( $car_names ) . ' مدل دیگر' : '' ) )
		);
	}

	echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'partyab_loop_terms_line', 4 );

/* ============================================================
 * سبد خرید و تسویه حساب
 * ============================================================ */

/**
 * آستانه ارسال رایگان (تومان).
 */
const PARTYAB_FREE_SHIPPING = 5000000;

/**
 * نوار «چقدر تا ارسال رایگان مانده» بالای جدول سبد.
 */
function partyab_free_shipping_progress(): void {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$subtotal  = (float) WC()->cart->get_subtotal();
	$threshold = (float) PARTYAB_FREE_SHIPPING;

	if ( $subtotal <= 0 ) {
		return;
	}

	$remaining = max( 0.0, $threshold - $subtotal );
	$percent   = min( 100, (int) round( ( $subtotal / $threshold ) * 100 ) );

	echo '<div class="pb-ship">';

	if ( $remaining > 0 ) {
		printf(
			'<p class="pb-ship__text">%s تومان تا <strong>ارسال رایگان</strong> مانده.</p>',
			esc_html( number_format_i18n( $remaining ) )
		);
	} else {
		echo '<p class="pb-ship__text pb-ship__text--done">✓ ارسال این سفارش رایگان است.</p>';
	}

	printf(
		'<span class="pb-ship__bar" role="progressbar" aria-valuenow="%1$d" aria-valuemin="0" aria-valuemax="100"><span style="width:%1$d%%"></span></span>',
		(int) $percent
	);

	echo '</div>';
}
add_action( 'woocommerce_before_cart_table', 'partyab_free_shipping_progress', 5 );

/**
 * دکمه «ادامه خرید» بالای سبد و در صفحه تسویه.
 */
function partyab_continue_shopping(): void {
	$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );

	printf(
		'<p class="pb-continue"><a href="%s">← ادامه خرید</a></p>',
		esc_url( $url )
	);
}
add_action( 'woocommerce_before_cart_table', 'partyab_continue_shopping', 4 );

/**
 * ردیف اعتماد در سبد و تسویه.
 */
function partyab_checkout_trust(): void {
	echo '<ul class="pb-product-trust pb-product-trust--wide">';
	echo '<li>پرداخت امن</li>';
	echo '<li>ضمانت اصالت کالا</li>';
	echo '<li>مرجوعی ۷ روزه</li>';
	echo '<li>پشتیبانی تلفنی</li>';
	echo '</ul>';
}
add_action( 'woocommerce_review_order_after_submit', 'partyab_checkout_trust', 10 );
add_action( 'woocommerce_after_cart_totals', 'partyab_checkout_trust', 10 );

/**
 * حذف فیلدهای غیرضروری تسویه در نسخه ۱ (شرکت، یادداشت سفارش جدا).
 *
 * @param array $fields فیلدها.
 * @return array
 */
function partyab_checkout_fields( array $fields ): array {
	unset( $fields['billing']['billing_company'] );
	unset( $fields['billing']['billing_address_2'] );

	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'یادداشت سفارش (اختیاری)';
		$fields['order']['order_comments']['placeholder'] = 'مثلاً: زنگ واحد ۳ را بزنید';
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'partyab_checkout_fields', 20 );

/**
 * پیام کوتاه زیر دکمه ثبت سفارش.
 */
function partyab_place_order_note(): void {
	echo '<p class="pb-checkout-note">با ثبت سفارش، شرایط فروش و مرجوعی را می‌پذیرید.</p>';
}
add_action( 'woocommerce_review_order_before_submit', 'partyab_place_order_note', 5 );
