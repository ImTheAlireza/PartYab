<?php
/**
 * بازنویسی قالب حساب کاربری ووکامرس.
 *
 * چرا بازنویسی؟ چون چیدمان پیش‌فرض ووکامرس (float + عرض درصدی) در قالب ما
 * به‌هم می‌ریخت: منو یک کارت عریض می‌شد و محتوا کارت کوچکی در گوشه.
 * اینجا ساختار صریح و قابل پیش‌بینی می‌دهیم.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>

<div class="pb-account">

	<aside class="pb-account__nav">
		<?php
		$nav_template = WC()->plugin_path() . '/templates/myaccount/navigation.php';

		if ( file_exists( $nav_template ) ) {
			wc_get_template( 'myaccount/navigation.php' );
		} else {
			?>
			<nav class="woocommerce-MyAccount-navigation" aria-label="منوی حساب">
				<ul>
					<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
						<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
			<?php
		}
		?>
	</aside>

	<div class="pb-account__main">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>

</div>

<?php
do_action( 'woocommerce_after_account_navigation' );
