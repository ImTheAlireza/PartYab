<?php
/**
 * REST API: vehicle catalog, model search, fitment state.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_REST
 */
class Partyab_REST {

	const NS = 'partyab/v1';

	/**
	 * Register routes.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Route definitions.
	 */
	public static function register() {
		register_rest_route(
			self::NS,
			'/vehicles',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_vehicles' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NS,
			'/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'search_models' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'q' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/state',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_state' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'product' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/vehicle',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'set_vehicle' ),
				'permission_callback' => '__return_true',
				'args'                => array(),
			)
		);

		register_rest_route(
			self::NS,
			'/coverage',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_coverage' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Full catalog (grouped).
	 *
	 * @return WP_REST_Response
	 */
	public static function get_vehicles() {
		$out = array(
			'groups' => Partyab_Vehicles::groups(),
			'makes'  => array(),
		);

		foreach ( Partyab_Vehicles::makes() as $make ) {
			$models = array();
			foreach ( (array) $make['models'] as $model ) {
				$models[] = array(
					'id'      => $model['id'],
					'name'    => $model['name'],
					'years'   => array( (int) $model['years'][0], (int) $model['years'][1] ),
					'engines' => isset( $model['engines'] ) ? array_values( (array) $model['engines'] ) : array(),
				);
			}
			$out['makes'][] = array(
				'id'     => $make['id'],
				'name'   => $make['name'],
				'latin'  => isset( $make['latin'] ) ? $make['latin'] : '',
				'group'  => isset( $make['group'] ) ? $make['group'] : 'iran',
				'models' => $models,
			);
		}

		return new WP_REST_Response( $out, 200 );
	}

	/**
	 * Typed model search.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function search_models( $request ) {
		$query = (string) $request->get_param( 'q' );
		$hits  = Partyab_Vehicles::search( $query, 8 );

		$out = array();
		foreach ( $hits as $model ) {
			$out[] = array(
				'id'       => $model['id'],
				'name'     => $model['name'],
				'makeId'   => $model['make_id'],
				'makeName' => $model['make_name'],
				'years'    => array( (int) $model['years'][0], (int) $model['years'][1] ),
				'engines'  => array_values( (array) $model['engines'] ),
			);
		}

		return new WP_REST_Response( array( 'results' => $out, 'query' => $query ), 200 );
	}

	/**
	 * Fitment state of one product for the visitor vehicle.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function get_state( $request ) {
		$product_id = (int) $request->get_param( 'product' );
		$vehicle    = Partyab_Vehicles::current();
		$info       = Partyab_Fitment::state( $product_id, $vehicle );

		return new WP_REST_Response(
			array(
				'product' => $product_id,
				'vehicle' => Partyab_Vehicles::to_public( $vehicle ),
				'state'   => $info['state'],
				'note'    => isset( $info['note'] ) ? $info['note'] : '',
				'line'    => Partyab_Fitment::card_line( $product_id, $vehicle ),
			),
			200
		);
	}

	/**
	 * Validate + echo back a vehicle (the client stores it in a cookie).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function set_vehicle( $request ) {
		$raw     = $request->get_json_params();
		$vehicle = Partyab_Vehicles::sanitize( is_array( $raw ) ? $raw : array() );

		if ( ! $vehicle ) {
			return new WP_Error( 'partyab_invalid_vehicle', __( 'خودروی انتخاب‌شده معتبر نیست.', 'partyab' ), array( 'status' => 400 ) );
		}

		return new WP_REST_Response(
			array(
				'vehicle'  => Partyab_Vehicles::to_public( $vehicle ),
				'coverage' => Partyab_Fitment::coverage( $vehicle ),
			),
			200
		);
	}

	/**
	 * Coverage of the current vehicle.
	 *
	 * @return WP_REST_Response
	 */
	public static function get_coverage() {
		$vehicle = Partyab_Vehicles::current();

		return new WP_REST_Response(
			array(
				'vehicle'  => Partyab_Vehicles::to_public( $vehicle ),
				'coverage' => Partyab_Fitment::coverage( $vehicle ),
			),
			200
		);
	}
}
