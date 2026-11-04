<?php
/** Helpful, branded not-found state. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="pb-container"><section class="pb-not-found" data-reveal><span class="pb-not-found__number" aria-hidden="true">۴۰۴</span><span class="pb-eyebrow">یک پیچ کوچک در مسیر</span><h1>این صفحه را پیدا نکردیم.</h1><p>ممکن است نشانی تغییر کرده باشد. از جستجو شروع کن یا به فروشگاه برگرد.</p>
<form class="pb-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"><input type="search" name="s" placeholder="نام قطعه یا شماره فنی…" aria-label="جستجو در سایت"><button class="pb-btn" type="submit"><?php echo partyab_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>جستجو</button></form>
<div class="partyab-empty__actions"><a class="pb-btn pb-btn--navy" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به خانه</a><a class="pb-btn pb-btn--ghost" href="<?php echo esc_url( home_url( '/shop' ) ); ?>">رفتن به فروشگاه</a></div></section></div>
<?php get_footer(); ?>
