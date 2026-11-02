# نصب بدون دکمه (خط فرمان) — مسیر پشتیبان

اگر آپلود یا فعال‌سازی افزونه نصب‌کننده به هر دلیلی نشد، همه‌کار را با WP-CLI انجام بده.
در **Local** روی نام سایت ← راست‌کلیک ← **Open site shell**؛ آن‌جا این دستورها را بزن.

> مسیر فایل‌ها را با مسیر واقعی روی سیستم خودت عوض کن.

## ۱) قالب

```bash
wp theme install hello-elementor --activate
wp theme install ~/Downloads/part-yab.zip --activate
wp theme list
```

## ۲) افزونه‌ها

```bash
wp plugin install woocommerce --activate
wp plugin install ~/Downloads/Elementor.zip --activate
wp plugin install ~/Downloads/elementor-pro-4.3.2.zip --activate
wp plugin install ~/Downloads/partyab-compat.zip --activate
wp plugin list
```

## ۳) تنظیمات پایه

```bash
wp option update timezone_string "Asia/Tehran"
wp option update start_of_week 6
wp option update permalink_structure "/%postname%/"
wp option update WPLANG "fa_IR"
wp language core install fa_IR --activate

wp option update woocommerce_currency IRR
wp option update woocommerce_price_num_decimals 0
wp option update woocommerce_price_thousand_sep ","
wp option update woocommerce_weight_unit kg
wp option update woocommerce_dimension_unit cm
wp option update woocommerce_manage_stock yes

wp option update elementor_disable_color_schemes yes
wp option update elementor_disable_typography_schemes yes
wp option update elementor_container_width 1240
wp option update elementor_experiment-container active
wp rewrite flush
```

## ۴) داده سازگاری

```bash
wp partyab import ~/Downloads/compat-sample.csv --replace
```

## ۵) یا کل نصب خودکار

اگر افزونه نصب‌کننده فعال شد:

```bash
wp partyab quickstart
```

## ۶) اگر فعال‌سازی افزونه خطا داد

خطا را دقیق ببین، نه حدس بزن:

```bash
# روشن کردن لاگ
wp config set WP_DEBUG true --raw
wp config set WP_DEBUG_LOG true --raw
wp config set WP_DEBUG_DISPLAY false --raw

# بعد از تلاش برای فعال‌سازی
wp plugin activate partyab-quickstart
tail -n 40 wp-content/debug.log
```

خطای `Fatal error` + نام فایل و شماره خط را برایم بفرست.

## چک‌لیست موفقیت

```bash
wp plugin list --status=active --field=name
wp theme list --status=active --field=name
wp post list --post_type=product --fields=ID,post_title
wp db query "SELECT COUNT(*) FROM $(wp db prefix)partyab_compat;"
```

انتظار: قالب `part-yab`، محصولات ۶ عدد، جدول سازگاری ۱۴ ردیف.
