<?php
/**
 * Admin: fitment meta box + product column.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_Admin
 */
class Partyab_Admin {

	/**
	 * Hook everything.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_product', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'manage_edit-product_columns', array( __CLASS__, 'column' ), 20 );
		add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
	}

	/**
	 * Meta box registration.
	 */
	public static function add_meta_box() {
		add_meta_box(
			'partyab_fitment',
			__( 'سازگاری خودرو (پارت‌یاب)', 'partyab' ),
			array( __CLASS__, 'render' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * Meta box markup.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render( $post ) {
		wp_nonce_field( 'partyab_fitment_save', 'partyab_fitment_nonce' );

		$rows      = Partyab_DB::rows_for( $post->ID );
		$universal = Partyab_Fitment::is_universal( $post->ID );
		$note      = Partyab_Fitment::universal_note( $post->ID );

		echo '<div class="partyab-fitment-box">';
		echo '<p class="description">' . esc_html__( 'خودروهایی را که این قطعه با آن‌ها سازگار است اضافه کنید. اگر هیچ ردیفی نگذارید، وضعیت «ثبت نشده» نمایش داده می‌شود (نامشخص، نه ناسازگار).', 'partyab' ) . '</p>';

		echo '<p><label><input type="checkbox" name="partyab_universal" value="yes" ' . checked( $universal, true, false ) . ' /> ';
		echo esc_html__( 'این قطعه عمومی است (مثل روغن موتور) و به خودروی خاصی محدود نیست.', 'partyab' ) . '</label></p>';

		echo '<p><input type="text" class="regular-text" name="partyab_universal_note" value="' . esc_attr( $note ) . '" placeholder="' . esc_attr__( 'توضیح اختیاری برای قطعهٔ عمومی', 'partyab' ) . '" /></p>';

		echo '<table class="widefat striped partyab-rows"><thead><tr>';
		echo '<th>' . esc_html__( 'مدل خودرو', 'partyab' ) . '</th>';
		echo '<th style="width:90px">' . esc_html__( 'از سال', 'partyab' ) . '</th>';
		echo '<th style="width:90px">' . esc_html__( 'تا سال', 'partyab' ) . '</th>';
		echo '<th style="width:130px">' . esc_html__( 'وضعیت', 'partyab' ) . '</th>';
		echo '<th>' . esc_html__( 'شرط / توضیح', 'partyab' ) . '</th>';
		echo '<th style="width:50px"></th>';
		echo '</tr></thead><tbody id="partyab-rows-body">';

		if ( empty( $rows ) ) {
			$rows = array( array( 'model_id' => '', 'year_from' => '', 'year_to' => '', 'status' => 'fit', 'note' => '' ) );
		}

		foreach ( $rows as $row ) {
			self::row_html( $row );
		}

		echo '</tbody></table>';

		echo '<p><button type="button" class="button" id="partyab-add-row">' . esc_html__( '+ افزودن خودرو', 'partyab' ) . '</button></p>';
		echo '<template id="partyab-row-template">';
		self::row_html( array( 'model_id' => '', 'year_from' => '', 'year_to' => '', 'status' => 'fit', 'note' => '' ) );
		echo '</template>';
		echo '</div>';
	}

	/**
	 * A single fitment row.
	 *
	 * @param array $row Row data.
	 */
	private static function row_html( $row ) {
		$model_id = isset( $row['model_id'] ) ? (string) $row['model_id'] : '';
		$from     = isset( $row['year_from'] ) ? (string) $row['year_from'] : '';
		$to       = isset( $row['year_to'] ) ? (string) $row['year_to'] : '';
		$status   = isset( $row['status'] ) ? (string) $row['status'] : 'fit';
		$note     = isset( $row['note'] ) ? (string) $row['note'] : '';

		echo '<tr>';
		echo '<td>';
		echo '<select name="partyab_model[]" style="width:100%">';
		echo '<option value="">' . esc_html__( '— انتخاب مدل —', 'partyab' ) . '</option>';
		foreach ( Partyab_Vehicles::makes() as $make ) {
			if ( empty( $make['models'] ) ) {
				continue;
			}
			echo '<optgroup label="' . esc_attr( $make['name'] ) . '">';
			foreach ( $make['models'] as $model ) {
				$years = isset( $model['years'] ) ? $model['years'] : array( 0, 0 );
				$label = $model['name'] . ' (' . Partyab_Vehicles::fa_digits( $years[0] ) . '–' . Partyab_Vehicles::fa_digits( $years[1] ) . ')';
				echo '<option value="' . esc_attr( $model['id'] ) . '" data-from="' . esc_attr( $years[0] ) . '" data-to="' . esc_attr( $years[1] ) . '" ' . selected( $model_id, $model['id'], false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</optgroup>';
		}
		echo '</select>';
		echo '</td>';
		echo '<td><input type="number" name="partyab_from[]" value="' . esc_attr( $from ) . '" min="1300" max="1500" style="width:80px" /></td>';
		echo '<td><input type="number" name="partyab_to[]" value="' . esc_attr( $to ) . '" min="1300" max="1500" style="width:80px" /></td>';
		echo '<td><select name="partyab_status[]">';
		echo '<option value="fit" ' . selected( $status, 'fit', false ) . '>' . esc_html__( 'سازگار', 'partyab' ) . '</option>';
		echo '<option value="conditional" ' . selected( $status, 'conditional', false ) . '>' . esc_html__( 'سازگار با شرط', 'partyab' ) . '</option>';
		echo '</select></td>';
		echo '<td><input type="text" name="partyab_note[]" value="' . esc_attr( $note ) . '" style="width:100%" placeholder="' . esc_attr__( 'مثلاً فقط تیپ ۵ و ۶', 'partyab' ) . '" /></td>';
		echo '<td><button type="button" class="button-link partyab-del-row" aria-label="' . esc_attr__( 'حذف ردیف', 'partyab' ) . '">×</button></td>';
		echo '</tr>';
	}

	/**
	 * Inline JS for the repeater (kept tiny and dependency-free).
	 *
	 * @param string $hook Current admin page.
	 */
	public static function assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || 'product' !== $screen->post_type ) {
			return;
		}
		?>
		<style>
			.partyab-fitment-box { padding: 6px 0 2px; }
			.partyab-rows td { vertical-align: middle; }
			.partyab-rows select, .partyab-rows input[type="number"] { height: 30px; }
			.partyab-del-row { color: #b32d2e; font-size: 18px; line-height: 1; cursor: pointer; }
		</style>
		<script>
		( function () {
			var body = document.getElementById( 'partyab-rows-body' );
			var tpl  = document.getElementById( 'partyab-row-template' );
			var add  = document.getElementById( 'partyab-add-row' );
			if ( ! body || ! tpl || ! add ) { return; }

			add.addEventListener( 'click', function () {
				body.appendChild( tpl.content.cloneNode( true ) );
			} );

			body.addEventListener( 'click', function ( e ) {
				if ( ! e.target.classList.contains( 'partyab-del-row' ) ) { return; }
				var rows = body.querySelectorAll( 'tr' );
				if ( rows.length === 1 ) {
					rows[0].querySelectorAll( 'input, select' ).forEach( function ( el ) { el.value = ''; } );
					return;
				}
				e.target.closest( 'tr' ).remove();
			} );

			body.addEventListener( 'change', function ( e ) {
				if ( e.target.name !== 'partyab_model[]' ) { return; }
				var opt = e.target.options[ e.target.selectedIndex ];
				var tr  = e.target.closest( 'tr' );
				var from = tr.querySelector( 'input[name="partyab_from[]"]' );
				var to   = tr.querySelector( 'input[name="partyab_to[]"]' );
				if ( opt && opt.dataset.from && ! from.value ) { from.value = opt.dataset.from; }
				if ( opt && opt.dataset.to && ! to.value ) { to.value = opt.dataset.to; }
			} );
		} )();
		</script>
		<?php
	}

	/**
	 * Save the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post.
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['partyab_fitment_nonce'] ) ) {
			return;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['partyab_fitment_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'partyab_fitment_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! $post || 'product' !== $post->post_type ) {
			return;
		}

		$universal = isset( $_POST['partyab_universal'] ) ? 'yes' : '';
		if ( $universal ) {
			update_post_meta( $post_id, '_partyab_universal', 'yes' );
		} else {
			delete_post_meta( $post_id, '_partyab_universal' );
		}

		$note = isset( $_POST['partyab_universal_note'] ) ? sanitize_text_field( wp_unslash( $_POST['partyab_universal_note'] ) ) : '';
		if ( '' !== $note ) {
			update_post_meta( $post_id, '_partyab_universal_note', $note );
		} else {
			delete_post_meta( $post_id, '_partyab_universal_note' );
		}

		$models = isset( $_POST['partyab_model'] ) ? (array) wp_unslash( $_POST['partyab_model'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$froms  = isset( $_POST['partyab_from'] ) ? (array) wp_unslash( $_POST['partyab_from'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$tos    = isset( $_POST['partyab_to'] ) ? (array) wp_unslash( $_POST['partyab_to'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$states = isset( $_POST['partyab_status'] ) ? (array) wp_unslash( $_POST['partyab_status'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$notes  = isset( $_POST['partyab_note'] ) ? (array) wp_unslash( $_POST['partyab_note'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		$rows = array();
		foreach ( $models as $i => $model_id ) {
			$model_id = sanitize_key( (string) $model_id );
			if ( '' === $model_id ) {
				continue;
			}
			$rows[] = array(
				'model_id'  => $model_id,
				'year_from' => isset( $froms[ $i ] ) ? (int) Partyab_Vehicles::latin_digits( (string) $froms[ $i ] ) : 0,
				'year_to'   => isset( $tos[ $i ] ) ? (int) Partyab_Vehicles::latin_digits( (string) $tos[ $i ] ) : 0,
				'status'    => isset( $states[ $i ] ) ? sanitize_key( (string) $states[ $i ] ) : 'fit',
				'note'      => isset( $notes[ $i ] ) ? sanitize_text_field( (string) $notes[ $i ] ) : '',
			);
		}

		Partyab_DB::replace_for( $post_id, $rows );
	}

	/**
	 * Add the "fitment" column.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function column( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'name' === $key ) {
				$new['partyab_fitment'] = __( 'سازگاری', 'partyab' );
			}
		}
		return $new;
	}

	/**
	 * Render the column value.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Product ID.
	 */
	public static function column_content( $column, $post_id ) {
		if ( 'partyab_fitment' !== $column ) {
			return;
		}

		if ( Partyab_Fitment::is_universal( $post_id ) ) {
			echo '<span style="color:#2563eb">' . esc_html__( 'عمومی', 'partyab' ) . '</span>';
			return;
		}

		$rows = Partyab_DB::rows_for( $post_id );

		if ( empty( $rows ) ) {
			echo '<span style="color:#b45309">' . esc_html__( 'ثبت نشده', 'partyab' ) . '</span>';
			return;
		}

		$conditional = 0;
		foreach ( $rows as $row ) {
			if ( 'conditional' === $row['status'] ) {
				$conditional++;
			}
		}

		echo esc_html( sprintf( __( '%d خودرو', 'partyab' ), count( $rows ) ) );
		if ( $conditional ) {
			echo '<br><small style="color:#b45309">' . esc_html( sprintf( __( '%d مورد با شرط', 'partyab' ), $conditional ) ) . '</small>';
		}
	}
}
