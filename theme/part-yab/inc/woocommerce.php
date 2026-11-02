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
