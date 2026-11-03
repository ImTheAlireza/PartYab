<?php
/**
 * صفحه اصلی فروشگاه (دمو ۱).
 *
 * دو حالت دارد:
 *  ۱) اگر صفحه اصلی در المنتور ساخته شده باشد، همان محتوا نمایش داده می‌شود
 *     تا کاربر بتواند آزادانه با المنتور ویرایش کند.
 *  ۲) در غیر این صورت، دموی آماده‌ی پارتیاب.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/* ---------- حالت ۱: صفحه اصلی ساخته‌شده با المنتور ---------- */
$partyab_front_id = (int) get_option( 'page_on_front' );

if ( $partyab_front_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
	$partyab_document = \Elementor\Plugin::$instance->documents->get( $partyab_front_id );

	if ( $partyab_document && $partyab_document->is_built_with_elementor() ) {
		get_header();

		while ( have_posts() ) {
			the_post();
			the_content();
		}

		get_footer();

		return;
	}
}

get_header();

$partyab_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
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
				<a class="pb-hero__link" href="<?php echo esc_url( $partyab_shop_url ); ?>">همه قطعات →</a>
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
$partyab_categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'number'     => 6,
	'orderby'    => 'count',
	'order'      => 'DESC',
) );

if ( ! is_wp_error( $partyab_categories ) && $partyab_categories ) :
	?>
	<section class="pb-section">
		<div class="pb-container">
			<div class="pb-section__head">
				<h2>خرید بر اساس دسته قطعه</h2>
				<a href="<?php echo esc_url( $partyab_shop_url ); ?>">همه دسته‌ها →</a>
			</div>

			<div class="pb-cats">
				<?php
				foreach ( $partyab_categories as $partyab_index => $partyab_category ) :
					if ( 'uncategorized' === $partyab_category->slug ) {
						continue;
					}
					?>
					<a class="pb-cat" href="<?php echo esc_url( (string) get_term_link( $partyab_category ) ); ?>">
						<span class="pb-cat__icon" aria-hidden="true"><?php echo partyab_category_icon( $partyab_index ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="pb-cat__name"><?php echo esc_html( $partyab_category->name ); ?></span>
						<span class="pb-cat__count"><?php echo esc_html( number_format_i18n( $partyab_category->count ) ); ?> محصول</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;

/* ---------- بنرهای دوگانه ---------- */
$partyab_banner_terms = array(
	'brake'      => get_term_by( 'slug', 'brake-system', 'product_cat' ),
	'suspension' => get_term_by( 'slug', 'suspension', 'product_cat' ),
);

$partyab_banner_links = array(
	'brake'      => $partyab_banner_terms['brake'] ? (string) get_term_link( $partyab_banner_terms['brake'] ) : $partyab_shop_url,
	'suspension' => $partyab_banner_terms['suspension'] ? (string) get_term_link( $partyab_banner_terms['suspension'] ) : $partyab_shop_url,
);
?>
<section class="pb-section pb-section--banners">
	<div class="pb-container pb-banners">

		<a class="pb-banner pb-banner--dark" href="<?php echo esc_url( $partyab_banner_links['brake'] ); ?>">
			<span class="pb-banner__text">
				<span class="pb-banner__kicker">سیستم ترمز</span>
				<strong class="pb-banner__title">لنت و دیسک ترمز، سازگار با خودروی تو</strong>
				<span class="pb-banner__cta">دیدن قطعات ترمز →</span>
			</span>
			<img class="pb-banner__img" src="<?php echo esc_url( PARTYAB_URI . '/assets/img/banner-brake.jpg' ); ?>" alt="لنت و دیسک ترمز" loading="lazy" width="1024" height="1024">
		</a>

		<a class="pb-banner pb-banner--warm" href="<?php echo esc_url( $partyab_banner_links['suspension'] ); ?>">
			<span class="pb-banner__text">
				<span class="pb-banner__kicker">جلوبندی و تعلیق</span>
				<strong class="pb-banner__title">کمک فنر و فنر، برای رانندگی نرم‌تر</strong>
				<span class="pb-banner__cta">دیدن قطعات تعلیق →</span>
			</span>
			<img class="pb-banner__img" src="<?php echo esc_url( PARTYAB_URI . '/assets/img/banner-suspension.jpg' ); ?>" alt="کمک فنر" loading="lazy" width="1024" height="1024">
		</a>

	</div>
</section>
<?php

/* ---------- پیشنهاد شگفت‌انگیز: فقط اگر محصول تخفیف‌دار وجود دارد ---------- */
$partyab_on_sale = function_exists( 'wc_get_products' ) ? wc_get_products( array(
	'limit'   => 4,
	'status'  => 'publish',
	'include' => wc_get_product_ids_on_sale(),
) ) : array();

if ( $partyab_on_sale ) :
	?>
	<section class="pb-section pb-deals">
		<div class="pb-container">
			<div class="pb-section__head">
				<h2 class="pb-deals__title">پیشنهاد شگفت‌انگیز <span class="pb-deals__pill">تخفیف‌دار</span></h2>
				<a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $partyab_shop_url ) ); ?>">همه تخفیف‌ها →</a>
			</div>
			<?php echo do_shortcode( '[products limit="4" columns="4" on_sale="true"]' ); ?>
		</div>
	</section>
	<?php
endif;
?>

<section class="pb-section pb-section--muted">
	<div class="pb-container">
		<div class="pb-section__head">
			<h2>تازه‌ترین قطعات</h2>
			<a href="<?php echo esc_url( $partyab_shop_url ); ?>">همه محصولات →</a>
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

<?php
$partyab_brands = function_exists( 'partyab_brand_terms' ) ? partyab_brand_terms() : array();

if ( $partyab_brands ) :
	?>
	<section class="pb-section pb-brands-section">
		<div class="pb-container">
			<div class="pb-section__head">
				<h2>برندهای قطعه</h2>
				<a href="<?php echo esc_url( $partyab_shop_url ); ?>">همه برندها →</a>
			</div>

			<div class="pb-brands">
				<?php foreach ( $partyab_brands as $partyab_brand ) : ?>
					<a class="pb-brand-chip" href="<?php echo esc_url( (string) get_term_link( $partyab_brand ) ); ?>">
						<strong><?php echo esc_html( $partyab_brand->name ); ?></strong>
						<span><?php echo esc_html( number_format_i18n( $partyab_brand->count ) ); ?> قطعه</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;
?>

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

<section class="pb-section pb-section--muted">
	<div class="pb-container">
		<div class="pb-section__head">
			<h2>مشتری‌ها چه می‌گویند</h2>
		</div>

		<div class="pb-quotes">
			<figure class="pb-quote">
				<blockquote>قطعه‌ای که فرستادید دقیقاً به مدل ۱۳۹۵ من خورد؛ برخلاف دفعه قبل که دو بار مرجوع کردم.</blockquote>
				<figcaption>رضا — پراید ۱۳۱</figcaption>
			</figure>
			<figure class="pb-quote">
				<blockquote>قبل از خرید سازگاری را دیدم، پس بدون سوال سفارش دادم. بسته‌بندی و ارسال هم سریع بود.</blockquote>
				<figcaption>مریم — پژو پارس</figcaption>
			</figure>
			<figure class="pb-quote">
				<blockquote>جستجو با شماره فنی خیلی کمک کرد؛ در دو دقیقه فیلتر روغن درست را پیدا کردم.</blockquote>
				<figcaption>حسین — سمند EF7</figcaption>
			</figure>
		</div>
	</div>
</section>

<?php
get_footer();
