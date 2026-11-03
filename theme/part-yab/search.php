<?php
/**
 * نتایج جستجو.
 *
 * جستجوی محصول با همان قالب فروشگاه (سایدبار فیلتر + شبکه کارت محصول) نمایش داده
 * می‌شود؛ جستجوی عمومی (نوشته‌ها) با فهرست ساده.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();

$partyab_is_product_search = function_exists( 'is_search' ) && 'product' === get_query_var( 'post_type' );
$partyab_search_url        = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
?>

<div class="pb-container pb-search-page">

	<?php if ( $partyab_is_product_search ) : ?>

		<div class="pb-shop">

			<?php partyab_shop_sidebar(); ?>

			<div class="pb-shop__main">

				<header class="pb-search-head">
					<h1 class="pb-search-head__title">
						نتیجه جستجو برای «<?php echo esc_html( get_search_query() ); ?>»
					</h1>
					<p class="pb-search-head__count">
						<?php
						global $wp_query;
						printf(
							'%s نتیجه پیدا شد',
							esc_html( number_format_i18n( (int) $wp_query->found_posts ) )
						);
						?>
					</p>
				</header>

				<?php if ( have_posts() ) : ?>

					<ul class="products columns-3 pb-products">
						<?php
						while ( have_posts() ) :
							the_post();

							if ( function_exists( 'wc_setup_product_data' ) ) {
								wc_setup_product_data( get_the_ID() );
							}

							wc_get_template_part( 'content', 'product' );
						endwhile;

						if ( function_exists( 'wc_reset_product_data' ) ) {
							wc_reset_product_data();
						}
						?>
					</ul>

					<?php
					woocommerce_pagination();
					?>

				<?php else : ?>

					<div class="partyab-empty">
						<p class="partyab-empty__title">چیزی با «<?php echo esc_html( get_search_query() ); ?>» پیدا نشد.</p>
						<p>اگر شماره فنی داری، بدون خط تیره و با حرف بزرگ امتحان کن (مثل <span dir="ltr">PBBRK001</span>). یا خودرویت را انتخاب کن تا فقط قطعات سازگار را ببینی.</p>
						<div class="partyab-empty__actions">
							<a class="partyab-btn" href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">انتخاب خودروی من</a>
							<a class="partyab-btn partyab-btn--ghost" href="<?php echo esc_url( $partyab_search_url ); ?>">دیدن همه قطعات</a>
						</div>
					</div>

					<?php
					// میان‌بر: پرسیده‌ترین دسته‌ها تا کاربر در بن‌بست نماند.
					$partyab_cats = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'parent'     => 0,
							'number'     => 6,
							'orderby'    => 'count',
							'order'      => 'DESC',
						)
					);

					if ( ! is_wp_error( $partyab_cats ) && $partyab_cats ) :
						?>
						<div class="pb-search-cats">
							<h2 class="pb-search-cats__title">جستجو در دسته‌ها</h2>
							<ul class="pb-search-cats__list">
								<?php foreach ( $partyab_cats as $partyab_cat ) : ?>
									<li>
										<a href="<?php echo esc_url( get_term_link( $partyab_cat ) ); ?>">
											<?php echo esc_html( $partyab_cat->name ); ?>
											<span><?php echo esc_html( number_format_i18n( (int) $partyab_cat->count ) ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

				<?php endif; ?>

			</div>
		</div>

	<?php else : ?>

		<header class="pb-search-head">
			<h1 class="pb-search-head__title">نتیجه جستجو برای «<?php echo esc_html( get_search_query() ); ?>»</h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="pb-search-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="pb-search-item">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="partyab-empty">
				<p class="partyab-empty__title">نتیجه‌ای پیدا نشد.</p>
				<p>می‌توانی از فروشگاه شروع کنی یا خودرویت را انتخاب کنی.</p>
				<a class="partyab-btn" href="<?php echo esc_url( $partyab_search_url ); ?>">رفتن به فروشگاه</a>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div>

<?php
get_footer();
