<?php
/**
 * داشبورد حساب کاربری.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

$partyab_user_id = get_current_user_id();
$partyab_stats   = partyab_customer_stats( $partyab_user_id );
$partyab_car     = function_exists( 'partyab_current_car' ) ? partyab_current_car() : array();
$partyab_has_car = function_exists( 'partyab_has_car' ) && partyab_has_car();
$partyab_name    = wp_get_current_user()->display_name;
?>

<div class="pb-dash">

	<div class="pb-dash__hello">
		<div>
			<strong>سلام <?php echo esc_html( $partyab_name ); ?> 👋</strong>
			<span>از این‌جا سفارش‌ها، آدرس‌ها و خودرویت را مدیریت کن.</span>
		</div>

		<a class="pb-dash__car" href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">
			<?php echo partyab_dashboard_icon( 'car' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span>
				<?php
				if ( $partyab_has_car ) {
					echo esc_html( $partyab_car['brand'] . ' ' . $partyab_car['model'] );
				} else {
					echo 'خودرویی انتخاب نکردی';
				}
				?>
			</span>
			<small><?php echo $partyab_has_car ? 'تغییر خودرو' : 'انتخاب خودرو'; ?></small>
		</a>
	</div>

	<div class="pb-dash__stats">
		<div class="pb-dash-card">
			<span class="pb-dash-card__icon"><?php echo partyab_dashboard_icon( 'orders' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<strong><?php echo esc_html( number_format_i18n( $partyab_stats['orders'] ) ); ?></strong>
			<span>کل سفارش‌ها</span>
		</div>

		<div class="pb-dash-card">
			<span class="pb-dash-card__icon"><?php echo partyab_dashboard_icon( 'active' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<strong><?php echo esc_html( number_format_i18n( $partyab_stats['active'] ) ); ?></strong>
			<span>در جریان</span>
		</div>

		<div class="pb-dash-card">
			<span class="pb-dash-card__icon"><?php echo partyab_dashboard_icon( 'wallet' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<strong><?php echo wp_kses_post( wc_price( $partyab_stats['spent'] ) ); ?></strong>
			<span>مجموع خرید</span>
		</div>
	</div>

	<?php if ( $partyab_stats['last'] instanceof WC_Order ) : ?>
		<?php $partyab_order = $partyab_stats['last']; ?>
		<div class="pb-dash__last">
			<div class="pb-dash__last-head">
				<h3>آخرین سفارش</h3>
				<a href="<?php echo esc_url( $partyab_order->get_view_order_url() ); ?>">جزئیات سفارش →</a>
			</div>

			<div class="pb-dash__last-body">
				<span class="pb-dash__order-num">#<?php echo esc_html( $partyab_order->get_order_number() ); ?></span>
				<span class="pb-status pb-status--<?php echo esc_attr( $partyab_order->get_status() ); ?>">
					<?php echo esc_html( wc_get_order_status_name( $partyab_order->get_status() ) ); ?>
				</span>
				<span class="pb-dash__order-date"><?php echo esc_html( wc_format_datetime( $partyab_order->get_date_created() ) ); ?></span>
				<span class="pb-dash__order-total"><?php echo wp_kses_post( $partyab_order->get_formatted_order_total() ); ?></span>
			</div>
		</div>
	<?php else : ?>
		<div class="pb-dash__empty">
			<strong>هنوز سفارشی ثبت نکردی.</strong>
			<span>خودرویت را انتخاب کن و از قطعات سازگار شروع کن.</span>
			<a class="pb-btn pb-btn--sm" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' ) ); ?>">رفتن به فروشگاه</a>
		</div>
	<?php endif; ?>

	<div class="pb-dash__links">
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">سفارش‌های من</a>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">آدرس‌ها</a>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>">اطلاعات حساب</a>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">سبد خرید</a>
	</div>

	<?php
	/**
	 * قلاب سازگاری با افزونه‌هایی که به داشبورد ووکامرس محتوا اضافه می‌کنند.
	 */
	do_action( 'woocommerce_account_dashboard' );
	?>

</div>
