<?php
/**
 * Header.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'پرش به محتوای اصلی', 'partyab' ); ?></a>

<header class="site-header">
	<div class="container header-main">
		<div class="logo-wrap">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'پارت‌یاب، صفحه اصلی', 'partyab' ); ?>">
					<span class="logo-mark">
						<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<circle cx="10.4" cy="10.4" r="6.6" stroke="#F59E0B" stroke-width="2"/>
							<path d="M15.4 15.4 21 21" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
							<circle cx="10.4" cy="10.4" r="2.1" stroke="#fff" stroke-width="1.5"/>
							<path d="M10.4 5.5v1.5M10.4 13.8v1.5M5.5 10.4H7M13.8 10.4h1.5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/>
						</svg>
					</span>
					<span class="logo-text">
						<strong><?php bloginfo( 'name' ); ?></strong>
						<span><?php esc_html_e( 'قطعهٔ درست، برای خودروی درست', 'partyab' ); ?></span>
					</span>
				</a>
			<?php endif; ?>
		</div>

		<div class="header-search">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'کد فنی، نام قطعه یا برند', 'partyab' ); ?>" aria-label="<?php esc_attr_e( 'جست‌وجو', 'partyab' ); ?>">
				<input type="hidden" name="post_type" value="product">
				<button class="search-submit" type="submit" aria-label="<?php esc_attr_e( 'جست‌وجو', 'partyab' ); ?>">
					<?php partyab_the_icon( 'search' ); ?>
					<span><?php esc_html_e( 'جست‌وجو', 'partyab' ); ?></span>
				</button>
			</form>
		</div>

		<div class="header-actions">
			<button class="nav-toggle icon-btn" type="button" data-open-nav aria-label="<?php esc_attr_e( 'منو', 'partyab' ); ?>" aria-expanded="false">
				<?php partyab_the_icon( 'menu' ); ?>
			</button>
			<a class="icon-btn" href="<?php echo esc_url( partyab_account_url() ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'partyab' ); ?>">
				<?php partyab_the_icon( 'user' ); ?>
			</a>
			<a class="cart-btn" href="<?php echo esc_url( partyab_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'partyab' ); ?>">
				<?php partyab_the_icon( 'cart' ); ?>
				<span class="cart-label"><?php esc_html_e( 'سبد خرید', 'partyab' ); ?></span>
				<?php
				$partyab_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
				?>
				<span class="cart-count <?php echo $partyab_count ? '' : 'is-zero'; ?>"><?php echo esc_html( partyab_num( $partyab_count ) ); ?></span>
			</a>
		</div>
	</div>

	<?php partyab_passport_bar(); ?>
</header>

<main id="main">
