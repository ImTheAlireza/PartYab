<?php
/** Blog archive and safe fallback for all content types. */
defined( 'ABSPATH' ) || exit;
get_header();
partyab_page_intro( is_home() ? 'دانشنامه و مجله پارتیاب' : 'تازه‌های پارتیاب', 'راهنمای انتخاب، نگهداری و شناخت بهتر قطعات خودرو.', 'بیشتر بدان، بهتر انتخاب کن' );
?>
<div class="pb-container pb-section">
	<?php if ( have_posts() ) : ?>
		<div class="pb-article-grid"><?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'pb-article-card' ); ?> data-reveal>
				<a class="pb-article-card__visual" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) : ?><?php the_post_thumbnail( 'large' ); ?><?php else : ?><img src="<?php echo esc_url( PARTYAB_URI . '/assets/img/hero-car.webp' ); ?>" alt="" loading="lazy"><?php endif; ?></a>
				<div class="pb-article-card__body"><small><?php echo esc_html( get_the_date() ); ?></small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p><span>ادامه مطلب <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></div>
			</article>
		<?php endwhile; ?></div>
		<div class="pb-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => 'قبلی', 'next_text' => 'بعدی' ) ); ?></div>
	<?php else : ?><div class="partyab-empty"><p class="partyab-empty__title">هنوز مطلبی منتشر نشده است.</p><p>در حال آماده‌کردن راهنماهای تازه هستیم.</p><a class="pb-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به خانه</a></div><?php endif; ?>
</div>
<?php get_footer(); ?>
