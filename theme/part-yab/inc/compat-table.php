<?php
/**
 * تب «سازگاری با خودرو» در صفحه محصول.
 *
 * منبع داده: جدول CSV افزونه سازگاری (partyab_compat_rows).
 * اگر کاربر خودروی خود را انتخاب کرده باشد، ردیف مربوط به خودروی او
 * هایلایت می‌شود و بالای جدول نتیجه صریح می‌گیرد.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * افزودن تب سازگاری به تب‌های محصول.
 *
 * @param array $tabs تب‌ها.
 * @return array
 */
function partyab_compat_product_tab( array $tabs ): array {
	global $product;

	if ( ! $product instanceof WC_Product || ! function_exists( 'partyab_compat_rows' ) ) {
		return $tabs;
	}

	$rows = partyab_compat_rows( $product->get_id() );

	if ( ! $rows ) {
		return $tabs;
	}

	$tabs['partyab_compat'] = array(
		'title'    => 'سازگاری با خودرو (' . number_format_i18n( count( $rows ) ) . ')',
		'priority' => 12,
		'callback' => 'partyab_render_compat_tab',
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'partyab_compat_product_tab', 25 );

/**
 * آیا این ردیف با خودروی انتخاب‌شده می‌خواند؟
 *
 * @param array $row       ردیف سازگاری.
 * @param array $car       خودروی کاربر.
 * @return bool
 */
function partyab_row_matches_car( array $row, array $car ): bool {
	if ( ( $row['car_brand'] ?? '' ) !== ( $car['brand'] ?? '' ) ) {
		return false;
	}

	if ( ( $row['car_model'] ?? '' ) !== ( $car['model'] ?? '' ) ) {
		return false;
	}

	$year = (int) ( $car['year'] ?? 0 );

	if ( $year > 0 ) {
		$from = (int) ( $row['year_from'] ?? 0 );
		$to   = (int) ( $row['year_to'] ?? 0 );

		if ( $from > 0 && $year < $from ) {
			return false;
		}

		if ( $to > 0 && $year > $to ) {
			return false;
		}
	}

	return true;
}

/**
 * رندر تب سازگاری.
 */
function partyab_render_compat_tab(): void {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$rows = function_exists( 'partyab_compat_rows' ) ? partyab_compat_rows( $product->get_id() ) : array();

	if ( ! $rows ) {
		echo '<p class="pb-compat-empty">برای این قطعه هنوز جدول سازگاری ثبت نشده است. برای اطمینان با پشتیبانی تماس بگیرید.</p>';

		return;
	}

	$car        = function_exists( 'partyab_current_car' ) ? partyab_current_car() : array();
	$has_car    = function_exists( 'partyab_has_car' ) && partyab_has_car();
	$match      = false;
	$brands     = array();
	$models     = 0;

	foreach ( $rows as $row ) {
		$brands[ $row['car_brand'] ] = true;
		$models++;

		if ( $has_car && partyab_row_matches_car( $row, $car ) ) {
			$match = true;
		}
	}
	?>

	<div class="pb-compat">

		<?php if ( $has_car ) : ?>
			<div class="pb-compat__verdict <?php echo $match ? 'is-yes' : 'is-no'; ?>">
				<strong>
					<?php
					echo $match
						? '✓ با خودروی شما سازگار است'
						: '✗ با خودروی شما سازگار نیست';
					?>
				</strong>
				<span>
					<?php echo esc_html( 'خودروی شما: ' . $car['brand'] . ' ' . $car['model'] . ( $car['year'] ? ' — مدل ' . partyab_fa_digits( (string) $car['year'] ) : '' ) ); ?>
				</span>
				<a href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">تغییر خودرو</a>
			</div>
		<?php else : ?>
			<div class="pb-compat__verdict is-unknown">
				<strong>خودرویت را انتخاب کن</strong>
				<span>تا سازگاری همین قطعه را با مدل و سال خودرویت ببینی.</span>
				<a href="<?php echo esc_url( home_url( '/my-car' ) ); ?>">انتخاب خودرو →</a>
			</div>
		<?php endif; ?>

		<div class="pb-compat__summary">
			<span><?php echo esc_html( number_format_i18n( count( $brands ) ) ); ?> برند خودرو</span>
			<span><?php echo esc_html( number_format_i18n( $models ) ); ?> ترکیب مدل/سال</span>
		</div>

		<div class="pb-compat__table-wrap">
			<table class="pb-compat__table">
				<thead>
					<tr>
						<th>برند</th>
						<th>مدل</th>
						<th>سال ساخت</th>
						<th>موتور</th>
						<th>یادداشت</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$current_brand = '';

					foreach ( $rows as $row ) :
						$is_you = $has_car && partyab_row_matches_car( $row, $car );

						if ( $row['car_brand'] !== $current_brand ) :
							$current_brand = $row['car_brand'];
							?>
							<tr class="pb-compat__brand-row">
								<td colspan="5"><?php echo esc_html( $current_brand ); ?></td>
							</tr>
							<?php
						endif;
						?>
						<tr class="<?php echo $is_you ? 'is-you' : ''; ?>">
							<td><?php echo esc_html( $row['car_brand'] ); ?></td>
							<td>
								<?php echo esc_html( $row['car_model'] ); ?>
								<?php if ( $is_you ) : ?>
									<span class="pb-compat__you-badge">خودروی شما</span>
								<?php endif; ?>
							</td>
							<td>
								<?php
								$from = (int) ( $row['year_from'] ?? 0 );
								$to   = (int) ( $row['year_to'] ?? 0 );

								if ( $from && $to ) {
									echo esc_html( partyab_fa_digits( (string) $from ) . ' تا ' . partyab_fa_digits( (string) $to ) );
								} elseif ( $from ) {
									echo esc_html( 'از ' . partyab_fa_digits( (string) $from ) );
								} elseif ( $to ) {
									echo esc_html( 'تا ' . partyab_fa_digits( (string) $to ) );
								} else {
									echo '—';
								}
								?>
							</td>
							<td><?php echo esc_html( $row['engine'] ?: '—' ); ?></td>
							<td><?php echo esc_html( $row['note'] ?: '—' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<p class="pb-compat__note">
			این جدول از فایل رسمی سازگاری قطعه آمده است. اگر خودرویت در لیست نیست،
			قبل از خرید با پشتیبانی تماس بگیر.
		</p>

	</div>
	<?php
}

/**
 * فهرست کوتاه خودروهای سازگار زیر دکمه خرید.
 */
function partyab_compat_inline_summary(): void {
	global $product;

	if ( ! $product instanceof WC_Product || ! function_exists( 'partyab_compat_rows' ) ) {
		return;
	}

	$rows = partyab_compat_rows( $product->get_id() );

	if ( ! $rows ) {
		return;
	}

	$brands = array();

	foreach ( $rows as $row ) {
		$brands[ $row['car_brand'] ] = ( $brands[ $row['car_brand'] ] ?? 0 ) + 1;
	}

	echo '<p class="pb-compat-inline">';
	echo '<span>سازگار با:</span> ';

	$parts = array();

	foreach ( $brands as $brand => $count ) {
		$parts[] = esc_html( $brand . ' (' . partyab_fa_digits( (string) $count ) . ')');
	}

	echo wp_kses_post( implode( ' · ', $parts ) );
	echo '</p>';
}
add_action( 'woocommerce_single_product_summary', 'partyab_compat_inline_summary', 45 );
