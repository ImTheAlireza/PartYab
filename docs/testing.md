# تست پارت‌یاب — از صفر تا «قبول»

این سند جواب «چطور تستش کنم؟» است. سه سطح دارد؛ لازم نیست همه را بروی.

| سطح | چه چیزی را ثابت می‌کند | زمان |
|---|---|---|
| ۱. پروتوتایپ استاتیک | طراحی، RTL، ریسپانسیو، پنج حالت سازگاری در UI | ۵ دقیقه |
| ۲. وردپرس لوکال + نصب خودکار | قالب، افزونه، ایمپورت داده، صفحه‌ها، سبد و تسویه واقعی | ۳۰ دقیقه |
| ۳. تست خودکار + چک‌لیست | این‌که داده، REST و منطق سازگاری سر جایشان هستند | ۱۰ دقیقه |

---

## سطح ۱ — پروتوتایپ (بدون هیچ نصبی)

```bash
cd PartYab
python3 -m http.server 3000 --bind 0.0.0.0
# http://localhost:3000
```

صفحه‌ها: `index.html` (صفحهٔ اصلی) · `heroes.html` (گالری دموی هیرو) · `shop.html` · `product.html?id=p1` · `checkout.html` · `garage.html`

این همان طراحی است که در قالب پیاده شده؛ فقط داده‌اش در `assets/js/data.js` است و در وردپرس از دیتابیس می‌آید.
برای دیدن «وضعیت‌های سازگاری» سریع‌ترین راه: در `product.html` بین `p1`، `p4`، `p8`، `p10` جابه‌جا شو و خودرو را عوض کن.

---

## سطح ۲ — یک وردپرس واقعی روی کامپیوتر خودت

### گزینهٔ الف — LocalWP (بدون ترمینال، ۱۰ دقیقه)

۱. [LocalWP](https://localwp.com) را نصب کن → `+` → نام: `partyab` → Preferred → **Create site**.
۲. روی سایت → **WP Admin** → پیشخوان باز می‌شود.
۳. **افزونه‌ها → افزودن → بارگذاری** → `dist/partyab-fitment.zip` → نصب → فعال.
۴. **نمایش → پوسته‌ها → افزودن → بارگذاری** → `dist/partyab.zip` → نصب → فعال.
۵. **ابزارها → نصب سریع پارت‌یاب** → «اجرای نصب خودکار» → گزارش را بخوان.
۶. دکمهٔ **WP Admin** کنار سایت → بالا **Visit site**.

ووکامرس: اگر نصب نیست، از «افزونه‌ها → افزودن» جست‌وجوی WooCommerce → نصب و فعال، بعد گام ۵ را بزن.

### گزینهٔ ب — WP-CLI روی سایت LocalWP

در LocalWP روی سایت → **Open site shell**:

```bash
cd /home/user/PartYab          # مسیر خودت
bash bin/build-zips.sh
bash bin/install.sh --path="$HOME/Local Sites/partyab/app/public"
bash bin/smoke-test.sh --path="$HOME/Local Sites/partyab/app/public"
```

### گزینهٔ ج — wp-env (اگر Node و Docker داری)

```bash
# در ریشهٔ همین مخزن
cat > .wp-env.json <<'JSON'
{
  "core": null,
  "phpVersion": "8.1",
  "port": 8888,
  "plugins": ["./plugin/partyab-fitment", "https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip"],
  "themes": ["./theme/partyab"],
  "config": { "WP_DEBUG": true, "WP_DEBUG_LOG": true }
}
JSON

npx @wordpress/env start
npx @wordpress/env run cli wp plugin activate woocommerce partyab-fitment
npx @wordpress/env run cli wp theme activate partyab
npx @wordpress/env run cli wp partyab install     # نصب خودکار
npx @wordpress/env run cli wp partyab verify      # تست سلامت
```

نکته: در کانتینر فقط `theme/` و `plugin/` سوار می‌شوند؛ برای همین دستورهای `wp partyab …` (که داخل افزونه هستند)
کار می‌کنند و اسکریپت‌های `bin/` نیاز به سوار کردن پوشه دارند.

سایت روی `http://localhost:8888` و ادمین `admin` / `password`.
لاگ خطاهای PHP: `npx @wordpress/env run cli cat wp-content/debug.log`

### گزینهٔ د — Docker Compose (بدون wp-env)

```yaml
# docker-compose.yml
services:
  db:
    image: mariadb:10.6
    environment:
      MARIADB_DATABASE: wp
      MARIADB_USER: wp
      MARIADB_PASSWORD: wp
      MARIADB_ROOT_PASSWORD: root
    volumes: ["db:/var/lib/mysql"]
  wp:
    image: wordpress:php8.2-apache
    depends_on: [db]
    ports: ["8080:80"]
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: wp
      WORDPRESS_DB_PASSWORD: wp
      WORDPRESS_DB_NAME: wp
      WORDPRESS_DEBUG: 1
    volumes:
      - wp:/var/www/html
      - ./theme/partyab:/var/www/html/wp-content/themes/partyab
      - ./plugin/partyab-fitment:/var/www/html/wp-content/plugins/partyab-fitment
  cli:
    image: wordpress:cli-php8.2
    depends_on: [db, wp]
    user: "33:33"
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: wp
      WORDPRESS_DB_PASSWORD: wp
      WORDPRESS_DB_NAME: wp
    volumes:
      - wp:/var/www/html
      - ./theme/partyab:/var/www/html/wp-content/themes/partyab
      - ./plugin/partyab-fitment:/var/www/html/wp-content/plugins/partyab-fitment
      - ./bin:/var/www/html/bin
volumes: { db: {}, wp: {} }
```

```bash
docker compose up -d wp db
# نصب وردپرس + ووکامرس + پارت‌یاب
docker compose run --rm cli wp core install --url=http://localhost:8080 \
  --title=PartyAb --admin_user=admin --admin_password=admin --admin_email=a@b.c --skip-email
docker compose run --rm cli wp plugin install woocommerce --activate
docker compose run --rm cli wp theme activate partyab
docker compose run --rm cli wp plugin activate partyab-fitment
docker compose run --rm cli wp partyab install
docker compose run --rm cli wp partyab verify
```

---

## سطح ۳ — تست خودکار

```bash
bash bin/smoke-test.sh --path=/path/to/wordpress   # پوشش: همین مخزن
wp partyab verify                                  # داخل خود سایت
wp eval-file bin/smoke-test.php                    # اگر پوشهٔ bin در دسترس است
```

هر سه یک چیز را اجرا می‌کنند: مجموعهٔ `Partyab_Verify` در افزونه.

چه چیزهایی را چک می‌کند و چه انتظاری داشته باش:

| گروه | نمونهٔ بررسی | اگر FAIL شود |
|---|---|---|
| پایه | قالب فعال، افزونه فعال، ووکامرس، پیوند یکتا، فونت/JS موجود | نصب ناقص یا قالب دیگری فعال است |
| داده | جدول `wp_partyab_fitment`، تعداد ردیف سازگاری، تعداد محصول | ایمپورت انجام نشده |
| صفحه‌ها | صفحهٔ اصلی، برگهٔ گاراژ با الگوی `page-garage.php`، چهار برگهٔ ووکامرس | نصب خودکار را دوباره اجرا کن |
| ناوبری | چهار منو وصل‌اند و آیتم دارند | «نمایش → فهرست‌ها» |
| REST | پنج مسیر `partyab/v1` + پاسخ کاتالوگ + جست‌وجوی «۲۰۶» | پیوند یکتا یا افزونه |
| **ماتریس سازگاری** | لنت+۲۰۶=با شرط، شمع+۲۰۶=سازگار، فیلتر کابین+۲۰۶=ناسازگار، تسمه تایم=ثبت‌نشده، برف‌پاک‌کن=عمومی، بدون خودرو=انتخاب خودرو | داده یا منطق `Partyab_Fitment` |
| فروشگاه | واحد پول IRR/IRT، پرداخت در محل، منطقهٔ «ایران» | تنظیمات ووکامرس |

کد خروجی: `0` یعنی همه‌چیز سبز، `1` یعنی حداقل یک خطا. مناسب برای اجرا در CI یا بعد از هر آپدیت.

---

## چک‌لیست دستی (مرورگر، ۱۵ دقیقه)

همیشه در **پنجرهٔ ناشناس (Incognito)** تست کن تا کش و کوکی قبلی گمراهت نکند.

| # | کاری که می‌کنی | چه چیزی باید ببینی |
|---|---|---|
| ۱ | `http://site/` | هیرو «قطعهٔ درست، برای خودروی درست»، نقشهٔ خودرو با هات‌اسپات، ۸ دسته، بخش پنج حالت |
| ۲ | در هیرو «۲۰۶» بنویس و انتخاب کن | لیست مدل‌ها، بعد صفحهٔ سال؛ ۱۳۹۵ را بزن → «تأیید» → می‌روی به فروشگاه `?compat=1` |
| ۳ | نوار خودرو در فروشگاه | «خودروی فعال: پژو ۲۰۶ ۱۳۹۵» + کلید «فقط سازگار با پژو ۲۰۶» |
| ۴ | همان کلید را خاموش/روشن کن | تعداد محصولات عوض می‌شود و آدرس `?compat` می‌گیرد/می‌دهد |
| ۵ | «همهٔ محصولات» را نگاه کن | کارت‌ها با نوار رنگی لبه + خط متن وضعیت («سازگار با ۲۰۶ ۱۳۹۵»، «با شرط…») |
| ۶ | برو روی «لنت ترمز جلو ۲۰۶» | پنل سازگاری **زرد: سازگار است، با شرط** و متن شرط |
| ۷ | تب «خودروهای سازگار» | جدول سازگاری با ستون سال و شرط؛ ردیف ۲۰۶ با پس‌زمینهٔ کهربایی |
| ۸ | محصول «کیت تسمه تایم» | **ثبت‌نشده** (نه ناسازگار) |
| ۹ | محصول «تیغه برف‌پاک‌کن» | **عمومی** |
| ۱۰ | یک محصول را به سبد بزن | شمارندهٔ هدر افزایش + پیام «به سبد خرید اضافه شد» |
| ۱۱ | سبد → تسویه | فرم گیرنده، روش ارسال (پیک/پست/حضوری)، پرداخت در محل، تا «ثبت سفارش» |
| ۱۲ | سفارش را ثبت کن | صفحهٔ «سفارش دریافت شد» + سفارش در «حساب کاربری → سفارش‌ها» |
| ۱۳ | `?compat=1` را دستی در آدرس بگذار روی یک دسته | فقط محصولات سازگار (بدون جاوااسکریپت هم کار می‌کند) |
| ۱۴ | موبایل: عرض ۳۶۰px | بدون اسکرول افقی، نوار پایین (خانه/دسته‌ها/خودرو/سبد)، انتخاب‌گر خودرو تمام‌صفحه |
| ۱۵ | زوم مرورگر ۲۰۰٪ + تب Network | چیزی نشکسته؛ هیچ درخواستی به دامین خارجی نیست (فونت/آیکون/تصویر همه محلی) |

---

## تست‌های عمیق‌تر (اگر وقت داشتی)

**REST با curl** (باید JSON با ۱۰ سازنده بدهد):
```bash
curl -s https://site/wp-json/partyab/v1/vehicles | head -c 300
curl -s "https://site/wp-json/partyab/v1/search?q=206" | head -c 300
```

**CSV: ایمپورت/اکسپورت** — «ابزارها → سازگاری پارت‌یاب»: یک SKU را در `fitment.csv` عوض کن، ایمپورت کن،
بعد «دریافت CSV» بگیر و ببین همان تغییر برگشته است. ردیف تکراری نباید ساخته شود.

**متاباکس:** یک محصول را باز کن، دو ردیف سازگاری با وضعیت/شرط اضافه کن، ذخیره کن،
بعد در سایت با آن خودرو نتیجه را ببین.

**کش:** اگر افزونهٔ کش داری، قبل/بعد از روشن‌کردنش سناریوهای ۲ و ۵ را تکرار کن —
اگر وضعیت خودروی یک کاربر به دیگری «نشت» کرد، یعنی مسیرها از کش استثنا نشده‌اند.

**بدون جاوااسکریپت:** JS را در DevTools خاموش کن؛ صفحات باید بالا بیایند و `?compat=1` کار کند
(انتخاب‌گر خودرو کار نمی‌کند، ولی چیزی نمی‌شکند).

**دسترسی‌پذیری سریع:** با Tab حرکت کن؛ فوکوس باید دیده شود. `Escape` مودال را ببندد.
تصویرها `alt` داشته باشند. رنگ تنها حامل معنا نباشد (همهٔ حالت‌ها متن + آیکون دارند).

---

## اگر چیزی کار نکرد

| نشانه | سریع‌ترین کار |
|---|---|
| صفحهٔ سفید / خطای ۵۰۰ | `wp plugin deactivate partyab-fitment` یا با FTP نام پوشهٔ قالب را عوض کن؛ لاگ: `wp-content/debug.log` |
| انتخاب‌گر خودرو خالی است | `wp rewrite structure '/%postname%/' --hard` و «تنظیمات → پیوندهای یکتا → ذخیره» |
| فروشگاه محصول ندارد | `wp eval-file bin/install.php` را دوباره بزن، گزارش را بخوان |
| سبد/تسویه بی‌استایل | برگه‌ها بلوکی‌اند → `[woocommerce_cart]` و `[woocommerce_checkout]` |
| همه‌چیز درست است ولی ظاهر قدیمی | کش مرورگر/افزونهٔ کش: Incognito + پاک کردن کش صفحه |
| تصویر محصول پیش‌فرض ووکامرس | تصاویر را در «رسانه» آپلود و به محصول وصل کن، یا نصب خودکار را با تصاویر دوباره بزن |

---

## معیار «قبول»

نصب وقتی قابل تحویل است که:

1. `bash bin/smoke-test.sh` بدون FAIL تمام شود.
2. شش سناریوی سازگاری (۲، ۶، ۸، ۹ جدول بالا) درست باشند.
3. یک سفارش واقعی تا «ثبت سفارش» با پرداخت در محل برود.
4. در ۳۶۰px اسکرول افقی نباشد و هیچ درخواست خارجی در Network نباشد.
5. `wp-content/debug.log` بعد از یک دور گشت‌زنی خالی بماند (بدون notice/warning جدید).
