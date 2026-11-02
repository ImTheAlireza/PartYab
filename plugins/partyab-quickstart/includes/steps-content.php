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

	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_partyab_demo_image',
		'meta_value'     => $basename,
	) );

	if ( $existing ) {
		return (int) $existing[0];
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

	return (int) $id;
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
function partyab_qs_step_content( array &$report ): void {
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
		. '<a class="pb-btn" href="/shop">ورود به فروشگاه</a></div>';

	$home = partyab_qs_create_page( 'خانه', 'home', $hero );
	$page_my_car = partyab_qs_create_page( 'خودروی من', 'my-car', '<p>در قطعه ۴ باکس انتخاب خودرو اینجا ساخته می‌شود.</p>' );
	$page_contact = partyab_qs_create_page( 'تماس با ما', 'contact', '<p>شماره تماس و فرم در قطعه ۱۳ تکمیل می‌شود.</p>' );

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
}
