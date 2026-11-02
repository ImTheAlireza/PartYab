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
		'partyab_qs_render_page',
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
	partyab_qs_debug( 'phase done: ' . $phase . ' rows: ' . count( $rows ) );

	wp_safe_redirect( partyab_qs_next_url( $phase ) );
	exit;
}

/**
 * رندر صفحه.
 */
function partyab_qs_render_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$report = get_option( 'partyab_qs_report', array() );
	$report = is_array( $report ) ? $report : array();
	$done   = ! empty( $_GET['pb_done'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$phase  = isset( $_GET['pb_phase'] ) ? sanitize_key( wp_unslash( $_GET['pb_phase'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	echo '<div class="wrap">';
	echo '<h1>نصب سریع پارتیاب</h1>';

	if ( ! empty( $_GET['pb_uploaded'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		printf(
			'<div class="notice notice-success is-dismissible"><p>بسته ذخیره شد: <code>%s</code></p></div>',
			esc_html( sanitize_file_name( rawurldecode( wp_unslash( $_GET['pb_uploaded'] ) ) ) ) // phpcs:ignore WordPress.Security.NonceVerification
		);
	}

	if ( ! empty( $_GET['pb_up_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		printf(
			'<div class="notice notice-error"><p>آپلود نشد: %s</p></div>',
			esc_html( rawurldecode( wp_unslash( $_GET['pb_up_error'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification
		);
	}

	// ---- دکمه اصلی ----
	echo '<div style="max-width:860px;background:#fff;border:1px solid #e5e7eb;border-right:4px solid #ea580c;border-radius:8px;padding:16px 20px;margin:16px 0">';
	echo '<p style="margin:0 0 12px;font-size:14px;line-height:2">این دکمه ووکامرس، المنتور، المنتور پرو، قالب و افزونه سازگاری را نصب می‌کند؛ تنظیمات فارسی و تومان را می‌چیند؛ ۶ محصول با عکس، ۵ دسته، ۱۴ ردیف سازگاری، صفحات و منو را می‌سازد.</p>';

	if ( ! $phase && ! $done ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'partyab_qs_start' );
		echo '<input type="hidden" name="action" value="partyab_qs_start">';
		echo '<button type="submit" class="button button-primary button-hero" style="background:#ea580c;border-color:#c2410c">🚀 نصب سریع — همه‌چیز را آماده کن</button>';
		echo '</form>';
	} else {
		echo '<p style="margin:0"><a class="button" href="' . esc_url( add_query_arg( array( 'page' => 'partyab-quickstart' ), admin_url( 'admin.php' ) ) ) . '">اجرای دوباره / بازگشت</a></p>';
	}

	echo '</div>';

	// ---- بسته‌ها ----
	$status = partyab_qs_packages_status();

	echo '<h2>بسته‌های نصب</h2>';
	echo '<p style="max-width:760px">اگر بسته‌ای «نیست»، همان‌جا فایل ZIP آن را آپلود کن. آپلود مستقیم از این صفحه، محدودیت حجمی آپلود یک ZIP بزرگ را دور می‌زند.</p>';
	echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th>بسته</th><th>وضعیت</th><th>حجم</th><th>آپلود ZIP</th></tr></thead><tbody>';

	foreach ( $status as $item ) {
		echo '<tr>';
		echo '<td><strong>' . esc_html( $item['label'] ) . '</strong><br><span style="color:#6b7280;font-size:12px">' . esc_html( $item['hint'] ) . '</span></td>';
		echo '<td>' . ( $item['found']
			? '<span style="color:#16a34a">✔ موجود</span> <code style="font-size:11px">' . esc_html( $item['file'] ) . '</code>'
			: '<span style="color:#b45309">✕ نیست</span>' ) . '</td>';
		echo '<td>' . esc_html( $item['size'] ) . '</td>';
		echo '<td><form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:flex;gap:6px;align-items:center">';
		wp_nonce_field( 'partyab_qs_upload' );
		echo '<input type="hidden" name="action" value="partyab_qs_upload">';
		echo '<input type="hidden" name="pb_package" value="' . esc_attr( $item['key'] ) . '">';
		echo '<input type="file" name="pb_zip" accept=".zip" required style="max-width:200px">';
		echo '<button type="submit" class="button">آپلود</button>';
		echo '</form></td>';
		echo '</tr>';
	}

	echo '</tbody></table>';

	// ---- عیب‌یابی ----
	echo '<details style="margin:20px 0;max-width:960px"><summary style="cursor:pointer;font-weight:700">عیب‌یابی محیط (اگر نصب خطا داد این را باز کن)</summary>';
	echo '<table class="widefat striped" style="margin-top:10px"><tbody>';

	foreach ( partyab_qs_diagnostics() as $row ) {
		printf(
			'<tr><td style="width:280px">%s</td><td>%s</td><td style="width:70px">%s</td></tr>',
			esc_html( $row['label'] ),
			esc_html( $row['value'] ),
			$row['ok'] ? '<span style="color:#16a34a">✔</span>' : '<span style="color:#dc2626">✕</span>'
		);
	}

	echo '</tbody></table>';
	echo '<p style="color:#6b7280;font-size:12px;margin-top:8px">مسیر پوشه بسته‌ها: <code>' . esc_html( partyab_qs_vendor_dir() ) . '</code></p>';
	echo '</details>';

	// ---- گزارش ----
	if ( $report ) {
		$fails = 0;
		foreach ( $report as $row ) {
			if ( empty( $row['ok'] ) ) {
				++$fails;
			}
		}

		if ( $done ) {
			printf(
				'<div class="notice" style="border-right:4px solid %s;padding:12px 16px"><p style="margin:0;font-size:15px"><strong>%s</strong></p></div>',
				$fails ? '#b45309' : '#16a34a',
				$fails ? sprintf( 'نصب تمام شد با %d هشدار — پایین را ببین.', $fails ) : 'همه‌چیز نصب و آماده شد ✅'
			);
		}

		echo '<h2>گزارش مرحله‌به‌مرحله</h2>';
		echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th style="width:150px">بخش</th><th>مورد</th><th style="width:60px">نتیجه</th><th>توضیح</th></tr></thead><tbody>';

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
			echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">محصولات</a> — شش محصول با عکس باید باشد.</li>';
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=partyab-compat' ) ) . '">سازگاری</a> — باید ۱۴ ردیف نشان دهد.</li>';
			echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">سایت</a> را باز کن (ترجیحاً موبایل).</li>';
			echo '<li>لایسنس Elementor Pro را با کلید خودت فعال کن.</li>';
			echo '</ol>';
		}
	}

	echo '</div>';
}
