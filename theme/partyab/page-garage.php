<?php
/**
 * Template Name: گاراژ پارت‌یاب
 * Template Post Type: page
 *
 * The garage page: vehicle passports, verified parts, catalogue coverage.
 * Cards are rendered client-side from the REST API so the page stays cached.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="page-head">
	<div class="container">
		<nav class="breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'partyab' ); ?></a><span class="sep">/</span>
			<span class="current"><?php the_title(); ?></span>
		</nav>
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'هر خودرو یک پاسپورت دارد: پوشش کاتالوگ، قطعات تأییدشده و سرویس‌های باقی‌مانده.', 'partyab' ); ?></p>
	</div>
</div>

<div class="section section--tight">
	<div class="container" id="garageRoot" data-garage>
		<div class="g-cards">
			<div class="gv-skeleton"></div>
			<div class="gv-skeleton"></div>
		</div>
	</div>

	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( trim( get_the_content() ) ) :
				?>
				<div class="prose" style="margin-top:24px"><?php the_content(); ?></div>
				<?php
			endif;
		endwhile;
		?>
	</div>
</div>
<?php
get_footer();
