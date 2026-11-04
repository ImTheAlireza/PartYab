<?php
/**
 * Motion & sound preferences. Sound is a front-end enhancement, never required.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/** Sanitize the sound checkbox: only explicit "1" enables audio. */
function partyab_sanitize_sound( $value ): string {
	return ( '1' === (string) $value ) ? '1' : '0';
}

/** Register a single, lightweight option under Appearance. */
function partyab_register_motion_settings(): void {
	register_setting(
		'partyab_motion',
		'partyab_motion_sound',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'partyab_sanitize_sound',
			'default'           => '1',
		)
	);
	add_settings_section( 'partyab_motion_main', 'افکت‌های حرکتی و صوتی', '__return_false', 'partyab-motion' );
	add_settings_field( 'partyab_motion_sound', 'صدای ترنزیشن صفحه‌ها', 'partyab_render_sound_field', 'partyab-motion', 'partyab_motion_main' );
}
add_action( 'admin_init', 'partyab_register_motion_settings' );

/** Checkbox copy explains browser autoplay rules without promising sound on first paint. */
function partyab_render_sound_field(): void {
	$enabled = '1' === (string) get_option( 'partyab_motion_sound', '1' );
	?>
	<label>
		<input type="checkbox" name="partyab_motion_sound" value="1" <?php checked( $enabled ); ?>>
		افکت صوتی کوتاه هنگام جابه‌جایی صفحه‌ها پخش شود
	</label>
	<p class="description">صدا ملایم و کوتاه است. مرورگرها معمولاً پخش صدا را تنها پس از نخستین کلیک یا لمس کاربر اجازه می‌دهند؛ بازدیدکننده هم می‌تواند از دکمهٔ «صدا» در فوتر آن را بی‌صدا کند. با خاموش کردن این گزینه، هیچ صدایی در سایت پخش نمی‌شود.</p>
	<?php
}

/** Add the preferences screen. */
function partyab_motion_settings_page(): void {
	add_theme_page( 'حرکت و صدا', 'حرکت و صدا', 'manage_options', 'partyab-motion', 'partyab_render_motion_settings_page' );
}
add_action( 'admin_menu', 'partyab_motion_settings_page' );

/** Render the settings form with WordPress nonces and capability checks. */
function partyab_render_motion_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'partyab_motion' );
			do_settings_sections( 'partyab-motion' );
			submit_button( 'ذخیره تنظیمات' );
			?>
		</form>
	</div>
	<?php
}

/**
 * Cover the arriving page before its first paint. The full controller lives in
 * page-transition.js; this tiny bootstrap only prevents a content flash.
 */
function partyab_transition_boot(): void {
	?>
	<script>
	(function(){try{var k='partyab-page-transition',v=sessionStorage.getItem(k);if(!v)return;var d=JSON.parse(v);if(!d||Date.now()-d.at>15000){sessionStorage.removeItem(k);return;}var r=matchMedia('(prefers-reduced-motion: reduce)').matches,p=false;try{p=localStorage.getItem('partyab-motion')==='paused';}catch(e){}if(!r&&!p)document.documentElement.classList.add('pb-page-arriving');}catch(e){}})();
	</script>
	<?php
}
add_action( 'wp_head', 'partyab_transition_boot', 0 );
