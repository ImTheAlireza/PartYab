<?php
/**
 * پیشنهاد زنده جستجو (AJAX) و گزارش جستجوهای بی‌نتیجه در پیشخوان.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_partyab_suggest', 'partyab_suggest_ajax' );
add_action( 'wp_ajax_nopriv_partyab_suggest', 'partyab_suggest_ajax' );

/**
 * خروجی JSON پیشنهادها.
 */
function partyab_suggest_ajax(): void {
	check_ajax_referer( 'partyab_nonce', 'nonce' );

	$term = isset( $_GET['q'] ) ? partyab_search_normalize( sanitize_text_field( wp_unslash( $_GET['q'] ) ) ) : '';

	if ( partyab_text_len( $term ) < 2 || ! function_exists( 'wc_get_product' ) ) {
		wp_send_json_success( array( 'items' => array(), 'categories' => array(), 'total' => 0 ) );
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => 6,
			's'                   => $term,
			'partyab_suggest'     => true,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
		)
	);

	$items = array();

	foreach ( $query->posts as $post ) {
		$product = wc_get_product( $post->ID );

		if ( ! $product ) {
			continue;
		}

		$items[] = array(
			'title'  => html_entity_decode( $product->get_name(), ENT_QUOTES, 'UTF-8' ),
			'url'    => (string) get_permalink( $post->ID ),
			'price'  => wp_strip_all_tags( $product->get_price_html() ),
			'sku'    => (string) $product->get_sku(),
			'image'  => (string) wp_get_attachment_image_url( (int) $product->get_image_id(), 'thumbnail' ),
			'stock'  => $product->is_in_stock(),
			'badge'  => function_exists( 'partyab_compat_badge_html' ) ? wp_strip_all_tags( partyab_compat_badge_html( (int) $post->ID ) ) : '',
		);
	}

	$categories = array();

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 3,
			'name__like' => $term,
		)
	);

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term_object ) {
			$categories[] = array(
				'name' => $term_object->name,
				'url'  => (string) get_term_link( $term_object ),
			);
		}
	}

	wp_send_json_success(
		array(
			'items'      => $items,
			'categories' => $categories,
			'total'      => (int) $query->found_posts,
			'term'       => $term,
		)
	);
}

add_action( 'admin_menu', 'partyab_zero_search_menu' );

/**
 * منوی گزارش جستجوهای بی‌نتیجه.
 */
function partyab_zero_search_menu(): void {
	add_submenu_page(
		'woocommerce',
		'جستجوهای بی‌نتیجه',
		'جستجوهای بی‌نتیجه',
		'manage_woocommerce',
		'partyab-zero-search',
		'partyab_zero_search_page'
	);
}

/**
 * صفحه گزارش: چه چیزی را مردم جستجو کردند و پیدا نکردند.
 */
function partyab_zero_search_page(): void {
	$log = get_option( 'partyab_zero_searches', array() );
	$log = is_array( $log ) ? $log : array();

	arsort( $log );

	echo '<div class="wrap"><h1>جستجوهای بی‌نتیجه</h1>';

	if ( ! $log ) {
		echo '<p>هنوز جستجوی بی‌نتیجه‌ای ثبت نشده است. هر جستجویی که محصولی پیدا نکند، این‌جا فهرست می‌شود تا برایش محصول اضافه کنی.</p></div>';

		return;
	}

	echo '<p>این‌ها عبارت‌هایی هستند که کاربران جستجو کردند و نتیجه‌ای نگرفتند — به ترتیب تکرار. هر کدام می‌تواند یک قطعه کم‌شده در انبار باشد.</p>';
	echo '<table class="widefat striped"><thead><tr><th>عبارت</th><th>تعداد جستجو</th><th>کنش</th></tr></thead><tbody>';

	foreach ( $log as $term => $count ) {
		printf(
			'<tr><td><strong>%s</strong></td><td>%s</td><td><a class="button button-small" href="%s">افزودن محصول</a></td></tr>',
			esc_html( $term ),
			esc_html( number_format_i18n( (int) $count ) ),
			esc_url( admin_url( 'post-new.php?post_type=product' ) )
		);
	}

	echo '</tbody></table>';
	printf(
		'<p><a href="%s" class="button">پاک کردن گزارش</a></p></div>',
		esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=partyab_zero_clear' ), 'partyab_zero_clear' ) )
	);
}

add_action( 'admin_post_partyab_zero_clear', 'partyab_zero_clear_handler' );

/**
 * پاک کردن گزارش.
 */
function partyab_zero_clear_handler(): void {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'دسترسی ندارید.' );
	}

	check_admin_referer( 'partyab_zero_clear' );
	delete_option( 'partyab_zero_searches' );

	wp_safe_redirect( admin_url( 'admin.php?page=partyab-zero-search' ) );
	exit;
}
