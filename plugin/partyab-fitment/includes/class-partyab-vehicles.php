<?php
/**
 * Vehicle catalog + current-vehicle cookie.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Vehicles
 */
class Partyab_Vehicles {

	/**
	 * Cached catalog.
	 *
	 * @var array|null
	 */
	private static $catalog = null;

	/**
	 * Load (and cache) the vehicle catalog from JSON.
	 *
	 * @return array
	 */
	public static function catalog() {
		if ( null !== self::$catalog ) {
			return self::$catalog;
		}

		$fallback = array( 'version' => 0, 'makes' => array(), 'groups' => array() );
		$file     = PARTYAB_FITMENT_DIR . 'data/vehicles.json';

		$raw = '';
		if ( file_exists( $file ) && is_readable( $file ) ) {
			$raw = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}

		$data = json_decode( (string) $raw, true );
		if ( ! is_array( $data ) || empty( $data['makes'] ) ) {
			self::$catalog = $fallback;
			return self::$catalog;
		}

		/**
		 * Filter the vehicle catalog (makes → models → years/engines).
		 *
		 * @param array $data Decoded catalog.
		 */
		self::$catalog = apply_filters( 'partyab_vehicles_catalog', $data );

		return self::$catalog;
	}

	/**
	 * All makes.
	 *
	 * @return array
	 */
	public static function makes() {
		$catalog = self::catalog();
		return isset( $catalog['makes'] ) ? $catalog['makes'] : array();
	}

	/**
	 * Group labels (iran / foreign).
	 *
	 * @return array
	 */
	public static function groups() {
		$catalog = self::catalog();
		return isset( $catalog['groups'] ) ? $catalog['groups'] : array(
			'iran'    => __( 'خودروهای ایرانی', 'partyab' ),
			'foreign' => __( 'خودروهای وارداتی', 'partyab' ),
		);
	}

	/**
	 * Flat list of models with their make attached.
	 *
	 * @return array
	 */
	public static function models() {
		$out = array();
		foreach ( self::makes() as $make ) {
			if ( empty( $make['models'] ) ) {
				continue;
			}
			foreach ( $make['models'] as $model ) {
				$model['make_id']   = $make['id'];
				$model['make_name'] = $make['name'];
				$model['group']     = isset( $make['group'] ) ? $make['group'] : 'iran';
				$out[]              = $model;
			}
		}
		return $out;
	}

	/**
	 * Find a model by id.
	 *
	 * @param string $model_id Model id.
	 * @return array|null
	 */
	public static function find_model( $model_id ) {
		foreach ( self::models() as $model ) {
			if ( $model['id'] === $model_id ) {
				return $model;
			}
		}
		return null;
	}

	/**
	 * Search models by (normalized) name / keywords / make.
	 *
	 * @param string $query Search query.
	 * @param int    $limit Max results.
	 * @return array
	 */
	public static function search( $query, $limit = 8 ) {
		$query = self::normalize( $query );
		if ( '' === $query ) {
			return array();
		}

		$hits = array();
		foreach ( self::models() as $model ) {
			$haystack = array( $model['name'], $model['make_name'], $model['id'] );
			if ( ! empty( $model['keywords'] ) ) {
				$haystack = array_merge( $haystack, $model['keywords'] );
			}
			foreach ( $haystack as $needle ) {
				if ( false !== strpos( self::normalize( $needle ), $query ) ) {
					$hits[] = $model;
					break;
				}
			}
			if ( count( $hits ) >= $limit ) {
				break;
			}
		}
		return $hits;
	}

	/**
	 * Validate and normalize a raw vehicle payload.
	 *
	 * @param mixed $raw Raw array (from cookie, REST or form).
	 * @return array|null
	 */
	public static function sanitize( $raw ) {
		if ( is_string( $raw ) ) {
			$raw = json_decode( $raw, true );
		}
		if ( ! is_array( $raw ) || empty( $raw['model_id'] ) ) {
			return null;
		}

		$model = self::find_model( sanitize_text_field( (string) $raw['model_id'] ) );
		if ( ! $model ) {
			return null;
		}

		$years   = isset( $model['years'] ) ? $model['years'] : array();
		$from    = isset( $years[0] ) ? (int) $years[0] : 0;
		$to      = isset( $years[1] ) ? (int) $years[1] : 0;
		$year    = isset( $raw['year'] ) ? (int) self::latin_digits( (string) $raw['year'] ) : 0;
		$engines = isset( $model['engines'] ) ? $model['engines'] : array();

		if ( $year < $from || $year > $to ) {
			return null;
		}

		$engine = isset( $raw['engine'] ) ? sanitize_text_field( (string) $raw['engine'] ) : '';
		if ( $engine && ! in_array( $engine, $engines, true ) ) {
			$engine = '';
		}

		return array(
			'key'        => $model['make_id'] . ':' . $model['id'] . ':' . $year,
			'make_id'    => $model['make_id'],
			'make_name'  => $model['make_name'],
			'model_id'   => $model['id'],
			'model_name' => $model['name'],
			'year'       => $year,
			'engine'     => $engine,
			'years'      => array( $from, $to ),
		);
	}

	/**
	 * Visitor vehicle from cookie.
	 *
	 * @return array|null
	 */
	public static function current() {
		if ( empty( $_COOKIE[ PARTYAB_COOKIE ] ) ) {
			return null;
		}
		$raw = wp_unslash( $_COOKIE[ PARTYAB_COOKIE ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		return self::sanitize( $raw );
	}

	/**
	 * Public payload for the REST API / JS.
	 *
	 * @param array|null $vehicle Vehicle.
	 * @return array
	 */
	public static function to_public( $vehicle ) {
		if ( ! $vehicle ) {
			return array();
		}
		return array(
			'key'        => $vehicle['key'],
			'makeId'     => $vehicle['make_id'],
			'makeName'   => $vehicle['make_name'],
			'modelId'    => $vehicle['model_id'],
			'modelName'  => $vehicle['model_name'],
			'year'       => (int) $vehicle['year'],
			'yearLabel'  => self::fa_digits( $vehicle['year'] ),
			'engine'     => $vehicle['engine'],
			'label'      => $vehicle['model_name'] . ' ' . self::fa_digits( $vehicle['year'] ),
		);
	}

	/**
	 * Persian digits.
	 *
	 * @param string|int $value Value.
	 * @return string
	 */
	public static function fa_digits( $value ) {
		return str_replace( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), (string) $value );
	}

	/**
	 * Latin digits (accepts Persian/Arabic input).
	 *
	 * @param string $value Value.
	 * @return string
	 */
	public static function latin_digits( $value ) {
		$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		$ar = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
		$value = str_replace( $fa, range( 0, 9 ), (string) $value );
		$value = str_replace( $ar, range( 0, 9 ), $value );
		return preg_replace( '/[^\d]/', '', $value );
	}

	/**
	 * Normalize a string for search (unify arabic/persian letters, strip diacritics).
	 *
	 * @param string $value Value.
	 * @return string
	 */
	public static function normalize( $value ) {
		$value = (string) $value;
		$value = str_replace( array( 'ي', 'ك', 'ۀ', 'ة', 'أ', 'إ', 'آ' ), array( 'ی', 'ک', 'ه', 'ه', 'ا', 'ا', 'ا' ), $value );
		$value = preg_replace( '/[\x{064B}-\x{065F}\x{0670}]/u', '', $value );
		$value = str_replace( array( '‌', '-', '_', '(', ')', '‎', '‏' ), ' ', $value );
		$value = mb_strtolower( $value, 'UTF-8' );
		return trim( preg_replace( '/\s+/u', ' ', $value ) );
	}
}
