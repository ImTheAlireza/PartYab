<?php
/**
 * Shared site header. Elementor headers remain supported.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0A1B33">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#pb-content">پرش به محتوا</a>
<?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) : ?>
<?php else : ?>
<header class="pb-header">
	<div class="pb-topbar">
		<div class="pb-container pb-topbar__inner">
			<span class="pb-topbar__item"><?php echo partyab_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><strong>قطعهٔ درست، از انتخاب مطمئن شروع می‌شود.</strong></span>
			<div class="pb-topbar__links">
				<a href="<?php echo esc_url( home_url( '/about' ) ); ?>">درباره پارتیاب</a>
				<a href="<?php echo esc_url( home_url( '/faq' ) ); ?>">راهنمای خرید</a>
				<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">پشتیبانی و پیگیری</a>
			</div>
		</div>
	</div>
	<div class="pb-header__main">
		<div class="pb-container pb-header__row">
			<button class="pb-nav-toggle" type="button" data-pb-nav-toggle aria-expanded="false" aria-controls="pb-primary-nav" aria-label="باز کردن منو"><span></span><span></span><span></span></button>
			<div class="pb-brand">
				<?php if ( has_custom_logo() ) : ?><?php the_custom_logo(); ?><?php else : ?><?php partyab_logo(); ?><?php endif; ?>
			</div>
			<form class="pb-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-pb-suggest-form>
				<label class="screen-reader-text" for="pb-search-field">نام قطعه یا شماره فنی</label>
				<input id="pb-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="نام قطعه، شماره فنی یا مدل خودرو…" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="pb-suggest-results" aria-autocomplete="list" data-pb-suggest-input>
				<input type="hidden" name="post_type" value="product">
				<button type="submit"><?php echo partyab_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>جستجو</span></button>
				<div class="pb-suggest" id="pb-suggest-results" role="listbox" aria-label="پیشنهادهای جستجو" data-pb-suggest-results hidden></div>
			</form>
			<div class="pb-actions">
				<a class="pb-action" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account' ) ); ?>" aria-label="حساب کاربری"><?php echo partyab_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>حساب من</span></a>
				<span class="pb-actions__sep" aria-hidden="true"></span>
				<a class="pb-action pb-action--cart" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' ) ); ?>" aria-label="سبد خرید"><?php echo partyab_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>سبد خرید</span><b class="pb-action__count"><?php echo esc_html( number_format_i18n( function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?></b></a>
			</div>
		</div>
	</div>
	<nav class="pb-nav" id="pb-primary-nav" aria-label="منوی اصلی">
		<div class="pb-container pb-nav__inner">
			<?php wp_nav_menu( array( 'theme_location' => 'partyab-primary', 'container' => false, 'menu_class' => 'pb-nav__menu', 'depth' => 2, 'fallback_cb' => 'partyab_nav_fallback' ) ); ?>
			<a class="pb-nav__support" href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php echo partyab_icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>برای انتخاب قطعه <b>کنارت هستیم</b></span></a>
		</div>
	</nav>
</header>
<?php endif; ?>
<div id="pb-content" class="pb-site-content">
