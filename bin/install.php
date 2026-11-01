<?php
/**
 * One-command installer, executed inside WordPress:
 *
 *   wp eval-file bin/install.php -- --currency=IRR --no-images
 *
 * It only calls Partyab_Setup (the same code behind Tools → «نصب سریع پارت‌یاب»),
 * so WP-CLI and the dashboard always behave identically.
 *
 * @package Partyab_Fitment
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "این فایل فقط با WP-CLI اجرا می‌شود.\n";
	return;
}

if ( ! class_exists( 'Partyab_Setup' ) ) {
	WP_CLI::error( 'افزونهٔ partyab-fitment فعال نیست؛ اول آن را فعال کنید: wp plugin activate partyab-fitment' );
}

$partyab_flags = array();

if ( isset( $args ) && is_array( $args ) ) {
	foreach ( $args as $partyab_arg ) {
		if ( 0 !== strpos( $partyab_arg, '--' ) ) {
			continue;
		}
		$partyab_pair = explode( '=', ltrim( $partyab_arg, '-' ), 2 );
		$partyab_flags[ $partyab_pair[0] ] = isset( $partyab_pair[1] ) ? $partyab_pair[1] : true;
	}
}

Partyab_Setup::cli( array(), $partyab_flags );
