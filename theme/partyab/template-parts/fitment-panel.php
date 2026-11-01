<?php
/**
 * Fitment panel on the single product page.
 *
 * Renders the current fitment state for the active vehicle, or an invitation to
 * pick one. Extra table lives in the "خودروهای سازگار" tab.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product ) {
	return;
}

$partyab_state   = partyab_state( $product );
$partyab_labels  = partyab_state_labels();
$partyab_tone    = isset( $partyab_state['state'] ) ? $partyab_state['state'] : 'unknown';
$partyab_label   = isset( $partyab_labels[ $partyab_tone ] ) ? $partyab_labels[ $partyab_tone ] : $partyab_labels['unknown'];
$partyab_vehicle = partyab_vehicle();
$partyab_rows    = class_exists( 'Partyab_Fitment' ) ? Partyab_Fitment::fit_table( $product->get_id() ) : array();
?>
<div class="fitment-panel fitment-panel--<?php echo esc_attr( $partyab_tone ); ?>">
	<div class="fp-head">
		<?php partyab_the_icon( $partyab_label['icon'] ); ?>
		<span><?php echo esc_html( $partyab_label['long'] ); ?></span>
	</div>

	<div class="fp-body">
		<?php echo esc_html( partyab_card_line( $product ) ); ?>
	</div>

	<?php if ( $partyab_vehicle ) : ?>
		<div class="fp-vehicle">
			<?php partyab_the_icon( 'car' ); ?>
			<?php echo esc_html( partyab_vehicle_label( $partyab_vehicle ) ); ?>
			<?php if ( ! empty( $partyab_vehicle['engine'] ) ) : ?>
				· <?php echo esc_html( $partyab_vehicle['engine'] ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $partyab_vehicle && ! empty( $partyab_rows ) ) : ?>
		<p class="fp-optional">
			<?php
			printf(
				/* translators: %s: number of compatible vehicles */
				esc_html__( 'این قطعه برای %s خودرو در فروشگاه ثبت شده است.', 'partyab' ),
				esc_html( partyab_num( count( $partyab_rows ) ) )
			);
			?>
			<a href="#tab-title-partyab_fitment"><?php esc_html_e( 'دیدن جدول کامل', 'partyab' ); ?></a>
		</p>
	<?php elseif ( ! $partyab_vehicle ) : ?>
		<p class="fp-optional">
			<a href="#" data-open-vehicle><?php esc_html_e( 'انتخاب خودرو', 'partyab' ); ?></a>
			<?php esc_html_e( 'تا وضعیت دقیق سازگاری با خودروی شما نمایش داده شود.', 'partyab' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( ! empty( $partyab_rows ) ) : ?>
		<table class="fit-table" style="margin-top:12px">
			<tbody>
				<?php foreach ( array_slice( $partyab_rows, 0, 3 ) as $partyab_row ) : ?>
					<tr class="<?php echo $partyab_vehicle && $partyab_vehicle['model_id'] === $partyab_row['model'] ? 'is-mine' : ''; ?>">
						<td><b><?php echo esc_html( $partyab_row['make'] . ' ' . $partyab_row['model'] ); ?></b></td>
						<td class="mono"><?php echo esc_html( $partyab_row['years'] ); ?></td>
						<td>
							<span class="p-fit p-fit--<?php echo esc_attr( $partyab_row['status'] ); ?>">
								<?php partyab_the_icon( 'conditional' === $partyab_row['status'] ? 'alert' : 'check' ); ?>
								<span><?php echo 'conditional' === $partyab_row['status'] ? esc_html__( 'با شرط', 'partyab' ) : esc_html__( 'سازگار', 'partyab' ); ?></span>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php if ( count( $partyab_rows ) > 3 ) : ?>
			<p class="fp-optional">
				<?php
				printf(
					/* translators: %s: remaining rows */
					esc_html__( 'و %s خودروی دیگر در جدول سازگاری.', 'partyab' ),
					esc_html( partyab_num( count( $partyab_rows ) - 3 ) )
				);
				?>
			</p>
		<?php endif; ?>
	<?php endif; ?>
</div>
