<?php
/**
 * Static page template.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="section">
		<div class="container">
			<nav class="crumbs" aria-label="<?php esc_attr_e( 'مسیر', 'partyab' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'partyab' ); ?></a>
				<?php partyab_the_icon( 'chevron-left' ); ?>
				<span><?php the_title(); ?></span>
			</nav>

			<div class="page-head">
				<h1><?php the_title(); ?></h1>
			</div>

			<article <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="prose"><?php the_content(); ?></div>
				<?php
				wp_link_pages(
					array(
						'before' => '<nav class="pager">',
						'after'  => '</nav>',
					)
				);
				?>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				echo '<div class="entry-comments">';
				comments_template();
				echo '</div>';
			}
			?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
