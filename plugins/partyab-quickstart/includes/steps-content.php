<?php
/**
 * مرحله ۴: محتوای دمو — دسته‌ها، ۶ محصول با عکس، صفحات، منو، داده سازگاری.
 *
 * @package PartYab_QuickStart
 */

defined( 'ABSPATH' ) || exit;

/**
 * پیوست کردن تصویر از vendor به کتابخانه رسانه (بدون تکرار).
 *
 * @param string $path مسیر فایل.
 * @return int شناسه پیوست.
 */
function partyab_qs_attach_image( string $path ): int {
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$basename = basename( $path );
	$hash     = (string) md5_file( $path );

	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_partyab_demo_image',
		'meta_value'     => $basename,
	) );

	if ( $existing ) {
		$id = (int) $existing[0];

		// اگر عکس بسته با عکس نصب‌شده فرق دارد، فایل را همان‌جا جایگزین کن
		// تا محصولات و برگه‌ها بدون تغییر لینک، عکس تازه را نشان دهند.
		if ( get_post_meta( $id, '_partyab_demo_hash', true ) !== $hash ) {
			partyab_qs_replace_attachment_file( $id, $path, $hash );
		}

		return $id;
	}

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $basename );
	copy( $path, $tmp );

	$id = media_handle_sideload(
		array(
			'name'     => $basename,
			'tmp_name' => $tmp,
		),
		0
	);

	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return 0;
	}

	update_post_meta( $id, '_partyab_demo_image', $basename );
	update_post_meta( $id, '_partyab_demo_hash', $hash );

	return (int) $id;
}

/**
 * جایگزینی فایل یک پیوست موجود و بازسازی بندانگشتی‌ها.
 *
 * چرا؟ چون شناسه پیوست در محصولات ذخیره شده؛ اگر پیوست جدید بسازیم
 * محصولات عکس قدیمی را نگه می‌دارند.
 *
 * @param int    $id   شناسه پیوست.
 * @param string $path فایل تازه.
 * @param string $hash هش فایل تازه.
 */
function partyab_qs_replace_attachment_file( int $id, string $path, string $hash ): void {
	$relative = (string) get_post_meta( $id, '_wp_attached_file', true );
	$uploads  = wp_get_upload_dir();

	if ( '' === $relative || empty( $uploads['basedir'] ) ) {
		return;
	}

	$target = trailingslashit( $uploads['basedir'] ) . $relative;

	if ( ! @copy( $path, $target ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$meta = wp_generate_attachment_metadata( $id, $target );

	if ( ! is_wp_error( $meta ) ) {
		wp_update_attachment_metadata( $id, $meta );
	}

	update_post_meta( $id, '_partyab_demo_hash', $hash );

	$GLOBALS['partyab_qs_images_refreshed'] = (int) ( $GLOBALS['partyab_qs_images_refreshed'] ?? 0 ) + 1;
}

/**
 * ساخت دسته محصول در صورت نبود.
 *
 * @param string $name نام.
 * @param string $slug اسلاگ.
 * @return int شناسه دسته.
 */
function partyab_qs_category( string $name, string $slug ): int {
	$term = term_exists( $slug, 'product_cat' );

	if ( $term ) {
		return (int) ( is_array( $term ) ? $term['term_id'] : $term );
	}

	$created = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );

	return is_wp_error( $created ) ? 0 : (int) $created['term_id'];
}

/**
 * ساخت/به‌روزرسانی یک محصول ساده.
 *
 * @param array $data داده محصول.
 * @param int   $cat_id دسته.
 * @return int شناسه محصول.
 */
function partyab_qs_product( array $data, int $cat_id ): int {
	if ( ! class_exists( 'WC_Product_Simple' ) ) {
		return 0;
	}

	$existing = wc_get_product_id_by_sku( $data['sku'] );
	$product  = $existing ? wc_get_product( $existing ) : new WC_Product_Simple();

	if ( ! $product ) {
		$product = new WC_Product_Simple();
	}

	$product->set_name( $data['name'] );
	$product->set_sku( $data['sku'] );
	$product->set_regular_price( (string) $data['price'] );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( (int) $data['stock'] );
	$product->set_stock_status( (int) $data['stock'] > 0 ? 'instock' : 'outofstock' );
	$product->set_short_description( $data['short'] );
	$product->set_description( $data['desc'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );

	if ( ! empty( $data['sale'] ) ) {
		$product->set_sale_price( (string) $data['sale'] );
	}

	if ( $cat_id ) {
		$product->set_category_ids( array( $cat_id ) );
	}

	$image = partyab_qs_attach_image( partyab_qs_vendor_dir() . 'demo-images/' . $data['image'] );
	if ( $image ) {
		$product->set_image_id( $image );
	}

	return (int) $product->save();
}

/**
 * ساخت صفحه در صورت نبود (نام تابع با رندر صفحه پیشخوان فرق دارد).
 *
 * @param string $title عنوان.
 * @param string $slug اسلاگ.
 * @param string $content محتوا.
 * @return int شناسه صفحه.
 */
function partyab_qs_create_page( string $title, string $slug, string $content ): int {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return (int) $page->ID;
	}

	return (int) wp_insert_post( array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	) );
}

/**
 * اجرای مرحله محتوا.
 *
 * @param array $report گزارش.
 */
/**
 * اطمینان از وجود صفحات ووکامرس (سبد خرید، تسویه، حساب کاربری) و اتصال آن‌ها.
 *
 * چرا لازم است؟ اگر ووکامرس به‌صورت برنامه‌ای فعال شود، صفحاتش ساخته نمی‌شوند و
 * سبد خرید صفحه‌ای خالی می‌ماند.
 *
 * @param array $report گزارش.
 */
function partyab_qs_ensure_woo_pages( array &$report ): void {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		partyab_qs_log( $report, 'محتوا', 'صفحات فروشگاه', false, 'ووکامرس در دسترس نیست' );
		return;
	}

	$pages = array(
		'cart'      => array( 'سبد خرید', 'cart', '[woocommerce_cart]' ),
		'checkout'  => array( 'تسویه حساب', 'checkout', '[woocommerce_checkout]' ),
		'myaccount' => array( 'حساب کاربری', 'my-account', '[woocommerce_my_account]' ),
	);

	$created = 0;

	foreach ( $pages as $key => $data ) {
		$page_id  = (int) wc_get_page_id( $key );
		$existing = $page_id > 0 ? get_post( $page_id ) : null;

		if ( ! $existing || 'page' !== $existing->post_type ) {
			$page_id = partyab_qs_create_page( $data[0], $data[1], $data[2] );
			$created++;
		// صفحه هست ولی شورت‌کد ندارد (صفحه خالی ساخته‌شده دستی).
		} elseif ( ! has_shortcode( (string) $existing->post_content, trim( $data[2], '[]' ) ) ) {
			wp_update_post( array(
				'ID'           => $page_id,
				'post_content' => $data[2],
				'post_status'  => 'publish',
			) );
			$created++;
		}

		if ( $page_id > 0 ) {
			update_option( 'woocommerce_' . $key . '_page_id', $page_id );
		}
	}

	// صفحه فروشگاه.
	$shop_id = (int) wc_get_page_id( 'shop' );
	if ( $shop_id > 0 ) {
		update_option( 'woocommerce_shop_page_id', $shop_id );
	}

	// پرmalink‌ها یک‌بار بازسازی شوند تا /shop و /cart درست باز شوند.
	flush_rewrite_rules( false );

	partyab_qs_log(
		$report,
		'محتوا',
		'صفحات فروشگاه (سبد، تسویه، حساب)',
		true,
		$created > 0 ? $created . ' صفحه ساخته/اصلاح شد' : 'هر سه صفحه سالم بودند'
	);
}

/**
 * ساخت شاخه برند/خودرو و اتصال محصولات دمو به آن‌ها.
 *
 * برندهای خودرو از فهرست رایج ایرانی ساخته می‌شوند؛ مدل‌ها از فایل سازگاری.
 *
 * @param array $report گزارش.
 */
function partyab_qs_seed_taxonomies( array &$report ): void {
	if ( ! taxonomy_exists( 'pb_car' ) || ! taxonomy_exists( 'pb_brand' ) ) {
		partyab_qs_log( $report, 'ساختار', 'برند و خودرو', false, 'قالب قدیمی است؛ تاکسونومی‌ها ثبت نشده‌اند' );
		return;
	}

	$cars = array(
		'پراید'   => array( '131', '111', '132', '141' ),
		'پژو'     => array( '405', 'پارس', '206', '207' ),
		'سمند'    => array( 'سمند', 'EF7', 'سورن' ),
		'دنا'     => array( 'دنا', 'دنا پلاس' ),
		'تیبا'    => array( 'تیبا', 'تیبا ۲' ),
		'ساینا'   => array( 'ساینا', 'ساینا S' ),
		'کوییک'   => array( 'کوییک', 'کوییک R' ),
		'شاهین'   => array( 'شاهین' ),
		'رانا'    => array( 'رانا', 'رانا پلاس' ),
		'آریسان'  => array( 'آریسان' ),
	);

	$brands = array( 'ایساکو', 'سایپا یدک', 'پارس یدک', 'کروز', 'بوش', 'سپاهان باتری', 'وامکو' );

	$created_terms = 0;

	foreach ( $cars as $brand => $models ) {
		$parent = term_exists( $brand, 'pb_car' );

		if ( ! $parent ) {
			$parent = wp_insert_term( $brand, 'pb_car' );
			$created_terms++;
		}

		if ( is_wp_error( $parent ) ) {
			continue;
		}

		$parent_id = (int) ( is_array( $parent ) ? $parent['term_id'] : $parent );

		foreach ( $models as $model ) {
			$child = term_exists( $model, 'pb_car', $parent_id );

			if ( ! $child ) {
				wp_insert_term( $model, 'pb_car', array( 'parent' => $parent_id ) );
				$created_terms++;
			}
		}
	}

	foreach ( $brands as $brand ) {
		if ( ! term_exists( $brand, 'pb_brand' ) ) {
			wp_insert_term( $brand, 'pb_brand' );
			$created_terms++;
		}
	}

	partyab_qs_log(
		$report,
		'ساختار',
		'برند و خودرو',
		true,
		count( $cars ) . ' برند خودرو، ' . $created_terms . ' ترم ساخته/بررسی شد'
	);

	// اتصال محصولات دمو به خودروهای سازگار از روی CSV.
	$mapping = array(
		'PB-BRK-001' => 'ایساکو',
		'PB-FLT-002' => 'ایساکو',
		'PB-SPK-003' => 'بوش',
		'PB-BAT-004' => 'سپاهان باتری',
		'PB-SHK-005' => 'کروز',
		'PB-WPR-006' => 'وامکو',
	);
	$linked = 0;

	foreach ( $mapping as $sku => $brand ) {
		$product_id = function_exists( 'wc_get_product_id_by_sku' ) ? (int) wc_get_product_id_by_sku( $sku ) : 0;

		if ( $product_id > 0 ) {
			wp_set_object_terms( $product_id, array( $brand ), 'pb_brand', false );
			$linked++;
		}
	}

	$csv = partyab_qs_vendor_dir() . 'compat-sample.csv';

	if ( ! file_exists( $csv ) ) {
		partyab_qs_log( $report, 'ساختار', 'سازگاری با خودرو', false, 'compat-sample.csv پیدا نشد' );
		return;
	}

	$handle = fopen( $csv, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! $handle ) {
		return;
	}

	$header = fgetcsv( $handle );
	$rows   = 0;

	if ( is_array( $header ) ) {
		$header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $header[0] );
	}

	while ( ( $row = fgetcsv( $handle ) ) !== false ) {
		if ( count( $row ) < 3 || empty( $row[0] ) ) {
			continue;
		}

		$product_id = function_exists( 'wc_get_product_id_by_sku' ) ? (int) wc_get_product_id_by_sku( trim( $row[0] ) ) : 0;

		if ( $product_id <= 0 ) {
			continue;
		}

		$car_brand = trim( $row[1] );
		$car_model = trim( $row[2] );
		$parent    = get_term_by( 'name', $car_brand, 'pb_car' );
		$terms     = array();

		if ( ! $parent ) {
			$parent = wp_insert_term( $car_brand, 'pb_car' );
			$parent = is_wp_error( $parent ) ? null : get_term_by( 'id', (int) $parent['term_id'], 'pb_car' );
		}

		if ( $parent ) {
			$terms[] = (int) $parent->term_id;

			$child = term_exists( $car_model, 'pb_car', (int) $parent->term_id );
			$child = $child ? get_term_by( 'id', (int) ( is_array( $child ) ? $child['term_id'] : $child ), 'pb_car' ) : false;

			if ( $child && (int) $child->parent === (int) $parent->term_id ) {
				$terms[] = (int) $child->term_id;
			} else {
				$new = wp_insert_term( $car_model, 'pb_car', array( 'parent' => (int) $parent->term_id ) );

				if ( ! is_wp_error( $new ) && isset( $new['term_id'] ) ) {
					$terms[] = (int) $new['term_id'];
				}
			}
		}

		if ( $terms ) {
			wp_set_object_terms( $product_id, $terms, 'pb_car', false );
			$rows++;
		}
	}

	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	partyab_qs_log( $report, 'ساختار', 'اتصال محصول به خودرو', $rows > 0, $rows . ' محصول به خودروهای سازگار وصل شد' );
}

function partyab_qs_step_content( array &$report ): void {
	partyab_qs_ensure_woo_pages( $report );

	$cats = array(
		'brake-system' => partyab_qs_category( 'سیستم ترمز', 'brake-system' ),
		'filters'      => partyab_qs_category( 'فیلترها', 'filters' ),
		'electrical'   => partyab_qs_category( 'برق و باتری', 'electrical' ),
		'suspension'   => partyab_qs_category( 'سیستم تعلیق', 'suspension' ),
		'body-glass'   => partyab_qs_category( 'بدنه و شیشه', 'body-glass' ),
	);

	partyab_qs_log( $report, 'محتوا', 'دسته‌های محصول', true, count( $cats ) . ' دسته ساخته/بررسی شد' );

	$products = array(
		array(
			'sku'   => 'PB-BRK-001',
			'name'  => 'لنت ترمز جلو پراید — طرح اصلی',
			'price' => 850000,
			'sale'  => 690000,
			'stock' => 24,
			'cat'   => 'brake-system',
			'image' => 'brake-pad.jpg',
			'short' => 'لنت ترمز جلو، سازگار با پراید ۱۳۱، ۱۱۱ و ۱۴۱ — با ضمانت اصالت کالا.',
			'desc'  => 'جنس فلاکس با ضریب اصطکاک FF. بدون صدا در ترمز‌های شهری. فروش به‌صورت ست (۴ عدد).',
		),
		array(
			'sku'   => 'PB-FLT-002',
			'name'  => 'فیلتر روغن پژو ۴۰۵ و پارس',
			'price' => 320000,
			'sale'  => 0,
			'stock' => 60,
			'cat'   => 'filters',
			'image' => 'oil-filter.jpg',
			'short' => 'فیلتر روغن موتور بنزینی، سازگار با پژو ۴۰۵ و پارس.',
			'desc'  => 'دریچه یک‌طرفه با آب‌بندی کامل. تعویض هر ۱۰ هزار کیلومتر.',
		),
		array(
			'sku'   => 'PB-SPK-003',
			'name'  => 'شمع دوگانه‌سوز سمند و دنا',
			'price' => 180000,
			'sale'  => 0,
			'stock' => 120,
			'cat'   => 'electrical',
			'image' => 'spark-plug.jpg',
			'short' => 'شمع ایریدیم، سازگار با سمند و دنا EF7 — بسته دو عددی.',
			'desc'  => 'الکترود ایریدیم با عمر بالای ۶۰ هزار کیلومتر. احتراق کامل‌تر و استارت سریع‌تر.',
		),
		array(
			'sku'   => 'PB-BAT-004',
			'name'  => 'باتری ۵۵ آمپر تیبا و ساینا',
			'price' => 4200000,
			'sale'  => 3900000,
			'stock' => 8,
			'cat'   => 'electrical',
			'image' => 'battery.jpg',
			'short' => 'باتری سیلد ۵۵ آمپر با گارانتی ۱۸ ماهه — تحویل با باربری.',
			'desc'  => 'مناسب تیبا و ساینا. جریان راه‌انداز ۴۵۰ آمپر. قیمت شامل باتری فرسوده نیست.',
		),
		array(
			'sku'   => 'PB-SHK-005',
			'name'  => 'کمک فنر جلو ساینا و تیبا',
			'price' => 1950000,
			'sale'  => 0,
			'stock' => 12,
			'cat'   => 'suspension',
			'image' => 'shock-absorber.jpg',
			'short' => 'کمک فنر گازی جلو، مناسب ساینا و تیبا — فروش به‌صورت جفت.',
			'desc'  => 'میرایی گازی با آب‌بندی دوگانه. تعویض جفتی توصیه می‌شود.',
		),
		array(
			'sku'   => 'PB-WPR-006',
			'name'  => 'تیغه برف‌پاک‌کن ۲۴ اینچ',
			'price' => 240000,
			'sale'  => 0,
			'stock' => 0,
			'cat'   => 'body-glass',
			'image' => 'wiper-blade.jpg',
			'short' => 'تیغه برف‌پاک‌کن تیغه‌ای ۲۴ اینچ — این محصول فعلاً ناموجود است.',
			'desc'  => 'لاستیک گرافیتی با فریم آیرودینامیک. سازگار با پژو ۴۰۵ و سمند.',
		),
	);

	$created = 0;

	foreach ( $products as $item ) {
		$cat_id = $cats[ $item['cat'] ] ?? 0;
		$id     = partyab_qs_product( $item, $cat_id );

		if ( $id ) {
			++$created;
		}
	}

	partyab_qs_log( $report, 'محتوا', 'محصولات دمو با عکس', $created === count( $products ), $created . ' از ' . count( $products ) . ' محصول ساخته شد' );

	// ---- صفحات ----
	$hero = '<div class="pb-container" style="padding:64px 24px;text-align:center">'
		. '<h1 style="font-size:34px;margin-bottom:12px">قطعه درست، همان بار اول</h1>'
		. '<p style="max-width:640px;margin:0 auto 24px;color:#6b7280">خودروی خود را انتخاب کنید و فقط قطعات سازگار را ببینید. ضمانت اصالت کالا، ارسال سریع، مرجوعی ۷ روزه.</p>'
		. '<a class="pb-btn" href="/shop">ورود به فروشگاه</a> '
		. '<a class="pb-btn pb-btn--ghost" href="/my-car" style="margin-inline-start:8px">خودروی من</a></div>';

	$home = partyab_qs_create_page( 'خانه', 'home', $hero );
	$page_my_car = partyab_qs_create_page( 'خودروی من', 'my-car', '[partyab_my_car]' );
	$page_contact = partyab_qs_create_page( 'تماس با ما', 'contact', '<p>شماره تماس و فرم در قطعه ۱۳ تکمیل می‌شود.</p>' );

	// صفحه خودروی من همیشه شورت‌کد انتخابگر را داشته باشد (تکرارپذیر).
	if ( $page_my_car ) {
		wp_update_post( array(
			'ID'           => $page_my_car,
			'post_content' => "<!-- خودروی من -->\n[partyab_my_car]",
		) );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );

	partyab_qs_log( $report, 'محتوا', 'صفحات (خانه، خودروی من، تماس)', $home > 0, 'صفحه اصلی ست شد' );

	// ---- منو ----
	$menu_name = 'منوی اصلی';
	$menu      = wp_get_nav_menu_object( $menu_name );
	$menu_id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );

	if ( $menu_id && ! is_wp_error( $menu_id ) ) {
		if ( ! $menu ) {
			$shop = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;

			if ( $shop > 0 ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => 'فروشگاه',
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $shop,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}

			foreach ( array( $home => 'خانه', $page_my_car => 'خودروی من', $page_contact => 'تماس با ما' ) as $pid => $label ) {
				if ( $pid ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title'     => $label,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $pid,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					) );
				}
			}
		}

		$locations                   = (array) get_theme_mod( 'nav_menu_locations', array() );
		$locations['partyab-primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		partyab_qs_log( $report, 'محتوا', 'منوی اصلی', true, 'به جایگاه هدر وصل شد' );
	}

	// ---- داده سازگاری ----
	partyab_qs_step_compat( $report );

	flush_rewrite_rules( false );
}

/**
 * نصب/فعال‌سازی افزونه سازگاری و ایمپورت CSV همراه.
 *
 * @param array $report گزارش.
 */
function partyab_qs_step_compat( array &$report ): void {
	if ( ! function_exists( 'partyab_compat_import_csv' ) ) {
		$zip = partyab_qs_find_zip( 'partyab-compat' );

		if ( $zip ) {
			$install = partyab_qs_install_plugin_zip( $zip );
			partyab_qs_log( $report, 'سازگاری', 'نصب افزونه سازگاری', $install['ok'], $install['msg'] );
			partyab_qs_activate_plugin( 'partyab-compat' );
		}
	}

	if ( ! function_exists( 'partyab_compat_import_csv' ) ) {
		partyab_qs_log( $report, 'سازگاری', 'ایمپورت داده', false, 'افزونه سازگاری فعال نشد' );

		return;
	}

	$csv = partyab_qs_vendor_dir() . 'compat-sample.csv';

	if ( ! file_exists( $csv ) ) {
		partyab_qs_log( $report, 'سازگاری', 'ایمپورت داده', false, 'فایل compat-sample.csv در vendor نبود' );

		return;
	}

	$result = partyab_compat_import_csv( $csv, 'replace' );

	partyab_qs_log(
		$report,
		'سازگاری',
		'ایمپورت جدول سازگاری',
		$result['inserted'] > 0,
		$result['inserted'] . ' ردیف درج، ' . $result['skipped'] . ' ردیف رد'
	);

	foreach ( array_slice( $result['errors'], 0, 5 ) as $error ) {
		partyab_qs_log( $report, 'سازگاری', 'هشدار ایمپورت', false, $error );
	}

	$refreshed = (int) ( $GLOBALS['partyab_qs_images_refreshed'] ?? 0 );

	if ( $refreshed > 0 ) {
		partyab_qs_log( $report, 'محتوا', 'نوسازی عکس‌های دمو', true, $refreshed . ' عکس با نسخه تازه جایگزین شد' );
	}

	partyab_qs_seed_taxonomies( $report );
}
