<?php
/**
 * Plugin Name:       PartYab Compatibility
 * Plugin URI:        https://github.com/ImTheAlireza/PartYab
 * Description:       موتور سازگاری قطعه با خودرو (برند، مدل، سال) + ایمپورت CSV + جستجوی معکوس قطعه. جدول اختصاصی، مستقل از قالب.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            PartYab
 * Text Domain:       partyab-compat
 * License:           GPL v3 or later
 *
 * چرا افزونه و نه قالب: مدل داده نباید با تغییر قالب از بین برود (تصمیم D3).
 *
 * @package PartYab_Compat
 */

defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_COMPAT_VERSION', '0.1.0' );
define( 'PARTYAB_COMPAT_DB_VERSION', '1' );
define( 'PARTYAB_COMPAT_FILE', __FILE__ );
define( 'PARTYAB_COMPAT_DIR', plugin_dir_path( __FILE__ ) );
define( 'PARTYAB_COMPAT_URI', plugin_dir_url( __FILE__ ) );

require_once PARTYAB_COMPAT_DIR . 'includes/table.php';
require_once PARTYAB_COMPAT_DIR . 'includes/query.php';
require_once PARTYAB_COMPAT_DIR . 'includes/import.php';
require_once PARTYAB_COMPAT_DIR . 'includes/admin.php';

register_activation_hook( __FILE__, 'partyab_compat_install' );

/**
 * ساخت/به‌روزرسانی جدول در هر بار بارگذاری اگر نسخه دیتابیس قدیمی باشد.
 */
add_action( 'plugins_loaded', 'partyab_compat_maybe_upgrade' );

/**
 * ارتقای خودکار اسکیمای جدول.
 */
function partyab_compat_maybe_upgrade(): void {
	if ( get_option( 'partyab_compat_db_version' ) !== PARTYAB_COMPAT_DB_VERSION ) {
		partyab_compat_install();
	}
}
