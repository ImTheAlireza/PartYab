<?php
/**
 * Front page: hero car map, category index, featured products, states guide.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="page-home-wrap">

	<?php get_template_part( 'template-parts/hero' ); ?>

	<section class="section section--tight">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php partyab_the_icon( 'grid' ); ?><?php esc_html_e( 'دسته‌بندی قطعات', 'partyab' ); ?></span>
					<h2><?php esc_html_e( 'از کدام بخش خودرو شروع کنیم؟', 'partyab' ); ?></h2>
				</div>
				<a class="lnk-more" href="<?php echo esc_url( partyab_shop_url() ); ?>">
					<?php esc_html_e( 'همهٔ قطعات', 'partyab' ); ?><?php partyab_the_icon( 'arrow-left' ); ?>
				</a>
			</div>
			<?php partyab_category_index(); ?>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php partyab_the_icon( 'spark' ); ?><?php esc_html_e( 'پیشنهاد پارت‌یاب', 'partyab' ); ?></span>
					<h2><?php esc_html_e( 'قطعات پرفروش با سازگاری مشخص', 'partyab' ); ?></h2>
				</div>
			</div>
			<?php
			$partyab_featured = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 8,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'featured',
						),
					),
				)
			);
			?>

			<?php if ( $partyab_featured->have_posts() ) : ?>
				<div class="p-grid">
					<?php
					while ( $partyab_featured->have_posts() ) :
						$partyab_featured->the_post();
						partyab_product_card();
					endwhile;
					?>
				</div>
			<?php else : ?>
				<?php
				echo do_shortcode( '[products limit="8" columns="4" visibility="featured"]' );
				?>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>

	<section class="section section--states" id="states">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php partyab_the_icon( 'check-shield' ); ?><?php esc_html_e( 'شفافیت سازگاری', 'partyab' ); ?></span>
					<h2><?php esc_html_e( 'پنج حالت سازگاری، بدون ابهام', 'partyab' ); ?></h2>
					<p><?php esc_html_e( 'وضعیت هر قطعه با متن و آیکون نوشته می‌شود؛ «ثبت‌نشده» با «ناسازگار» یکی نیست.', 'partyab' ); ?></p>
				</div>
			</div>

			<div class="states-grid">
				<?php
				$partyab_states = array(
					'fit'         => array( 'check', __( 'سازگار', 'partyab' ), __( 'برای خودروی شما در فروشگاه تأیید شده است.', 'partyab' ) ),
					'conditional' => array( 'alert', __( 'سازگار با شرط', 'partyab' ), __( 'مثلاً فقط برای تیپ ۵ و ۶ با ترمز ABS.', 'partyab' ) ),
					'no'          => array( 'x', __( 'ناسازگار', 'partyab' ), __( 'با مدل یا سال خودروی شما نمی‌خورد.', 'partyab' ) ),
					'unknown'     => array( 'info', __( 'ثبت نشده', 'partyab' ), __( 'اطلاعات سازگاری نداریم — نامشخص، نه ناسازگار.', 'partyab' ) ),
					'universal'   => array( 'box', __( 'عمومی', 'partyab' ), __( 'مثل روغن موتور، محدود به خودروی خاصی نیست.', 'partyab' ) ),
				);
				foreach ( $partyab_states as $partyab_key => $partyab_state ) :
					?>
					<article class="state-card state-card--<?php echo esc_attr( $partyab_key ); ?>">
						<span class="state-rail"></span>
						<span class="state-icon"><?php partyab_the_icon( $partyab_state[0] ); ?></span>
						<b><?php echo esc_html( $partyab_state[1] ); ?></b>
						<p><?php echo esc_html( $partyab_state[2] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
		<section class="section section--tight">
			<div class="container">
				<div class="home-cta">
					<div>
						<h2><?php esc_html_e( 'خودروی خود را ثبت کنید', 'partyab' ); ?></h2>
						<p><?php esc_html_e( 'با انتخاب پلاک، فقط قطعاتی را می‌بینید که فروشگاه سازگاری‌شان را تأیید کرده است.', 'partyab' ); ?></p>
					</div>
					<button type="button" class="btn btn-amber" data-open-vehicle>
						<?php partyab_the_icon( 'plus' ); ?><?php esc_html_e( 'ثبت خودرو', 'partyab' ); ?>
					</button>
				</div>
			</div>
		</section>
	<?php endif; ?>

</div>

<?php
get_footer();
