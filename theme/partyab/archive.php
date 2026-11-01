<?php
/**
 * Archive (blog categories, tags, dates, authors).
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="section">
	<div class="container">
		<div class="section-head">
			<div>
				<span class="eyebrow"><?php partyab_the_icon( 'book' ); ?><?php esc_html_e( 'آرشیو', 'partyab' ); ?></span>
				<h1><?php the_archive_title(); ?></h1>
				<?php the_archive_description( '<p>', '</p>' ); ?>
			</div>
		</div>

		<?php if ( have_posts() ) : ?>
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
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>
			<nav class="pager"><?php echo wp_kses_post( paginate_links( array( 'prev_text' => esc_html__( 'قبلی', 'partyab' ), 'next_text' => esc_html__( 'بعدی', 'partyab' ) ) ) ); ?></nav>
		<?php else : ?>
			<p class="prose"><?php esc_html_e( 'موردی در این آرشیو نیست.', 'partyab' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
