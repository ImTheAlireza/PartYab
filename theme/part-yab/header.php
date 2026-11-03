<?php
/**
 * هدر سایت پارتیاب.
 *
 * اگر در المنتور قالب «هدر» ساخته شود، همان نمایش داده می‌شود؛ در غیر این صورت این هدر.
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
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#pb-content">پرش به محتوا</a>

<?php if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) : ?>
	<?php // هدر ساخته‌شده در المنتور. ?>
<?php else : ?>

<header class="pb-header">

	<div class="pb-topbar">
		<div class="pb-container pb-topbar__inner">
			<span class="pb-topbar__item">ارسال رایگان سفارش‌های بالای ۵ میلیون تومان</span>
			<div class="pb-topbar__links">
				<a href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">خودروی من</a>
				<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">پیگیری سفارش</a>
				<a href="tel:02100000000" dir="ltr">۰۲۱-۰۰۰۰۰۰۰۰</a>
			</div>
		</div>
	</div>

	<div class="pb-header__main">
		<div class="pb-container pb-header__row">

			<button class="pb-nav-toggle" type="button" data-pb-nav-toggle aria-expanded="false" aria-label="منو">
				<span></span><span></span><span></span>
			</button>

			<div class="pb-brand">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a class="pb-brand__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url( PARTYAB_URI . '/assets/img/logo-horizontal.svg' ); ?>" width="180" height="40" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					</a>
				<?php endif; ?>
			</div>

			<form class="pb-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-pb-suggest-form>
				<label class="screen-reader-text" for="pb-search-field">جستجوی قطعه</label>
				<input
					id="pb-search-field"
					type="search"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="شماره فنی یا نام قطعه را بنویس…"
					autocomplete="off"
					role="combobox"
					aria-expanded="false"
					aria-controls="pb-suggest-results"
					aria-autocomplete="list"
					data-pb-suggest-input
				>
				<input type="hidden" name="post_type" value="product">
				<button type="submit" aria-label="جستجو">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
				</button>
				<div class="pb-suggest" id="pb-suggest-results" role="listbox" aria-label="پیشنهادهای جستجو" data-pb-suggest-results hidden></div>
			</form>

			<div class="pb-actions">
				<a class="pb-action" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account' ) ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
					<span>حساب من</span>
				</a>

				<a class="pb-action pb-action--cart" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' ) ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h2l2.2 10.2A2 2 0 0 0 10.2 17h7.6a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
					<span>سبد خرید</span>
					<?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
						<b class="pb-action__count"><?php echo esc_html( number_format_i18n( WC()->cart->get_cart_contents_count() ) ); ?></b>
					<?php endif; ?>
				</a>
			</div>
		</div>
	</div>

	<nav class="pb-nav" aria-label="منوی اصلی">
		<div class="pb-container pb-nav__inner">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'partyab-primary',
				'container'      => false,
				'menu_class'     => 'pb-nav__menu',
				'depth'          => 2,
				'fallback_cb'    => 'partyab_nav_fallback',
			) );
			?>
		</div>
	</nav>

</header>
<?php endif; ?>

<div id="pb-content" class="pb-site-content">
