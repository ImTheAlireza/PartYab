<?php
/**
 * Template Name: قطعه‌یاب کد فنی
 * Description: جست‌وجوی قطعه با کد فنی / کد معادل.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();

$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$code = trim( $code );

$products = array();

if ( '' !== $code ) {
	$ids = array();

	/* ۱) تطابق دقیق کد کالا (SKU) */
	if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
		$exact = (int) wc_get_product_id_by_sku( $code );
		if ( $exact ) {
			$ids[] = $exact;
		}
	}

	/* ۲) جست‌وجوی متنی ووکامرس (عنوان، توضیح، SKU) */
	$query = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			's'                   => $code,
			'posts_per_page'      => 12,
			'ignore_sticky_posts' => true,
			'fields'              => 'ids',
		)
	);

	foreach ( (array) $query->posts as $pid ) {
		$ids[] = (int) $pid;
	}

	/* ۳) کدهای معادل ذخیره‌شده در متای کالا: _partyab_equiv (با کاما جدا شده) */
	$meta = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_partyab_equiv',
					'value'   => $code,
					'compare' => 'LIKE',
				),
			),
		)
	);

	foreach ( (array) $meta->posts as $pid ) {
		$ids[] = (int) $pid;
	}

	$ids = array_values( array_unique( array_filter( $ids ) ) );

	foreach ( $ids as $pid ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $pid ) : null;
		if ( $product ) {
			$products[] = $product;
		}
	}
}
?>

<main id="main">

	<section class="code-hero">
		<div class="container">
			<span class="eyebrow-line"><?php partyab_the_icon( 'spark' ); ?> <?php esc_html_e( 'قطعه‌یاب کد فنی', 'partyab' ); ?></span>
			<h1><?php esc_html_e( 'کد فنی را بزن، معادل‌هایش را بگیر', 'partyab' ); ?></h1>
			<p><?php esc_html_e( 'کدی که روی قطعهٔ قدیمی، جعبه یا فاکتور دارید را وارد کنید تا قطعه و کدهای معادلش را پیدا کنیم.', 'partyab' ); ?></p>

			<form class="code-form" role="search" method="get" action="<?php echo esc_url( partyab_code_url() ); ?>">
				<div class="cf-field">
					<?php partyab_the_icon( 'search' ); ?>
					<label class="sr-only" for="codeInput"><?php esc_html_e( 'کد فنی یا کد معادل', 'partyab' ); ?></label>
					<input id="codeInput" type="text" name="code" value="<?php echo esc_attr( $code ); ?>" placeholder="<?php esc_attr_e( 'مثلاً 4253.42', 'partyab' ); ?>" spellcheck="false" dir="ltr">
				</div>
				<button class="btn btn-amber" type="submit"><?php partyab_the_icon( 'search' ); ?> <?php esc_html_e( 'پیدا کن', 'partyab' ); ?></button>
			</form>

			<div class="code-hints">
				<span><?php esc_html_e( 'کد را ندارید؟', 'partyab' ); ?></span>
				<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'جست‌وجو در فروشگاه', 'partyab' ); ?></a>
				<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( partyab_trust_url() ); ?>"><?php esc_html_e( 'تضمین سازگاری کد', 'partyab' ); ?></a>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'نتیجه', 'partyab' ); ?></span>
					<h2 class="section-title">
						<?php
						if ( '' === $code ) {
							esc_html_e( 'کد فنی خود را جست‌وجو کنید', 'partyab' );
						} else {
							/* translators: %s: searched code */
							printf( esc_html__( 'نتیجه برای «%s»', 'partyab' ), esc_html( $code ) );
						}
						?>
					</h2>
					<p class="section-sub">
						<?php
						if ( '' === $code ) {
							esc_html_e( 'کد کالا (SKU) و کدهای معادل ثبت‌شده برای هر قطعه جست‌وجو می‌شود.', 'partyab' );
						} else {
							/* translators: %s: number of results */
							printf( esc_html__( '%s قطعه پیدا شد.', 'partyab' ), esc_html( partyab_num( count( $products ) ) ) );
						}
						?>
					</p>
				</div>
			</div>

			<?php if ( '' !== $code && ! $products ) : ?>
				<div class="code-empty">
					<b><?php esc_html_e( 'این کد در کاتالوگ پیدا نشد', 'partyab' ); ?></b>
					<?php esc_html_e( 'ممکن است کد قدیمی یا جایگزین باشد. کد را برای کارشناس بفرستید تا معادلش را پیدا کنیم.', 'partyab' ); ?>
					<p style="margin-top:12px">
						<a class="btn btn-navy btn-sm" href="<?php echo esc_url( get_theme_mod( 'partyab_whatsapp', 'https://wa.me/989121234567' ) ); ?>" target="_blank" rel="noopener"><?php partyab_the_icon( 'whatsapp' ); ?> <?php esc_html_e( 'ارسال کد در واتساپ', 'partyab' ); ?></a>
					</p>
				</div>
			<?php elseif ( $products ) : ?>
				<?php foreach ( $products as $product ) : ?>
					<?php
					$pid   = $product->get_id();
					$equiv = (string) get_post_meta( $pid, '_partyab_equiv', true );
					$codes = array_filter( array_map( 'trim', explode( ',', $equiv ) ) );
					$sku   = $product->get_sku();
					?>
					<article class="code-result">
						<a href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<div>
							<div class="meta">
								<?php
								$brands = wc_get_product_terms( $pid, 'product_brand', array( 'fields' => 'names' ) );
								if ( $brands && ! is_wp_error( $brands ) ) :
									?>
									<span class="brand-chip"><?php echo esc_html( $brands[0] ); ?></span>
								<?php endif; ?>
							</div>
							<h3><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
							<div class="chain">
								<?php if ( $sku ) : ?>
									<span class="chain-node is-main" dir="ltr"><?php echo esc_html( $sku ); ?></span>
								<?php endif; ?>
								<?php foreach ( $codes as $c ) : ?>
									<span class="chain-sep" aria-hidden="true">↔</span>
									<span class="chain-node" dir="ltr"><?php echo esc_html( $c ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="price">
							<b><?php echo wp_kses_post( $product->get_price_html() ? $product->get_price_html() : esc_html__( 'استعلام', 'partyab' ) ); ?></b>
						</div>
					</article>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="code-empty">
					<b><?php esc_html_e( 'کد را نمی‌دانید؟', 'partyab' ); ?></b>
					<?php esc_html_e( 'مدل و سال خودرو را انتخاب کنید یا عکس قطعه را برای ما بفرستید تا کد و سازگاری را بررسی کنیم.', 'partyab' ); ?>
					<p style="margin-top:12px">
						<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( partyab_garage_url() ); ?>"><?php partyab_the_icon( 'garage' ); ?> <?php esc_html_e( 'انتخاب خودرو', 'partyab' ); ?></a>
					</p>
				</div>
			<?php endif; ?>

			<?php
			while ( have_posts() ) :
				the_post();
				$content = get_the_content();
				if ( '' !== trim( (string) $content ) ) :
					?>
					<div class="code-empty" style="margin-top:16px;text-align:start"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		</div>
	</section>
</main>

<?php
get_footer();
