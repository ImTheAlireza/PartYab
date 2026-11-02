<?php
/**
 * فوتر سایت پارتیاب.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- #pb-content -->

<?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) : ?>
	<?php // فوتر ساخته‌شده در المنتور. ?>
<?php else : ?>

<footer class="pb-footer">

	<div class="pb-trust">
		<div class="pb-container pb-trust__grid">
			<div class="pb-trust__item">
				<strong>ضمانت اصالت کالا</strong>
				<span>همه قطعات با فاکتور و ضمانت</span>
			</div>
			<div class="pb-trust__item">
				<strong>ارسال سریع</strong>
				<span>پیک تهران همان‌روز</span>
			</div>
			<div class="pb-trust__item">
				<strong>مرجوعی ۷ روزه</strong>
				<span>اگر سازگار نبود، برگردان</span>
			</div>
			<div class="pb-trust__item">
				<strong>پشتیبانی فنی</strong>
				<span>راهنمای انتخاب قطعه</span>
			</div>
		</div>
	</div>

	<div class="pb-footer__main">
		<div class="pb-container pb-footer__grid">

			<div class="pb-footer__col pb-footer__col--brand">
				<img src="<?php echo esc_url( PARTYAB_URI . '/assets/img/logo-mono-white.svg' ); ?>" width="170" height="38" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<p>قطعه درست برای خودروی درست. سازگاری هر قطعه با برند، مدل و سال خودرو پیش از خرید مشخص است.</p>
			</div>

			<div class="pb-footer__col">
				<h3>دسترسی سریع</h3>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'partyab-footer',
					'container'      => false,
					'menu_class'     => 'pb-footer__menu',
					'depth'          => 1,
					'fallback_cb'    => 'partyab_footer_fallback',
				) );
				?>
			</div>

			<div class="pb-footer__col">
				<h3>خدمات مشتریان</h3>
				<ul class="pb-footer__menu">
					<li><a href="<?php echo esc_url( home_url( '/faq' ) ); ?>">سوال‌های پرتکرار</a></li>
					<li><a href="<?php echo esc_url( home_url( '/warranty' ) ); ?>">شرایط گارانتی</a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms' ) ); ?>">شرایط مرجوعی</a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">تماس با ما</a></li>
				</ul>
			</div>

			<div class="pb-footer__col">
				<h3>ارتباط</h3>
				<ul class="pb-footer__menu">
					<li><a href="tel:02100000000" dir="ltr">۰۲۱-۰۰۰۰۰۰۰۰</a></li>
					<li>شنبه تا پنجشنبه، ۹ تا ۱۸</li>
					<li><a href="mailto:info@example.com" dir="ltr">info@example.com</a></li>
				</ul>
			</div>

		</div>
	</div>

	<div class="pb-footer__bottom">
		<div class="pb-container pb-footer__bottom-inner">
			<span>کلیه حقوق محفوظ است.</span>
			<span class="pb-footer__pay">پرداخت امن · درگاه بانکی · فاکتور رسمی</span>
		</div>
	</div>

</footer>

<button class="pb-top" type="button" data-pb-top aria-label="بازگشت به بالا">↑</button>

<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
