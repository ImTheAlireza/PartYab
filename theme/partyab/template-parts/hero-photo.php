<?php
/**
 * Hero visual — photo variant (the pre-v3 treatment).
 *
 * Enable with: set_theme_mod( 'partyab_hero_visual', 'photo' ).
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hero-visual">
	<figure class="hero-photo">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/hero-parts.jpg' ); ?>"
			alt="<?php esc_attr_e( 'قطعات یدکی خودرو', 'partyab' ); ?>" width="1200" height="800" loading="eager">
		<figcaption>
			<?php partyab_the_icon( 'check-shield' ); ?>
			<?php esc_html_e( 'قطعهٔ درست، برای خودروی درست', 'partyab' ); ?>
		</figcaption>
	</figure>
</div>
