<?php
/**
 * Plugin Name:       PartYab Quick Start
 * Description:       نصب‌کننده یک‌کلیکی پارتیاب: نصب قالب و افزونه‌ها، تنظیمات، محصولات تست با عکس، داده سازگاری.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            PartYab
 * License:           GPL v3 or later
 * Text Domain:       partyab-quickstart
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_QS_VERSION', '1.0.0' );
define( 'PARTYAB_QS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PARTYAB_QS_URI', plugin_dir_url( __FILE__ ) );

require_once PARTYAB_QS_DIR . 'includes/helpers.php';
require_once PARTYAB_QS_DIR . 'includes/steps-plugins.php';
require_once PARTYAB_QS_DIR . 'includes/steps-settings.php';
require_once PARTYAB_QS_DIR . 'includes/steps-content.php';
require_once PARTYAB_QS_DIR . 'includes/runner.php';
require_once PARTYAB_QS_DIR . 'includes/admin.php';
