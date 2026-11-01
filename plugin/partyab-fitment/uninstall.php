<?php
/**
 * Uninstall: drop the fitment table and options.
 *
 * @package PartYab_Fitment
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/includes/class-partyab-db.php';

if ( class_exists( 'Partyab_DB' ) ) {
	Partyab_DB::uninstall();
}
