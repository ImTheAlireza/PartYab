<?php
/** Single editorial article. Product pages use WooCommerce templates. */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	partyab_page_intro( get_the_title(), get_the_date() . ' · دانشنامه پارتیاب', 'بیشتر بدان، بهتر انتخاب کن' );
	?>
	<div class="pb-page"><article id="post-<?php the_ID(); ?>" <?php post_class( 'pb-page__article' ); ?>>
		<?php if ( has_post_thumbnail() ) : ?><div class="pb-article-cover"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
		<div class="pb-page__body"><?php the_content(); wp_link_pages(); ?></div>
		<footer class="pb-article-footer"><a class="pb-btn pb-btn--ghost" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php echo partyab_icon( 'chev-r' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>بازگشت به راهنماها</a></footer>
	</article></div>
	<?php
endwhile;
get_footer();
