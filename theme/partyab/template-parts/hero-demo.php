<?php
/**
 * Hero visual — a real product card preview (replaces the car map).
 *
 * Shows the one thing that makes PartYab different: every card carries a fitment
 * state in words and colour, plus the technical code.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

$partyab_img  = get_template_directory_uri() . '/assets/img/';
$partyab_makes = 0;
$partyab_models = 0;

if ( class_exists( 'Partyab_Vehicles' ) ) {
	foreach ( Partyab_Vehicles::makes() as $partyab_make ) {
		$partyab_makes++;
		$partyab_models += count( (array) $partyab_make['models'] );
	}
}

if ( $partyab_models < 1 ) {
	$partyab_models = 33;
}
if ( $partyab_makes < 1 ) {
	$partyab_makes = 10;
}
?>
<div class="hero-visual">
	<div class="hero-demo">
		<div class="hero-demo-head">
			<b><?php partyab_the_icon( 'check-shield' ); ?><?php esc_html_e( 'کارت محصول در پارت‌یاب', 'partyab' ); ?></b>
			<span class="hero-demo-tag"><?php esc_html_e( 'نمونه', 'partyab' ); ?></span>
		</div>

		<article class="p-card hero-demo-card" aria-hidden="true">
			<span class="p-rail p-rail--conditional"></span>
			<div class="p-media">
				<img src="<?php echo esc_url( $partyab_img . 'p-brake.jpg' ); ?>" alt="" width="480" height="360" loading="eager">
				<span class="p-code">4253.42</span>
				<span class="off-badge">−<?php echo esc_html( partyab_num( 14 ) ); ?>٪</span>
			</div>
			<div class="p-body">
				<div class="p-head">
					<span class="p-brand">TRW</span>
					<span class="p-equiv"><?php partyab_the_icon( 'copy' ); ?><?php echo esc_html( partyab_num( 3 ) ); ?> <?php esc_html_e( 'کد', 'partyab' ); ?></span>
				</div>
				<h3 class="p-title"><?php esc_html_e( 'لنت ترمز جلو پژو ۲۰۶ / سمند / دنا', 'partyab' ); ?></h3>
				<div class="p-fit p-fit--conditional">
					<?php partyab_the_icon( 'alert' ); ?>
					<span><?php esc_html_e( 'سازگار با شرط · فقط تیپ ۵ و ۶ با ABS', 'partyab' ); ?></span>
				</div>
				<div class="p-foot">
					<div class="p-price">
						<span class="old"><?php echo esc_html( partyab_num( '2٬150٬000' ) ); ?></span>
						<strong><?php echo esc_html( partyab_num( '1٬850٬000' ) ); ?> <small><?php esc_html_e( 'تومان', 'partyab' ); ?></small></strong>
					</div>
					<span class="p-add"><?php partyab_the_icon( 'plus' ); ?></span>
				</div>
			</div>
		</article>

		<p class="hero-demo-note">
			<?php esc_html_e( 'هر کارت: کد فنی، برند، کدهای معادل و وضعیت سازگاری با خودروی انتخاب‌شده.', 'partyab' ); ?>
		</p>

		<div class="hero-mini-stats">
			<span><b><?php echo esc_html( partyab_num( $partyab_models ) ); ?></b><?php esc_html_e( 'مدل خودرو', 'partyab' ); ?></span>
			<span><b><?php echo esc_html( partyab_num( $partyab_makes ) ); ?></b><?php esc_html_e( 'سازنده', 'partyab' ); ?></span>
			<span><b><?php echo esc_html( partyab_num( 5 ) ); ?></b><?php esc_html_e( 'حالت سازگاری', 'partyab' ); ?></span>
		</div>
	</div>
</div>
