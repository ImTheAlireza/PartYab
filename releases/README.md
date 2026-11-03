# بسته‌های آماده نصب

آخرین ساخت: بازطراحی نسخه **۲٫۰٫۰** قالب (سرمه‌ای/نارنجی، انیمیشن‌های دسترس‌پذیر، صفحه‌های یکدست). نسخه نصب‌کننده **۱٫۱٫۱**.

| فایل | حجم | لینک دانلود مستقیم |
|---|---|---|
| **نصب‌کننده کامل** (Elementor + Pro داخلش) | ۲۷MB | https://github.com/ImTheAlireza/PartYab/raw/7604492612050a92109206aa9c9cc5fad805f600/releases/partyab-quickstart-full.zip |
| نصب‌کننده سبک (المنتور از مخزن وردپرس) | ۶۴۲KB | https://github.com/ImTheAlireza/PartYab/raw/7604492612050a92109206aa9c9cc5fad805f600/releases/partyab-quickstart.zip |
| چایلد تم پارتیاب | ۲۲۷KB | https://github.com/ImTheAlireza/PartYab/raw/7604492612050a92109206aa9c9cc5fad805f600/releases/part-yab.zip |
| افزونه سازگاری قطعه-خودرو | ۱۱KB | https://github.com/ImTheAlireza/PartYab/raw/7604492612050a92109206aa9c9cc5fad805f600/releases/partyab-compat.zip |

## نصب ۳ قدمی

1. وردپرس تازه (PHP 8.3).
2. `افزونه‌ها ← افزودن ← بارگذاری` → `partyab-quickstart-full.zip` → نصب → **فعال‌سازی**.
3. منوی **«نصب سریع»** → دکمه نارنجی → صبر تا پایان گزارش.

## چه چیزی نصب می‌شود

- افزونه‌ها: ووکامرس، المنتور، المنتور پرو، افزونه سازگاری
- قالب: Hello Elementor + چایلد تم پارتیاب (فونت وزیرمتن داخلش)
- تنظیمات: فارسی، تهران، هفته از شنبه، تومان، کیلوگرم/سانتی‌متر، Container فعال، عرض ۱۲۴۰
- محتوا: ۵ دسته، ۶ محصول با عکس، ۱۴ ردیف سازگاری، ۳ صفحه، منوی اصلی

## ساخت دوباره

```bash
bash tools/build-release.sh --full   # کامل
bash tools/build-release.sh          # سبک
```
