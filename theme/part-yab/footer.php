<?php
/**
 * Shared footer with real navigation and configurable contact channels.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- #pb-content -->
<?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) : ?>
<?php else : ?>
<footer class="pb-footer">
	<div class="pb-container">
		<div class="pb-footer__cta">
			<span class="pb-footer__cta-icon"><?php echo partyab_icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div><h2>مطمئن نباشی، با هم پیدایش می‌کنیم.</h2><p>مدل خودرو یا شماره فنی را بفرست؛ برای انتخاب قطعه راهنمایی‌ات می‌کنیم.</p></div>
			<a class="pb-btn pb-btn--white" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">گفتگو با پشتیبانی <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<div class="pb-footer__grid">
			<div class="pb-footer__col pb-footer__col--brand">
				<?php partyab_logo(); ?>
				<p class="pb-footer__about">قطعه درست برای خودروی درست. فروشگاه تخصصی لوازم یدکی با تطبیق دقیق قطعه و خودرو، تأمین معتبر و همراهی در تمام مسیر خرید.</p>
				<div class="pb-footer__trust"><span><?php echo partyab_icon( 'verified' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>ضمانت اصالت</span><span><?php echo partyab_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>فاکتور رسمی</span></div>
			</div>
			<div class="pb-footer__col"><h3>دسترسی سریع</h3><?php wp_nav_menu( array( 'theme_location' => 'partyab-footer', 'container' => false, 'menu_class' => 'pb-footer__menu', 'depth' => 1, 'fallback_cb' => 'partyab_footer_fallback' ) ); ?></div>
			<div class="pb-footer__col"><h3>خدمات مشتریان</h3><ul class="pb-footer__menu">
				<li><a href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">گاراژ من</a></li>
				<li><a href="<?php echo esc_url( home_url( '/faq' ) ); ?>">سؤال‌های پرتکرار</a></li>
				<li><a href="<?php echo esc_url( home_url( '/warranty' ) ); ?>">شرایط گارانتی</a></li>
				<li><a href="<?php echo esc_url( home_url( '/returns' ) ); ?>">مرجوعی و تعویض</a></li>
				<li><a href="<?php echo esc_url( home_url( '/guide' ) ); ?>">راهنمای خرید</a></li>
			</ul></div>
			<div class="pb-footer__col"><h3>در ارتباط باشیم</h3>
				<?php $partyab_channels = function_exists( 'partyab_contact_channels' ) ? partyab_contact_channels() : array(); ?>
				<?php foreach ( array( 'address', 'phone', 'email' ) as $partyab_key ) : ?>
					<?php if ( empty( $partyab_channels[ $partyab_key ] ) ) { continue; } ?>
					<?php $partyab_channel = $partyab_channels[ $partyab_key ]; ?>
					<div class="pb-footer__contact"><?php echo partyab_icon( 'address' === $partyab_key ? 'pin' : ( 'phone' === $partyab_key ? 'phone' : 'mail' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><small><?php echo esc_html( $partyab_channel['label'] ); ?></small><?php if ( ! empty( $partyab_channel['href'] ) ) : ?><a href="<?php echo esc_url( $partyab_channel['href'] ); ?>"><b dir="auto"><?php echo esc_html( $partyab_channel['value'] ); ?></b></a><?php else : ?><b><?php echo esc_html( $partyab_channel['value'] ); ?></b><?php endif; ?></div></div>
				<?php endforeach; ?>
				<div class="pb-footer__contact"><?php echo partyab_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><small>ساعت پاسخگویی</small><b>شنبه تا پنجشنبه، ۹ تا ۱۸</b></div></div>
			</div>
		</div>
		<div class="pb-footer__bottom-inner"><span>© <?php echo esc_html( partyab_fa_digits( gmdate( 'Y' ) ) ); ?> پارتیاب — تمامی حقوق محفوظ است.</span><div class="pb-footer__pay"><span>پرداخت امن</span><span>درگاه بانکی</span><span>فاکتور رسمی</span></div><button class="pb-motion-toggle" type="button" data-motion-toggle aria-pressed="false"><?php echo partyab_icon( 'spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>توقف انیمیشن‌ها</span></button><button class="pb-motion-toggle" type="button" data-sound-toggle aria-pressed="true" hidden><span>خاموش کردن صدا</span></button></div>
	</div>
</footer>
<button class="pb-top" type="button" data-pb-top aria-label="بازگشت به بالا"><?php echo partyab_icon( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
