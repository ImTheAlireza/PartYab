<?php
/**
 * Template Name: پایه محتوایی پارتی‌یاب
 * Description: قالب صفحه‌های متنی (درباره، تماس، قوانین، راهنما) با عرض خوانا.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="pb-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'pb-page__article' ); ?>>
			<h1 class="pb-page__title"><?php the_title(); ?></h1>
			<div class="pb-page__body">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</div>

<?php
get_footer();
