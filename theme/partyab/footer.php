<?php
/**
 * Footer + overlays (drawer, vehicle picker, mobile nav).
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="site-footer">
	<div class="container footer-top">
		<div class="footer-brand">
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="logo-mark">
					<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<circle cx="10.4" cy="10.4" r="6.6" stroke="#F59E0B" stroke-width="2"/>
						<path d="M15.4 15.4 21 21" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
						<circle cx="10.4" cy="10.4" r="2.1" stroke="#fff" stroke-width="1.5"/>
					</svg>
				</span>
				<span class="logo-text">
					<strong><?php bloginfo( 'name' ); ?></strong>
					<span><?php esc_html_e( 'قطعهٔ درست، برای خودروی درست', 'partyab' ); ?></span>
				</span>
			</a>
			<p><?php bloginfo( 'description' ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-about',
					'container'      => false,
					'items_wrap'     => '<nav class="footer-links">%3$s</nav>',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
			<div class="footer-social">
				<a href="<?php echo esc_url( get_theme_mod( 'partyab_instagram', '#' ) ); ?>" aria-label="اینستاگرام"><?php partyab_the_icon( 'instagram' ); ?></a>
				<a href="<?php echo esc_url( get_theme_mod( 'partyab_telegram', '#' ) ); ?>" aria-label="تلگرام"><?php partyab_the_icon( 'telegram' ); ?></a>
				<a href="<?php echo esc_url( get_theme_mod( 'partyab_whatsapp', '#' ) ); ?>" aria-label="واتساپ"><?php partyab_the_icon( 'whatsapp' ); ?></a>
			</div>
		</div>

		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) ) : ?>
			<?php dynamic_sidebar( 'footer-1' ); ?>
			<?php dynamic_sidebar( 'footer-2' ); ?>
		<?php else : ?>
			<div class="footer-col">
				<b><?php esc_html_e( 'دسته‌بندی‌ها', 'partyab' ); ?></b>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-shop',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'fallback_cb'    => 'partyab_footer_categories',
						'depth'          => 1,
					)
				);
				?>
			</div>
			<div class="footer-col">
				<b><?php esc_html_e( 'راهنما', 'partyab' ); ?></b>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-help',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'fallback_cb'    => 'partyab_footer_help',
						'depth'          => 1,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<div class="footer-col footer-col--hide">
			<b><?php esc_html_e( 'ارتباط با ما', 'partyab' ); ?></b>
			<ul class="footer-contact">
				<li><?php partyab_the_icon( 'map-pin' ); ?><span><?php echo esc_html( get_theme_mod( 'partyab_address', __( 'تهران، خیابان ولیعصر، پلاک ۱۲۴۵، طبقهٔ سوم', 'partyab' ) ) ); ?></span></li>
				<li><?php partyab_the_icon( 'phone' ); ?><span class="mono" dir="ltr"><?php echo esc_html( get_theme_mod( 'partyab_phone', '021-33947195' ) ); ?></span></li>
				<li><?php partyab_the_icon( 'mail' ); ?><span class="mono" dir="ltr"><?php echo esc_html( get_theme_mod( 'partyab_email', 'support@partyab.ir' ) ); ?></span></li>
			</ul>
		</div>
	</div>

	<div class="container footer-bottom">
		<span><?php printf( esc_html__( '© %s پارت‌یاب — تمام حقوق محفوظ است.', 'partyab' ), esc_html( partyab_num( wp_date( 'Y' ) ) ) ); ?></span>
		<div class="footer-pay">
			<span><?php esc_html_e( 'پرداخت آنلاین', 'partyab' ); ?></span>
			<span><?php esc_html_e( 'درگاه بانکی', 'partyab' ); ?></span>
			<span><?php esc_html_e( 'ارسال سراسری', 'partyab' ); ?></span>
		</div>
	</div>
</footer>

<div class="drawer" id="navDrawer" aria-hidden="true">
	<div class="drawer-backdrop" data-close-nav></div>
	<aside class="drawer-panel" role="dialog" aria-label="<?php esc_attr_e( 'منوی اصلی', 'partyab' ); ?>">
		<div class="drawer-head">
			<b><?php esc_html_e( 'منوی پارت‌یاب', 'partyab' ); ?></b>
			<button type="button" class="modal-close" data-close-nav aria-label="<?php esc_attr_e( 'بستن', 'partyab' ); ?>"><?php partyab_the_icon( 'x' ); ?></button>
		</div>
		<div class="drawer-body">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'fallback_cb'    => 'partyab_nav_fallback',
					'depth'          => 1,
				)
			);
			?>
		</div>
		<div class="drawer-foot">
			<a class="btn btn-amber btn-block" href="<?php echo esc_url( partyab_account_url() ); ?>">
				<?php partyab_the_icon( 'user' ); ?><?php esc_html_e( 'حساب کاربری من', 'partyab' ); ?>
			</a>
		</div>
	</aside>
</div>

<?php partyab_vehicle_picker(); ?>

<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'partyab' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-active' : ''; ?>"><?php partyab_the_icon( 'grid' ); ?><span><?php esc_html_e( 'خانه', 'partyab' ); ?></span></a>
	<a href="<?php echo esc_url( partyab_shop_url() ); ?>" class="<?php echo function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ? 'is-active' : ''; ?>"><?php partyab_the_icon( 'box' ); ?><span><?php esc_html_e( 'دسته‌ها', 'partyab' ); ?></span></a>
	<button type="button" data-open-vehicle><?php partyab_the_icon( 'garage' ); ?><span><?php esc_html_e( 'خودرو', 'partyab' ); ?></span></button>
	<a href="<?php echo esc_url( partyab_cart_url() ); ?>"><?php partyab_the_icon( 'cart' ); ?><span><?php esc_html_e( 'سبد', 'partyab' ); ?></span></a>
</nav>

<?php wp_footer(); ?>
</body>
</html>
