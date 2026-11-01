<?php
/**
 * Template Name: ضمانت و بازگشت کالا
 * Description: شرایط ضمانت اصالت، بازگشت ۷ روزه، گارانتی و ارسال.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;

get_header();

$partyab_wa = get_theme_mod( 'partyab_whatsapp', 'https://wa.me/989121234567' );
$partyab_tel = get_theme_mod( 'partyab_phone', '021-33947195' );
$partyab_tel_link = 'tel:' . preg_replace( '/[^0-9+]/', '', $partyab_tel );
?>

<main id="main">
	<div class="container trust-page">
		<nav class="breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'partyab' ); ?></a>
			<span class="sep">/</span>
			<span class="current"><?php esc_html_e( 'ضمانت و بازگشت', 'partyab' ); ?></span>
		</nav>

		<span class="eyebrow"><?php esc_html_e( 'خرید بی‌ریسک', 'partyab' ); ?></span>
		<h1><?php esc_html_e( 'اگر نخورد، برمی‌گردانیم', 'partyab' ); ?></h1>
		<p class="lead"><?php esc_html_e( 'خرید قطعه بدون اطمینان از سازگاری ریسک دارد. ما سه چیز را تضمین می‌کنیم: اصالت کالا، درستی کد فنی و بازگشت آسان اگر قطعه با خودروی شما نخورد.', 'partyab' ); ?></p>

		<div class="trust-grid">
			<article class="trust-card" id="authenticity">
				<div class="tc-icon"><?php partyab_the_icon( 'shield' ); ?></div>
				<h2><?php esc_html_e( 'ضمانت اصالت کالا', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'هر قطعه با برند، کشور سازنده و کد فنی ثبت می‌شود. اگر کالایی اصل نبود، کل مبلغ برمی‌گردد.', 'partyab' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'فاکتور با کد فنی و برند', 'partyab' ); ?></li>
					<li><?php esc_html_e( 'بسته‌بندی سالم سازنده در اقلام حساس', 'partyab' ); ?></li>
				</ul>
			</article>

			<article class="trust-card" id="returns">
				<div class="tc-icon"><?php partyab_the_icon( 'refresh' ); ?></div>
				<h2><?php esc_html_e( 'بازگشت ۷ روزه', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'تا ۷ روز پس از تحویل، اگر قطعه استفاده نشده و بسته‌بندی سالم باشد، مرجوعی پذیرفته می‌شود.', 'partyab' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'مرجوعی به دلیل «نخوردن»: رایگان', 'partyab' ); ?></li>
					<li><?php esc_html_e( 'بازگشت وجه تا ۷۲ ساعت کاری', 'partyab' ); ?></li>
				</ul>
			</article>

			<article class="trust-card" id="fit">
				<div class="tc-icon"><?php partyab_the_icon( 'check-shield' ); ?></div>
				<h2><?php esc_html_e( 'تضمین سازگاری', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'اگر بر اساس خودروی ثبت‌شده در گاراژ قطعه پیشنهاد دادیم و نخورد، هزینهٔ ارسال هم با ماست.', 'partyab' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'پنج حالت سازگاری روی هر کارت', 'partyab' ); ?></li>
					<li><?php esc_html_e( '«سازگار با شرط» با ذکر تیپ و موتور', 'partyab' ); ?></li>
				</ul>
			</article>

			<article class="trust-card" id="shipping">
				<div class="tc-icon"><?php partyab_the_icon( 'truck' ); ?></div>
				<h2><?php esc_html_e( 'ارسال و تحویل', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'تهران: پیک ۲۴ ساعته. شهرستان: تیپاکس یا باربری، بر اساس وزن و حجم سفارش.', 'partyab' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'کد رهگیری پس از ارسال', 'partyab' ); ?></li>
					<li><?php esc_html_e( 'امکان تحویل حضوری در تهران', 'partyab' ); ?></li>
				</ul>
			</article>

			<article class="trust-card" id="payment">
				<div class="tc-icon"><?php partyab_the_icon( 'key' ); ?></div>
				<h2><?php esc_html_e( 'پرداخت امن', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'پرداخت آنلاین از درگاه بانکی، یا پرداخت در محل برای تهران با هماهنگی تلفنی.', 'partyab' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'فاکتور رسمی برای همکاران', 'partyab' ); ?></li>
					<li><?php esc_html_e( 'بدون ثبت‌نام هم می‌توانید سفارش دهید', 'partyab' ); ?></li>
				</ul>
			</article>

			<article class="trust-card" id="support">
				<div class="tc-icon"><?php partyab_the_icon( 'headset' ); ?></div>
				<h2><?php esc_html_e( 'پشتیبانی انسانی', 'partyab' ); ?></h2>
				<p><?php esc_html_e( 'اگر مطمئن نیستید کدام قطعه درست است، زنگ بزنید؛ قبل از خرید راهنمایی می‌کنیم.', 'partyab' ); ?></p>
				<ul>
					<li class="mono" dir="ltr"><?php echo esc_html( $partyab_tel ); ?></li>
					<li><?php esc_html_e( 'پاسخ در ساعات کاری فروشگاه', 'partyab' ); ?></li>
				</ul>
			</article>
		</div>

		<h2 class="section-title" style="margin-top:32px"><?php esc_html_e( 'اگر قطعه نخورد، چه کار کنم؟', 'partyab' ); ?></h2>
		<div class="trust-steps">
			<div><?php esc_html_e( 'از پنل کاربری، سفارش را باز کنید و «درخواست مرجوعی» را بزنید.', 'partyab' ); ?></div>
			<div><?php esc_html_e( 'عکس قطعه و بسته‌بندی را در واتساپ بفرستید تا کارشناس تأیید کند.', 'partyab' ); ?></div>
			<div><?php esc_html_e( 'پیک برای دریافت مراجعه می‌کند (تهران) یا کد پستی برای ارسال به ما داده می‌شود.', 'partyab' ); ?></div>
			<div><?php esc_html_e( 'پس از بررسی، مبلغ تا ۷۲ ساعت کاری به حساب یا کیف پول شما برمی‌گردد.', 'partyab' ); ?></div>
		</div>

		<h2 class="section-title" id="rules" style="margin-top:32px"><?php esc_html_e( 'قوانین و مقررات', 'partyab' ); ?></h2>
		<div class="trust-card">
			<ul>
				<li><?php esc_html_e( 'قیمت نهایی هنگام تأیید سفارش یا استعلام تلفنی اعلام می‌شود.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'برای اقلام «استعلام قیمت»، سفارش پس از تأیید تلفنی نهایی می‌شود.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'مسئولیت نصب صحیح قطعه با نصاب است؛ تعویض کالای سالم فقط در چارچوب بازگشت ۷ روزه انجام می‌شود.', 'partyab' ); ?></li>
				<li><?php esc_html_e( 'حریم خصوصی: شمارهٔ تماس و آدرس فقط برای ارسال سفارش استفاده می‌شود و در اختیار شخص ثالث قرار نمی‌گیرد.', 'partyab' ); ?></li>
			</ul>
		</div>

		<div class="trust-cta">
			<div>
				<b><?php esc_html_e( 'مطمئن نیستید قطعه به خودروی شما می‌خورد؟', 'partyab' ); ?></b>
				<span><?php esc_html_e( 'کد فنی یا مدل و سال خودرو را بفرستید؛ قبل از خرید جواب می‌دهیم.', 'partyab' ); ?></span>
			</div>
			<div style="display:flex;gap:8px;flex-wrap:wrap">
				<a class="btn btn-amber" href="<?php echo esc_url( $partyab_wa ); ?>" target="_blank" rel="noopener"><?php partyab_the_icon( 'whatsapp' ); ?> <?php esc_html_e( 'واتساپ', 'partyab' ); ?></a>
				<a class="btn btn-ghost" href="<?php echo esc_url( $partyab_tel_link ); ?>"><?php partyab_the_icon( 'phone' ); ?> <?php esc_html_e( 'تماس', 'partyab' ); ?></a>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
