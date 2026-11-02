<?php
/**
 * صفحه «نصب سریع» در پیشخوان.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'partyab_qs_menu' );

/**
 * افزودن منو (بالای منوها تا گم نشود).
 */
function partyab_qs_menu(): void {
	add_menu_page(
		'نصب سریع پارتیاب',
		'نصب سریع',
		'manage_options',
		'partyab-quickstart',
		'partyab_qs_page',
		'dashicons-download',
		2
	);
}

add_action( 'admin_post_partyab_qs_start', 'partyab_qs_start' );

/**
 * شروع فرآیند: پاک کردن گزارش قبلی و رفتن به مرحله اول.
 */
function partyab_qs_start(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'دسترسی ندارید.' );
	}

	check_admin_referer( 'partyab_qs_start' );

	delete_option( 'partyab_qs_report' );

	wp_safe_redirect( add_query_arg(
		array( 'page' => 'partyab-quickstart', 'pb_phase' => 'plugins' ),
		admin_url( 'admin.php' )
	) );
	exit;
}

add_action( 'load-toplevel_page_partyab-quickstart', 'partyab_qs_maybe_run_phase' );

/**
 * اجرای مرحله جاری قبل از هر خروجی (تا Redirect سالم بماند).
 */
function partyab_qs_maybe_run_phase(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['pb_phase'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$phase  = sanitize_key( wp_unslash( $_GET['pb_phase'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$phases = partyab_qs_phases();

	if ( ! isset( $phases[ $phase ] ) ) {
		return;
	}

	$rows = partyab_qs_run_phase( $phase );
	partyab_qs_store_report( $rows );

	wp_safe_redirect( partyab_qs_next_url( $phase ) );
	exit;
}

/**
 * رندر صفحه.
 */
function partyab_qs_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$report = get_option( 'partyab_qs_report', array() );
	$report = is_array( $report ) ? $report : array();
	$done   = ! empty( $_GET['pb_done'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$phase  = isset( $_GET['pb_phase'] ) ? sanitize_key( wp_unslash( $_GET['pb_phase'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	echo '<div class="wrap">';
	echo '<h1>نصب سریع پارتیاب</h1>';

	echo '<p style="max-width:720px;font-size:14px;line-height:2">'
		. 'این دکمه همه‌چیز را آماده می‌کند: نصب ووکامرس، المنتور و المنتور پرو، نصب قالب پارتیاب، '
		. 'تنظیمات فارسی و تومان، شش محصول تست با عکس، دسته‌بندی‌ها، صفحات، منو و جدول سازگاری خودرو.'
		. '</p>';

	echo '<p><strong>قبل از زدن دکمه:</strong> مطمئن شو در «افزونه‌ها ← افزودن» دسترسی نصب داری و اگر اینترنت وصل است بهتر است (برای ووکامرس از مخزن وردپرس استفاده می‌شود).</p>';

	// دکمه شروع.
	if ( ! $phase && ! $done ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'partyab_qs_start' );
		echo '<input type="hidden" name="action" value="partyab_qs_start">';
		echo '<p><button type="submit" class="button button-primary button-hero" style="background:#ea580c;border-color:#c2410c">🚀 نصب سریع — همه‌چیز را آماده کن</button></p>';
		echo '</form>';
	}

	// گزارش.
	if ( $report ) {
		$fails = 0;
		foreach ( $report as $row ) {
			if ( empty( $row['ok'] ) ) {
				++$fails;
			}
		}

		if ( $done ) {
			$color = $fails ? '#b45309' : '#16a34a';
			printf(
				'<div class="notice" style="border-right:4px solid %s;padding:12px 16px"><p style="margin:0;font-size:15px"><strong>%s</strong></p></div>',
				esc_attr( $color ),
				$fails
					? sprintf( 'نصب تمام شد با %d هشدار — پایین را ببین.', $fails )
					: 'همه‌چیز نصب و آماده شد ✅'
			);
		}

		echo '<h2>گزارش مرحله‌به‌مرحله</h2>';
		echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th style="width:150px">بخش</th><th>مورد</th><th>نتیجه</th><th>توضیح</th></tr></thead><tbody>';

		foreach ( $report as $row ) {
			printf(
				'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
				esc_html( $row['group'] ),
				esc_html( $row['label'] ),
				$row['ok'] ? '<span style="color:#16a34a">✔</span>' : '<span style="color:#dc2626">✕</span>',
				esc_html( $row['msg'] )
			);
		}

		echo '</tbody></table>';

		if ( $done ) {
			echo '<h2>قدم‌های بعدی</h2><ol style="line-height:2">';
			echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">محصولات</a> را ببین — شش محصول با عکس باید باشد.</li>';
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=partyab-compat' ) ) . '">صفحه سازگاری</a> باید «۱۴ ردیف» نشان دهد.</li>';
			echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">سایت</a> را در مرورگر باز کن (ترجیحاً موبایل) و بدون خطا ببین.</li>';
			echo '<li>برای تکرار نصب: <code>wp partyab quickstart</code> در ترمینال.</li>';
			echo '</ol>';

			echo '<p style="margin-top:24px"><a class="button" href="' . esc_url( add_query_arg( array( 'page' => 'partyab-quickstart' ), admin_url( 'admin.php' ) ) ) . '">اجرای دوباره</a></p>';
		}
	}

	echo '</div>';
}
