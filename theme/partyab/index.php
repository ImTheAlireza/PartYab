<?php
/**
 * Fallback template (blog index).
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php partyab_the_icon( 'book' ); ?><?php esc_html_e( 'راهنما و مقاله', 'partyab' ); ?></span>
					<h1><?php echo esc_html( is_home() && ! is_front_page() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : wp_get_document_title() ); ?></h1>
				</div>
			</div>

			<div class="blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'blog-card' ); ?>>
						<a class="blog-thumb" href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'partyab-card' );
							} else {
								partyab_the_icon( 'book' );
							}
							?>
						</a>
						<div class="blog-body">
							<span class="blog-date mono"><?php echo esc_html( partyab_num( get_the_date( 'Y/m/d' ) ) ); ?></span>
							<h2 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<a class="lnk-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'ادامهٔ مطلب', 'partyab' ); ?><?php partyab_the_icon( 'arrow-left' ); ?></a>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<nav class="pager" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'partyab' ); ?>">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'prev_text' => esc_html__( 'قبلی', 'partyab' ),
							'next_text' => esc_html__( 'بعدی', 'partyab' ),
						)
					)
				);
				?>
			</nav>
		<?php else : ?>
			<div class="empty" style="padding:48px">
				<span class="empty-icon"><?php partyab_the_icon( 'search' ); ?></span>
				<b><?php esc_html_e( 'مطلبی پیدا نشد', 'partyab' ); ?></b>
				<p><?php esc_html_e( 'می‌توانید از فروشگاه قطعهٔ سازگار با خودرویتان را جست‌وجو کنید.', 'partyab' ); ?></p>
				<a class="btn btn-amber" href="<?php echo esc_url( partyab_shop_url() ); ?>"><?php esc_html_e( 'رفتن به فروشگاه', 'partyab' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
