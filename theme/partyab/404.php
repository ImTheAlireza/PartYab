<?php
/**
 * 404.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="section">
	<div class="container">
		<div class="notfound">
			<span class="notfound-code mono">۴۰۴</span>
			<h1><?php esc_html_e( 'این صفحه در گاراژ ما نیست', 'partyab' ); ?></h1>
			<p><?php esc_html_e( 'نشانی را دوباره بررسی کنید، یا از بخش‌های زیر شروع کنید.', 'partyab' ); ?></p>
			<div class="notfound-actions">
				<a class="btn btn-amber" href="<?php echo esc_url( partyab_shop_url() ); ?>"><?php partyab_the_icon( 'grid' ); ?><?php esc_html_e( 'فروشگاه', 'partyab' ); ?></a>
				<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php partyab_the_icon( 'arrow-right' ); ?><?php esc_html_e( 'صفحهٔ اصلی', 'partyab' ); ?></a>
			</div>
			<?php get_search_form(); ?>
		</div>
	</div>
</div>
<?php
get_footer();
