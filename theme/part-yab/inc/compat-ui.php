<?php
/**
 * رابط کاربری سازگاری: باکس «خودروی من»، بج سازگاری، فیلتر آرشیو.
 *
 * این فایل فقط زمانی اثر دارد که افزونه PartYab Compatibility فعال باشد.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * خودروی انتخاب‌شده کاربر (از کوکی).
 *
 * @return array{brand:string,model:string,year:int}
 */
function partyab_current_car(): array {
	static $car = null;

	if ( null !== $car ) {
		return $car;
	}

	$empty = array( 'brand' => '', 'model' => '', 'year' => 0 );

	if ( empty( $_COOKIE['partyab_car'] ) ) {
		return $car = $empty;
	}

	$raw   = rawurldecode( wp_unslash( $_COOKIE['partyab_car'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$parts = explode( '|', $raw );

	if ( count( $parts ) < 3 ) {
		return $car = $empty;
	}

	$car = array(
		'brand' => sanitize_text_field( $parts[0] ),
		'model' => sanitize_text_field( $parts[1] ),
		'year'  => absint( $parts[2] ),
	);

	return $car;
}

/**
 * آیا خودرویی انتخاب شده است؟
 *
 * @return bool
 */
function partyab_has_car(): bool {
	$car = partyab_current_car();

	return '' !== $car['brand'];
}

/**
 * ذخیره/پاک کردن خودرو از روی پارامترهای URL.
 */
add_action( 'init', 'partyab_maybe_save_car' );

/**
 * پردازش فرم انتخاب خودرو (بدون جاوااسکریپت هم کار می‌کند).
 */
/**
 * تبدیل ارقام فارسی/عربی به لاتین (ورودی فرم را فارسی می‌نویسند).
 *
 * @param string $text متن.
 * @return string
 */
function partyab_normalize_digits( string $text ): string {
	return str_replace(
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		$text
	);
}

function partyab_maybe_save_car(): void {
	if ( is_admin() ) {
		return;
	}

	// پاک کردن.
	if ( isset( $_GET['pb_car_clear'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		setcookie( 'partyab_car', '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE['partyab_car'] = '';

		wp_safe_redirect( remove_query_arg( 'pb_car_clear' ) );
		exit;
	}

	if ( empty( $_GET['pb_car_brand'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$brand = sanitize_text_field( wp_unslash( $_GET['pb_car_brand'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$model = isset( $_GET['pb_car_model'] ) ? sanitize_text_field( wp_unslash( $_GET['pb_car_model'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$year  = isset( $_GET['pb_car_year'] ) ? absint( partyab_normalize_digits( (string) wp_unslash( $_GET['pb_car_year'] ) ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification

	$value = rawurlencode( $brand . '|' . $model . '|' . $year );

	setcookie( 'partyab_car', $value, time() + MONTH_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
	$_COOKIE['partyab_car'] = $value;

	wp_safe_redirect( remove_query_arg( array( 'pb_car_brand', 'pb_car_model', 'pb_car_year' ) ) );
	exit;
}

/**
 * داده برند/مدل برای فیلتر سمت مرورگر.
 *
 * @return array<string,string[]>
 */
function partyab_cars_map(): array {
	$map = array();

	// مسیر اصلی: درخت تاکسونومی pb_car (همان چیزی که فیلترها استفاده می‌کنند).
	if ( function_exists( 'partyab_car_tree' ) ) {
		foreach ( partyab_car_tree() as $node ) {
			$models = array();

			foreach ( $node['models'] as $model ) {
				$models[] = $model->name;
			}

			if ( $models ) {
				$map[ $node['term']->name ] = $models;
			}
		}

		if ( $map ) {
			return $map;
		}
	}

	// مسیر پشتیبان: جدول سازگاری.
	if ( ! function_exists( 'partyab_compat_brands' ) ) {
		return $map;
	}

	foreach ( partyab_compat_brands() as $row ) {
		$models = function_exists( 'partyab_compat_models' ) ? partyab_compat_models( $row->car_brand ) : array();

		if ( $models ) {
			$map[ $row->car_brand ] = $models;
		}
	}

	return $map;
}

/**
 * شورت‌کد باکس انتخاب خودرو: [partyab_my_car]
 *
 * @return string
 */
function partyab_car_selector_shortcode(): string {
	$car = partyab_current_car();
	$map = partyab_cars_map();

	if ( empty( $map ) ) {
		return '<div class="pb-note">داده سازگاری خودرو هنوز ثبت نشده است. از پیشخوان ← سازگاری، فایل CSV را ایمپورت کن.</div>';
	}

	ob_start();
	?>
	<form class="pb-car" method="get" action="">
		<div class="pb-car__head">
			<?php if ( partyab_has_car() ) : ?>
				<p class="pb-car__current">
					خودروی شما: <strong><?php echo esc_html( $car['brand'] . ' ' . $car['model'] ); ?></strong>
					<?php if ( $car['year'] ) : ?>
						<span>(مدل <?php echo esc_html( (string) $car['year'] ); ?>)</span>
					<?php endif; ?>
				</p>
			<?php else : ?>
				<p class="pb-car__current">خودرویت را انتخاب کن تا فقط قطعات سازگار را ببینی.</p>
			<?php endif; ?>
		</div>

		<div class="pb-car__fields">
			<label class="pb-field">
				<span>برند خودرو</span>
				<select name="pb_car_brand" data-pb-brand required>
					<option value="">انتخاب کن…</option>
					<?php foreach ( array_keys( $map ) as $brand ) : ?>
						<option value="<?php echo esc_attr( $brand ); ?>" <?php selected( $car['brand'], $brand ); ?>><?php echo esc_html( $brand ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<label class="pb-field">
				<span>مدل</span>
				<?php
				$current_brand  = (string) ( $car['brand'] ?? '' );
				$current_models = ( '' !== $current_brand && isset( $map[ $current_brand ] ) ) ? (array) $map[ $current_brand ] : array();
				?>
				<select name="pb_car_model" data-pb-model required>
					<option value=""><?php echo $current_models ? 'انتخاب کن…' : 'اول برند را انتخاب کن'; ?></option>
					<?php foreach ( $current_models as $model_name ) : ?>
						<option value="<?php echo esc_attr( $model_name ); ?>" <?php selected( $car['model'] ?? '', $model_name ); ?>><?php echo esc_html( $model_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<label class="pb-field">
				<span>سال ساخت</span>
				<input type="text" name="pb_car_year" inputmode="numeric" pattern="[۰-۹0-9]{4}" maxlength="4"
					value="<?php echo esc_attr( partyab_fa_digits( (string) ( $car['year'] ?: '' ) ) ); ?>" placeholder="مثلاً ۱۳۹۵">
			</label>

			<button type="submit" class="pb-btn">ذخیره خودرو</button>

			<?php if ( partyab_has_car() ) : ?>
				<a class="pb-car__clear" href="<?php echo esc_url( add_query_arg( 'pb_car_clear', 1 ) ); ?>">پاک کردن</a>
			<?php endif; ?>
		</div>
	</form>
	<?php

	return (string) ob_get_clean();
}

add_shortcode( 'partyab_my_car', 'partyab_car_selector_shortcode' );

/**
 * نشان سازگاری یک محصول با خودروی انتخاب‌شده.
 *
 * @param int $product_id شناسه محصول.
 * @return string HTML یا رشته خالی.
 */
function partyab_compat_badge_html( int $product_id ): string {
	if ( ! partyab_has_car() || ! function_exists( 'partyab_compat_status' ) ) {
		return '';
	}

	$car    = partyab_current_car();
	$status = partyab_compat_status( $product_id, array(
		'brand' => $car['brand'],
		'model' => $car['model'],
		'year'  => $car['year'],
	) );

	$labels = array(
		'yes'     => '✅ سازگار با ' . $car['brand'] . ' ' . $car['model'],
		'no'      => '✕ با خودروی شما سازگار نیست',
		'unknown' => '؟ سازگاری این قطعه ثبت نشده',
	);

	return sprintf(
		'<span class="pb-badge pb-badge--%s">%s</span>',
		esc_attr( $status ),
		esc_html( $labels[ $status ] ?? '' )
	);
}

/**
 * نمایش بج در حلقه محصولات.
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'partyab_print_loop_badge', 6 );

/**
 * چاپ بج در آرشیو.
 */
function partyab_print_loop_badge(): void {
	global $product;

	if ( $product instanceof WC_Product ) {
		echo partyab_compat_badge_html( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

add_action( 'woocommerce_single_product_summary', 'partyab_print_single_badge', 6 );

/**
 * چاپ بج در صفحه محصول.
 */
function partyab_print_single_badge(): void {
	global $product;

	if ( $product instanceof WC_Product ) {
		echo '<div class="pb-single-badge">' . partyab_compat_badge_html( $product->get_id() ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * نوار باریک بالای سایت: خودروی فعلی + لینک تغییر.
 */
add_action( 'wp_body_open', 'partyab_car_bar' );

/**
 * نمایش نوار خودرو.
 */
function partyab_car_bar(): void {
	if ( ! partyab_has_car() || is_admin() ) {
		return;
	}

	$car = partyab_current_car();
	?>
	<div class="pb-carbar">
		<div class="pb-container pb-carbar__inner">
			<span>قطعات سازگار با <strong><?php echo esc_html( $car['brand'] . ' ' . $car['model'] ); ?></strong></span>
			<a href="<?php echo esc_url( add_query_arg( 'pb_car_clear', 1 ) ); ?>">تغییر خودرو</a>
		</div>
	</div>
	<?php
}

/**
 * فیلتر آرشیو فروشگاه بر اساس خودروی انتخاب‌شده.
 */
add_action( 'pre_get_posts', 'partyab_filter_shop_by_car' );

/**
 * محدود کردن کوئری آرشیو به قطعات سازگار.
 *
 * @param WP_Query $query کوئری.
 */
function partyab_filter_shop_by_car( $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! partyab_has_car() ) {
		return;
	}

	if ( ! ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) ) {
		return;
	}

	$car = partyab_current_car();

	// مسیر اصلی: ترم خودرو در تاکسونومی pb_car.
	if ( function_exists( 'partyab_find_car_term' ) ) {
		$term = partyab_find_car_term( (string) $car['brand'], (string) $car['model'] );

		if ( $term ) {
			$tax_query   = (array) $query->get( 'tax_query' );
			$tax_query[] = array(
				'taxonomy' => 'pb_car',
				'field'    => 'term_id',
				'terms'    => array( (int) $term->term_id ),
			);

			$query->set( 'tax_query', $tax_query );
			return;
		}
	}

	// مسیر پشتیبان: جدول سازگاری CSV.
	$ids = partyab_compat_product_ids( array(
		'brand' => $car['brand'],
		'model' => $car['model'],
		'year'  => $car['year'],
	) );

	if ( empty( $ids ) ) {
		return;
	}

	$query->set( 'post__in', $ids );
	$query->set( 'orderby', 'post__in' );
}

add_action( 'woocommerce_before_shop_loop', 'partyab_car_filter_notice', 5 );

/**
 * اطلاع‌رسانی فیلتر فعال بالای آرشیو.
 */
function partyab_car_filter_notice(): void {
	if ( ! partyab_has_car() ) {
		return;
	}

	$car = partyab_current_car();

	printf(
		'<div class="pb-note pb-note--filter">فقط قطعات سازگار با <strong>%s %s</strong> نمایش داده می‌شود. <a href="%s">نمایش همه</a></div>',
		esc_html( $car['brand'] ),
		esc_html( $car['model'] ),
		esc_url( add_query_arg( 'pb_car_clear', 1 ) )
	);
}
