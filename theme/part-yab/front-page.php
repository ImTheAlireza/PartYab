<?php
/**
 * Automotive storefront. Existing Elementor-built front pages are preserved.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

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
	<div class="pb-hero__glow" aria-hidden="true"></div><div class="pb-hero__grid" aria-hidden="true"></div>
	<div class="pb-container pb-hero__inner">
		<div class="pb-hero__text">
			<span class="pb-hero__eyebrow"><?php echo partyab_icon( 'spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>انتخاب هوشمند، خرید مطمئن</span>
			<h1 class="pb-hero__title">قطعه‌ای درست،<span>برای خودروی شما.</span></h1>
			<p class="pb-hero__lead">از اولین استارت تا هر کیلومتر از مسیر؛ قطعه‌ای را پیدا کن که واقعاً به خودرویت می‌خورد. ساده، دقیق و با خیال راحت.</p>
			<div class="pb-hero__cta"><a class="pb-btn pb-btn--primary pb-btn--lg" href="<?php echo esc_url( $partyab_shop_url ); ?>">کشف قطعات <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><a class="pb-btn pb-btn--glass pb-btn--lg" href="<?php echo esc_url( home_url( '/my-car' ) ); ?>"><?php echo partyab_icon( 'car' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>انتخاب خودروی من</a></div>
			<div class="pb-hero__stats"><div class="pb-hero__stat"><b><?php echo partyab_icon( 'verified' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>اصالت کالا</b><span>تأمین از منبع معتبر</span></div><div class="pb-hero__stat"><b><?php echo partyab_icon( 'car' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>تطبیق دقیق</b><span>بر اساس مدل خودرو</span></div><div class="pb-hero__stat"><b><?php echo partyab_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>ارسال سراسری</b><span>هر جای ایران که باشی</span></div></div>
		</div>
		<div class="pb-hero__scene" data-scene><div class="pb-hero__stage"><div class="pb-hero__orbit"></div><canvas class="pb-hero__canvas" data-hero-car data-model="<?php echo esc_url( add_query_arg( 'ver', partyab_asset_version( 'assets/models/brick-car.glb' ), PARTYAB_URI . '/assets/models/brick-car.glb' ) ); ?>" aria-hidden="true"></canvas><img class="pb-hero__car pb-hero__car--fallback" src="<?php echo esc_url( PARTYAB_URI . '/assets/img/hero-car-poster.webp' ); ?>" alt="" width="671" height="454" fetchpriority="high"><div class="pb-hero__live">برای مسیرهای پیش رو</div><span class="pb-hero__credit">سه‌بعدی: کانسپت آجری پارتیاب</span><div class="pb-hero__float pb-hero__float--one"><?php echo partyab_icon( 'verified' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><b>قطعهٔ درست، انتخاب مطمئن</b><small>بررسی سازگاری پیش از خرید</small></div></div><div class="pb-hero__float pb-hero__float--two"><?php echo partyab_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><b>با خیال راحت بران</b><small>ضمانت اصالت کالا</small></div></div></div></div>
	</div>
</section>
<section class="pb-section pb-section--car"><div class="pb-container">
	<?php if ( shortcode_exists( 'partyab_my_car' ) ) : ?><?php echo do_shortcode( '[partyab_my_car]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php else : ?><div class="pb-car"><div class="pb-car__head"><?php echo partyab_icon( 'car' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><p class="pb-car__current">از خودروی خودت شروع کن.</p><a class="pb-btn pb-btn--sm" href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">انتخاب خودرو</a></div></div><?php endif; ?>
</div></section>
<section class="pb-section"><div class="pb-container"><?php partyab_services(); ?></div></section>
<?php
$partyab_categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC', 'exclude' => array( get_option( 'default_product_cat' ) ) ) );
if ( ! is_wp_error( $partyab_categories ) && $partyab_categories ) :
	?>
	<section class="pb-section"><div class="pb-container">
		<?php partyab_section_heading( 'هر قطعه، سر جای خودش', 'از کدام بخش خودرو شروع می‌کنی؟', $partyab_shop_url, 'همه دسته‌ها' ); ?>
		<div class="pb-cats"><?php foreach ( $partyab_categories as $partyab_category ) : ?>
			<a class="pb-cat" data-reveal href="<?php echo esc_url( (string) get_term_link( $partyab_category ) ); ?>"><span class="pb-cat__icon"><?php echo partyab_design_category_icon( $partyab_category->slug ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="pb-cat__name"><?php echo esc_html( $partyab_category->name ); ?></span><span class="pb-cat__count"><?php echo esc_html( number_format_i18n( $partyab_category->count ) ); ?> قطعه</span></a>
		<?php endforeach; ?></div>
	</div></section>
<?php endif; ?>
<section class="pb-section"><div class="pb-container">
	<?php partyab_section_heading( 'انتخاب امروز، کیفیت هر روز', 'قطعات منتخب فروشگاه را بررسی کن', $partyab_shop_url, 'همه محصولات' ); ?>
	<?php if ( shortcode_exists( 'products' ) ) : ?><div data-reveal><?php echo do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php else : ?><p class="pb-note">برای نمایش محصولات، ووکامرس را فعال کنید.</p><?php endif; ?>
</div></section>
<?php
$partyab_on_sale = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
if ( $partyab_on_sale ) :
	?>
	<section class="pb-section"><div class="pb-container"><div class="pb-deals" data-reveal><header class="pb-section__head"><div class="pb-deals__head"><div><h2><?php echo partyab_icon( 'bolt' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>پیشنهاد ویژه پارتیاب</h2><p>فرصت خرید قطعات تخفیف‌دار</p></div></div><a class="pb-section__link" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $partyab_shop_url ) ); ?>">همه پیشنهادها <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></header><?php echo do_shortcode( '[products limit="4" columns="4" on_sale="true"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div></section>
<?php endif; ?>
<section class="pb-section"><div class="pb-container pb-banners">
	<?php
	$partyab_banner_data = array(
		array( 'brake-system', 'dark', 'سیستم ترمز', 'اعتماد به ترمز،<br>آرامش در مسیر.', 'banner-brake.webp' ),
		array( 'suspension', 'warm', 'جلوبندی و تعلیق', 'مسیر ناهموار،<br>سواری نرم‌تر.', 'banner-suspension.webp' ),
	);
	foreach ( $partyab_banner_data as $partyab_banner ) :
		$partyab_term = get_term_by( 'slug', $partyab_banner[0], 'product_cat' );
		$partyab_link = $partyab_term && ! is_wp_error( $partyab_term ) ? (string) get_term_link( $partyab_term ) : $partyab_shop_url;
		?>
		<a class="pb-banner pb-banner--<?php echo esc_attr( $partyab_banner[1] ); ?>" data-reveal href="<?php echo esc_url( $partyab_link ); ?>"><span class="pb-banner__text"><span class="pb-banner__kicker"><?php echo esc_html( $partyab_banner[2] ); ?></span><strong class="pb-banner__title"><?php echo wp_kses( $partyab_banner[3], array( 'br' => array() ) ); ?></strong><span class="pb-banner__cta">دیدن قطعات <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></span><img class="pb-banner__img" src="<?php echo esc_url( PARTYAB_URI . '/assets/img/' . $partyab_banner[4] ); ?>" alt="<?php echo esc_attr( $partyab_banner[2] ); ?>" width="900" height="687" loading="lazy"></a>
	<?php endforeach; ?>
</div></section>
<?php
$partyab_brands = function_exists( 'partyab_brand_terms' ) ? partyab_brand_terms() : array();
if ( $partyab_brands ) :
	?>
	<section class="pb-section"><div class="pb-container"><?php partyab_section_heading( 'برندهایی که می‌شناسی', 'انتخاب قطعه بر اساس برند سازنده' ); ?><div class="pb-marquee"><div class="pb-marquee__track"><?php for ( $partyab_repeat = 0; $partyab_repeat < 2; $partyab_repeat++ ) : ?><div class="pb-marquee__group" <?php echo $partyab_repeat ? 'aria-hidden="true"' : ''; ?>><?php foreach ( $partyab_brands as $partyab_brand ) : ?><?php $partyab_brand_logo = partyab_brand_logo( $partyab_brand->name ); ?><a class="pb-brand-tile" href="<?php echo esc_url( (string) get_term_link( $partyab_brand ) ); ?>" <?php echo $partyab_repeat ? 'tabindex="-1"' : ''; ?> aria-label="<?php echo esc_attr( $partyab_brand->name ); ?>"><?php if ( $partyab_brand_logo ) : ?><img src="<?php echo esc_url( $partyab_brand_logo ); ?>" alt="<?php echo esc_attr( $partyab_brand->name ); ?>" width="360" height="120" loading="lazy"><?php else : ?><b><?php echo esc_html( $partyab_brand->name ); ?></b><?php endif; ?><span><?php echo esc_html( number_format_i18n( $partyab_brand->count ) ); ?> قطعه</span></a><?php endforeach; ?></div><?php endfor; ?></div></div></div></section>
<?php endif; ?>
<section class="pb-section"><div class="pb-container"><?php partyab_section_heading( 'سه قدم تا قطعهٔ درست', 'انتخاب قطعه قرار نیست پیچیده باشد.', home_url( '/guide' ), 'راهنمای خرید' ); ?><div class="pb-steps">
	<div class="pb-step" data-reveal><span class="pb-step__num">۱</span><strong>خودرویت را انتخاب کن</strong><p>برند، مدل و سال ساخت را وارد کن تا مسیر انتخاب دقیق‌تر شود.</p></div>
	<div class="pb-step" data-reveal><span class="pb-step__num">۲</span><strong>سازگاری را بررسی کن</strong><p>شماره فنی و جدول سازگاری قطعه را با خودروی خودت تطبیق بده.</p></div>
	<div class="pb-step" data-reveal><span class="pb-step__num">۳</span><strong>با اطمینان سفارش بده</strong><p>قطعه را به سبد اضافه کن و سفارشت را از حساب کاربری پیگیری کن.</p></div>
</div></div></section>
<section class="pb-section"><div class="pb-container"><div class="pb-editorial" data-reveal><div><span class="pb-eyebrow">دانشنامه فنی پارتیاب</span><h2>قبل از خرید،<br>کمی بیشتر بدان.</h2><p>نگهداری درست، از شناخت قطعات شروع می‌شود. راهنماهای کاربردی برای انتخاب آگاهانه و مراقبت بهتر از خودرو.</p><a class="pb-btn pb-btn--navy" href="<?php echo esc_url( home_url( '/guide' ) ); ?>">خواندن راهنماها <?php echo partyab_icon( 'arrow-l' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></div><div class="pb-editorial__visual" aria-hidden="true"><?php echo partyab_icon( 'gear' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div></div></section>
<?php get_footer(); ?>
