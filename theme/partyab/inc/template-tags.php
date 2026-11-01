<?php
/**
 * Template tags: icons, passport bar, product card, prices.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon.
 *
 * @param string $name Icon key.
 * @param string $class Extra class.
 * @return string
 */
function partyab_icon( $name, $class = '' ) {
	$paths = partyab_icon_paths();
	$inner = isset( $paths[ $name ] ) ? $paths[ $name ] : '';

	return sprintf(
		'<svg class="icon %s" viewBox="0 0 24 24" aria-hidden="true">%s</svg>',
		esc_attr( $class ),
		$inner // Trusted: static markup from inc/icons.php.
	);
}

/**
 * Echo an icon.
 *
 * @param string $name  Icon key.
 * @param string $class Extra class.
 */
function partyab_the_icon( $name, $class = '' ) {
	echo partyab_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Persian digits (proxies the plugin helper, with a local fallback).
 *
 * @param string|int $value Value.
 * @return string
 */
function partyab_num( $value ) {
	if ( function_exists( 'partyab_fa' ) ) {
		return partyab_fa( $value );
	}
	return str_replace( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), (string) $value );
}

/**
 * Price in the design language (Persian digits, ٬ separator, «تومان»).
 *
 * @param WC_Product|float $product Product or amount.
 * @return string
 */
function partyab_price( $product ) {
	$amount = $product instanceof WC_Product ? (float) $product->get_price() : (float) $product;

	return '<strong>' . esc_html( partyab_num( number_format( $amount, 0, '.', '٬' ) ) ) . ' <small>' . esc_html__( 'تومان', 'partyab' ) . '</small></strong>';
}

/**
 * Current vehicle (safe when the plugin is inactive).
 *
 * @return array|null
 */
function partyab_vehicle() {
	if ( function_exists( 'partyab_current_vehicle' ) ) {
		return partyab_current_vehicle();
	}
	return null;
}

/**
 * Vehicle label.
 *
 * @param array|null $vehicle Vehicle.
 * @return string
 */
function partyab_vehicle_label( $vehicle ) {
	if ( ! $vehicle ) {
		return '';
	}
	return $vehicle['model_name'] . ' ' . partyab_num( $vehicle['year'] );
}

/**
 * Fitment state of the current product/product ID.
 *
 * @param int|WC_Product|null $product Product.
 * @return array
 */
function partyab_state( $product = null ) {
	if ( ! function_exists( 'partyab_fit_state' ) ) {
		return array( 'state' => 'ask', 'note' => '' );
	}

	if ( null === $product ) {
		$product = get_the_ID();
	}
	if ( $product instanceof WC_Product ) {
		$product = $product->get_id();
	}

	return partyab_fit_state( (int) $product );
}

/**
 * Label set for the five states.
 *
 * @return array
 */
function partyab_state_labels() {
	if ( class_exists( 'Partyab_Fitment' ) ) {
		return Partyab_Fitment::labels();
	}

	return array(
		'ask' => array(
			'short' => __( 'انتخاب خودرو', 'partyab' ),
			'icon'  => 'car',
		),
	);
}

/**
 * Card fitment line.
 *
 * @param int|WC_Product $product Product.
 * @return string
 */
function partyab_card_line( $product ) {
	if ( $product instanceof WC_Product ) {
		$product = $product->get_id();
	}
	if ( class_exists( 'Partyab_Fitment' ) ) {
		return Partyab_Fitment::card_line( (int) $product, partyab_vehicle() );
	}
	return '';
}

/**
 * The passport bar shown under the header.
 *
 * @return void
 */
function partyab_passport_bar() {
	$vehicle          = partyab_vehicle();
	$coverage_defaults = array(
		'ok'    => 0,
		'total' => 0,
		'pct'   => 0,
	);

	$coverage = array(
		'ok'    => 0,
		'total' => 0,
		'pct'   => 0,
	);

	if ( class_exists( 'Partyab_Fitment' ) ) {
		$coverage = Partyab_Fitment::coverage( $vehicle );
	}

	$count = wp_parse_args( is_array( $coverage ) ? $coverage : array(), $coverage_defaults );
	?>
	<div class="passport-bar">
		<div class="container passport-inner">
			<div class="passport-slot">
				<?php if ( ! $vehicle ) : ?>
					<button type="button" class="pp pp--empty" data-open-vehicle aria-label="<?php esc_attr_e( 'ثبت خودرو', 'partyab' ); ?>">
						<span class="pp-plate pp-plate--empty"><?php partyab_the_icon( 'car' ); ?></span>
						<span class="pp-id">
							<small><?php esc_html_e( 'پلاک خودرو خالی است', 'partyab' ); ?></small>
							<b><?php esc_html_e( 'خودرو خود را ثبت کنید', 'partyab' ); ?></b>
						</span>
						<span class="pp-cov"><?php esc_html_e( 'با ثبت خودرو فقط قطعات سازگار نمایش داده می‌شود', 'partyab' ); ?></span>
						<span class="pp-btn pp-btn--amber"><?php partyab_the_icon( 'plus' ); ?><?php esc_html_e( 'ثبت خودرو', 'partyab' ); ?></span>
					</button>
				<?php else : ?>
					<div class="pp is-set">
						<span class="pp-plate"><?php partyab_the_icon( 'car' ); ?></span>
						<span class="pp-id">
							<small><?php esc_html_e( 'خودروی من', 'partyab' ); ?></small>
							<b>
								<?php echo esc_html( $vehicle['model_name'] ); ?>
								<span class="pp-year"><?php echo esc_html( partyab_num( $vehicle['year'] ) ); ?></span>
								<?php if ( ! empty( $vehicle['engine'] ) ) : ?>
									<span class="pp-eng"><?php echo esc_html( $vehicle['engine'] ); ?></span>
								<?php endif; ?>
							</b>
						</span>
						<span class="pp-meter" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'پوشش کاتالوگ %s درصد', 'partyab' ), partyab_num( $count['pct'] ) ) ); ?>">
							<i style="width:<?php echo esc_attr( (int) $count['pct'] ); ?>%"></i>
						</span>
						<span class="pp-cov">
							<?php
							printf(
								/* translators: 1: compatible count, 2: total count */
								esc_html__( '%1$s از %2$s قطعه سازگار', 'partyab' ),
								esc_html( partyab_num( $count['ok'] ) ),
								esc_html( partyab_num( $count['total'] ) )
							);
							?>
						</span>
						<a class="pp-btn pp-btn--link" href="<?php echo esc_url( partyab_garage_url() ); ?>"><?php partyab_the_icon( 'garage' ); ?><?php esc_html_e( 'گاراژ', 'partyab' ); ?></a>
						<button type="button" class="pp-btn" data-open-vehicle><?php partyab_the_icon( 'refresh' ); ?><?php esc_html_e( 'تغییر', 'partyab' ); ?></button>
					</div>
				<?php endif; ?>
			</div>

			<nav class="passport-nav" aria-label="<?php esc_attr_e( 'ناوبری اصلی', 'partyab' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'fallback_cb'    => 'partyab_nav_fallback',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>
	</div>
	<?php
}

/**
 * Fallback menu when no menu is assigned yet.
 */
function partyab_nav_fallback() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	$links = array(
		array( home_url( '/' ), __( 'خانه', 'partyab' ) ),
		array( $shop, __( 'فروشگاه قطعات', 'partyab' ) ),
	);

	foreach ( $links as $link ) {
		printf( '<a href="%s">%s</a>', esc_url( $link[0] ), esc_html( $link[1] ) );
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 4,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			printf( '<a href="%s">%s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
		}
	}
}

/**
 * The vehicle picker modal.
 */
function partyab_vehicle_picker() {
	?>
	<div class="modal modal--sheet" id="vehicleModal" aria-hidden="true">
		<div class="modal-backdrop" data-close-vehicle-modal></div>
		<div class="modal-card sheet-card" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
			<div class="sheet-head">
				<div>
					<span class="sheet-eyebrow"><?php partyab_the_icon( 'garage' ); ?><?php esc_html_e( 'پاسپورت خودرو', 'partyab' ); ?></span>
					<h3 id="vmTitle"><?php esc_html_e( 'انتخاب خودرو', 'partyab' ); ?></h3>
				</div>
				<button type="button" class="modal-close" data-close-vehicle-modal aria-label="<?php esc_attr_e( 'بستن', 'partyab' ); ?>"><?php partyab_the_icon( 'x' ); ?></button>
			</div>
			<div class="modal-body sheet-body" id="vehicleModalBody"></div>
		</div>
	</div>
	<?php
}

/**
 * Product card used by all listings.
 *
 * @param WC_Product|null $product Product.
 */
function partyab_product_card( $product = null ) {
	if ( ! $product && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product ) {
		return;
	}

	$id    = $product->get_id();
	$state = partyab_state( $product );
	$tone  = $state['state'];
	$labels = partyab_state_labels();
	$icon   = isset( $labels[ $tone ]['icon'] ) ? $labels[ $tone ]['icon'] : 'info';

	$oem    = get_post_meta( $id, '_partyab_oem', true );
	$brand  = get_post_meta( $id, '_partyab_brand', true );
	$equiv  = get_post_meta( $id, '_partyab_equivalents', true );
	$equiv  = is_string( $equiv ) ? array_filter( array_map( 'trim', explode( '|', $equiv ) ) ) : array();
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	$off     = ( $sale && $regular > $sale ) ? (int) round( ( 1 - ( $sale / $regular ) ) * 100 ) : 0;
	?>
	<article class="p-card <?php echo 'no' === $tone ? 'p-card--dim' : ''; ?>">
		<span class="p-rail p-rail--<?php echo esc_attr( $tone ); ?>" aria-hidden="true"></span>

		<a class="p-media" href="<?php echo esc_url( get_permalink( $id ) ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
			<?php
			echo $product->get_image( 'partyab-card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<?php if ( $oem ) : ?>
				<span class="p-code"><?php echo esc_html( $oem ); ?></span>
			<?php endif; ?>
			<?php if ( $off ) : ?>
				<span class="off-badge">−<?php echo esc_html( partyab_num( $off ) ); ?>٪</span>
			<?php endif; ?>
		</a>

		<div class="p-body">
			<div class="p-head">
				<span class="p-brand"><?php echo esc_html( $brand ? $brand : __( 'پارت‌یاب', 'partyab' ) ); ?></span>
				<?php if ( count( $equiv ) > 1 ) : ?>
					<span class="p-equiv"><?php partyab_the_icon( 'copy' ); ?><?php echo esc_html( partyab_num( count( $equiv ) ) ); ?> <?php esc_html_e( 'کد', 'partyab' ); ?></span>
				<?php endif; ?>
			</div>

			<h3 class="p-title">
				<a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			</h3>

			<div class="p-fit p-fit--<?php echo esc_attr( $tone ); ?>">
				<?php partyab_the_icon( $icon ); ?>
				<span><?php echo esc_html( partyab_card_line( $id ) ); ?></span>
			</div>

			<div class="p-foot">
				<div class="p-price">
					<?php if ( $off ) : ?>
						<span class="old"><?php echo esc_html( partyab_num( number_format( $regular, 0, '.', '٬' ) ) ); ?></span>
					<?php endif; ?>
					<?php echo partyab_price( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<a class="p-add <?php echo esc_attr( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button ajax_add_to_cart' : '' ); ?>"
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					data-product_id="<?php echo esc_attr( $id ); ?>"
					data-quantity="1"
					rel="nofollow"
					aria-label="<?php echo esc_attr( sprintf( __( 'افزودن %s به سبد خرید', 'partyab' ), $product->get_name() ) ); ?>">
					<?php partyab_the_icon( 'plus' ); ?>
				</a>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Categories index rows (home page).
 */
function partyab_category_index() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 8,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}

	echo '<div class="cat-grid">';
	$i = 1;
	foreach ( $terms as $term ) {
		$icon = get_term_meta( $term->term_id, 'partyab_icon', true );
		if ( ! $icon ) {
			$icon = 'box';
		}
		?>
		<a class="cat-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
			<span class="cat-num mono"><?php echo esc_html( partyab_num( $i ) ); ?></span>
			<span class="cat-icon"><?php partyab_the_icon( $icon ); ?></span>
			<span class="cat-name"><?php echo esc_html( $term->name ); ?></span>
			<span class="cat-lead" aria-hidden="true"></span>
			<span class="cat-count mono"><?php echo esc_html( partyab_num( $term->count ) ); ?></span>
			<span class="cat-arrow"><?php partyab_the_icon( 'chevron-left' ); ?></span>
		</a>
		<?php
		$i++;
	}
	echo '</div>';
}

/**
 * Footer fallback — product categories.
 */
function partyab_footer_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 5,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( is_wp_error( $terms ) ) {
		return;
	}

	foreach ( $terms as $term ) {
		printf( '<a href="%s">%s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
	}
}

/**
 * Footer fallback — help links.
 */
function partyab_footer_help() {
	$links = array(
		array( wc_get_page_permalink( 'myaccount' ), __( 'حساب کاربری و سفارش‌ها', 'partyab' ) ),
		array( wc_get_page_permalink( 'shop' ), __( 'همهٔ قطعات', 'partyab' ) ),
		array( wc_get_cart_url(), __( 'سبد خرید', 'partyab' ) ),
		array( wc_get_checkout_url(), __( 'تسویهٔ سفارش', 'partyab' ) ),
	);

	foreach ( $links as $link ) {
		printf( '<a href="%s">%s</a>', esc_url( $link[0] ), esc_html( $link[1] ) );
	}
}

/**
 * URL of a product category by slug, with a shop fallback.
 *
 * @param string $slug Category slug.
 * @return string
 */
function partyab_cat_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}

	return wc_get_page_permalink( 'shop' );
}

/**
 * Garage page URL (page with the "garage" slug, else the shop).
 *
 * @return string
 */
function partyab_garage_url() {
	$page = get_page_by_path( 'garage' );
	if ( $page ) {
		return get_permalink( $page );
	}
	$page = get_page_by_path( 'گاراژ' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Shop URL (safe when WooCommerce is inactive).
 *
 * @return string
 */
function partyab_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Cart URL (safe when WooCommerce is inactive).
 *
 * @return string
 */
function partyab_cart_url() {
	return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : partyab_shop_url();
}

/**
 * Checkout URL (safe when WooCommerce is inactive).
 *
 * @return string
 */
function partyab_checkout_url() {
	return function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : partyab_shop_url();
}

/**
 * Account URL (safe when WooCommerce is inactive).
 *
 * @return string
 */
function partyab_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'myaccount' );
		if ( $url ) {
			return $url;
		}
	}
	return wp_login_url();
}
