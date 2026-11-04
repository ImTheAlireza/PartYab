<?php
/** Generic WordPress page, including shortcode-driven content pages. */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	partyab_page_intro( get_the_title(), '', 'پارتیاب؛ همراه مطمئن تو' );
	?>
	<div class="pb-page"><article id="post-<?php the_ID(); ?>" <?php post_class( 'pb-page__article' ); ?>><div class="pb-page__body"><?php the_content(); wp_link_pages(); ?></div></article></div>
	<?php
endwhile;
get_footer();
