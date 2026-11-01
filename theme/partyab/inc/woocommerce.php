<?php
/**
 * WooCommerce integration: layout hooks, fitment panel, tabs, cart fragments.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Remove the default sidebar and wrapper noise.
 */
function partyab_wc_setup() {
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	add_action( 'woocommerce_before_main_content', 'partyab_wc_open_container', 10 );
	add_action( 'woocommerce_after_main_content', 'partyab_wc_close_container', 10 );
	add_action( 'woocommerce_before_shop_loop', 'partyab_wc_toolbar', 20 );
	add_action( 'woocommerce_before_shop_loop', 'partyab_wc_vehicle_bar', 5 );
}
add_action( 'after_setup_theme', 'partyab_wc_setup' );

/**
 * Open the page container.
 */
function partyab_wc_open_container() {
	echo '<div class="container shop-wrap">';
}

/**
 * Close the page container.
 */
function partyab_wc_close_container() {
	echo '</div>';
}

/**
 * Vehicle bar above the loop (mirrors the passport bar on the shop page).
 */
function partyab_wc_vehicle_bar() {
	$vehicle = partyab_vehicle();
	$compat  = class_exists( 'Partyab_Query' ) && Partyab_Query::compat_requested();
	?>
	<div class="vbar <?php echo $vehicle ? '' : 'vbar--empty'; ?>">
		<div class="vbar-left">
			<span class="vb-icon"><?php partyab_the_icon( 'car' ); ?></span>
			<div>
				<?php if ( $vehicle ) : ?>
					<b>
						<?php esc_html_e( 'خودروی فعال:', 'partyab' ); ?>
						<?php echo esc_html( partyab_vehicle_label( $vehicle ) ); ?>
						<?php if ( ! empty( $vehicle['engine'] ) ) : ?>
							· <?php echo esc_html( $vehicle['engine'] ); ?>
						<?php endif; ?>
					</b>
					<span><?php echo $compat ? esc_html__( 'فقط قطعاتی نمایش داده می‌شود که سازگاری آن‌ها تأیید شده است.', 'partyab' ) : esc_html__( 'همهٔ قطعات نمایش داده می‌شود؛ نشان سازگاری روی هر کارت مشخص است.', 'partyab' ); ?></span>
				<?php else : ?>
					<b><?php esc_html_e( 'خودرویتان را انتخاب کنید', 'partyab' ); ?></b>
					<span><?php esc_html_e( 'با انتخاب خودرو، فقط قطعات سازگار را می‌بینید و از خرید اشتباه جلوگیری می‌کنید.', 'partyab' ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="vbar-actions">
			<?php echo do_shortcode( '[partyab_compat_toggle]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button type="button" class="btn <?php echo $vehicle ? 'btn-ghost' : 'btn-amber'; ?> btn-sm" data-open-vehicle>
				<?php partyab_the_icon( 'garage' ); ?>
				<?php echo $vehicle ? esc_html__( 'گاراژ', 'partyab' ) : esc_html__( 'انتخاب خودرو', 'partyab' ); ?>
			</button>
			<?php if ( $vehicle ) : ?>
				<button type="button" class="btn btn-ghost btn-sm" data-remove-vehicle>
					<?php partyab_the_icon( 'x' ); ?><?php esc_html_e( 'حذف', 'partyab' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Shop toolbar: result count + ordering.
 */
function partyab_wc_toolbar() {
	echo '<div class="shop-toolbar">';
	echo '<div class="result-count">';
	global $wp_query;
	printf(
		/* translators: %s: number of results */
		esc_html__( '%s قطعه یافت شد', 'partyab' ),
		esc_html( partyab_num( (int) $wp_query->found_posts ) )
	);
	echo '</div>';
	echo '<div class="toolbar-right">';
	woocommerce_catalog_ordering();
	echo '</div></div>';
}

/**
 * Four columns + our grid wrapper instead of <ul class="products">.
 *
 * @return int
 */
function partyab_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'partyab_loop_columns' );

/**
 * Product loop start.
 *
 * @return string
 */
function partyab_loop_start() {
	return '<div class="p-grid">';
}
add_filter( 'woocommerce_product_loop_start', 'partyab_loop_start' );

/**
 * Product loop end.
 *
 * @return string
 */
function partyab_loop_end() {
	return '</div>';
}
add_filter( 'woocommerce_product_loop_end', 'partyab_loop_end' );

/**
 * Fitment panel on the single product page.
 */
function partyab_single_fitment_panel() {
	global $product;
	if ( ! $product ) {
		return;
	}
	get_template_part( 'template-parts/fitment-panel' );
}
add_action( 'woocommerce_single_product_summary', 'partyab_single_fitment_panel', 25 );

/**
 * Add the "compatible cars" tab.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function partyab_fitment_tab( $tabs ) {
	global $product;
	if ( ! $product ) {
		return $tabs;
	}

	$rows = class_exists( 'Partyab_Fitment' ) ? Partyab_Fitment::fit_table( $product->get_id() ) : array();
	if ( empty( $rows ) ) {
		return $tabs;
	}

	$tabs['partyab_fitment'] = array(
		'title'    => __( 'خودروهای سازگار', 'partyab' ),
		'priority' => 15,
		'callback' => 'partyab_fitment_tab_content',
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'partyab_fitment_tab' );

/**
 * Tab content.
 */
function partyab_fitment_tab_content() {
	global $product;
	if ( ! $product || ! class_exists( 'Partyab_Fitment' ) ) {
		return;
	}

	$rows    = Partyab_Fitment::fit_table( $product->get_id() );
	$vehicle = partyab_vehicle();
	?>
	<table class="fit-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'سازنده', 'partyab' ); ?></th>
				<th><?php esc_html_e( 'مدل', 'partyab' ); ?></th>
				<th><?php esc_html_e( 'بازهٔ سال', 'partyab' ); ?></th>
				<th><?php esc_html_e( 'وضعیت', 'partyab' ); ?></th>
				<th><?php esc_html_e( 'شرط', 'partyab' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr class="<?php echo $vehicle && $vehicle['model_id'] === $row['model'] ? 'is-mine' : ''; ?>">
					<td><?php echo esc_html( $row['make'] ); ?></td>
					<td><?php echo esc_html( $row['model'] ); ?></td>
					<td class="mono"><?php echo esc_html( $row['years'] ); ?></td>
					<td>
						<span class="p-fit p-fit--<?php echo esc_attr( $row['status'] ); ?>">
							<?php partyab_the_icon( 'conditional' === $row['status'] ? 'alert' : 'check' ); ?>
							<span><?php echo 'conditional' === $row['status'] ? esc_html__( 'با شرط', 'partyab' ) : esc_html__( 'سازگار', 'partyab' ); ?></span>
						</span>
					</td>
					<td><?php echo esc_html( $row['note'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Keep the header cart counter in sync (WooCommerce fragments).
 *
 * @param array $fragments Fragments.
 * @return array
 */
function partyab_cart_fragments( $fragments ) {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

	$fragments['.cart-count'] = sprintf(
		'<span class="cart-count %s">%s</span>',
		$count ? '' : 'is-zero',
		esc_html( partyab_num( $count ) )
	);

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'partyab_cart_fragments' );

/**
 * Empty cart message in Persian shop tone.
 *
 * @return string
 */
function partyab_empty_cart_message() {
	return __( 'سبد خرید شما خالی است. از فروشگاه قطعهٔ سازگار با خودرویتان را انتخاب کنید.', 'partyab' );
}
add_filter( 'wc_empty_cart_message', 'partyab_empty_cart_message' );

/**
 * Use the theme's card in WooCommerce loops.
 */
function partyab_loop_product_template() {
	return locate_template( 'woocommerce/content-product.php' );
}
