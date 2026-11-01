<?php
/**
 * Home hero: vehicle-first search card + car map.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

$partyab_vehicle = partyab_vehicle();
?>

<section class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<span class="hero-eyebrow">
				<?php partyab_the_icon( 'spark' ); ?>
				<?php esc_html_e( 'فقط قطعه‌ای که به خودروی شما می‌خورد', 'partyab' ); ?>
			</span>

			<h1>
				<?php esc_html_e( 'قطعهٔ درست،', 'partyab' ); ?><br>
				<?php esc_html_e( 'برای', 'partyab' ); ?> <em><?php esc_html_e( 'خودروی درست', 'partyab' ); ?></em>
			</h1>

			<p class="hero-lead">
				<?php esc_html_e( 'خودرویتان را انتخاب کنید و به‌جای جست‌وجو بین هزاران کالا، فقط قطعاتی را ببینید که فروشگاه سازگاری‌شان را تأیید کرده است.', 'partyab' ); ?>
			</p>

			<div class="vehicle-card">
				<div class="vehicle-card-head">
					<span class="vc-icon"><?php partyab_the_icon( 'garage' ); ?></span>
					<div>
						<strong><?php esc_html_e( 'پلاک خودرو', 'partyab' ); ?></strong>
						<span>
							<?php
							echo $partyab_vehicle
								? esc_html( sprintf( __( 'خودروی فعال: %s', 'partyab' ), partyab_vehicle_label( $partyab_vehicle ) ) )
								: esc_html__( 'مدل را بنویسید؛ سال و تیپ را انتخاب کنید', 'partyab' );
							?>
						</span>
					</div>
					<?php if ( $partyab_vehicle ) : ?>
						<button type="button" class="vc-change" data-open-vehicle><?php partyab_the_icon( 'refresh' ); ?><?php esc_html_e( 'تغییر', 'partyab' ); ?></button>
					<?php endif; ?>
				</div>

				<form class="hero-search" action="<?php echo esc_url( partyab_shop_url() ); ?>" method="get" role="search">
					<div class="field field--full">
						<label for="heroCar"><span class="step-dot">۱</span> <?php esc_html_e( 'مدل خودرو', 'partyab' ); ?></label>
						<div class="combo combo--hero">
							<?php partyab_the_icon( 'search', 'combo-icon' ); ?>
							<input id="heroCar" type="text" name="s" value="<?php echo esc_attr( $partyab_vehicle ? $partyab_vehicle['model_name'] : '' ); ?>"
								placeholder="<?php esc_attr_e( 'مثلاً ۲۰۶، پراید، سمند، کرولا…', 'partyab' ); ?>" autocomplete="off"
								role="combobox" aria-expanded="false" aria-controls="heroResults" aria-autocomplete="list">
							<input type="hidden" name="post_type" value="product">
							<div class="combo-list" id="heroResults" role="listbox" aria-label="<?php esc_attr_e( 'نتایج خودرو', 'partyab' ); ?>"></div>
						</div>
					</div>

					<div class="vehicle-steps vehicle-steps--2">
						<div class="field">
							<label for="heroYear"><span class="step-dot">۲</span> <?php esc_html_e( 'سال ساخت', 'partyab' ); ?></label>
							<div class="select-wrap">
								<select id="heroYear" disabled aria-label="<?php esc_attr_e( 'سال ساخت', 'partyab' ); ?>"><option value=""><?php esc_html_e( 'سال ساخت', 'partyab' ); ?></option></select>
							</div>
						</div>
						<div class="field">
							<label for="heroEngine"><span class="step-dot">۳</span> <?php esc_html_e( 'تیپ / موتور', 'partyab' ); ?> <em><?php esc_html_e( 'اختیاری', 'partyab' ); ?></em></label>
							<div class="select-wrap">
								<select id="heroEngine" disabled aria-label="<?php esc_attr_e( 'تیپ یا موتور', 'partyab' ); ?>"><option value=""><?php esc_html_e( 'تیپ / موتور', 'partyab' ); ?></option></select>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-amber btn-block" data-hero-go>
						<?php partyab_the_icon( 'search' ); ?>
						<?php esc_html_e( 'جست‌وجوی قطعات سازگار', 'partyab' ); ?>
					</button>
				</form>

				<div class="vehicle-card-foot">
					<span><?php esc_html_e( 'یا', 'partyab' ); ?></span>
					<a href="<?php echo esc_url( partyab_shop_url() ); ?>#search"><?php esc_html_e( 'با کد فنی / OEM جست‌وجو کنید', 'partyab' ); ?></a>
				</div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/car-map' ); ?>
	</div>
</section>
