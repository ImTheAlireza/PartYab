# PartYab Compatibility — موتور سازگاری قطعه با خودرو

افزونه مستقل (نه بخشی از قالب) چون **مدل داده نباید با تغییر قالب از بین برود** — تصمیم D3.

## چرا جدول اختصاصی

| روش | مشکل در مقیاس |
|---|---|
| متای محصول (`postmeta`) | هر فیلتر «سازگار با پراید ۱۳۹۰» یک JOIN سنگین می‌سازد؛ با ۵ هزار محصول کند می‌شود |
| چند تاکسونومی | ترکیب برند×مدل×سال انفجار term می‌دهد |
| **جدول اختصاصی** | ایندکس مرکب `car_brand, car_model, year_from, year_to` → جستجوی زیر ۵۰ms |

## مدل داده

```
{prefix}partyab_compat
  id, product_id, car_brand, car_model, year_from, year_to,
  engine, note, created_at
  KEY product_id
  KEY car_lookup (car_brand, car_model, year_from, year_to)
```

قاعده‌های کسب‌وکار:
1. تطبیق سال **بسته** است: `year_from <= سال <= year_to`.
2. `engine` خالی یعنی «همه تیپ‌ها».
3. **نبود ردیف = نامشخص (unknown)، نه ناسازگار (no).** این تفاوت در UI حیاتی است.

## توابع عمومی (برای استفاده در قالب و دموها)

```php
partyab_compat_product_ids( array( 'brand' => 'پراید', 'model' => '131', 'year' => 1395 ) ); // int[]
partyab_compat_status( $product_id, $args ); // 'yes' | 'no' | 'unknown'
partyab_compat_rows( $product_id );          // همه ردیف‌های یک محصول
partyab_compat_brands();                     // برندهای خودرو + تعداد
partyab_compat_models( 'پراید' );            // مدل‌های یک برند
partyab_compat_normalize( 'پـژو ۴۰۵' );      // یکسان‌سازی ك/ي/نیم‌فاصله
```

## ایمپورت

**از پیشخوان:** منوی «سازگاری» → آپلود CSV → حالت افزودن یا جایگزینی.

**از خط فرمان:**

```bash
wp partyab import data/compat-sample.csv --replace
```

قالب CSV (نمونه: `data/compat-sample.csv`):

```csv
sku,car_brand,car_model,year_from,year_to,engine,note
PB-BRK-001,پراید,131,1381,1399,,جلو
```

- ستون `sku` یا `product_id` (یکی کافی است)
- سال‌ها با رقم لاتین
- `engine` و `note` اختیاری
- ردیف ناقص یا با SKU ناموجود: **رد می‌شود و در گزارش می‌آید** (کل ایمپورت متوقف نمی‌شود)

## کارایی

- نتیجه کوئری با `wp_cache_set` به مدت ۵ دقیقه کش می‌شود؛ پس از هر ایمپورت کش پاک می‌شود.
- نمایش «سازگار/ناسازگار» روی گریت محصول همیشه با IDهای کش‌شده مقایسه می‌شود، نه کوئری تکی برای هر کارت.

## وضعیت

| مورد | وضعیت |
|---|---|
| جدول + dbDelta + ارتقای خودکار | ✅ |
| توابع کوئری و وضعیت سه‌حالته | ✅ |
| ایمپورت CSV (پیشخوان + WP-CLI) | ✅ |
| صفحه مدیریت با آمار و گزارش | ✅ |
| UI انتخاب خودرو (`[partyab_my_car]`) | ⏳ قطعه ۴ |
| بج «سازگار با خودروی من» روی کارت محصول | ⏳ قطعه ۵ |
| فیلتر آرشیو (FacetWP) | ⏳ قطعه ۸ |
