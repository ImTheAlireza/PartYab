<?php
/**
 * فرم تماس سبک: بدون افزونه، با نانس، هانی‌پات و ارسال با wp_mail.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * طول متن یونیکد با بازگشت امن اگر mbstring نبود.
 *
 * @param string $text متن.
 * @return int
 */
function partyab_text_len( string $text ): int {
	if ( function_exists( 'mb_strlen' ) ) {
		return (int) mb_strlen( $text );
	}

	return (int) preg_match_all( '/./u', $text );
}

/**
 * اعتبارسنجی داده فرم تماس.
 *
 * @param array<string, mixed> $data داده خام.
 * @return array<string, string> پیام خطا به تفکیک فیلد (خالی یعنی معتبر).
 */
function partyab_contact_validate( array $data ): array {
	$errors = array();
	$name   = trim( (string) ( $data['name'] ?? '' ) );
	$phone  = trim( (string) ( $data['phone'] ?? '' ) );
	$body   = trim( (string) ( $data['message'] ?? '' ) );

	if ( partyab_text_len( $name ) < 3 ) {
		$errors['name'] = 'نام را کامل بنویس (حداقل ۳ حرف).';
	}

	$digits = partyab_normalize_phone( $phone );

	if ( partyab_text_len( $digits ) < 10 ) {
		$errors['phone'] = 'شماره تماس معتبر نیست؛ مثال: ۰۹۱۲۱۲۳۴۵۶۷.';
	}

	if ( partyab_text_len( $body ) < 10 ) {
		$errors['message'] = 'متن پیام حداقل ۱۰ حرف باشد تا بتوانیم راهنمایی کنیم.';
	}

	return $errors;
}

/**
 * یکسان‌سازی شماره تلفن به رقم لاتین.
 *
 * @param string $phone شماره ورودی.
 * @return string
 */
function partyab_normalize_phone( string $phone ): string {
	$phone = function_exists( 'partyab_normalize_digits' ) ? partyab_normalize_digits( $phone ) : $phone;

	return (string) preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * هندلر ارسال فرم (admin-post).
 */
function partyab_handle_contact_form(): void {
	$redirect = wp_get_referer() ?: home_url( '/contact' );

	if ( ! isset( $_POST['partyab_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['partyab_contact_nonce'] ) ), 'partyab_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'pb_contact', 'error', $redirect ) );
		exit;
	}

	// هانی‌پات: اگر پر شده بود، ربات است.
	if ( ! empty( $_POST['partyab_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'pb_contact', 'sent', $redirect ) );
		exit;
	}

	$data = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['pb_name'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['pb_phone'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['pb_message'] ?? '' ) ),
	);

	if ( partyab_contact_validate( $data ) ) {
		wp_safe_redirect( add_query_arg( 'pb_contact', 'error', $redirect ) );
		exit;
	}

	$lines = array(
		'نام: ' . $data['name'],
		'تلفن: ' . $data['phone'],
		'پیام: ' . $data['message'],
		'صفحه: ' . $redirect,
	);

	wp_mail(
		apply_filters( 'partyab_contact_recipient', get_option( 'admin_email' ) ),
		'پیام تازه از فرم تماس سایت',
		implode( "\n", $lines )
	);

	wp_safe_redirect( add_query_arg( 'pb_contact', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_nopriv_partyab_contact', 'partyab_handle_contact_form' );
add_action( 'admin_post_partyab_contact', 'partyab_handle_contact_form' );

/**
 * شورت‌کد فرم تماس.
 *
 * @return string
 */
function partyab_contact_form_shortcode(): string {
	$state   = isset( $_GET['pb_contact'] ) ? sanitize_key( wp_unslash( $_GET['pb_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice  = '';
	$value   = static function ( string $key ): string {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};

	if ( 'sent' === $state ) {
		$notice = '<div class="pb-form__notice is-ok">پیامت رسید؛ تا یک روز کاری تماس می‌گیریم.</div>';
	} elseif ( 'error' === $state ) {
		$notice = '<div class="pb-form__notice is-bad">ارسال نشد. نام، شماره و متن پیام را بررسی کن.</div>';
	}

	return '<div class="pb-form">' . $notice
		. '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
		. '<input type="hidden" name="action" value="partyab_contact">'
		. wp_nonce_field( 'partyab_contact', 'partyab_contact_nonce', true, false )
		. '<p class="pb-form__hp"><label>وب‌سایت<input type="text" name="partyab_website" tabindex="-1" autocomplete="off"></label></p>'
		. '<label>نام و نام خانوادگی<input type="text" name="pb_name" required value="' . esc_attr( $value( 'pb_name' ) ) . '"></label>'
		. '<label>شماره تماس<input type="tel" name="pb_phone" required dir="ltr" value="' . esc_attr( $value( 'pb_phone' ) ) . '"></label>'
		. '<label>پیام<textarea name="pb_message" rows="5" required>' . esc_textarea( $value( 'pb_message' ) ) . '</textarea></label>'
		. '<button type="submit" class="pb-btn">ارسال پیام</button>'
		. '</form></div>';
}
add_shortcode( 'partyab_contact_form', 'partyab_contact_form_shortcode' );
