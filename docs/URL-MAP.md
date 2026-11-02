# ساختار URL و نام‌گذاری

خروجی کار ۰-۱۹. قاعده: **لاتین، کوتاه، یکدست، بدون تاریخ و بدون شماره**. اسلاگ فارسی نمی‌گذاریم تا لینک‌ها در پیامک، QR و فاکتور سالم بمانند.

## ۱. قالب لینک‌ها

| نوع | الگو | نمونه |
|---|---|---|
| محصول | `/product/{slug}` | `/product/brake-pad-pride-front` |
| دسته قطعه | `/part/{slug}` | `/part/brake-system` |
| زیردسته قطعه | `/part/{parent}/{child}` | `/part/brake-system/brake-pad` |
| برند قطعه | `/brand/{slug}` | `/brand/bosch` |
| صفحه برندها | `/brands` | `/brands` |
| برند خودرو | `/car/{brand}` | `/car/pride` |
| مدل خودرو | `/car/{brand}/{model}` | `/car/pride/131` |
| بررسی سازگاری | `/compatibility/{brand}/{model}/{year}` | `/compatibility/pride/131/1390` |
| قطعات سازگار با خودروی من | `/my-car` | `/my-car` |
| جستجو | `/?s=...` (noindex) | `/?s=لنت` |
| مقاله | `/blog/{slug}` | `/blog/how-to-change-brake-pad` |
| صفحات محتوایی | `/{slug}` | `/about` · `/contact` · `/faq` · `/warranty` · `/terms` |
| حساب کاربری | `/account/{tab}` | `/account/orders` · `/account/garage` |
| سبد / چک‌اوت | `/cart` · `/checkout` | — |

## ۲. نام دقیق تاکسونومی‌ها و متاها

| موجودیت | نوع | نام سیستمی | سلسله‌مراتبی |
|---|---|---|---|
| دسته قطعه | `product_cat` | `part` (base) | ✅ |
| برند قطعه | تاکسونومی سفارشی | `part_brand` | ❌ |
| برند خودرو | تاکسونومی سفارشی | `car_brand` | ❌ |
| مدل خودرو | تاکسونومی سفارشی | `car_model` | ✅ (زیر `car_brand`) |
| سال ساخت | تاکسونومی سفارشی | `car_year` | ❌ (بازه‌ها به‌عنوان term) |
| موقعیت نصب | تاکسونومی سفارشی | `position` | ❌ |
| نوع کالا | تاکسونومی سفارشی | `part_type` | ❌ (اصل، طرح اصلی، استوک، تعمیری) |
| برچسب | `product_tag` | — | ❌ |

**متاهای محصول:** `_technical_no` (همان SKU) · `_oem_no` · `_alt_nos` (چند شماره با `,`) · `_warranty` · `_country` · `_pack_qty` · `_sell_unit`

## ۳. جدول سازگاری (D3)

```
wp_partyab_compat
  id            BIGINT PK
  product_id    BIGINT  INDEX
  car_brand     VARCHAR(64)
  car_model     VARCHAR(64)
  year_from     SMALLINT
  year_to       SMALLINT
  engine        VARCHAR(64)   NULL
  note          VARCHAR(255)  NULL
  INDEX (car_brand, car_model, year_from, year_to)
  INDEX (product_id)
```

قواعد:
1. تطبیق سال **بسته** است: `year_from <= سال خودرو <= year_to`.
2. اگر `engine` خالی باشد، یعنی همه تیپ‌ها.
3. نبود ردیف = «نامشخص»، نه «ناسازگار». UI باید این دو را متفاوت نشان دهد.
4. ایمپورت CSV ستون‌های: `product_sku, car_brand, car_model, year_from, year_to, engine, note`.

## ۴. قواعد سئو

- صفحات `?s=` و `?filter` → `noindex, follow`؛ لندینگ‌های ساخته‌شده (`/part/brake-pad/pride`) → ایندکس.
- سبد، چک‌اوت، حساب، مقایسه خالی → `noindex`.
- یک H1 در هر صفحه؛ بردکرامب همه‌جا + Schema `BreadcrumbList`.
- Slug محصول شامل دسته نیست (کوتاه بماند)؛ ولی محصول در `product_cat` قرار می‌گیرد.

## ۵. قواعد تغییر لینک

هر تغییر ساختار URL بعد از انتشار باید ریدایرکت ۳۰۱ داشته باشد و در `docs/DECISIONS.md` ثبت شود.
