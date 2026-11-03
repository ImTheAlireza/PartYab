# بسته‌های آماده نصب

آخرین ساخت: کامیت `e5ec32fd30b00a903dbd97ae4c6392ed625aa455` · نسخه نصب‌کننده **۱٫۱٫۱**

| فایل | حجم | لینک دانلود مستقیم |
|---|---|---|
| **نصب‌کننده کامل** (Elementor + Pro داخلش) | ۲۷MB | https://github.com/ImTheAlireza/PartYab/raw/f65b58a40f56db0ee78cf276570b0d45190181f5/releases/partyab-quickstart-full.zip |
| نصب‌کننده سبک (المنتور از مخزن وردپرس) | ۶۴۲KB | https://github.com/ImTheAlireza/PartYab/raw/f65b58a40f56db0ee78cf276570b0d45190181f5/releases/partyab-quickstart.zip |
| چایلد تم پارتیاب | ۲۲۷KB | https://github.com/ImTheAlireza/PartYab/raw/f65b58a40f56db0ee78cf276570b0d45190181f5/releases/part-yab.zip |
| افزونه سازگاری قطعه-خودرو | ۱۱KB | https://github.com/ImTheAlireza/PartYab/raw/f65b58a40f56db0ee78cf276570b0d45190181f5/releases/partyab-compat.zip |

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
