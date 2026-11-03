<?php
/**
 * اجرای مرحله‌به‌مرحله نصب.
 *
 * چرا مرحله‌ای و با Redirect؟ چون ووکامرس بعد از فعال شدن در همان درخواست کلاس‌هایش
 * کامل بالا نمی‌آید. هر مرحله در یک درخواست تازه اجرا می‌شود تا محیط سالم باشد.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * فهرست مرحله‌ها به ترتیب.
 *
 * @return array<string,string>
 */
function partyab_qs_phases(): array {
	return array(
		'plugins'  => 'نصب افزونه‌ها',
		'theme'    => 'نصب و فعال‌سازی قالب',
		'settings' => 'تنظیمات وردپرس، ووکامرس و المنتور',
		'content'  => 'محصولات دمو، صفحات، منو و داده سازگاری',
	);
}

/**
 * اجرای یک مرحله و برگرداندن گزارش آن.
 *
 * @param string $phase کلید مرحله.
 * @return array
 */
function partyab_qs_run_phase( string $phase ): array {
	$report = array();

	@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

	switch ( $phase ) {
		case 'plugins':
			partyab_qs_step_plugins( $report );
			break;

		case 'theme':
			partyab_qs_step_theme( $report );
			break;

		case 'settings':
			partyab_qs_step_settings( $report );
			break;

		case 'content':
			partyab_qs_step_content( $report );
			break;
	}

	return $report;
}

/**
 * ذخیره گزارش تجمعی.
 *
 * @param array $rows ردیف‌های تازه.
 */
function partyab_qs_store_report( array $rows ): void {
	$saved = get_option( 'partyab_qs_report', array() );
	$saved = is_array( $saved ) ? $saved : array();

	update_option( 'partyab_qs_report', array_merge( $saved, $rows ), false );
}

/**
 * آدرس مرحله بعد (یا صفحه پایانی).
 *
 * @param string $current مرحله فعلی.
 * @return string
 */
function partyab_qs_next_url( string $current ): string {
	$phases = array_keys( partyab_qs_phases() );
	$index  = array_search( $current, $phases, true );
	$next   = ( false !== $index && isset( $phases[ $index + 1 ] ) ) ? $phases[ $index + 1 ] : '';

	$args = array( 'page' => 'partyab-quickstart' );

	if ( '' === $next ) {
		$args['pb_done'] = 1;
	} else {
		$args['pb_phase'] = $next;
	}

	return add_query_arg( $args, admin_url( 'admin.php' ) );
}

// ---------------------------------------------------------------------------
// WP-CLI
// ---------------------------------------------------------------------------
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * اجرای کامل نصب سریع از خط فرمان.
	 *
	 * ## EXAMPLES
	 *     wp partyab quickstart
	 */
	WP_CLI::add_command( 'partyab quickstart', function () {
		foreach ( partyab_qs_phases() as $phase => $label ) {
			WP_CLI::log( '▶ ' . $label );

			$rows = partyab_qs_run_phase( $phase );
			partyab_qs_store_report( $rows );

			foreach ( $rows as $row ) {
				$row['ok'] ? WP_CLI::log( '   ✓ ' . $row['label'] . ' — ' . $row['msg'] ) : WP_CLI::warning( $row['label'] . ' — ' . $row['msg'] );
			}
		}

		WP_CLI::success( 'نصب سریع تمام شد.' );
	} );
}
