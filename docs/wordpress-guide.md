# راهنمای اجرای پارت‌یاب روی وردپرس

این سند توضیح می‌دهد چطور از خروجی HTML/CSS فعلی (`index.html`، `shop.html`، `product.html`)
به یک **قالب وردپرس + افزونهٔ سازگاری** برسید که همان ظاهر و همان رفتار را داشته باشد.

> نکتهٔ مهم: این سند **نقشهٔ ساخت** است. نسخهٔ ساخته‌شده و قابل نصب همین حالا در مخزن هست:
> قالب در [`theme/partyab`](../theme/partyab) و افزونه در [`plugin/partyab-fitment`](../plugin/partyab-fitment).
> اگر فقط می‌خواهید سایت را بالا بیاورید، بخش «نصب سریع» را بخوانید و به بخش‌های ساخت داخلی بروید.

## نصب سریع (نسخهٔ آماده)

| گام | کار | نتیجه |
|---|---|---|
| ۱ | وردپرس ۶.۴+ و PHP 7.4+ نصب کنید | — |
| ۲ | ووکامرس را نصب/فعال کنید | صفحه‌های فروشگاه، سبد و تسویه ساخته می‌شوند |
| ۳ | `theme/partyab` → `wp-content/themes/partyab` و فعال‌سازی | `front-page.php`، آرشیو و صفحهٔ محصول با طراحی پارت‌یاب |
| ۴ | `plugin/partyab-fitment` → `wp-content/plugins/partyab-fitment` و فعال‌سازی | جدول `{prefix}partyab_fitment` + متاباکس + REST `partyab/v1` |
| ۵ | «تنظیمات → پیوندهای یکتا → نام نوشته» | مسیرهای REST و آرشیو درست می‌شوند |
| ۶ | «ابزارها → سازگاری پارت‌یاب» → ایمپورت [`sample/fitment.csv`](../plugin/partyab-fitment/sample/fitment.csv) | ۳۶ ردیف سازگاری نمونه |
| ۷ | «محصولات → درون‌ریزی» → [`sample/products.csv`](../plugin/partyab-fitment/sample/products.csv) | ۱۳ محصول نمونه با کد OEM و کدهای معادل |
| ۸ | برگهٔ «گاراژ» بسازید و الگوی «گاراژ پارت‌یاب» را انتخاب کنید | صفحهٔ گاراژ با پاسپورت خودرو |
| ۹ | «نمایش → فهرست‌ها»: منوی اصلی، دسته‌های فوتر، راهنمای فوتر | ناوبری کامل |
| ۱۰ | «تنظیمات → خواندن»: صفحهٔ اصلی = یک برگهٔ خالی | `front-page.php` اجرا می‌شود |

پس از نصب، انتخاب خودرو را با این سناریوها امتحان کنید:
`p1` + ۲۰۶ (سازگار با شرط)، `p4` + ۲۰۶ (سازگار)، `p10` + ۲۰۶ (ناسازگار)،
`p8` + ۲۰۶ (ثبت‌نشده)، `p7` بدون خودرو (عمومی)، `p1` بدون خودرو (انتخاب خودرو).

اگر ووکامرس فعال نباشد قالب خطای مرگبار نمی‌دهد: لینک‌های فروشگاه/سبد به صفحهٔ اصلی برمی‌گردند
و بخش‌های ووکامرس رندر نمی‌شوند؛ ولی برای تجربهٔ کامل ووکامرس لازم است.

---

## ۰. تصمیم‌های معماری (قبل از شروع)

| تصمیم | پیشنهاد | دلیل |
|---|---|---|
| نوع قالب | **قالب کلاسیک** (PHP template) نه قالب بلوکی | مارک‌آپ فعلی کلاسیک است و کنترل کامل روی HTML می‌دهد |
| محل منطق | **افزونهٔ جدا** برای سازگاری | با تعویض قالب، داده و فیلترها از بین نمی‌رود (اصل پروپوزال) |
| فیلتر خودرو | سمت **سرور** با جدول اختصاصی | صفحه‌بندی، شمارش نتایج و SEO درست می‌ماند |
| انتخاب خودرو در مرورگر | کوکی + localStorage | سرور بتواند خودرو را در رندر اولیه بخواند |
| صفحه‌ساز | سازگاری با Elementor فقط در صفحات محتوایی | هستهٔ فروشگاه نباید به Elementor Pro وابسته باشد |

**سه سطح اجرا:**

| سطح | خروجی | زمان تقریبی |
|---|---|---|
| ۱. فقط ظاهر | قالب فعال است، محصولات نمونه نمایش داده می‌شوند، سازگاری دستی در متاباکس | ۳ تا ۵ روز |
| ۲. فروشگاه واقعی | ووکامرس کامل + فیلتر سازگاری سمت سرور + تب محصول | ۱ تا ۲ هفته |
| ۳. افزونهٔ کامل | ایمپورت CSV با Dry run، گزارش خطا، REST، ابزار مدیر | ۲ تا ۳ هفته |

---

## ۱. راه‌اندازی محیط تست

### گزینهٔ الف — Docker (پیشنهادی برای تیم)

```yaml
# docker-compose.yml
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wp
      MYSQL_PASSWORD: wp
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - db:/var/lib/mysql

  wp:
    image: wordpress:php8.2-apache
    depends_on: [db]
    ports:
      - "8080:80"
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: wp
      WORDPRESS_DB_PASSWORD: wp
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_DEBUG: 1
    volumes:
      - ./wordpress/wp-content/themes/partyab:/var/www/html/wp-content/themes/partyab
      - ./wordpress/wp-content/plugins/partyab-fitment:/var/www/html/wp-content/plugins/partyab-fitment

volumes:
  db: {}
```

```bash
docker compose up -d      # سپس http://localhost:8080
```

### گزینهٔ ب — wp-env (اگر Node و Docker داری)

```json
// .wp-env.json
{
  "core": null,
  "phpVersion": "8.2",
  "plugins": ["https://downloads.wordpress.org/plugin/woocommerce.zip"],
  "themes": ["./wordpress/partyab"],
  "config": { "WP_DEBUG": true, "WP_DEBUG_LOG": true },
  "port": 8888
}
```

```bash
npx @wordpress/env@latest start
```

### گزینهٔ ج — Local (بدون ترمینال)

[Local](https://localwp.com) را نصب کن، یک سایت خالی بساز، بعد پوشهٔ قالب/افزونه را در
`wp-content` همان سایت کپی کن. برای کار طراحی سریع‌ترین راه است.

---

## ۲. آماده‌سازی وردپرس و ووکامرس

1. **زبان:** تنظیمات → عمومی → زبان سایت = «فارسی». وردپرس خودش `rtl.css` را لود می‌کند و `is_rtl()` درست می‌شود.
2. **پیوندهای یکتا:** تنظیمات → پیوندهای یکتا → «نام نوشته» (بدون این، صفحهٔ محصول ۴۰۴ می‌دهد).
3. **ووکامرس:** نصب و فعال‌سازی؛ در ویزارد فقط کشور/شهر و واحد پول را رد کن (بقیه را بعداً).
4. **واحد پول تومان:** ووکامرس «تومان» را پیش‌فرض ندارد. یکی از این دو:
   - افزونهٔ «واحد پول ایران» نصب کن، یا
   - اسنیپت زیر در `functions.php` قالب:

```php
add_filter( 'woocommerce_currencies', function ( $currencies ) {
    $currencies['IRT'] = 'تومان';
    return $currencies;
} );

add_filter( 'woocommerce_currency_symbol', function ( $symbol, $code ) {
    return 'IRT' === $code ? 'تومان' : $symbol;
}, 10, 2 );
```

5. **صفحات:** فروشگاه/سبد/پرداخت/حساب من را ووکامرس خودش می‌سازد؛ فقط مطمئن شو «فروشگاه» به یک صفحهٔ واقعی وصل است.
6. **محصولات نمونه:** حداقل ۱۲ محصول با SKU، قیمت، موجودی و تصویر بساز (همان ۱۲ محصول دموی پروتوتایپ کافی است).

---

## ۳. ساخت اسکلت قالب

```
wordpress/wp-content/themes/partyab/
├── style.css                 ← فقط هدر قالب (کد زیر)
├── functions.php
├── header.php
├── footer.php
├── front-page.php            ← صفحهٔ اصلی
├── index.php
├── page.php
├── single.php
├── woocommerce/
│   ├── archive-product.php   ← فروشگاه (فیلتر + گرید)
│   └── content-product.php   ← کارت محصول (کلاس p-card)
├── template-parts/
│   ├── hero.php
│   ├── vehicle-selector.php
│   └── fitment-badge.php
├── inc/
│   ├── enqueue.php
│   ├── woocommerce.php       ← هوک‌ها و حذف/جابه‌جایی اجزا
│   └── currency.php
└── assets/                   ← همان پوشهٔ فعلی پروتوتایپ
    ├── css/style.css
    ├── js/app.js
    ├── fonts/…
    └── img/…
```

### `style.css` (فقط هدر — CSS اصلی در `assets` می‌ماند)

```css
/*
Theme Name: PartYab
Theme URI: https://partyab.ir
Author: PartYab Team
Description: قالب فروشگاهی قطعات خودرو با انتخاب خودرو و مدیریت سازگاری.
Version: 0.1.0
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: partyab
Tags: e-commerce, rtl-language-support, custom-menu, custom-logo
*/
```

### `functions.php`

```php
<?php
defined( 'ABSPATH' ) || exit;

define( 'PARTYAB_VERSION', '0.1.0' );

require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/currency.php';

add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'partyab', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [ 'height' => 64, 'width' => 200, 'flex-height' => true ] );
    add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ] );

    // ووکامرس
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus( [
        'primary' => 'منوی اصلی',
        'footer'  => 'منوی فوتر',
    ] );
} );

// عرض محتوا برای ووکامرس
add_action( 'after_setup_theme', function () {
    $GLOBALS['content_width'] = 1280;
}, 0 );
```

### `inc/enqueue.php`

```php
<?php
add_action( 'wp_enqueue_scripts', function () {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style(
        'partyab',
        $uri . '/assets/css/style.css',
        [],
        filemtime( $dir . '/assets/css/style.css' )   // cache-busting خودکار
    );

    wp_enqueue_script(
        'partyab',
        $uri . '/assets/js/app.js',
        [],
        filemtime( $dir . '/assets/js/app.js' ),
        [ 'strategy' => 'defer', 'in_footer' => true ]
    );

    // داده‌های سرور به‌جای data.js استاتیک
    wp_localize_script( 'partyab', 'PARTYAB', [
        'restUrl'  => esc_url_raw( rest_url( 'partyab/v1/' ) ),
        'nonce'    => wp_create_nonce( 'wp_rest' ),
        'shopUrl'  => esc_url( wc_get_page_permalink( 'shop' ) ),
        'cartUrl'  => esc_url( wc_get_cart_url() ),
        'isRTL'    => is_rtl(),
        'currency' => get_woocommerce_currency_symbol(),
    ] );

    // فقط در صفحاتی که ووکامرس لازم است
    if ( is_woocommerce() || is_cart() || is_checkout() ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }
} );

// فونت‌ها را حذف نکن؛ @font-face داخل assets/css/style.css با مسیر نسبی ../fonts کار می‌کند.
```

> اگر گوگل‌فونت اضافه می‌کنی، `wp_enqueue_style` استفاده کن نه `<link>` دستی؛ و طبق پروپوزال فونت باید مجوز توزیع داشته باشد.

---

## ۴. انتقال دارایی‌ها

| از پروتوتایپ | به قالب |
|---|---|
| `assets/css/style.css` | `partyab/assets/css/style.css` |
| `assets/js/app.js` | `partyab/assets/js/app.js` |
| `assets/fonts/*.woff2` | `partyab/assets/fonts/` |
| `assets/img/*.jpg` | تصاویر **محصول** → کتابخانهٔ رسانه؛ تصویر هیرو → `assets/img/` |

تغییرات لازم در CSS:

1. مسیر تصاویر هیرو را نسبی نگه دار (`../img/hero-parts.jpg` درست است چون CSS در `assets/css` است).
2. تصویر محصول دیگر از `p.img` نمی‌آید؛ از `the_post_thumbnail()` ووکامرس می‌آید. نسبت مربع کارت را حفظ کن (`aspect-ratio: 1/1` + `object-fit: cover`).
3. دکمهٔ «افزودن به سبد» ووکامرس کلاس‌های `add_to_cart_button` و `ajax_add_to_cart` دارد؛ استایل `.p-add` را به `.add_to_cart_button` هم بده تا AJAX کار کند:

```css
.p-add,
.p-card .add_to_cart_button {
  width: 36px; height: 36px; border-radius: 10px;
  background: var(--navy); color: #fff;
  display: grid; place-items: center;
}
```

---

## ۵. نقشهٔ تبدیل صفحه‌به‌صفحه

| بخش پروتوتایپ | جای وردپرس | نکته |
|---|---|---|
| `#site-header` (JS) | `header.php` | لوگو با `the_custom_logo()`، منو با `wp_nav_menu()` |
| تاپ‌بار | `header.php` | شماره و ساعت را از Customizer/ACF بخوان، هاردکد نکن |
| انتخاب‌گر خودرو | `template-parts/vehicle-selector.php` + `app.js` | داده از REST، انتخاب در کوکی |
| هیرو | `front-page.php` → `template-parts/hero.php` | متن‌ها قابل ویرایش با ACF یا بلوک |
| دسته‌ها | `get_terms(['taxonomy'=>'product_cat'])` | آیکون هر دسته در term meta |
| کارت محصول | `woocommerce/content-product.php` | استفادهٔ مجدد در همهٔ گریدها |
| برندها | `get_terms(['taxonomy'=>'product_brand'])` یا ACF repeater | |
| راهنماها | `WP_Query` روی نوشته‌ها | |
| فوتر | `footer.php` | منو + ویجت‌ها + `wp_footer()` |
| فروشگاه + فیلتر | `woocommerce/archive-product.php` | فیلترها را با query string مدیریت کن |
| صفحهٔ محصول | هوک‌های ووکامرس در `inc/woocommerce.php` | تب سازگاری با `woocommerce_product_tabs` |
| سبد/پرداخت | قالب پیش‌فرض ووکامرس + استایل | دست نزن، فقط ظاهر را هماهنگ کن |

### `header.php` (نمونهٔ مینیمال و درست)

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-group">
      <a class="topbar-item" href="tel:<?php echo esc_attr( get_theme_mod( 'partyab_phone', '02191002233' ) ); ?>">
        <?php echo esc_html( get_theme_mod( 'partyab_phone_label', '۰۲۱–۹۱۰۰۲۲۳۳' ) ); ?>
      </a>
      <a class="topbar-item" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">پیگیری سفارش</a>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="container header-main">
    <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?>
        <span class="logo-text"><strong><?php bloginfo( 'name' ); ?></strong></span>
      <?php } ?>
    </a>

    <div id="vehicleSlot" class="vehicle-slot">
      <?php get_template_part( 'template-parts/vehicle-selector' ); ?>
    </div>

    <div class="header-search">
      <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
               placeholder="نام قطعه، برند یا کد فنی را وارد کنید">
        <input type="hidden" name="post_type" value="product">
        <button class="search-submit" type="submit">جست‌وجو</button>
      </form>
    </div>

    <div class="header-actions">
      <a class="icon-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">حساب</a>
      <a class="cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
        سبد خرید
        <span class="cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
      </a>
    </div>
  </div>
</header>
```

### `footer.php`

```php
<footer class="site-footer">
  <div class="container footer-top">
    <?php if ( has_nav_menu( 'footer' ) ) {
        wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ] );
    } ?>
  </div>
  <div class="footer-note">این سایت یک نمونهٔ طراحی است…</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
```

---

## ۶. کارت محصول (`woocommerce/content-product.php`)

همان مارک‌آپ `.p-card` فعلی، ولی با دادهٔ واقعی:

```php
<?php
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$oem   = get_post_meta( $product->get_id(), '_partyab_oem', true );
$badge = function_exists( 'partyab_fitment_badge' )
    ? partyab_fitment_badge( $product->get_id() )
    : '';
?>
<article class="p-card" data-product="<?php echo esc_attr( $product->get_id() ); ?>">
  <a class="p-media" href="<?php the_permalink(); ?>">
    <?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?>
    <?php echo $badge; // HTML امن، در افزونه escape شده ?>
    <?php if ( $product->is_on_sale() ) : ?>
      <span class="off-badge">تخفیف</span>
    <?php endif; ?>
  </a>

  <div class="p-body">
    <div class="p-brand-row">
      <?php
      $brands = wc_get_product_terms( $product->get_id(), 'product_brand', [ 'fields' => 'names' ] );
      ?>
      <span class="p-brand"><?php echo esc_html( $brands[0] ?? '' ); ?></span>
    </div>

    <h3 class="p-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

    <?php if ( $oem ) : ?>
      <div class="p-meta">کد فنی: <b><?php echo esc_html( $oem ); ?></b></div>
    <?php elseif ( $product->get_sku() ) : ?>
      <div class="p-meta">SKU: <b><?php echo esc_html( $product->get_sku() ); ?></b></div>
    <?php endif; ?>

    <div class="p-price-row">
      <div class="p-price"><?php echo $product->get_price_html(); ?></div>
      <?php woocommerce_template_loop_add_to_cart(); ?>
    </div>
  </div>
</article>
```

نکته‌ها:
- `woocommerce_template_loop_add_to_cart()` کلاس‌های لازم AJAX را خودش می‌گذارد؛ دستی `<button>` نساز.
- اگر می‌خواهی آیکون «+» ببینی، با CSS `::before` یا فیلتر `woocommerce_loop_add_to_cart_link` آیکون را داخل لینک بگذار.
- `content-product.php` را از `plugins/woocommerce/templates/content-product.php` کپی کن و در قالب بازنویسی کن؛ نسخهٔ اصلی را دستکاری نکن.
- تاکسونومی برند در ووکامرس جدید `product_brand` است؛ در نسخه‌های قدیمی‌تر ممکن است برند یک ویژگی (`pa_brand`) باشد — قبل از استفاده در پنل محصولات چک کن.

---

## ۷. صفحهٔ محصول: تب «خودروهای سازگار»

```php
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
    $tabs['partyab_fitment'] = [
        'title'    => 'خودروهای سازگار',
        'priority' => 20,
        'callback' => 'partyab_render_fitment_tab',
    ];
    return $tabs;
} );

function partyab_render_fitment_tab() {
    global $product;
    if ( function_exists( 'partyab_render_fitments' ) ) {
        partyab_render_fitments( $product->get_id() ); // خروجی HTML امن
    }
}
```

برای پنل «وضعیت سازگاری با خودروی شما» بالای دکمهٔ خرید:

```php
add_action( 'woocommerce_single_product_summary', function () {
    if ( function_exists( 'partyab_render_fitment_panel' ) ) {
        partyab_render_fitment_panel( get_the_ID() );
    }
}, 25 );
```

---

## ۸. انتخاب‌گر خودرو: از localStorage به REST

1. انتخاب کاربر در `localStorage` **و** یک کوکی ذخیره شود:

```js
function saveVehicle(v) {
  localStorage.setItem('py_vehicle_v1', JSON.stringify(v));
  // کوکی برای اینکه PHP در رندر اولیه هم بداند
  document.cookie = 'partyab_vehicle=' + encodeURIComponent(JSON.stringify(v)) +
    '; path=/; max-age=' + 60 * 60 * 24 * 180 + '; SameSite=Lax';
}
```

2. سرور با sanitize بخواند:

```php
function partyab_get_vehicle_from_cookie() {
    if ( empty( $_COOKIE['partyab_vehicle'] ) ) {
        return null;
    }
    $raw = json_decode( wp_unslash( $_COOKIE['partyab_vehicle'] ), true );
    if ( ! is_array( $raw ) ) {
        return null;
    }
    $make  = sanitize_text_field( $raw['makeId'] ?? '' );
    $model = sanitize_text_field( $raw['modelId'] ?? '' );
    $year  = absint( $raw['year'] ?? 0 );
    if ( ! $make || ! $model || ! $year ) {
        return null;
    }
    return compact( 'make', 'model', 'year' );
}
```

3. لیست خودروها از REST بیاید (نه از `data.js`):

```php
add_action( 'rest_api_init', function () {
    register_rest_route( 'partyab/v1', '/vehicles', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $cached = get_transient( 'partyab_vehicles' );
            if ( $cached ) {
                return rest_ensure_response( $cached );
            }
            global $wpdb;
            $table = $wpdb->prefix . 'partyab_vehicles';
            $rows  = $wpdb->get_results( "SELECT make, model, year_from, year_to, engines FROM {$table} ORDER BY make, model", ARRAY_A );
            set_transient( 'partyab_vehicles', $rows, 12 * HOUR_IN_SECONDS );
            return rest_ensure_response( $rows );
        },
    ] );
} );
```

4. بعد از هر ذخیرهٔ داده در پنل: `delete_transient( 'partyab_vehicles' );`

---

## ۹. افزونهٔ سازگاری

```
wordpress/wp-content/plugins/partyab-fitment/
├── partyab-fitment.php
├── includes/
│   ├── class-db.php
│   ├── class-admin.php
│   ├── class-importer.php
│   └── functions.php    ← partyab_fitment_badge / panel / get_fitments
└── assets/admin.js
```

### جدول داده (طبق پروپوزال: جدول اختصاصی با ایندکس)

```sql
CREATE TABLE {$wpdb->prefix}partyab_fitment (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id  BIGINT UNSIGNED NOT NULL,
  make        VARCHAR(60)  NOT NULL,
  model       VARCHAR(60)  NOT NULL,
  year_from   SMALLINT UNSIGNED NOT NULL,
  year_to     SMALLINT UNSIGNED NOT NULL,
  status      VARCHAR(20)  NOT NULL DEFAULT 'confirmed',  -- confirmed | unknown
  note        VARCHAR(191) NULL,
  updated_at  DATETIME NOT NULL,
  PRIMARY KEY  (id),
  KEY product_vehicle (product_id, make, model),
  KEY vehicle_range (make, model, year_from, year_to)
) {$charset_collate};
```

ساخت جدول با `dbDelta()` در `register_activation_hook` — و اگر افزونه بعداً جدول را تغییر می‌دهد،
شمارهٔ نسخهٔ اسکیما را در option نگه دار و مهاجرت را یک‌بار اجرا کن.

### متاباکس «سازگاری خودرو»

- فقط برای `product` و با بررسی `current_user_can( 'edit_product', $post_id )`
- ذخیره با `check_admin_referer()` + `sanitize_text_field()`/`absint()`
- فرم: تکرارپذیر (repeater) ردیف‌های «سازنده / مدل / از سال / تا سال / وضعیت / یادداشت»

### توابع خروجی

```php
function partyab_fitment_badge( int $product_id ): string {
    $vehicle = partyab_get_vehicle_from_cookie();
    if ( ! $vehicle ) {
        return '<span class="fit-badge fit-badge--ask">انتخاب خودرو</span>';
    }
    $row = partyab_find_fitment( $product_id, $vehicle );
    if ( $row && 'confirmed' === $row['status'] ) {
        return '<span class="fit-badge fit-badge--fit">سازگار</span>';
    }
    if ( partyab_has_any_fitment( $product_id ) ) {
        return '<span class="fit-badge fit-badge--no">ناسازگار</span>';
    }
    return '<span class="fit-badge fit-badge--unknown">ثبت نشده</span>';
}
```

این دقیقاً همان منطق سه‌حالتهٔ `fitStatus()` در `app.js` است، فقط سمت سرور.

---

## ۱۰. فیلتر «فقط سازگار» در آرشیو (سمت سرور)

اگر محصولات را در کوئری اصلی فیلتر نکنی، صفحه‌بندی و شمارش غلط می‌شود. راه درست:

```php
add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! ( is_shop() || is_product_taxonomy() ) ) {
        return;
    }
    $vehicle = partyab_get_vehicle_from_cookie();
    if ( $vehicle && isset( $_GET['fitment'] ) && '1' === $_GET['fitment'] ) {
        $query->set( 'partyab_vehicle', $vehicle );
    }
} );

add_filter( 'posts_clauses', function ( $clauses, $query ) {
    global $wpdb;
    $vehicle = $query->get( 'partyab_vehicle' );
    if ( ! $vehicle ) {
        return $clauses;
    }
    $table = $wpdb->prefix . 'partyab_fitment';

    $clauses['join'] .= $wpdb->prepare(
        " INNER JOIN {$table} pf
            ON pf.product_id = {$wpdb->posts}.ID
           AND pf.make = %s AND pf.model = %s
           AND %d BETWEEN pf.year_from AND pf.year_to
           AND pf.status = 'confirmed' ",
        $vehicle['make'],
        $vehicle['model'],
        (int) $vehicle['year']
    );
    $clauses['groupby'] = "{$wpdb->posts}.ID";

    return $clauses;
}, 10, 2 );
```

نکات:
- `pre_get_posts` فقط وقتی پارامتر `?fitment=1` در URL باشد فعال می‌شود تا حالت «همهٔ قطعات» هم ممکن بماند.
- نتیجه‌ها را کش کن (transient بر اساس `make+model+year+paged`) و بعد از تغییر سازگاری پاک کن.
- خودرو در URL بیاید (`?fitment=1`) تا لینک قابل اشتراک و قابل ایندکس باشد.

---

## ۱۱. ایمپورت CSV

1. آپلود با `wp_handle_upload` + محدودیت `text/csv` و اندازه.
2. خواندن با `fgetcsv` و **نگاشت ستون‌ها** (همان جدول بخش ۱۳ پروپوزال: `sku, oem_number, vehicle_make, vehicle_model, year_from, year_to, fitment_status, fitment_note`).
3. **Dry run:** فقط اعتبارسنجی، بدون insert؛ گزارش در فایل جلسه.
4. اجرای دسته‌ای: هر درخواست ۲۰۰ ردیف (AJAX یا `wp_schedule_single_event`) تا timeout نشود.
5. SKU ناشناخته → خطا با شمارهٔ ردیف؛ ردیف تکراری → policy «نادیده بگیر / به‌روزرسانی».
6. گزارش نهایی قابل دانلود (CSV).

```php
$product_id = wc_get_product_id_by_sku( $sku );
if ( ! $product_id ) {
    $errors[] = [ 'row' => $i, 'sku' => $sku, 'error' => 'SKU در فروشگاه وجود ندارد' ];
    continue;
}
```

---

## ۱۲. امنیت و کارایی (چک‌لیست)

- [ ] همهٔ خروجی‌ها `esc_html` / `esc_attr` / `wp_kses_post`
- [ ] فرم‌های مدیریت: `nonce` + `current_user_can`
- [ ] ایمپورت: فقط کاربر با `manage_woocommerce`
- [ ] کوکی خودرو: `sanitize_text_field` و `absint` (هرگز SQL خام)
- [ ] ایندکس روی `product_id` و `(make, model, year_from, year_to)`
- [ ] کش لیست خودروها و نتایج فیلتر + پاک‌سازی بعد از تغییر داده
- [ ] `wp_enqueue_script` با `defer`؛ بدون jQuery اضافه
- [ ] تصاویر `woocommerce_thumbnail` (نه فایل اصلی) و `loading="lazy"`
- [ ] قیمت در Schema.org با عدد لاتین بماند؛ تبدیل به رقم فارسی فقط در نمایش
- [ ] تست با افزونهٔ کش (WP Rocket/LiteSpeed) و پاک‌کردن کش پس از ایمپورت

---

## ۱۳. چک‌لیست تست

- [ ] قالب بدون خطای PHP فعال می‌شود و `WP_DEBUG` چیزی نشان نمی‌دهد
- [ ] صفحهٔ اصلی، فروشگاه، دسته، محصول و سبد با ظاهر پروتوتایپ رندر می‌شوند
- [ ] کارت محصول: تصویر، عنوان، کد فنی، قیمت، افزودن به سبد (AJAX بدون رفرش)
- [ ] انتخاب خودرو → برچسب سبز/زرد/قرمز درست تغییر می‌کند و بعد از رفرش می‌ماند
- [ ] فیلتر `?fitment=1` تعداد و صفحه‌بندی را درست می‌دهد
- [ ] محصول بدون دادهٔ سازگاری هرگز «سازگار» نمایش داده نمی‌شود
- [ ] ایمپورت CSV: ردیف معتبر اضافه، SKU ناموجود گزارش، اجرای دوباره داده را تکثیر نمی‌کند
- [ ] موبایل ۳۶۰/۳۹۰، تبلت ۷۶۸ و دسکتاپ ۱۴۴۰ بدون اسکرول افقی
- [ ] WooCommerce → Status → Templates: هیچ قالبی «outdated» نیست

---

## ۱۴. خطاهای رایج

| علامت | علت | راه‌حل |
|---|---|---|
| صفحهٔ فروشگاه ۴۰۴ | پیوند یکتا روی «ساده» است | تنظیمات → پیوندهای یکتا → «نام نوشته» |
| استایل ووکامرس روی طرح را خراب می‌کند | `woocommerce.css` قبل از CSS قالب لود می‌شود | استایل‌های خودت را با specificity یا `wp_enqueue_style` بعد از آن لود کن |
| «قالب‌های ووکامرس قدیمی» | فایل قالب کپی‌شده نسخهٔ قدیمی دارد | هدر `@version` را با نسخهٔ فعلی افزونه هم‌تراز کن |
| AJAX سبد کار نمی‌کند | کلاس‌های `ajax_add_to_cart` حذف شده‌اند | از `woocommerce_template_loop_add_to_cart()` استفاده کن |
| فارسی/راست‌چین نیست | زبان سایت انگلیسی است | زبان را فارسی کن، `dir` را از `language_attributes()` بگیر |
| فونت لود نمی‌شود | مسیر نسبی اشتباه یا MIME | فایل در `assets/fonts` باشد و CSS در `assets/css` |
| قیمت «تومان» ندارد | واحد پول پیش‌فرض نیست | اسنیپت بخش ۲ یا افزونهٔ واحد پول |

---

## ۱۵. ترتیب پیشنهادی کار

1. **روز ۱:** محیط + ووکامرس + محصولات نمونه + قالب خالی فعال
2. **روز ۲–۳:** `header.php` / `footer.php` / صفحهٔ اصلی با محصولات واقعی
3. **روز ۴–۵:** `content-product.php` + آرشیو با فیلترهای ظاهری (بدون منطق سازگاری)
4. **هفتهٔ ۲:** افزونه: جدول + متاباکس + برچسب سه‌حالته + تب محصول
5. **هفتهٔ ۳:** فیلتر سمت سرور + REST خودروها + کش
6. **هفتهٔ ۴:** ایمپورت CSV با Dry run و گزارش
7. **هفتهٔ ۵:** QA روی هاست واقعی، موبایل، افزونه‌های کش/پیامک/پرداخت

> این تخمین است، نه تعهد؛ بعد از مرحلهٔ ۳ بازبینی کن.

---

### یادآوری

پروتوتایپ فعلی هر وقت خواستی **بدون وردپرس** هم قابل نمایش است:

```bash
python3 -m http.server 3000 --bind 0.0.0.0
```

از آن برای تأیید طراحی و گرفتن بازخورد استفاده کن؛ کد وردپرس را بر اساس همین مارک‌آپ بساز.
