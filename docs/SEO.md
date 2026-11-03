# سئو و سرعت (قطعه ۱۴)

## متا و اشتراک‌گذاری

| مورد | منبع |
|---|---|
| `meta description` | توضیح کوتاه محصول → چکیده/متن برگه → توضیح دسته → شعار سایت |
| `og:type` | `article` در صفحات تک، `website` در بقیه |
| `og:title` / `og:description` / `og:url` | عنوان و توضیح صفحه جاری |
| `og:image` + `twitter:card` | تصویر شاخص/محصول؛ در نبود آن آیکن سایت |
| `og:locale` | `fa_IR` |

متن توضیح با `partyab_seo_description()` روی مرز کلمه بریده می‌شود (پیش‌فرض ۱۶۰ نویسه) و HTML حذف می‌شود.

## داده ساختاریافته (JSON-LD)

| صفحه | اسکیما |
|---|---|
| تک محصول | `Product` + `Offer` (قیمت، ارز IRR، موجودی InStock/OutOfStock) + `Brand` + `sku` |
| همه صفحه‌ها | `BreadcrumbList` (خانه → دسته → محصول) |
| صفحه اصلی | `WebSite` + `SearchAction` (جستجوی محصولات) |
| `/faq` | `FAQPage` با ۸ پرسش |

سازنده‌ها خالص هستند و جداگانه تست می‌شوند: `partyab_product_schema()`، `partyab_breadcrumb_schema()`، `partyab_faq_schema()`.

## سرعت

- `preload` فونت وزیرمتن متغیر (از قبل) + حذف فونت‌های گوگل قالب والد.
- تصویر شاخص محصول: `fetchpriority="high"` و `loading="eager"` (بهبود LCP)؛ بقیه تصاویر lazy پیش‌فرض وردپرس.
- حذف ایموجی (اسکریپت + استایل، فرانت و پیشخوان)، `wp_generator`، `rsd_link`، `wlwmanifest_link`، `wp_shortlink_wp_head`.
- حذف اسکریپت `wp-embed` از فرانت.
- همه استایل/اسکریپت‌ها با `filemtime` نسخه می‌گیرند (کش طولانی، بدون نسخه کهنه).

## ایندکس

- `noindex,follow` روی سبد خرید، تسویه، حساب کاربری و نتایج جستجو.
- نقشه سایت وردپرس: محصولات، برگه‌ها و دسته‌ها می‌مانند؛ نقشه کاربران حذف می‌شود.
