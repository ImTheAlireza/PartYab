<?php
/**
 * CSV import / export for fitment rows.
 *
 * @package PartYab_Fitment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Partyab_CSV
 */
class Partyab_CSV {

	/**
	 * Hook the admin page.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_partyab_import_fitment', array( __CLASS__, 'handle_import' ) );
		add_action( 'admin_post_partyab_export_fitment', array( __CLASS__, 'handle_export' ) );
	}

	/**
	 * Tools → PartyYab fitment.
	 */
	public static function menu() {
		add_management_page(
			__( 'سازگاری پارت‌یاب', 'partyab' ),
			__( 'سازگاری پارت‌یاب', 'partyab' ),
			'manage_woocommerce',
			'partyab-fitment',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Admin page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'دسترسی ندارید.', 'partyab' ) );
		}

		$message = isset( $_GET['partyab_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['partyab_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$fitted  = Partyab_DB::fitted_product_count();
		$total   = (int) wp_count_posts( 'product' )->publish;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'سازگاری پارت‌یاب', 'partyab' ); ?></h1>

			<?php if ( $message ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
			<?php endif; ?>

			<p>
				<?php
				printf(
					/* translators: 1: fitted products, 2: total products */
					esc_html__( 'برای %1$s محصول از %2$s محصول منتشرشده، جدول سازگاری ثبت شده است.', 'partyab' ),
					esc_html( Partyab_Vehicles::fa_digits( $fitted ) ),
					esc_html( Partyab_Vehicles::fa_digits( $total ) )
				);
				?>
			</p>

			<div class="card" style="max-width:720px">
				<h2><?php esc_html_e( 'ایمپورت CSV', 'partyab' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'ستون‌ها: sku, make_id, model_id, year_from, year_to, status, note — وضعیت می‌تواند fit یا conditional باشد. ردیف‌های هر SKU جایگزین ردیف‌های قبلی همان محصول می‌شوند.', 'partyab' ); ?>
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<?php wp_nonce_field( 'partyab_import_fitment' ); ?>
					<input type="hidden" name="action" value="partyab_import_fitment" />
					<p><input type="file" name="partyab_csv" accept=".csv,text/csv" required /></p>
					<p><label><input type="checkbox" name="replace" value="1" checked /> <?php esc_html_e( 'ردیف‌های قبلی هر محصول حذف شوند', 'partyab' ); ?></label></p>
					<p><button class="button button-primary" type="submit"><?php esc_html_e( 'ایمپورت', 'partyab' ); ?></button></p>
				</form>
			</div>

			<div class="card" style="max-width:720px;margin-top:16px">
				<h2><?php esc_html_e( 'خروجی گرفتن', 'partyab' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'partyab_export_fitment' ); ?>
					<input type="hidden" name="action" value="partyab_export_fitment" />
					<button class="button" type="submit"><?php esc_html_e( 'دانلود CSV سازگاری', 'partyab' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle the upload.
	 */
	public static function handle_import() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'دسترسی ندارید.', 'partyab' ) );
		}
		check_admin_referer( 'partyab_import_fitment' );

		if ( empty( $_FILES['partyab_csv']['tmp_name'] ) ) {
			self::redirect( __( 'فایلی انتخاب نشده بود.', 'partyab' ) );
		}

		$tmp = sanitize_text_field( wp_unslash( $_FILES['partyab_csv']['tmp_name'] ) );
		$fh  = fopen( $tmp, 'r' );
		if ( false === $fh ) {
			self::redirect( __( 'خواندن فایل ممکن نشد.', 'partyab' ) );
		}

		$replace = ! empty( $_POST['replace'] );
		$header  = fgetcsv( $fh );
		if ( ! is_array( $header ) ) {
			fclose( $fh );
			self::redirect( __( 'فایل CSV خالی است.', 'partyab' ) );
		}

		$header[0] = preg_replace( '/^\x{FEFF}/u', '', (string) $header[0] );
		$map       = array_flip( array_map( 'trim', $header ) );

		$by_product = array();
		$unknown    = 0;
		$line       = 1;

		while ( false !== ( $row = fgetcsv( $fh ) ) ) {
			$line++;
			if ( ! is_array( $row ) || count( $row ) < 6 ) {
				continue;
			}

			$get = function ( $key ) use ( $row, $map ) {
				return isset( $map[ $key ], $row[ $map[ $key ] ] ) ? trim( (string) $row[ $map[ $key ] ] ) : '';
			};

			$sku = $get( 'sku' );
			if ( '' === $sku || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
				continue;
			}

			$product_id = (int) wc_get_product_id_by_sku( $sku );
			if ( ! $product_id ) {
				$unknown++;
				continue;
			}

			$by_product[ $product_id ][] = array(
				'model_id'  => $get( 'model_id' ),
				'year_from' => (int) Partyab_Vehicles::latin_digits( $get( 'year_from' ) ),
				'year_to'   => (int) Partyab_Vehicles::latin_digits( $get( 'year_to' ) ),
				'status'    => 'conditional' === $get( 'status' ) ? 'conditional' : 'fit',
				'note'      => $get( 'note' ),
			);
		}

		fclose( $fh );

		$saved = 0;
		foreach ( $by_product as $product_id => $rows ) {
			if ( ! $replace ) {
				foreach ( Partyab_DB::rows_for( $product_id ) as $existing ) {
					$rows[] = $existing;
				}
			}
			$saved += Partyab_DB::replace_for( $product_id, $rows );
		}

		self::redirect(
			sprintf(
				/* translators: 1: rows, 2: products, 3: unknown SKUs */
				__( '%1$s ردیف برای %2$s محصول ذخیره شد. %3$s ردیف با SKU ناشناس رد شد.', 'partyab' ),
				Partyab_Vehicles::fa_digits( $saved ),
				Partyab_Vehicles::fa_digits( count( $by_product ) ),
				Partyab_Vehicles::fa_digits( $unknown )
			)
		);
	}

	/**
	 * Stream a CSV export.
	 */
	public static function handle_export() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'دسترسی ندارید.', 'partyab' ) );
		}
		check_admin_referer( 'partyab_export_fitment' );

		global $wpdb;
		$table = Partyab_DB::table();
		$rows  = $wpdb->get_results( "SELECT product_id, make_id, model_id, year_from, year_to, status, note FROM {$table} ORDER BY product_id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=partyab-fitment.csv' );

		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" );
		fputcsv( $out, array( 'sku', 'make_id', 'model_id', 'year_from', 'year_to', 'status', 'note' ) );

		foreach ( (array) $rows as $row ) {
			$sku = function_exists( 'wc_get_product' ) ? wc_get_product( (int) $row['product_id'] ) : null;
			fputcsv(
				$out,
				array(
					$sku ? $sku->get_sku() : '',
					$row['make_id'],
					$row['model_id'],
					$row['year_from'],
					$row['year_to'],
					$row['status'],
					$row['note'],
				)
			);
		}

		fclose( $out );
		exit;
	}

	/**
	 * Redirect back with a message.
	 *
	 * @param string $message Message.
	 */
	private static function redirect( $message ) {
		$url = add_query_arg(
			array(
				'page'        => 'partyab-fitment',
				'partyab_msg' => rawurlencode( $message ),
			),
			admin_url( 'tools.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}
}
