<?php
/**
 * Smoke test shim — runs the plugin's verification suite:
 *
 *   wp eval-file bin/smoke-test.php
 *
 * Identical to `wp partyab verify`. The checks live in
 * plugin/partyab-fitment/includes/class-partyab-verify.php so the same suite runs
 * on LocalWP, wp-env, Docker and a live server.
 *
 * @package PartYab_Fitment
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "این فایل فقط با WP-CLI اجرا می‌شود.\n";
	return;
}

if ( ! class_exists( 'Partyab_Verify' ) ) {
	WP_CLI::error( 'افزونهٔ partyab-fitment فعال نیست: wp plugin activate partyab-fitment' );
}

Partyab_Verify::cli();
