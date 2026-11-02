<?php
/**
 * مرحله ۱: نصب افزونه‌ها و قالب.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * نصب و فعال‌سازی WooCommerce، Elementor، Elementor Pro.
 *
 * @param array $report گزارش.
 */
function partyab_qs_step_plugins( array &$report ): void {
	// ---- WooCommerce ----
	if ( partyab_qs_is_active( 'woocommerce' ) ) {
		partyab_qs_log( $report, 'افزونه‌ها', 'WooCommerce', true, 'از قبل فعال بود' );
	} else {
		$zip = partyab_qs_find_zip( 'woocommerce*' );

		$install = $zip ? partyab_qs_install_plugin_zip( $zip ) : partyab_qs_install_plugin_wporg( 'woocommerce' );
		partyab_qs_log( $report, 'افزونه‌ها', 'نصب WooCommerce', $install['ok'], $install['msg'] );

		if ( $install['ok'] ) {
			$activate = partyab_qs_activate_plugin( 'woocommerce' );
			partyab_qs_log( $report, 'افزونه‌ها', 'فعال‌سازی WooCommerce', $activate['ok'], $activate['msg'] );
		}
	}

	// ---- Elementor ----
	if ( partyab_qs_is_active( 'elementor' ) ) {
		partyab_qs_log( $report, 'افزونه‌ها', 'Elementor', true, 'از قبل فعال بود' );
	} else {
		$zip     = partyab_qs_find_zip( 'elementor' );
		$install = $zip ? partyab_qs_install_plugin_zip( $zip ) : partyab_qs_install_plugin_wporg( 'elementor' );
		partyab_qs_log( $report, 'افزونه‌ها', 'نصب Elementor', $install['ok'], $install['msg'] );

		if ( $install['ok'] ) {
			$activate = partyab_qs_activate_plugin( 'elementor' );
			partyab_qs_log( $report, 'افزونه‌ها', 'فعال‌سازی Elementor', $activate['ok'], $activate['msg'] );
		}
	}

	// ---- Elementor Pro ----
	if ( partyab_qs_is_active( 'elementor-pro' ) ) {
		partyab_qs_log( $report, 'افزونه‌ها', 'Elementor Pro', true, 'از قبل فعال بود' );
	} else {
		$zip = partyab_qs_find_zip( 'elementor-pro*' );

		if ( ! $zip ) {
			partyab_qs_log(
				$report,
				'افزونه‌ها',
				'Elementor Pro',
				false,
				'فایل elementor-pro*.zip در پوشه vendor نبود — دستی نصب کن'
			);
		} else {
			$install = partyab_qs_install_plugin_zip( $zip );
			partyab_qs_log( $report, 'افزونه‌ها', 'نصب Elementor Pro', $install['ok'], $install['msg'] );

			if ( $install['ok'] ) {
				$activate = partyab_qs_activate_plugin( 'elementor-pro' );
				partyab_qs_log( $report, 'افزونه‌ها', 'فعال‌سازی Elementor Pro', $activate['ok'], $activate['msg'] );
			}
		}
	}
}

/**
 * مرحله ۲: قالب والد و چایلد.
 *
 * @param array $report گزارش.
 */
function partyab_qs_step_theme( array &$report ): void {
	// Hello Elementor از مخزن وردپرس.
	$hello = wp_get_theme( 'hello-elementor' );

	if ( $hello->exists() ) {
		partyab_qs_log( $report, 'قالب', 'Hello Elementor', true, 'از قبل نصب بود' );
	} else {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/theme.php';

		$upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( 'https://downloads.wordpress.org/theme/hello-elementor.latest-stable.zip' );

		partyab_qs_log(
			$report,
			'قالب',
			'نصب Hello Elementor',
			! is_wp_error( $result ),
			is_wp_error( $result ) ? $result->get_error_message() : 'از مخزن وردپرس'
		);
	}

	// چایلد تم پارتیاب.
	$zip = partyab_qs_find_zip( 'part-yab' );

	if ( $zip ) {
		$install = partyab_qs_install_theme_zip( $zip );
		partyab_qs_log( $report, 'قالب', 'نصب چایلد تم PartYab', $install['ok'], $install['msg'] );
	} else {
		partyab_qs_log( $report, 'قالب', 'چایلد تم PartYab', false, 'فایل part-yab.zip در vendor نبود' );
	}

	switch_theme( 'part-yab' );
	$active = wp_get_theme();
	partyab_qs_log( $report, 'قالب', 'فعال‌سازی چایلد تم', $active->get_stylesheet() === 'part-yab', 'قالب فعال: ' . $active->get( 'Name' ) );
}
