<?php
/**
 * Shared design-system helpers. Content stays dynamic; decoration is inert.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/** Return an inline reference to the local, allow-listed icon sprite. */
function partyab_icon( string $name, string $class = '' ): string {
	$allowed = array( 'logo', 'search', 'cart', 'user', 'phone', 'heart', 'chev-d', 'chev-l', 'chev-r', 'arrow-l', 'plus', 'minus', 'truck', 'shield', 'refresh', 'headset', 'car', 'gear', 'oil', 'brake', 'body', 'shock', 'light', 'electric', 'star', 'check', 'info', 'pin', 'mail', 'bolt', 'heart-fill', 'compare', 'eye', 'card', 'clock', 'up', 'instagram', 'telegram', 'whatsapp', 'aparat', 'verified', 'bell', 'spark' );
	if ( ! in_array( $name, $allowed, true ) ) {
		$name = 'gear';
	}
	return '<svg class="pb-icon ' . esc_attr( $class ) . '" aria-hidden="true"><use href="#i-' . esc_attr( $name ) . '"></use></svg>';
}

/** Print the sprite once, before either theme or Elementor header markup. */
function partyab_print_sprite(): void {
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;
	$file = PARTYAB_DIR . '/assets/img/icons.svg';
	if ( is_readable( $file ) ) {
		echo file_get_contents( $file ); // phpcs:ignore WordPress.Security.EscapeOutput -- Bundled, trusted SVG.
	}
	echo '<div class="pb-scroll-progress" aria-hidden="true"><i></i></div>';
	echo '<div class="pb-toast-stack" role="status" aria-live="polite" aria-atomic="false"></div>';
}
add_action( 'wp_body_open', 'partyab_print_sprite', 1 );

/** The shared logo, retaining an uploaded custom logo in the header. */
function partyab_logo(): void {
	?>
	<a class="pb-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<svg class="pb-logo__mark" aria-hidden="true"><use href="#i-logo"></use></svg>
		<span class="pb-logo__text"><b>پارتیاب</b><small>PARTYAB AUTO PARTS</small></span>
	</a>
	<?php
}

/** A compact, reusable page heading. */
function partyab_page_intro( string $title, string $description = '', string $eyebrow = 'پارتیاب؛ انتخاب درست' ): void {
	?>
	<section class="pb-page-intro">
		<div class="pb-container">
			<span class="pb-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
		</div>
	</section>
	<?php
}

/** Service promises already established by the store's content policy. */
function partyab_services(): void {
	$items = array(
		array( 'shield', 'ضمانت اصالت کالا', 'قطعه مشخص، تأمین معتبر' ),
		array( 'truck', 'ارسال به سراسر ایران', 'بسته‌بندی مطمئن قطعات' ),
		array( 'refresh', '۷ روز فرصت مرجوعی', 'مطابق شرایط بازگشت کالا' ),
		array( 'headset', 'پشتیبانی فنی', 'همراه تو در انتخاب قطعه' ),
	);
	echo '<div class="pb-services">';
	foreach ( $items as $item ) {
		echo '<div class="pb-service" data-reveal><span class="pb-service__icon">' . partyab_icon( $item[0] ) . '</span><div><b>' . esc_html( $item[1] ) . '</b><small>' . esc_html( $item[2] ) . '</small></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}

/** A section heading with an optional real destination. */
function partyab_section_heading( string $title, string $subtitle = '', string $url = '', string $label = 'مشاهده همه' ): void {
	echo '<header class="pb-section__head" data-reveal><div><h2>' . esc_html( $title ) . '</h2>';
	if ( $subtitle ) {
		echo '<p>' . esc_html( $subtitle ) . '</p>';
	}
	echo '</div>';
	if ( $url ) {
		echo '<a class="pb-section__link" href="' . esc_url( $url ) . '">' . esc_html( $label ) . partyab_icon( 'arrow-l' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</header>';
}

/** Map known product categories to meaningful icons, not arbitrary positions. */
function partyab_design_category_icon( string $slug ): string {
	$map = array( 'brake-system' => 'brake', 'filters' => 'oil', 'electrical' => 'electric', 'suspension' => 'shock', 'body-glass' => 'body', 'lighting' => 'light', 'engine' => 'gear' );
	return partyab_icon( $map[ $slug ] ?? 'gear' );
}
