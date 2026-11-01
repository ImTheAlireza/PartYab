<?php
/**
 * Single post.
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
				<a href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'راهنما', 'partyab' ); ?></a>
				<?php partyab_the_icon( 'chevron-left' ); ?>
				<span><?php the_title(); ?></span>
			</nav>

			<article <?php post_class( 'entry' ); ?>>
				<header class="entry-head">
					<span class="blog-date mono"><?php echo esc_html( partyab_num( get_the_date( 'Y/m/d' ) ) ); ?></span>
					<h1><?php the_title(); ?></h1>
					<div class="entry-meta">
						<span><?php partyab_the_icon( 'user' ); ?><?php the_author(); ?></span>
						<?php if ( has_category() ) : ?>
							<span><?php partyab_the_icon( 'grid' ); ?><?php the_category( '، ' ); ?></span>
						<?php endif; ?>
					</div>
				</header>

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
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-dir">' . esc_html__( 'مطلب قبلی', 'partyab' ) . '</span><b>%title</b>',
					'next_text' => '<span class="nav-dir">' . esc_html__( 'مطلب بعدی', 'partyab' ) . '</span><b>%title</b>',
					'class'     => 'post-nav',
				)
			);

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
