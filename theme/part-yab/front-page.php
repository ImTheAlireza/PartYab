<?php
/**
 * صفحه اصلی فروشگاه.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="pb-hero">
	<div class="pb-container pb-hero__inner">
		<div class="pb-hero__text">
			<span class="pb-hero__eyebrow">قطعه درست، همان بار اول</span>
			<h1 class="pb-hero__title">قطعه‌ای که مطمئنی به خودرویت می‌خورد</h1>
			<p class="pb-hero__lead">شماره فنی قطعه را بنویس یا خودرویت را انتخاب کن؛ فقط قطعات سازگار را می‌بینی. ضمانت اصالت، ارسال سریع، مرجوعی ۷ روزه.</p>

			<form class="pb-hero__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="pb-hero-search">جستجوی قطعه</label>
				<input id="pb-hero-search" type="search" name="s" placeholder="مثلاً: لنت ترمز پراید یا 131-698">
				<input type="hidden" name="post_type" value="product">
				<button type="submit" class="pb-btn">جستجو</button>
			</form>

			<div class="pb-hero__cta">
				<a class="pb-btn pb-btn--ghost" href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">انتخاب خودروی من</a>
				<a class="pb-hero__link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' ) ); ?>">همه قطعات →</a>
			</div>
		</div>

		<div class="pb-hero__card">
			<div class="pb-hero__card-head">چرا پارتیاب؟</div>
			<ul class="pb-hero__list">
				<li>سازگاری هر قطعه با برند، مدل و سال خودرو — قبل از خرید</li>
				<li>شماره فنی روی قطعه، همان چیزی است که جستجو می‌شود</li>
				<li>فاکتور رسمی و ضمانت اصالت کالا</li>
				<li>مرجوعی ۷ روزه اگر سازگار نبود</li>
			</ul>
		</div>
	</div>
</section>

<section class="pb-section pb-section--car">
	<div class="pb-container">
		<?php
		if ( shortcode_exists( 'partyab_my_car' ) ) {
			echo do_shortcode( '[partyab_my_car]' );
		}
		?>
	</div>
</section>

<?php
$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'number'     => 6,
	'orderby'    => 'count',
	'order'      => 'DESC',
) );

if ( ! is_wp_error( $categories ) && $categories ) :
	?>
	<section class="pb-section">
		<div class="pb-container">
			<div class="pb-section__head">
				<h2>خرید بر اساس دسته قطعه</h2>
				<a href="<?php echo esc_url( home_url( '/shop' ) ); ?>">همه دسته‌ها →</a>
			</div>

			<div class="pb-cats">
				<?php foreach ( $categories as $category ) : ?>
					<?php if ( 'uncategorized' === $category->slug ) { continue; } ?>
					<a class="pb-cat" href="<?php echo esc_url( get_term_link( $category ) ); ?>">
						<span class="pb-cat__name"><?php echo esc_html( $category->name ); ?></span>
						<span class="pb-cat__count"><?php echo esc_html( number_format_i18n( $category->count ) ); ?> کالا</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;
?>

<section class="pb-section pb-section--muted">
	<div class="pb-container">
		<div class="pb-section__head">
			<h2>تازه‌ترین قطعات</h2>
			<a href="<?php echo esc_url( home_url( '/shop' ) ); ?>">همه محصولات →</a>
		</div>

		<?php
		if ( shortcode_exists( 'products' ) ) {
			echo do_shortcode( '[products limit="8" columns="4" orderby="date" order="DESC"]' );
		} else {
			echo '<p class="pb-note">ووکامرس فعال نیست؛ محصولات نمایش داده نمی‌شوند.</p>';
		}
		?>
	</div>
</section>

<section class="pb-section">
	<div class="pb-container pb-steps">
		<div class="pb-step">
			<span class="pb-step__num">۱</span>
			<strong>خودرو را انتخاب کن</strong>
			<p>برند، مدل و سال ساخت. یک‌بار؛ تا ۳۰ روز ذخیره می‌ماند.</p>
		</div>
		<div class="pb-step">
			<span class="pb-step__num">۲</span>
			<strong>قطعه سازگار را ببین</strong>
			<p>فقط قطعاتی که با خودروی تو می‌خوانند، با بج سبز.</p>
		</div>
		<div class="pb-step">
			<span class="pb-step__num">۳</span>
			<strong>سفارش بده</strong>
			<p>پرداخت آنلاین یا در محل، ارسال پیک تهران همان‌روز.</p>
		</div>
	</div>
</section>

<?php
get_footer();
