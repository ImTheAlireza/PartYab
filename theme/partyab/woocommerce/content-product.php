<?php
/**
 * Product card inside WooCommerce loops.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<div <?php wc_product_class( 'p-grid__item', $product ); ?>>
	<?php partyab_product_card( $product ); ?>
</div>
