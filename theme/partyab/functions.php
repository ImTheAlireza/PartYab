<?php
/**
 * PartYab theme bootstrap.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_THEME_VERSION', '1.0.0' );
define( 'PARTYAB_THEME_DIR', get_template_directory() );
define( 'PARTYAB_THEME_URI', get_template_directory_uri() );

require_once PARTYAB_THEME_DIR . '/inc/icons.php';
require_once PARTYAB_THEME_DIR . '/inc/setup.php';
require_once PARTYAB_THEME_DIR . '/inc/template-tags.php';
require_once PARTYAB_THEME_DIR . '/inc/enqueue.php';
require_once PARTYAB_THEME_DIR . '/inc/woocommerce.php';
