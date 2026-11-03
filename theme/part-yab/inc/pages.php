<?php
/**
 * محتوای صفحات ثابت: سؤالات متداول، راه‌های تماس و راهنمای خرید.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

/**
 * پرسش‌های متداول — قابل تغییر با فیلتر.
 *
 * @return array<int, array{0:string,1:string}>
 */
function partyab_faq_items(): array {
	$items = array(
		array(
			'چطور مطمئن شوم قطعه با خودروی من سازگار است؟',
			'در صفحه هر قطعه، تب «سازگاری با خودرو» فهرست کامل برند، مدل و بازه سال را نشان می‌دهد. اگر خودرویت را در بالای سایت انتخاب کرده باشی، ردیف خودروی تو با رنگ سبز و نشان «خودروی شما» مشخص می‌شود.',
		),
		array(
			'اگر قطعه سازگار نبود چه کار کنم؟',
			'تا ۷ روز پس از دریافت، بدون استفاده بودن کالا و سالم بودن بسته‌بندی، مرجوعی پذیرفته می‌شود. برای قطعات برقی و باتری، تماس با پشتیبانی پیش از ارسال لازم است.',
		),
		array(
			'هزینه و زمان ارسال چقدر است؟',
			'ارسال در تهران ۲۴ ساعته و در شهرستان‌ها ۲ تا ۴ روز کاری است. سفارش‌های بالای ۵ میلیون تومان ارسال رایگان دارند؛ باقی سفارش‌ها بر اساس وزن و مقصد محاسبه می‌شود.',
		),
		array(
			'امکان پرداخت در محل وجود دارد؟',
			'در تهران برای سفارش‌های زیر ۵ میلیون تومان پرداخت در محل فعال است. برای شهرستان‌ها پرداخت آنلاین یا کارت‌به‌کارت پیش از ارسال انجام می‌شود.',
		),
		array(
			'ضمانت اصالت کالا یعنی چه؟',
			'هر قطعه با برند و کد فنی مشخص از تأمین‌کننده معتبر خریداری می‌شود. در صورت اثبات غیراصل بودن، مبلغ کامل بازگردانده و قطعه جایگزین می‌شود.',
		),
		array(
			'فاکتور رسمی صادر می‌شود؟',
			'بله. در مرحله تسویه، گزینه «فاکتور رسمی» را انتخاب کن و اطلاعات حقوقی را وارد کن. فاکتور همراه سفارش ارسال می‌شود.',
		),
		array(
			'سفارش را چطور پیگیری کنم؟',
			'از بخش «حساب کاربری → سفارش‌ها» وضعیت لحظه‌ای سفارش و کد رهگیری مرسوله را می‌بینی. پیامک اطلاع‌رسانی هم در هر تغییر وضعیت ارسال می‌شود.',
		),
		array(
			'اگر قطعه در سایت موجود نبود؟',
			'شماره یا کد فنی قطعه را از طریق واتس‌اپ برای ما بفرست؛ در صورت موجود بودن در انبار، برایت لینک خرید اختصاصی می‌سازیم.',
		),
	);

	return (array) apply_filters( 'partyab_faq_items', $items );
}

/**
 * راه‌های ارتباطی — شماره‌ها از تنظیمات سفارشی‌شدنی‌اند.
 *
 * @return array<string, array{label:string,value:string,href:string,note:string}>
 */
function partyab_contact_channels(): array {
	$channels = array(
		'phone'    => array(
			'label' => 'تلفن فروش',
			'value' => '۰۲۱–۰۰۰۰۰۰۰۰',
			'href'  => 'tel:+982100000000',
			'note'  => 'شنبه تا پنجشنبه، ۹ تا ۱۸',
		),
		'whatsapp' => array(
			'label' => 'واتس‌اپ',
			'value' => '۰۹۱۲۰۰۰۰۰۰۰',
			'href'  => 'https://wa.me/989120000000',
			'note'  => 'ارسال عکس قطعه یا کد فنی',
		),
		'email'    => array(
			'label' => 'ایمیل',
			'value' => 'info@part-yab.ir',
			'href'  => 'mailto:info@part-yab.ir',
			'note'  => 'پاسخ حداکثر تا یک روز کاری',
		),
		'address'  => array(
			'label' => 'آدرس انبار',
			'value' => 'تهران، خیابان امام خمینی',
			'href'  => '',
			'note'  => 'تحویل حضوری با هماهنگی قبلی',
		),
	);

	return (array) apply_filters( 'partyab_contact_channels', $channels );
}

/**
 * مراحل انتخاب قطعه درست.
 *
 * @return array<int, array{title:string,text:string}>
 */
function partyab_guide_steps(): array {
	return array(
		array(
			'title' => 'خودرویت را انتخاب کن',
			'text'  => 'برند و مدل خودرو را از نوار بالای سایت انتخاب کن تا فهرست قطعات سازگار فیلتر شود.',
		),
		array(
			'title' => 'دسته قطعه را باز کن',
			'text'  => 'از منوی «دسته‌بندی» گروه قطعه مورد نیازت را انتخاب کن؛ تعداد قطعات سازگار روی هر دسته نوشته شده است.',
		),
		array(
			'title' => 'سازگاری را بررسی کن',
			'text'  => 'در صفحه محصول، تب سازگاری و بازه سال ساخت را ببین. اگر مدل خودرو در فهرست نبود، با پشتیبانی تماس بگیر.',
		),
		array(
			'title' => 'سفارش را ثبت کن',
			'text'  => 'قطعه را به سبد اضافه کن، آدرس را وارد کن و روش ارسال را انتخاب کن. کد رهگیری پس از ارسال پیامک می‌شود.',
		),
	);
}

/**
 * آیکن‌های ساده صفحه‌های محتوایی.
 *
 * @param string $name نام آیکن.
 * @return string
 */
function partyab_content_icon( string $name ): string {
	$icons = array(
		'phone'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M7 3h3l2 5-2 1a11 11 0 0 0 5 5l1-2 5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 5 5a2 2 0 0 1 2-2z"/></svg>',
		'chat'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 21l1.9-5.4A8 8 0 1 1 21 12z"/></svg>',
		'mail'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/></svg>',
		'pin'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
	);

	return $icons[ $name ] ?? $icons['phone'];
}

/**
 * شورت‌کد سؤالات متداول: آکاردئون بدون جاوااسکریپت.
 *
 * @return string
 */
function partyab_faq_shortcode(): string {
	$html = '<div class="pb-faq">';

	foreach ( partyab_faq_items() as $index => $item ) {
		$html .= sprintf(
			'<details class="pb-faq__item"%s><summary class="pb-faq__q">%s</summary><div class="pb-faq__a"><p>%s</p></div></details>',
			0 === $index ? ' open' : '',
			esc_html( $item[0] ),
			esc_html( $item[1] )
		);
	}

	return $html . '</div>';
}
add_shortcode( 'partyab_faq', 'partyab_faq_shortcode' );

/**
 * شورت‌کد کارت‌های تماس.
 *
 * @return string
 */
function partyab_contact_shortcode(): string {
	$icons = array(
		'phone'    => 'phone',
		'whatsapp' => 'chat',
		'email'    => 'mail',
		'address'  => 'pin',
	);

	$html = '<div class="pb-contact-cards">';

	foreach ( partyab_contact_channels() as $key => $channel ) {
		$inner = '<span class="pb-contact-cards__icon">' . partyab_content_icon( $icons[ $key ] ?? 'phone' ) . '</span>'
			. '<span class="pb-contact-cards__label">' . esc_html( $channel['label'] ) . '</span>'
			. '<strong dir="auto">' . esc_html( $channel['value'] ) . '</strong>'
			. '<small>' . esc_html( $channel['note'] ) . '</small>';

		$html .= $channel['href']
			? sprintf( '<a class="pb-contact-cards__card" href="%s">%s</a>', esc_url( $channel['href'] ), $inner )
			: sprintf( '<div class="pb-contact-cards__card">%s</div>', $inner );
	}

	return $html . '</div>';
}
add_shortcode( 'partyab_contact_cards', 'partyab_contact_shortcode' );

/**
 * شورت‌کد مراحل راهنما.
 *
 * @return string
 */
function partyab_steps_shortcode(): string {
	$html = '<ol class="pb-guide">';

	foreach ( partyab_guide_steps() as $index => $step ) {
		$html .= sprintf(
			'<li class="pb-guide__item"><span class="pb-guide__num">%s</span><div><strong>%s</strong><p>%s</p></div></li>',
			esc_html( partyab_fa_digits( (string) ( $index + 1 ) ) ),
			esc_html( $step['title'] ),
			esc_html( $step['text'] )
		);
	}

	return $html . '</ol>';
}
add_shortcode( 'partyab_steps', 'partyab_steps_shortcode' );

/**
 * جعبه راهنمای سریع بالای صفحه‌های محتوایی.
 *
 * @return string
 */
function partyab_help_box_shortcode(): string {
	return '<div class="pb-help-box"><strong>جواب سؤالت را پیدا نکردی؟</strong>'
		. '<span>با پشتیبانی تماس بگیر؛ کد فنی قطعه یا عکس آن را بفرست تا سریع‌تر راهنمایی شوی.</span>'
		. '<a class="pb-btn pb-btn--sm" href="' . esc_url( home_url( '/contact' ) ) . '">تماس با پشتیبانی</a></div>';
}
add_shortcode( 'partyab_help_box', 'partyab_help_box_shortcode' );
