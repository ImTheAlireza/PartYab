<?php
/**
 * Fitment resolution: the five states + coverage.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Fitment
 */
class Partyab_Fitment {

	/**
	 * State labels (short + long).
	 *
	 * @return array
	 */
	public static function labels() {
		return array(
			'fit'         => array(
				'short' => __( 'سازگار', 'partyab' ),
				'long'  => __( 'با خودروی شما سازگار است', 'partyab' ),
				'icon'  => 'check',
				'tone'  => 'fit',
			),
			'conditional' => array(
				'short' => __( 'با شرط', 'partyab' ),
				'long'  => __( 'سازگار است، با شرط', 'partyab' ),
				'icon'  => 'alert',
				'tone'  => 'conditional',
			),
			'no'          => array(
				'short' => __( 'ناسازگار', 'partyab' ),
				'long'  => __( 'با خودروی شما سازگار نیست', 'partyab' ),
				'icon'  => 'x',
				'tone'  => 'no',
			),
			'unknown'     => array(
				'short' => __( 'ثبت نشده', 'partyab' ),
				'long'  => __( 'سازگاری برای این خودرو ثبت نشده', 'partyab' ),
				'icon'  => 'info',
				'tone'  => 'unknown',
			),
			'universal'   => array(
				'short' => __( 'عمومی', 'partyab' ),
				'long'  => __( 'قطعهٔ عمومی', 'partyab' ),
				'icon'  => 'box',
				'tone'  => 'universal',
			),
			'ask'         => array(
				'short' => __( 'انتخاب خودرو', 'partyab' ),
				'long'  => __( 'خودرو را انتخاب کنید', 'partyab' ),
				'icon'  => 'car',
				'tone'  => 'ask',
			),
		);
	}

	/**
	 * Is this product universal?
	 *
	 * @param int $product_id Product ID.
	 * @return bool
	 */
	public static function is_universal( $product_id ) {
		$flag = get_post_meta( (int) $product_id, '_partyab_universal', true );
		return in_array( $flag, array( 'yes', '1', 1, true ), true );
	}

	/**
	 * Universal note.
	 *
	 * @param int $product_id Product ID.
	 * @return string
	 */
	public static function universal_note( $product_id ) {
		return (string) get_post_meta( (int) $product_id, '_partyab_universal_note', true );
	}

	/**
	 * Resolve the state of a product for a vehicle.
	 *
	 * @param int        $product_id Product ID.
	 * @param array|null $vehicle    Vehicle (null = no vehicle chosen).
	 * @return array
	 */
	public static function state( $product_id, $vehicle ) {
		$product_id = (int) $product_id;

		if ( self::is_universal( $product_id ) ) {
			return array(
				'state' => 'universal',
				'note'  => self::universal_note( $product_id ),
				'range' => null,
			);
		}

		if ( ! $vehicle ) {
			return array(
				'state' => 'ask',
				'note'  => '',
				'range' => null,
			);
		}

		$rows = Partyab_DB::rows_for( $product_id );
		if ( empty( $rows ) ) {
			return array(
				'state' => 'unknown',
				'note'  => '',
				'range' => null,
			);
		}

		foreach ( $rows as $row ) {
			if ( $row['model_id'] !== $vehicle['model_id'] ) {
				continue;
			}

			$from = (int) $row['year_from'];
			$to   = (int) $row['year_to'];

			if ( $vehicle['year'] < $from || $vehicle['year'] > $to ) {
				return array(
					'state'        => 'no',
					'note'         => isset( $row['note'] ) ? (string) $row['note'] : '',
					'range'        => array( $from, $to ),
					'out_of_range' => array( $from, $to ),
				);
			}

			return array(
				'state' => 'conditional' === $row['status'] ? 'conditional' : 'fit',
				'note'  => isset( $row['note'] ) ? (string) $row['note'] : '',
				'range' => array( $from, $to ),
			);
		}

		return array(
			'state' => 'no',
			'note'  => '',
			'range' => null,
		);
	}

	/**
	 * Human readable line for a card.
	 *
	 * @param int        $product_id Product ID.
	 * @param array|null $vehicle    Vehicle.
	 * @return string
	 */
	public static function card_line( $product_id, $vehicle ) {
		$info = self::state( $product_id, $vehicle );
		$car  = $vehicle ? $vehicle['model_name'] . ' ' . Partyab_Vehicles::fa_digits( $vehicle['year'] ) : '';

		switch ( $info['state'] ) {
			case 'fit':
				return $car ? sprintf( __( 'سازگار با %s', 'partyab' ), $car ) : __( 'سازگار', 'partyab' );
			case 'conditional':
				return $info['note'] ? sprintf( __( 'سازگار با شرط · %s', 'partyab' ), $info['note'] ) : __( 'سازگار با شرط', 'partyab' );
			case 'no':
				return $car ? sprintf( __( 'ناسازگار با %s', 'partyab' ), $car ) : __( 'ناسازگار', 'partyab' );
			case 'unknown':
				return __( 'سازگاری ثبت نشده', 'partyab' );
			case 'universal':
				return __( 'عمومی برای همهٔ خودروها', 'partyab' );
			default:
				return __( 'سازگاری را بررسی کنید', 'partyab' );
		}
	}

	/**
	 * Catalog coverage for a vehicle.
	 *
	 * @param array $vehicle Vehicle.
	 * @return array{ok:int,total:int,pct:int}
	 */
	public static function coverage( $vehicle ) {
		$total = (int) wp_count_posts( 'product' )->publish;
		if ( ! $vehicle ) {
			return array( 'ok' => 0, 'total' => $total, 'pct' => 0 );
		}

		$ok = count( self::compatible_ids( $vehicle ) );

		return array(
			'ok'    => $ok,
			'total' => $total,
			'pct'   => $total > 0 ? (int) round( ( $ok / $total ) * 100 ) : 0,
		);
	}

	/**
	 * IDs of published products compatible with a vehicle (universal included).
	 *
	 * @param array $vehicle Vehicle.
	 * @return int[]
	 */
	public static function compatible_ids( $vehicle ) {
		if ( ! $vehicle ) {
			return array();
		}

		$ids = Partyab_DB::product_ids_for( $vehicle );

		$universal = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 100,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'key'   => '_partyab_universal',
						'value' => array( 'yes', '1' ),
					),
				),
			)
		);

		return array_values( array_unique( array_map( 'intval', array_merge( $ids, $universal ) ) ) );
	}

	/**
	 * Detail rows for the product page ("which cars does it fit").
	 *
	 * @param int $product_id Product ID.
	 * @return array
	 */
	public static function fit_table( $product_id ) {
		$out = array();
		foreach ( Partyab_DB::rows_for( $product_id ) as $row ) {
			$model = Partyab_Vehicles::find_model( $row['model_id'] );
			if ( ! $model ) {
				continue;
			}
			$out[] = array(
				'make'   => $model['make_name'],
				'model'  => $model['name'],
				'years'  => Partyab_Vehicles::fa_digits( $row['year_from'] ) . ' – ' . Partyab_Vehicles::fa_digits( $row['year_to'] ),
				'status' => 'conditional' === $row['status'] ? 'conditional' : 'fit',
				'note'   => (string) $row['note'],
			);
		}
		return $out;
	}
}
