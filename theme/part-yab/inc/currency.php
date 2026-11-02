<?php
/**
 * نمایش واحد پول به تومان.
 *
 * تصمیم D15: ارز ووکامرس روی IRR است ولی عدد ذخیره‌شده «تومان» است،
 * پس فقط نماد عوض می‌شود و هیچ محاسبه‌ای دست‌کاری نمی‌شود.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_currency_symbol', 'partyab_currency_symbol', 10, 2 );

/**
 * جایگزینی نماد ریال با تومان.
 *
 * @param string $symbol نماد فعلی.
 * @param string $currency کد ارز.
 * @return string
 */
function partyab_currency_symbol( $symbol, $currency ) {
	if ( in_array( $currency, array( 'IRR', 'IRT' ), true ) ) {
		return 'تومان';
	}

	return $symbol;
}

add_filter( 'woocommerce_price_format', 'partyab_price_format', 10, 2 );

/**
 * قالب قیمت: «۸۵۰,۰۰۰ تومان» با فاصله نشکن.
 *
 * @param string $format قالب فعلی.
 * @param string $currency_position جایگاه ارز.
 * @return string
 */
function partyab_price_format( $format, $currency_position ) {
	if ( 'right_space' === $currency_position ) {
		return '%2$s&nbsp;%1$s';
	}

	return $format;
}
