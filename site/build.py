#!/usr/bin/env python3
"""
PartYab v2 — page builder.

تمام صفحههای سایت را از روی سیستم طراحی واحد (assets/css/partyab.css)،
آیکونها (assets/partials/sprite.html) و قطعات مشترک این فایل میسازد.

    python3 build.py        → بازسازی همهٔ فایلهای HTML

خروجیها استاتیک و بدون وابستگیاند؛ با هر سرور استاتیکی سرو میشوند.
"""
from __future__ import annotations

import re
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent
SPRITE = (ROOT / "assets/partials/sprite.html").read_text(encoding="utf-8").strip()
SG = (ROOT / "styleguide.html").read_text(encoding="utf-8")

# خودروی هیرو و آیکونهای محصول مستقیماً از استایل‌گاید مرجع میآیند.
CAR_SVG = re.search(r'<div class="scene-car">\s*(<svg.*?</svg>)\s*</div>', SG, re.S).group(1)
_PC_ARTS = re.findall(r'(<svg class="pc-img" viewBox="0 0 120 120".*?</svg>)', SG, re.S)

ART: dict[str, str] = {
    "brake": _PC_ARTS[4],
    "oil": _PC_ARTS[5],
    "spark": _PC_ARTS[6],
    "shock": _PC_ARTS[7],
    "pump": _PC_ARTS[2],
    "belt": _PC_ARTS[3],
    "disc": _PC_ARTS[0],
    "filter": _PC_ARTS[1],
    "battery": '<svg class="pc-img" viewBox="0 0 120 120" fill="none"><rect x="22" y="36" width="76" height="56" rx="9" fill="#fff" stroke="#0E2340" stroke-width="3"/><rect x="22" y="46" width="76" height="10" fill="#0E2340" opacity=".13"/><rect x="36" y="27" width="12" height="9" rx="3" fill="#FF6A00"/><rect x="72" y="27" width="12" height="9" rx="3" fill="#FF6A00"/><path d="M48 67h8m-4-4v8M70 67h8" stroke="#FF6A00" stroke-width="3" stroke-linecap="round"/><path d="M30 36v-8h14v8" stroke="#0E2340" stroke-width="3"/></svg>',
    "wiper": '<svg class="pc-img" viewBox="0 0 120 120" fill="none"><path d="M18 84c22-6 52-20 84-40" stroke="#0E2340" stroke-width="7" stroke-linecap="round"/><path d="M26 90c24-6 52-18 76-36" stroke="#FF6A00" stroke-width="4" stroke-linecap="round"/><rect x="13" y="78" width="18" height="22" rx="6" fill="#fff" stroke="#0E2340" stroke-width="3"/><circle cx="22" cy="89" r="3" fill="#FF6A00"/></svg>',
    "light": '<svg class="pc-img" viewBox="0 0 120 120" fill="none"><path d="M22 42h50l20 18-20 18H22z" fill="#FFF4EB" stroke="#FF6A00" stroke-width="3"/><rect x="30" y="51" width="28" height="18" rx="9" fill="#fff" stroke="#0E2340" stroke-width="3"/><path d="M94 50h12M94 60h16M94 70h12" stroke="#FF6A00" stroke-width="3" stroke-linecap="round"/><circle cx="66" cy="60" r="5" fill="#FF6A00"/></svg>',
    "coil": '<svg class="pc-img" viewBox="0 0 120 120" fill="none"><rect x="42" y="16" width="36" height="20" rx="7" fill="#fff" stroke="#0E2340" stroke-width="3"/><rect x="46" y="36" width="28" height="50" rx="8" fill="#F4F8FD" stroke="#0E2340" stroke-width="3"/><path d="M50 48h20M50 58h20M50 68h20" stroke="#8A99AE" stroke-width="2.6" stroke-linecap="round"/><path d="M60 86v18M54 104h12" stroke="#FF6A00" stroke-width="4" stroke-linecap="round"/></svg>',
}

PRODUCTS: list[dict] = [
    dict(id="brake", art="brake", brand="ایساکو", sku="4254-108",
         title="لنت ترمز جلو پژو ۲۰۶ / ۲۰۷ تیپ ۲ و ۵",
         short="لنت ترمز جلو ۲۰۶", fit="سازگار با ۲۰۶، ۲۰۷ صندوق‌دار",
         rate="۴٫۶", reviews="۸۹ نظر", price="۱٬۹۱۰٬۰۰۰", old="۲٬۴۵۰٬۰۰۰",
         flag="۲۲٪", flag_small="تخفیف", status="in", stock="62%", stock_n="۱۲ عدد",
         note="یا ۳ قسط ۶۶۰٬۰۰۰ تومانی", cat="سیستم ترمز", href="product.html"),
    dict(id="oil", art="oil", brand="سرکان", sku="0551",
         title="فیلتر روغن پژو ۴۰۵ و سمند EF7", short="فیلتر روغن EF7",
         fit="سازگار با ۴۰۵، پارس، سمند", rate="۴٫۴", reviews="۶۳ نظر",
         price="۲۸۵٬۰۰۰", old="", flag="", flag_small="پرفروش", flag_kind="navy",
         status="in", stock="88%", stock_n="۴۰ عدد", note="ارسال امروز برای سفارش تا ۱۴", cat="مصرفی و فیلترها", href="product.html"),
    dict(id="spark", art="spark", brand="بوش", sku="FR7DC+",
         title="شمع موتور چهار شعله بوش تیپ پلاتینی", short="شمع موتور پلاتینی بوش",
         fit="سازگار با خانواده پژو و سمند", rate="۴٫۸", reviews="۲۱۲ نظر",
         price="۵۴۴٬۰۰۰", old="۶۴۰٬۰۰۰", flag="۱۵٪", flag_small="تخفیف",
         status="low", stock="14%", stock_n="۲ عدد", note="در حال فروش سریع — تا پایان امروز", cat="موتور و متعلقات", href="product.html"),
    dict(id="shock", art="shock", brand="کایابا", sku="3392",
         title="کمک فنر جلو چپ سمند و پژو پارس", short="کمک فنر جلو سمند",
         fit="سازگار با سمند، پارس، دنا", rate="۴٫۲", reviews="۳۱ نظر",
         price="۱٬۲۳۰٬۰۰۰", old="", flag="ناموجود", flag_kind="out",
         status="out", stock="0%", stock_n="—", note="با ثبت ایمیل، موجود شدن را اطلاع می‌دهیم", cat="جلوبندی و تعلیق", href="product.html"),
    dict(id="pump", art="pump", brand="کروز", sku="WP-405",
         title="واتر پمپ پژو ۴۰۵ و پارس (اصل)", short="واتر پمپ ۴۰۵",
         fit="سازگار با ۴۰۵، پارس، سمند", rate="۴٫۵", reviews="۴۷ نظر",
         price="۱٬۴۵۰٬۰۰۰", old="۱٬۷۲۰٬۰۰۰", flag="۱۶٪", flag_small="تخفیف",
         status="in", stock="55%", stock_n="۹ عدد", note="همراه با واشر و اورینگ", cat="موتور و متعلقات", href="product.html"),
    dict(id="belt", art="belt", brand="گیتس", sku="TB-206",
         title="تسمه تایم کامل پژو ۲۰۶ تیپ ۲", short="تسمه تایم کامل",
         fit="سازگار با ۲۰۶ تیپ ۲ و ۵", rate="۴٫۷", reviews="۱۰۴ نظر",
         price="۲٬۳۸۰٬۰۰۰", old="", flag="اصل", flag_kind="navy",
         status="in", stock="70%", stock_n="۱۸ عدد", note="گارانتی ۲۴ ماهه اصالت کالا", cat="موتور و متعلقات", href="product.html"),
    dict(id="battery", art="battery", brand="صدرا", sku="BAT-60",
         title="باتری ۶۰ آمپر سیلد ایرانی", short="باتری ۶۰ آمپر",
         fit="سازگار با اکثر خودروهای سواری", rate="۴٫۳", reviews="۵۸ نظر",
         price="۳٬۱۲۰٬۰۰۰", old="۳٬۴۵۰٬۰۰۰", flag="۱۰٪", flag_small="تخفیف",
         status="in", stock="48%", stock_n="۸ عدد", note="نصب و تحویل داغی در تهران", cat="برقی و الکترونیک", href="product.html"),
    dict(id="wiper", art="wiper", brand="والئو", sku="WB-22",
         title="تیغه برف‌پاک‌کن جفت ۲۲ اینچ", short="تیغه برف‌پاک‌کن",
         fit="سازگار با پژو، سمند، دنا", rate="۴٫۶", reviews="۱۳۶ نظر",
         price="۴۲۰٬۰۰۰", old="", flag="جدید", flag_kind="navy",
         status="in", stock="92%", stock_n="۵۲ عدد", note="ارسال رایگان در سفارش بالای ۵ میلیون", cat="بدنه و تزئینات", href="product.html"),
    dict(id="light", art="light", brand="کروز", sku="HL-405",
         title="چراغ جلو راست پژو ۴۰۵ (کریستالی)", short="چراغ جلو ۴۰۵",
         fit="سازگار با ۴۰۵ و پارس", rate="۴٫۱", reviews="۲۴ نظر",
         price="۱٬۸۷۰٬۰۰۰", old="", flag="", flag_small="",
         status="in", stock="36%", stock_n="۵ عدد", note="همراه با گارانتی تعویض", cat="روشنایی", href="product.html"),
    dict(id="coil", art="coil", brand="ایساکو", sku="IC-EF7",
         title="کویل احتراق سمند EF7 و دنا", short="کویل احتراق EF7",
         fit="سازگار با سمند EF7، دنا", rate="۴٫۵", reviews="۴۲ نظر",
         price="۱٬۶۴۰٬۰۰۰", old="۱٬۹۲۰٬۰۰۰", flag="۱۴٪", flag_small="تخفیف",
         status="in", stock="60%", stock_n="۱۱ عدد", note="مشاوره فنی رایگان پیش از خرید", cat="برقی و الکترونیک", href="product.html"),
    dict(id="disc", art="disc", brand="والئو", sku="DB-206",
         title="دیسک ترمز جلو پژو ۲۰۶ (جفت)", short="دیسک ترمز جلو ۲۰۶",
         fit="سازگار با ۲۰۶، ۲۰۷", rate="۴٫۴", reviews="۶۷ نظر",
         price="۲٬۷۵۰٬۰۰۰", old="", flag="", flag_small="",
         status="in", stock="74%", stock_n="۱۴ عدد", note="فاکتور رسمی و ضمانت اصالت", cat="سیستم ترمز", href="product.html"),
    dict(id="filter", art="filter", brand="سرکان", sku="AF-206",
         title="فیلتر هوای پژو ۲۰۶ تیپ ۲", short="فیلتر هوا ۲۰۶",
         fit="سازگار با ۲۰۶، ۲۰۷", rate="۴٫۷", reviews="۹۳ نظر",
         price="۲۳۰٬۰۰۰", old="۲۸۰٬۰۰۰", flag="۱۸٪", flag_small="تخفیف",
         status="in", stock="95%", stock_n="۶۰ عدد", note="پیشنهاد ویژه سرویس دوره‌ای", cat="مصرفی و فیلترها", href="product.html"),
]
BY_ID = {p["id"]: p for p in PRODUCTS}

NAV = [
    ("home", "خانه", "index.html", ""),
    ("shop", "فروشگاه", "shop.html", ""),
    ("cats", "دسته‌بندیها", "index.html#cats", "gear"),
    ("brands", "برندها", "brands.html", ""),
    ("garage", "خودروی من", "garage.html", "car"),
    ("compare", "مقایسه", "compare.html", ""),
    ("blog", "دانشنامه", "blog.html", ""),
    ("about", "درباره ما", "about.html", ""),
    ("contact", "تماس با ما", "contact.html", ""),
]

PAGES = [
    ("index.html", "خانه", "صفحه اصلی، هیرو سه‌بعدی، دسته‌ها، حراج و محصولات", "spark", "home"),
    ("shop.html", "فروشگاه", "فهرست محصولات با فیلتر، مرتب‌سازی و صفحه‌بندی", "cart", "shop"),
    ("product.html", "صفحه محصول", "گالری، خرید، مشخصات فنی، سازگاری و نظرات", "cart", "product"),
    ("cart.html", "سبد خرید", "اقلام سبد، تخفیف و خلاصه سفارش", "cart", "cart"),
    ("checkout.html", "تسویه حساب", "اطلاعات ارسال، روش پرداخت و تأیید نهایی", "card", "checkout"),
    ("account.html", "حساب کاربری", "داشبورد، سفارش‌ها، گاراژ و آدرسها", "user", "account"),
    ("orders.html", "سفارش‌های من", "تاریخچه و وضعیت سفارش‌ها", "truck", "orders"),
    ("wishlist.html", "علاقه‌مندیها", "کالاهای ذخیره‌شده", "heart", "wishlist"),
    ("garage.html", "خودروی من", "انتخاب خودرو، سازگاری و قطعات پیشنهادی", "car", "garage"),
    ("compare.html", "مقایسه محصولات", "جدول مقایسه مشخصات تا چهار کالا", "compare", "compare"),
    ("brands.html", "برندها", "فهرست برندهای قطعات", "verified", "brands"),
    ("blog.html", "دانشنامه فنی", "مقالات نگهداری و راهنمای خرید", "info", "blog"),
    ("article.html", "مقاله", "نمونه مقاله دانشنامه فنی", "info", "article"),
    ("about.html", "درباره ما", "داستان و ارزشهای پارتیاب", "spark", "about"),
    ("contact.html", "تماس با ما", "راههای ارتباطی و فرم پیام", "phone", "contact"),
    ("faq.html", "سوالات متداول", "پاسخ پرسشهای پرتکرار", "info", "faq"),
    ("warranty.html", "گارانتی و مرجوعی", "شرایط ضمانت اصالت و بازگشت کالا", "shield", "warranty"),
    ("guide.html", "راهنمای خرید", "چگونه قطعه درست را انتخاب کنیم", "check", "guide"),
    ("track.html", "پیگیری سفارش", "وضعیت لحظه‌ای مرسوله", "truck", "track"),
    ("thankyou.html", "سفارش ثبت شد", "صفحه تشکر پس از پرداخت", "check", "thankyou"),
    ("search.html", "نتایج جستجو", "جستجوی نام قطعه یا شماره فنی", "search", "search"),
    ("404.html", "صفحه پیدا نشد", "خطای ۴۰۴", "info", "404"),
    ("sitemap.html", "نقشه صفحات", "فهرست تمام صفحات بازطراحی‌شده", "pin", "sitemap"),
]


# ---------------------------------------------------------------------------
# helpers
# ---------------------------------------------------------------------------
def ic(name: str, cls: str = "") -> str:
    if not name:
        return ""
    classes = "pb-ic" + (f" {cls}" if cls else "")
    return f'<svg class="{classes}"><use href="#i-{name}"/></svg>'


def art(key: str, cls: str = "pc-img", attrs: str = "") -> str:
    svg = ART.get(key, ART["disc"])
    return svg.replace('class="pc-img"', f'class="{cls}" {attrs}'.strip())


def stars(n: int = 5) -> str:
    return '<span class="stars">' + "".join(ic("star") for _ in range(n)) + "</span>"


def crumbs(items: list[tuple[str, str | None]], dark: bool = False) -> str:
    out = ['<nav class="crumbs" aria-label="مسیر صفحه">']
    for label, href in items:
        if href:
            out.append(f'<a href="{href}">{label}</a>' + ic("chev-l"))
        else:
            out.append(f'<span class="current">{label}</span>')
    out.append("</nav>")
    return "".join(out)


def page_hero(title: str, lead: str, eyebrow: str,
              trail: list[tuple[str, str | None]], stats: list[tuple[str, str]] | None = None) -> str:
    st = ""
    if stats:
        st = '<div class="ph-side">' + "".join(
            f'<div class="ph-stat"><b>{a}</b><span>{b}</span></div>' for a, b in stats
        ) + "</div>"
    return f'''<section class="page-hero">
  <div class="container">
    {crumbs(trail, dark=True)}
    <div class="ph-grid">
      <div>
        <span class="ph-eyebrow">{ic("spark")}{eyebrow}</span>
        <h1>{title}</h1>
        <p class="ph-lead">{lead}</p>
      </div>
      {st}
    </div>
  </div>
</section>'''


def section_head(title: str, sub: str = "", href: str = "", label: str = "مشاهده همه", draw: bool = True) -> str:
    t = f'<h2 class="h2{" title-draw" if draw else ""}">{title}</h2>'
    s = f'<div class="sub">{sub}</div>' if sub else ""
    a = (f'<a class="link-more" href="{href}">{label}{ic("chev-l")}</a>' if href else "")
    return f'<div class="section-head"><div data-reveal>{t}{s}</div>{a}</div>'


def pc(p: dict, compact: bool = False, dark: bool = False, delay: float = 0) -> str:
    classes = ["pc"]
    if compact:
        classes.append("pc-compact")
    if dark:
        classes.append("pc-dark")
    status = p.get("status", "in")
    if status == "low":
        classes.append("is-low")
    if status == "out":
        classes.append("is-out")

    flag = ""
    if p.get("flag") or p.get("flag_small"):
        kind = p.get("flag_kind", "")
        mod = {"navy": " pc-flag-navy", "out": " pc-flag-out"}.get(kind, "")
        content = p.get("flag") or ic("bolt")
        small = f'<small>{p["flag_small"]}</small>' if p.get("flag_small") else ""
        flag = f'<span class="pc-flag{mod}">{content}{small}</span>'

    rate = "" if compact else (
        f'<div class="pc-rate">{stars()}<b>{p["rate"]}</b><span>({p["reviews"]})</span></div>'
    )
    fit = "" if compact else f'<div class="pc-fit">{ic("car")}<span>{p["fit"]}</span></div>'
    stock = "" if compact else f'''<div class="pc-stock"><span class="dot"></span>
      <span>{"تنها ۲ عدد" if status == "low" else ("ناموجود" if status == "out" else "موجود")}</span>
      <span class="pc-stock-bar"><i style="--w:{p["stock"]}"></i></span>
      <span class="pc-stock-n">{p["stock_n"]}</span></div>'''
    old = f'<span class="old">{p["old"]}</span>' if p.get("old") else ""
    note = "" if compact else f'<div class="pc-installment">{ic("card")}{p["note"]}</div>'
    add_icon = "bell" if status == "out" else "cart"
    add_label = "اطلاع" if status == "out" else "افزودن"
    disabled = " disabled" if status == "out" else ""

    return f'''<article class="{" ".join(classes)}" data-reveal style="--d:{delay}s">
  <div class="pc-media">
    {flag}
    <div class="pc-tools">
      <button class="pc-tool" data-fav title="افزودن به علاقه‌مندیها">{ic("heart")}</button>
      <button class="pc-tool" title="مقایسه" data-toast="کالا به لیست مقایسه اضافه شد">{ic("compare")}</button>
      <button class="pc-tool" title="مشاهده سریع" data-toast="مشاهده سریع — {p["short"]}">{ic("eye")}</button>
    </div>
    {art(p["art"])}
    <div class="pc-quick">{ic("eye")}مشاهده سریع</div>
  </div>
  <div class="pc-body">
    <div class="pc-topline"><span class="pc-brand">{p["brand"]}</span><span class="pc-sku">{p["sku"]}</span></div>
    <h3 class="pc-title"><a href="{p["href"]}">{p["title"]}</a></h3>
    {fit}{rate}{stock}
    <div class="pc-price-row">
      <div class="pc-price">{old}<b>{p["price"]} <small>تومان</small></b></div>
      <button class="pc-add"{disabled}>{ic(add_icon)}<span class="t">{add_label}</span></button>
    </div>
    {note}
  </div>
</article>'''


def pc_grid(products: list[dict], cols: int = 4, compact: bool = False, dark: bool = False,
            data_grid: bool = False) -> str:
    cards = "".join(pc(p, compact=compact, dark=dark, delay=i * 0.06) for i, p in enumerate(products))
    attr = ' data-pc-grid' if data_grid else ""
    return f'<div class="pc-grid cols-{cols}"{attr}>{cards}</div>'


def pager() -> str:
    return '''<nav class="pager" aria-label="صفحات">
  <a href="shop.html">%s</a><a href="shop.html" class="is-active">۱</a><a href="shop.html">۲</a>
  <a href="shop.html">۳</a><a href="shop.html">…</a><a href="shop.html">۱۲</a><a href="shop.html">%s</a>
</nav>''' % (ic("chev-r"), ic("chev-l"))


def usp() -> str:
    items = [
        ("shield", "ضمانت اصالت کالا", "کالای اورجینال با فاکتور رسمی"),
        ("truck", "ارسال سریع", "تهران ۲۴ ساعته، شهرستان ۱–۳ روز"),
        ("refresh", "۷ روز بازگشت", "در صورت عدم سازگاری قطعه"),
        ("headset", "مشاوره تخصصی", "پشتیبانی فنی ۹ تا ۱۸"),
    ]
    return '<div class="usp">' + "".join(
        f'<div class="usp-item"><span class="ic">{ic(i)}</span><div><b>{t}</b><span>{s}</span></div></div>'
        for i, t, s in items
    ) + "</div>"


def svc_grid() -> str:
    items = [
        ("shield", "ضمانت اصالت", "کالای اورجینال + فاکتور رسمی"),
        ("truck", "ارسال سریع", "تهران ۲۴ ساعته، شهرستان ۱–۳ روز"),
        ("card", "پرداخت اقساطی", "تا ۴ قسط بدون بهره"),
        ("headset", "مشاوره فنی", "انتخاب قطعه درست، رایگان"),
    ]
    return '<div class="svc-grid">' + "".join(
        f'<div class="svc" data-reveal style="--d:{i * .06}s"><span class="ic">{ic(a)}</span>'
        f'<div><b>{b}</b><span>{c}</span></div></div>'
        for i, (a, b, c) in enumerate(items)
    ) + "</div>"


def stories() -> str:
    items = [
        ("brake", "سیستم ترمز", "۱٬۸۵۰ کالا"), ("oil", "فیلتر و مصرفی", "۹۶۰ کالا"),
        ("gear", "موتور", "۴٬۲۱۰ کالا"), ("shock", "جلوبندی", "۲٬۳۴۰ کالا"),
        ("electric", "برقی", "۳٬۱۱۰ کالا"), ("light", "روشنایی", "۷۴۰ کالا"),
        ("body", "بدنه", "۲٬۷۳۰ کالا"), ("gear", "انتقال قدرت", "۱٬۲۵۰ کالا"),
    ]
    return '<div class="stories">' + "".join(
        f'<a class="story" href="shop.html"><span class="story-ring"><span>{ic(a)}</span></span>'
        f'<b>{b}</b><small>{c}</small></a>' for a, b, c in items
    ) + "</div>"


def cat_grid() -> str:
    items = [
        ("gear", "موتور و متعلقات", "۴٬۲۱۰ کالا"), ("brake", "سیستم ترمز", "۱٬۸۵۰ کالا"),
        ("shock", "جلوبندی و تعلیق", "۲٬۳۴۰ کالا"), ("electric", "برقی و الکترونیک", "۳٬۱۱۰ کالا"),
        ("oil", "مصرفی و فیلترها", "۹۶۰ کالا"), ("body", "بدنه و تزئینات", "۲٬۷۳۰ کالا"),
    ]
    return '<div class="cat-grid" id="cats">' + "".join(
        f'<a class="cat-tile" href="shop.html"><span class="ic">{ic(a)}</span><b>{b}</b><span>{c}</span></a>'
        for a, b, c in items
    ) + "</div>"


def marquee() -> str:
    items = [
        ("truck", "ارسال رایگان", "برای سفارش‌های بالای ۵ میلیون تومان"),
        ("bolt", "حراج پایان هفته", "تا ۴۵٪ تخفیف روی قطعات مصرفی"),
        ("shield", "ضمانت اصالت", "همه کالاها با فاکتور رسمی"),
        ("card", "پرداخت اقساطی", "تا ۴ قسط بدون بهره"),
        ("headset", "مشاوره فنی رایگان", "۰۲۱–۵۵۶۶۷۷۸۸"),
    ]
    unit = "".join(f'<span class="marquee-item">{ic(a)}<b>{b}</b>{c}</span>' for a, b, c in items)
    return f'<div class="marquee"><div class="marquee-track">{unit}{unit}</div></div>'


def rail(products: list[dict], on_light: bool = True) -> str:
    cards = "".join(pc(p, compact=True, dark=not on_light, delay=i * 0.06) for i, p in enumerate(products))
    nav = f'''<div class="rail-nav{" on-light" if on_light else ""}">
      <button class="rail-btn" data-rail="prev" aria-label="قبلی">{ic("chev-r")}</button>
      <button class="rail-btn" data-rail="next" aria-label="بعدی">{ic("chev-l")}</button>
    </div>'''
    return f'''<div class="rail">{nav}<div class="rail-track">{cards}</div></div>'''


def flash() -> str:
    return f'''<div class="flash">
  <div class="flash-side">
    <span class="badge badge-orange" style="height:22px">{ic("bolt")}حراج پایان هفته</span>
    <h3 style="margin-top:10px">تا <em>۴۵٪ تخفیف</em><br>روی قطعات مصرفی</h3>
    <p>تا پایان موجودی — تمدید نمی‌شود</p>
    <div class="countdown" data-countdown="29520">
      <div class="countdown-unit"><b data-cd="h">۰۸</b><span>ساعت</span></div>
      <div class="countdown-unit"><b data-cd="m">۱۲</b><span>دقیقه</span></div>
      <div class="countdown-unit"><b data-cd="s">۰۰</b><span>ثانیه</span></div>
    </div>
    <a class="btn btn-white btn-sm" style="margin-top:14px" href="shop.html">دیدن همه کالاهای حراج{ic("arrow-l")}</a>
  </div>
  <div class="flash-main">
    <div class="row-between" style="margin-bottom:12px">
      <span style="font-size:12px;color:#A8BED6">۴۸ کالا در این حراج</span>
      <div class="rail-nav">
        <button class="rail-btn" data-rail="prev" aria-label="قبلی">{ic("chev-r")}</button>
        <button class="rail-btn" data-rail="next" aria-label="بعدی">{ic("chev-l")}</button>
      </div>
    </div>
    <div class="rail"><div class="rail-track">{"".join(
        pc(p, compact=True, dark=True, delay=i * .06) for i, p in enumerate(
            [BY_ID["brake"], BY_ID["oil"], BY_ID["pump"], BY_ID["belt"], BY_ID["filter"]])
    )}</div></div>
  </div>
</div>'''


def findcar() -> str:
    return f'''<div class="findcar">
  <div class="findcar-head">{ic("car")}<b>خودروی من</b><span>— خودروی خود را انتخاب کنید تا فقط قطعات سازگار نمایش داده شود</span></div>
  <div class="findcar-grid">
    <label class="field"><span class="field-label">برند</span><select class="select"><option>ایران خودرو</option><option>سایپا</option><option>مدیران خودرو</option></select></label>
    <label class="field"><span class="field-label">مدل خودرو</span><select class="select"><option>پژو ۲۰۶ تیپ ۲</option><option>پژو پارس</option><option>سمند EF7</option></select></label>
    <label class="field"><span class="field-label">سال ساخت</span><select class="select"><option>۱۳۹۰</option><option>۱۳۹۱</option><option>۱۳۹۲</option></select></label>
    <button class="btn btn-primary" data-confirm-car style="height:46px">تأیید خودرو</button>
  </div>
</div>'''


def hero3d() -> str:
    return f'''<div class="container">
  <section class="hero3d" style="border-radius:28px">
    <div class="hero3d-bg">
      <div class="hero3d-blob"></div><div class="hero3d-grid"></div>
      <div class="hero3d-vignette"></div><div class="hero3d-noise"></div>
    </div>
    <div class="hero3d-inner">
      <div class="hero3d-copy" data-reveal>
        <span class="hero-eyebrow"><i>{ic("spark")}</i>فروشگاه تخصصی قطعات یدکی خودرو</span>
        <h1 class="hero3d-title" style="margin-top:18px">قطعه‌ای درست،<br><span class="grad">برای خودروی شما.</span></h1>
        <p>بیش از ۳۵٬۰۰۰ قطعه اصل و استاندارد برای خودروهای ایرانی و چینی؛ با تطبیق دقیق بر اساس مدل و سال ساخت، ضمانت اصالت و ارسال سریع.</p>
        <div class="hero3d-cta">
          <a class="btn btn-primary btn-lg" href="garage.html" data-magnetic>انتخاب خودرو و شروع خرید{ic("arrow-l")}</a>
          <a class="btn btn-glass btn-lg" href="shop.html">مشاهده دسته‌بندیها</a>
        </div>
        <div class="hero3d-stats">
          <div class="hero3d-stat"><b data-count="35000" data-suffix="+">۰</b><span>قطعه موجود در انبار</span></div>
          <div class="hero3d-stat"><b data-count="24" data-suffix=" ماه">۰</b><span>ضمانت اصالت کالا</span></div>
          <div class="hero3d-stat"><b>۱–۳ روز</b><span>ارسال سراسر کشور</span></div>
        </div>
      </div>
      <div class="hero3d-scene" data-scene>
        <div class="scene-stage">
          <div class="scene-halo"></div><div class="scene-orbit"></div><div class="scene-shadow"></div>
          <div class="scene-car">{CAR_SVG}</div>
          <div class="fcard fcard-1"><div class="fcard-in"><span class="ic">{ic("brake")}</span><span><b>لنت ترمز جلو ۲۰۶</b><span>۱٬۹۱۰٬۰۰۰ تومان</span></span></div></div>
          <div class="fcard fcard-2"><div class="fcard-in"><span class="ic">{ic("oil")}</span><span><b>فیلتر روغن EF7</b><span>۲۸۵٬۰۰۰ تومان</span></span><span class="badge badge-success">موجود</span></div></div>
          <div class="fcard fcard-3"><div class="fcard-in"><span class="ic" style="background:linear-gradient(160deg,#E7F6EE,#CDEEDD);color:#12874A">{ic("verified")}</span><span><b>سازگار با خودروی شما</b><span>پژو ۲۰۶ تیپ ۲ · ۱۳۹۰</span></span></div></div>
          <div class="fcard-live">۳۵٬۰۰۰+ قطعه در انبار</div>
        </div>
      </div>
    </div>
  </section>
</div>
<div class="container hero-findcar">{findcar()}</div>'''


def filters() -> str:
    return '''<aside class="filters">
  <div class="f-block"><h4>دسته‌بندی%s</h4>
    <label class="check"><input type="checkbox" checked> لنت ترمز <span class="cnt">۱۸۴</span></label>
    <label class="check"><input type="checkbox"> دیسک و کاسه چرخ <span class="cnt">۹۶</span></label>
    <label class="check"><input type="checkbox"> سیلندر و پمپ ترمز <span class="cnt">۷۲</span></label>
    <label class="check"><input type="checkbox"> ABS و سنسور <span class="cnt">۳۱</span></label></div>
  <div class="f-block"><h4>محدوده قیمت%s</h4>
    <div class="range"><span class="fill"></span><span class="knob" style="inset-inline-start:12%%"></span><span class="knob" style="inset-inline-start:72%%"></span></div>
    <div class="row-between text-xs text-muted"><span>۲۰۰٬۰۰۰ تومان</span><span>۵٬۰۰۰٬۰۰۰ تومان</span></div></div>
  <div class="f-block"><h4>برند%s</h4>
    <label class="check"><input type="checkbox" checked> ایساکو <span class="cnt">۶۴</span></label>
    <label class="check"><input type="checkbox"> والئو <span class="cnt">۲۸</span></label>
    <label class="check"><input type="checkbox"> تکسطار <span class="cnt">۱۹</span></label>
    <label class="check"><input type="checkbox"> کروز <span class="cnt">۴۴</span></label></div>
  <div class="f-block"><h4>وضعیت موجودی%s</h4>
    <label class="switch" style="margin-bottom:8px"><input type="checkbox" checked> فقط کالاهای موجود</label>
    <label class="switch"><input type="checkbox"> فقط دارای تخفیف</label></div>
  <div class="f-block"><h4>سازگاری خودرو%s</h4>
    <label class="check"><input type="checkbox" checked> پژو ۲۰۶ تیپ ۲ <span class="cnt">۲۳۴</span></label>
    <label class="check"><input type="checkbox"> سمند EF7 <span class="cnt">۹۸</span></label>
    <a class="link-more" style="margin-top:10px" href="garage.html">تغییر خودرو%s</a></div>
</aside>''' % ((ic("chev-d"),) * 5 + (ic("chev-l"),))


def acc_menu(active: str) -> str:
    items = [
        ("account", "user", "داشبورد", "account.html"),
        ("orders", "truck", "سفارش‌های من", "orders.html"),
        ("garage", "car", "گاراژ خودرو", "garage.html"),
        ("wishlist", "heart", "علاقه‌مندیها", "wishlist.html"),
        ("compare", "compare", "مقایسه", "compare.html"),
        ("track", "pin", "پیگیری سفارش", "track.html"),
    ]
    links = "".join(
        f'<a class="{"is-active" if k == active else ""}" href="{h}">{ic(i)} {t}</a>'
        for k, i, t, h in items
    )
    return f'''<aside class="panel acc-menu" style="padding:10px">
  <div class="acc-user"><span class="avatar">ع</span><div><b>علی محمدی</b><span>۰۹۱۲۳۴۵۶۷۸۹</span></div></div>
  {links}<a class="logout" href="index.html">{ic("user")} خروج از حساب</a>
</aside>'''


# ---------------------------------------------------------------------------
# shared chrome
# ---------------------------------------------------------------------------
def header(active: str) -> str:
    parts = []
    for k, t, h, i in NAV:
        pill = '<span class="pill">جدید</span>' if k == "blog" else ""
        links_cls = "nav-link is-active" if k == active else "nav-link"
        parts.append(f'<a class="{links_cls}" href="{h}">{ic(i) if i else ""}{t}{pill}</a>')
    links = "".join(parts)
    return f'''<div class="topbar"><div class="container">
  <div class="topbar-nav">
    <a href="tel:02155667788">{ic("phone")}۰۲۱–۵۵۶۶۷۷۸۸</a>
    <a href="track.html">{ic("truck")}ارسال رایگان سفارش‌های بالای ۵ میلیون</a>
  </div>
  <div class="topbar-nav">
    <a href="track.html">پیگیری سفارش</a><a href="faq.html">سوالات متداول</a>
    <a class="accent" href="warranty.html">۷ روز کاری ضمانت بازگشت</a>
    <a href="sitemap.html">همه صفحات</a>
  </div>
</div></div>

<header class="site-header">
  <div class="container">
    <div class="header-main">
      <button class="hamburger" type="button" data-drawer-open aria-expanded="false" aria-label="باز کردن منو">
        <span></span><span></span><span></span>
      </button>
      <a href="index.html" class="logo">
        <svg class="logo-mark"><use href="#i-logo"/></svg>
        <span class="logo-text"><b>پارتیاب</b><span>قطعات اصل، انتخاب مطمئن</span></span>
      </a>
      <form class="search" role="search" action="search.html" method="get">
        {ic("search")}<input name="q" type="search" placeholder="جستجوی قطعه، برند یا کد فنی… مثلاً «لنت ترمز ۲۰۶»" aria-label="جستجو">
        <button class="search-btn" type="submit">جستجو</button>
      </form>
      <div class="header-actions">
        <a class="h-action" href="account.html">{ic("user")}<span class="label">حساب کاربری</span></a>
        <a class="h-action" href="wishlist.html">{ic("heart")}<span class="label">علاقه‌مندی</span></a>
        <span class="h-sep hide-md"></span>
        <a class="h-action" href="cart.html">{ic("cart")}<span class="label">سبد خرید</span><span class="count">۳</span></a>
      </div>
    </div>
  </div>
  <nav class="main-nav"><div class="container">{links}
    <a class="nav-link" href="contact.html" style="margin-inline-start:auto;color:var(--orange-600)">فروش عمده / تعمیرگاهها</a>
  </div></nav>
</header>'''


def drawer(active: str) -> str:
    items = [(t, h, i) for _, t, h, i in NAV] + [
        ("سبد خرید", "cart.html", "cart"),
        ("تسویه حساب", "checkout.html", "card"),
        ("سفارش‌های من", "orders.html", "truck"),
        ("علاقه‌مندیها", "wishlist.html", "heart"),
        ("پیگیری سفارش", "track.html", "pin"),
        ("سوالات متداول", "faq.html", "info"),
        ("همه صفحات", "sitemap.html", "pin"),
    ]
    return f'''<div class="drawer-backdrop" data-drawer-close></div>
<aside class="drawer" aria-label="منوی موبایل">
  <div class="drawer-head"><b>پارتیاب</b><button class="drawer-close" data-drawer-close aria-label="بستن">{ic("plus")}</button></div>
  {"".join(f'<a class="{"is-active" if h.startswith(active) else ""}" href="{h}">{ic(i)}{t}</a>' for t, h, i in items)}
  <div class="drawer-foot">قطعات اصل، انتخاب مطمئن<br>۰۲۱–۵۵۶۶۷۷۸۸ · شنبه تا پنجشنبه ۹ تا ۱۸</div>
</aside>'''


def footer() -> str:
    return f'''<footer class="ft">
  <div class="ft-clip">
    <svg class="ft-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width=".7">
      <circle cx="12" cy="12" r="3.2"/><circle cx="12" cy="12" r="8.6"/>
      <path d="M12 1.4v3M12 19.6v3M1.4 12h3M19.6 12h3M4.5 4.5l2.1 2.1M17.4 17.4l2.1 2.1M4.5 19.5l2.1-2.1M17.4 6.6l2.1-2.1"/>
    </svg>
  </div>
  <div class="container ft-cta-wrap">
    <div class="ft-cta">
      <span class="ic">{ic("headset")}</span>
      <div><b>مطمئن نیستید کدام قطعه مناسب خودروی شماست؟</b>
      <span>کارشناسان فنی پارتیاب رایگان راهنمایی می‌کنند — هر روز ۹ تا ۱۸</span></div>
      <span class="spacer"></span>
      <a class="btn btn-white btn-lg" href="tel:02155667788">{ic("phone")}۰۲۱–۵۵۶۶۷۷۸۸</a>
    </div>
  </div>
  <div class="container">
    <div class="ft-main">
      <div>
        <a href="index.html" class="ft-logo"><svg class="logo-mark"><use href="#i-logo"/></svg>
          <span class="logo-text"><b>پارتیاب</b><span>PARITYAB AUTO PARTS</span></span></a>
        <p class="ft-about">فروشگاه تخصصی لوازم یدکی خودرو؛ تأمین قطعات اصل و استاندارد برای خودروهای ایرانی و چینی با ضمانت اصالت، تطبیق دقیق با مدل خودرو و ارسال سراسری.</p>
        <div class="ft-socials">
          <a class="ft-social" href="contact.html" title="اینستاگرام">{ic("instagram")}</a>
          <a class="ft-social" href="contact.html" title="تلگرام">{ic("telegram")}</a>
          <a class="ft-social" href="contact.html" title="واتساپ">{ic("whatsapp")}</a>
          <a class="ft-social" href="contact.html" title="آپارات">{ic("aparat")}</a>
        </div>
      </div>
      <div class="ft-col"><h5>خدمات مشتریان</h5><ul>
        <li><a href="track.html">پیگیری سفارش</a></li>
        <li><a href="warranty.html">شرایط مرجوعی و تعویض</a></li>
        <li><a href="guide.html">روشهای ارسال</a></li>
        <li><a href="faq.html">سوالات متداول</a></li>
        <li><a href="about.html">درباره پارتیاب</a></li></ul></div>
      <div class="ft-col"><h5>دسترسی سریع</h5><ul>
        <li><a href="garage.html">انتخاب خودرو</a></li>
        <li><a href="contact.html">فروش عمده و تعمیرگاهها</a></li>
        <li><a href="blog.html">دانشنامه فنی</a></li>
        <li><a href="brands.html">برندها</a></li>
        <li><a href="sitemap.html">نقشه سایت</a></li></ul></div>
      <div class="ft-news">
        <b>خبرنامه فنی پارتیاب</b>
        <p>تخفیفهای هفتگی، قطعات جدید و نکات نگهداری خودرو — بدون ایمیل تبلیغاتی اضافه.</p>
        <form class="ft-news-form" onsubmit="return false"><input type="email" placeholder="ایمیل شما">
          <button class="btn btn-primary">عضویت</button></form>
        <div class="ft-trust">
          <span>{ic("verified")}ضمانت اصالت کالا</span>
          <span>{ic("check")}۷ روز بازگشت</span>
          <span>{ic("check")}فاکتور رسمی</span>
        </div>
      </div>
    </div>
    <div class="ft-info">
      <div class="ft-info-item"><span class="ic">{ic("pin")}</span><span><span>نشانی فروشگاه</span><b>تهران، خیابان امیرکبیر، پاساژ کاشانی، پلاک ۲۱۱</b></span></div>
      <div class="ft-info-item"><span class="ic">{ic("phone")}</span><span><span>تلفن سفارش و پشتیبانی</span><b class="ltr" style="text-align:start">۰۲۱–۵۵۶۶۷۷۸۸</b></span></div>
      <div class="ft-info-item"><span class="ic">{ic("clock")}</span><span><span>ساعت پاسخگویی</span><b>شنبه تا پنجشنبه ۹ تا ۱۸</b></span></div>
      <div class="ft-info-item"><span class="ic">{ic("mail")}</span><span><span>ایمیل</span><b class="ltr" style="text-align:start">info@part-yab.ir</b></span></div>
    </div>
    <div class="ft-bottom">
      <div class="ft-copy">© ۱۴۰۴ <b>پارتیاب</b> — تمامی حقوق محفوظ است. طراحی مجدد v2.0</div>
      <div class="ft-bottom-right"><div class="ft-pays">
        <span class="ft-pay">شاپرک</span><span class="ft-pay">زرین‌پال</span>
        <span class="ft-pay">ای‌نماد</span><span class="ft-pay">پرداخت در محل</span></div>
        <button class="ft-top-btn">{ic("up")}بازگشت به بالا</button></div>
    </div>
  </div>
</footer>'''


def shell(title: str, body: str, desc: str = "", active: str = "",
          hero: str = "", extra_head: str = "") -> str:
    return f'''<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0E2340">
<meta name="description" content="{desc}">
<title>{title} — پارتیاب | قطعات اصل، انتخاب مطمئن</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Cpath d='M24 2.6 43 13.3v21.4L24 45.4 5 34.7V13.3z' fill='%230E2340' stroke='%23FF6A00' stroke-width='3'/%3E%3C/svg%3E">
<link rel="preload" href="assets/fonts/Vazirmatn-Variable.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="assets/css/partyab.css">
<link rel="stylesheet" href="assets/css/pages.css">
{extra_head}
</head>
<body>
<div class="scroll-progress"><i></i></div>
{SPRITE}
{header(active)}
{drawer(active)}
{hero}
<main id="content">{body}</main>
{footer()}
<div class="toast-wrap" aria-live="polite"></div>
<script src="assets/js/partyab.js"></script>
<script src="assets/js/pages.js"></script>
</body>
</html>
'''


# ---------------------------------------------------------------------------
# pages
# ---------------------------------------------------------------------------
def page_home() -> tuple[str, str, str, str, str]:
    posts = post_grid(POSTS[:3])
    body = f'''{hero3d()}
{marquee()}
<div class="container block">
  {section_head("دسته‌های پرطرفدار", "آیکونها روی هاور می‌چرخند و رینگ گرادیانی فعال می‌شود", "shop.html", "همه دسته‌ها")}
  {stories()}
</div>
<div class="container block">
  {section_head("حراج این هفته", "تا پایان موجودی — تمدید نمی‌شود", "shop.html", "همه تخفیفها")}
  {flash()}
</div>
<div class="container block">
  {section_head("خرید بر اساس دسته‌بندی", "آیکون‌های خطی ساده، لود سریع و خوانا در هر سایز", "shop.html")}
  {cat_grid()}
</div>
<div class="container block">
  {section_head("پرفروش‌ترین قطعات", "بر اساس خرید ۳۰ روز گذشته · موس را روی کارت‌ها ببرید", "shop.html")}
  {pc_grid([BY_ID[k] for k in ("brake", "oil", "spark", "shock")], cols=4)}
</div>
<div class="container block">{usp()}</div>
<div class="container block">
  {section_head("چرا پارتیاب؟", "چهار دلیلی که خرید قطعه را مطمئن می‌کند", "")}
  {svc_grid()}
</div>
<div class="container block">
  {section_head("سازگار با خودروی شما", "پژو ۲۰۶ تیپ ۲ · ۱۳۹۰ — فقط قطعات تأییدشده", "garage.html", "تغییر خودرو")}
  {rail([BY_ID[k] for k in ("pump", "belt", "battery", "wiper", "coil", "disc")], on_light=True)}
</div>
<div class="container block">
  {section_head("از دانشنامه فنی", "راهنمای نگهداری و انتخاب قطعه، به زبان ساده", "blog.html")}
  {posts}
</div>
<div class="container block">
  <div class="panel panel-pad row" style="gap:18px;flex-wrap:wrap;background:linear-gradient(100deg,#FFF4EB,#fff 60%)">
    <span class="ic" style="width:52px;height:52px;border-radius:15px;display:grid;place-items:center;background:var(--orange-50);color:var(--orange-600)">{ic("spark")}</span>
    <div class="grow"><b style="font-size:16px">همه صفحات سایت، از نو و یکدست</b>
      <div class="text-sm text-muted">هدر، فروشگاه، محصول، سبد، تسویه، حساب، گاراژ، مقایسه، دانشنامه و صفحات محتوایی — همگی روی سیستم طراحی v2.</div></div>
    <a class="btn btn-navy" href="sitemap.html">دیدن نقشه صفحات{ic("arrow-l")}</a>
  </div>
</div>'''
    return ("خانه", body, "صفحه اصلی فروشگاه لوازم یدکی خودرو پارتیاب با هیرو سه‌بعدی و سیستم طراحی v2", "home", "")


def page_shop() -> tuple[str, str, str, str, str]:
    hero = page_hero("فروشگاه قطعات یدکی", "۳۵٬۰۰۰ قطعه اصل و استاندارد، با فیلتر دقیق بر اساس دسته، برند، قیمت و خودروی شما. فقط قطعات سازگار با پژو ۲۰۶ تیپ ۲ نشان داده می‌شود.",
                     "فهرست محصولات", [("خانه", "index.html"), ("فروشگاه", None)],
                     [("۳۵٬۰۰۰+", "کالای موجود"), ("۲۳۴", "نتیجه این فیلتر")])
    body = f'''<div class="container block">
  {crumbs([("خانه", "index.html"), ("سیستم ترمز", "shop.html"), ("لنت ترمز", None)])}
  <div class="shop-layout">
    {filters()}
    <div>
      <div class="shop-toolbar">
        <div class="chips-row">
          <button class="btn btn-outline btn-sm filter-toggle" data-filter-toggle>{ic("compare")}فیلترها</button>
          <span class="f-active">لنت ترمز {ic("plus")}</span>
          <span class="f-active">پژو ۲۰۶ {ic("plus")}</span>
          <span class="f-active">فقط موجود {ic("plus")}</span>
          <span class="text-xs text-muted">۲۳۴ کالا</span>
        </div>
        <div class="row" style="gap:8px;flex-wrap:wrap">
          <div class="tabs"><button>پرفروش‌ترین</button><button class="is-active">ارزان‌ترین</button><button>جدیدترین</button></div>
          <div class="view-toggle"><button class="is-active" data-view="grid" aria-label="نمایش شبکه‌ای">{ic("gear")}</button>
            <button data-view="list" aria-label="نمایش ردیفی">{ic("car")}</button></div>
        </div>
      </div>
      {pc_grid([BY_ID[k] for k in ("brake", "disc", "filter", "oil", "spark", "pump", "belt", "coil", "wiper")],
               cols=3, data_grid=True)}
      <div style="margin-top:22px">{pager()}</div>
    </div>
  </div>
  <div class="note-box" style="margin-top:22px">{ic("info")}<span>شماره فنی قطعه را داری؟ بدون خط تیره و با حروف لاتین جستجو کن. <a href="search.html"><b>جستجوی شماره فنی</b></a> ارقام فارسی و لاتین را یکسان می‌خواند.</span></div>
</div>
<div class="container block">{usp()}</div>'''
    return ("فروشگاه", body, "فهرست محصولات پارتیاب با فیلتر دسته، برند، قیمت و سازگاری خودرو", "shop", hero)


def page_product() -> tuple[str, str, str, str, str]:
    p = BY_ID["brake"]
    specs = [
        ("برند", "ایساکو (ISACO)"), ("کد فنی / SKU", "4254-108"), ("شماره OEM", "4254-108 · 1610101580"),
        ("نوع کالا", "اصل / OEM"), ("موقعیت نصب", "محور جلو (چپ و راست)"), ("جنس لنت", "نیمه‌فلزی کم‌سروصدا"),
        ("تعداد در بسته", "۴ عدد (یک محور)"), ("کشور سازنده", "ایران"), ("گارانتی", "۲۴ ماه ضمانت اصالت کالا"),
        ("وزن بسته", "۱٫۸ کیلوگرم"),
    ]
    spec_rows = "".join(f"<tr><th>{a}</th><td>{b}</td></tr>" for a, b in specs)
    compat_rows = "".join(
        f'<tr class="{c}"><td>{a}</td><td>{b}</td><td>{d}</td><td><span class="badge {"badge-success" if c == "is-you" else "badge-outline"}">{e}</span></td></tr>'
        for a, b, d, e, c in [
            ("ایران خودرو", "پژو ۲۰۶ تیپ ۲", "۱۳۸۸–۱۳۹۲", "خودروی شما", "is-you"),
            ("ایران خودرو", "پژو ۲۰۶ تیپ ۵", "۱۳۸۸–۱۳۹۴", "سازگار", ""),
            ("ایران خودرو", "پژو ۲۰۷ صندوق‌دار", "۱۳۹۰–۱۳۹۶", "سازگار", ""),
            ("ایران خودرو", "پژو ۴۰۵", "۱۳۷۵–۱۳۹۵", "نامشخص", ""),
            ("سایپا", "پراید ۱۳۱", "۱۳۸۵–۱۳۹۹", "ناسازگار", ""),
        ])
    reviews = "".join(f'''<div class="review"><span class="avatar">{n}</span><div>
      <div class="row-between"><b>{name}</b><span class="date">{date}</span></div>
      <div class="row" style="gap:6px">{stars()}<span class="badge badge-success">خریدار تأییدشده</span></div>
      <p>{text}</p></div></div>''' for n, name, date, text in [
        ("م", "محمد رضایی", "۱۴۰۴/۰۷/۲۱", "لنت اصل ایساکو بود، بسته‌بندی سالم و شماره فنی روی جعبه مطابق سایت. بعد از ۵۰۰ کیلومتر صدا و گرد نداشت."),
        ("ع", "علیرضا کریمی", "۱۴۰۴/۰۶/۰۹", "با انتخاب خودرو در سایت، همین مدل پیشنهاد شد و دقیقاً به ۲۰۶ تیپ ۲ خورد. ارسال به کرج یک روزه بود."),
        ("س", "سعید احمدی", "۱۴۰۴/۰۵/۱۴", "قیمت نسبت به بازار مناسب بود و فاکتور رسمی هم همراه سفارش آمد. پیشنهاد می‌کنم."),
    ])
    shots = "".join(art(p["art"], "pdp-shot" + (" is-active" if i == 0 else ""), f'data-shot="{i}"')
                    for i in range(4))
    thumbs = "".join(
        f'<button class="pdp-thumb{" is-active" if i == 0 else ""}" data-thumb="{i}" aria-label="نمای {i + 1}">{art(p["art"], "thumb-art")}</button>'
        for i in range(4))
    tabs = [
        ("specs", "مشخصات فنی"), ("compat", "سازگاری خودرو"),
        ("reviews", "نظرات (۸۹)"), ("shipping", "ارسال و گارانتی"),
    ]
    tab_btns = "".join(
        f'<button data-tab="{k}" data-tab-group="pdp" class="{"is-active" if i == 0 else ""}">{t}</button>'
        for i, (k, t) in enumerate(tabs))
    body = f'''<div class="container block-sm">
  {crumbs([("خانه", "index.html"), ("سیستم ترمز", "shop.html"), ("لنت ترمز", "shop.html"), (p["short"], None)])}
  <div class="pdp">
    <div class="pdp-gallery">
      <div class="pdp-main"><span class="badge badge-orange pdp-flag">۲۲٪ تخفیف</span>{shots}
        <button class="pdp-zoom" data-toast="نمای بزرگ تصویر" aria-label="بزرگ‌نمایی">{ic("eye")}</button></div>
      <div class="pdp-thumbs">{thumbs}</div>
      <div class="alert" style="margin-top:14px">{ic("info")}<span>تصاویر نمایشی‌اند؛ کالای ارسالی دقیقاً مطابق برند و کد فنی درج‌شده است.</span></div>
    </div>
    <div class="pdp-buy">
      <div class="row" style="gap:8px;flex-wrap:wrap"><span class="badge badge-navy">ایساکو</span>
        <span class="badge badge-soft-orange">{ic("verified")}اصل / OEM</span>
        <span class="badge badge-outline">کد فنی 4254-108</span></div>
      <h1 class="pdp-title">{p["title"]}</h1>
      <div class="pdp-meta"><span class="row" style="gap:6px">{stars()}<b>۴٫۶</b> (۸۹ نظر)</span>
        <span>برند: <b>ایساکو</b></span><span>دسته: <a href="shop.html"><b>سیستم ترمز</b></a></span></div>
      <div class="pdp-compat">{ic("check")}<span>این قطعه با خودروی انتخابی شما <b>پژو ۲۰۶ تیپ ۲ (۱۳۹۰)</b> سازگار است. <a href="garage.html">تغییر خودرو</a></span></div>
      <div class="pdp-price-box">
        <div><span class="old">۲٬۴۵۰٬۰۰۰ تومان</span><b>۱٬۹۱۰٬۰۰۰ <small>تومان</small></b>
          <span class="save">{ic("bolt")} ۵۴۰٬۰۰۰ تومان صرفه‌جویی</span></div>
        <span class="badge badge-success" style="height:28px">۲۲٪ تخفیف</span>
      </div>
      <div class="pdp-stock">{ic("check")}<span class="dot"></span> موجود در انبار — ۱۲ عدد · ارسال امروز تا ساعت ۱۴</div>
      <div class="pdp-actions">
        <div class="stepper" data-stepper><button data-minus aria-label="کمتر">{ic("minus")}</button><span data-qty>۱</span><button data-plus aria-label="بیشتر">{ic("plus")}</button></div>
        <a class="btn btn-primary btn-lg" href="cart.html">{ic("cart")}افزودن به سبد خرید</a>
        <a class="btn btn-navy btn-lg btn-icon" href="wishlist.html" aria-label="علاقه‌مندی">{ic("heart")}</a>
        <a class="btn btn-outline btn-lg btn-icon" href="compare.html" aria-label="مقایسه">{ic("compare")}</a>
      </div>
      <div class="pdp-trust">
        <div>{ic("shield")}ضمانت اصالت</div><div>{ic("refresh")}۷ روز بازگشت</div><div>{ic("truck")}ارسال سریع</div>
      </div>
      <div class="panel panel-pad" style="background:var(--bg)">
        <b style="font-size:13px">{ic("car")} سرویس دوره‌ای پیشنهادی</b>
        <p class="text-sm text-muted" style="margin-top:6px">همزمان با تعویض لنت، دیسک و فیلتر هوا را هم بررسی کن. <a href="article.html"><b>راهنمای کامل</b></a></p>
      </div>
    </div>
  </div>
</div>
<div class="container block">
  <div class="panel panel-pad">
    <div class="tab-bar">{tab_btns}</div>
    <div class="tab-panel" data-tab-panel="specs" data-tab-group="pdp">
      <h3 class="h3" style="margin-bottom:12px">مشخصات فنی</h3>
      <table class="spec-table"><tbody>{spec_rows}</tbody></table>
    </div>
    <div class="tab-panel hidden" data-tab-panel="compat" data-tab-group="pdp">
      <h3 class="h3" style="margin-bottom:6px">سازگاری با خودرو</h3>
      <p class="text-sm text-muted" style="margin-bottom:14px">تطبیق بر اساس برند، مدل و بازه سال ساخت. «نامشخص» یعنی داده‌ای ثبت نشده، نه ناسازگاری.</p>
      <table class="compat-table"><thead><tr><th>برند خودرو</th><th>مدل</th><th>سال ساخت</th><th>وضعیت</th></tr></thead><tbody>{compat_rows}</tbody></table>
    </div>
    <div class="tab-panel hidden" data-tab-panel="reviews" data-tab-group="pdp">
      <div class="grid-2 split-220">
        <div class="rating-score"><b>۴٫۶</b>{stars()}<div class="text-sm text-muted" style="margin-top:6px">از ۸۹ نظر خریداران</div>
          <div class="rating-bars" style="margin-top:14px">{"".join(f'<div><span>۵ ستاره</span><span class="rb"><i style="width:{w}%"></i></span><span>{n}</span></div>' for n, w in [("۶۲", 78), ("۵", 46), ("۱۸", 21), ("۳", 8), ("۱", 4)])}</div>
        </div>
        <div>{reviews}<button class="btn btn-outline btn-sm" style="margin-top:14px" data-toast="فرم ثبت نظر باز شد">ثبت نظر شما</button></div>
      </div>
    </div>
    <div class="tab-panel hidden" data-tab-panel="shipping" data-tab-group="pdp">
      <div class="grid-2">{svc_grid()}<div class="prose"><h3>شرایط ارسال و بازگشت</h3>
        <p>سفارش‌های ثبت‌شده تا ساعت ۱۴ در تهران همان‌روز و در شهرستان‌ها طی ۱ تا ۳ روز کاری ارسال می‌شود. سفارش‌های بالای ۵ میلیون تومان ارسال رایگان دارند.</p>
        <p>تا ۷ روز پس از دریافت، در صورت استفاده‌نشدن کالا و سالم بودن بسته‌بندی، مرجوعی پذیرفته می‌شود. <a href="warranty.html"><b>شرایط کامل گارانتی</b></a></p></div></div>
    </div>
  </div>
</div>
<div class="container block">
  {section_head("همراه با این محصول", "مکمل‌های پیشنهادی برای سرویس ترمز", "shop.html")}
  {rail([BY_ID[k] for k in ("disc", "pump", "filter", "oil", "belt")])}
</div>
<div class="container block">{usp()}</div>'''
    return ("لنت ترمز جلو پژو ۲۰۶", body, "صفحه محصول لنت ترمز جلو پژو ۲۰۶ ایساکو با مشخصات فنی و سازگاری خودرو", "product", "")


def cart_row(p: dict, qty: str = "۱") -> str:
    return f'''<div class="cart-row">
  <a class="c-media" href="product.html">{art(p["art"], "c-art")}</a>
  <div><h3><a href="product.html">{p["title"]}</a></h3>
    <div class="c-meta">{p["brand"]} · کد فنی {p["sku"]} · <span class="badge badge-soft-orange" style="height:20px">سازگار با خودروی شما</span></div>
    <div class="stepper" data-stepper style="height:36px;margin-top:8px">
      <button data-minus aria-label="کمتر">{ic("minus")}</button><span data-qty>{qty}</span><button data-plus aria-label="بیشتر">{ic("plus")}</button>
    </div></div>
  <div class="c-price">{p["price"]} <small>تومان</small></div>
  <button class="c-remove" data-remove-row aria-label="حذف">{ic("plus")}</button>
</div>'''


def page_cart() -> tuple[str, str, str, str, str]:
    hero = page_hero("سبد خرید", "۳ کالا در سبد شماست. تا ساعت ۱۴ سفارش را نهایی کن تا امروز ارسال شود.",
                     "مرحله ۱ از ۳", [("خانه", "index.html"), ("سبد خرید", None)],
                     [("۳", "کالا"), ("رایگان", "ارسال")])
    rows = "".join(cart_row(BY_ID[k], q) for k, q in [("brake", "۱"), ("oil", "۲"), ("filter", "۱")])
    body = f'''<div class="container block">
  <div class="steps" style="margin-bottom:22px">
    <div class="step is-now"><span class="n">۱</span><b>سبد خرید</b><span class="line"></span></div>
    <div class="step"><span class="n">۲</span><b>اطلاعات ارسال و پرداخت</b><span class="line"></span></div>
    <div class="step"><span class="n">۳</span><b>تأیید و ثبت سفارش</b></div>
  </div>
  <div class="cart-layout">
    <div class="panel">
      <div class="panel-head"><h2>اقلام سبد <span class="badge badge-navy">۳ کالا</span></h2>
        <a class="link-more" href="shop.html">ادامه خرید{ic("chev-l")}</a></div>
      {rows}
      <div class="panel-pad" style="border-top:1px solid var(--line-soft)">
        <div class="alert alert-success">{ic("check")}<span>ارسال این سفارش رایگان است — به حداقل ۵ میلیون تومان رسیدهاید.</span></div>
      </div>
    </div>
    <aside class="panel cart-summary">
      <h3 class="h3" style="margin-bottom:12px">خلاصه سفارش</h3>
      <div class="totals">
        <div><span>جمع کالاها (۴ عدد)</span><b>۲٬۷۱۰٬۰۰۰ تومان</b></div>
        <div><span>تخفیف محصولات</span><b style="color:var(--danger)">− ۵۴۰٬۰۰۰ تومان</b></div>
        <div><span>هزینه ارسال</span><b class="free">رایگان</b></div>
        <div class="grand"><span>مبلغ قابل پرداخت</span><b>۲٬۱۷۰٬۰۰۰ تومان</b></div>
      </div>
      <div class="coupon"><input class="input" placeholder="کد تخفیف" data-coupon-input>
        <button class="btn btn-outline" data-coupon>اعمال</button></div>
      <div class="ship-progress">با ۸۳۰٬۰۰۰ تومان دیگر، ارسال ویژه رایگان فعال می‌شود
        <div class="bar"><i style="width:72%"></i></div></div>
      <a class="btn btn-primary btn-block btn-lg" style="margin-top:18px" href="checkout.html">ادامه به تسویه حساب{ic("arrow-l")}</a>
      <div class="row" style="justify-content:center;gap:8px;margin-top:12px">
        <span class="text-xs text-muted">{ic("shield")} پرداخت امن</span>
        <span class="text-xs text-muted">{ic("refresh")} ۷ روز بازگشت</span></div>
    </aside>
  </div>
</div>
<div class="container block">
  {section_head("پیشنهاد ویژه برای سبد شما", "معمولاً همراه این قطعات خریداری می‌شود", "shop.html")}
  {rail([BY_ID[k] for k in ("disc", "pump", "spark", "belt", "wiper")])}
</div>
<div class="container block">{usp()}</div>'''
    return ("سبد خرید", body, "سبد خرید پارتیاب — اقلام، کد تخفیف و خلاصه سفارش", "cart", hero)


def page_checkout() -> tuple[str, str, str, str, str]:
    hero = page_hero("تسویه حساب", "اطلاعات ارسال و پرداخت را در یک صفحه وارد کن. خرید بدون ثبت‌نام هم ممکن است.",
                     "مرحله ۲ از ۳", [("خانه", "index.html"), ("سبد خرید", "cart.html"), ("تسویه حساب", None)],
                     [("۲٬۱۷۰٬۰۰۰", "تومان"), ("رایگان", "ارسال")])
    fields = [
        ("نام و نام خانوادگی", "علی محمدی"), ("شماره تماس", "۰۹۱۲۳۴۵۶۷۸۹"),
        ("ایمیل (اختیاری)", "ali@example.com"), ("کد ملی (اختیاری)", "۰۰۱۲۳۴۵۶۷۸"),
        ("استان", "تهران"), ("شهر", "تهران"),
    ]
    f1 = "".join(f'<label class="field"><span class="field-label">{a}</span><input class="input" value="{b}"></label>' for a, b in fields)
    pay = [
        ("card", "پرداخت آنلاین", "کلیه کارت‌های شتاب · درگاه امن شاپرک"),
        ("truck", "پرداخت در محل", "فقط تهران و سفارش‌های زیر ۵ میلیون تومان"),
        ("card", "پرداخت اقساطی", "۴ قسط بدون بهره با زرین‌پال"),
    ]
    pays = "".join(
        f'<div class="pay-option{" is-active" if i == 0 else ""}" data-pay><span class="ic">{ic(a)}</span>'
        f'<div><b>{b}</b><span>{c}</span></div><span class="radio"></span></div>' for i, (a, b, c) in enumerate(pay))
    mini = "".join(f'''<div class="row"><span class="c-media">{art(BY_ID[k]["art"], "c-art")}</span>
      <div class="grow"><b>{BY_ID[k]["short"]}</b><div class="qty">تعداد {q} · {BY_ID[k]["brand"]}</div></div>
      <b style="font-size:12px">{BY_ID[k]["price"]}</b></div>''' for k, q in [("brake", "۱"), ("oil", "۲"), ("filter", "۱")])
    body = f'''<div class="container block">
  <div class="steps" style="margin-bottom:22px">
    <div class="step is-done"><span class="n">{ic("check")}</span><b>سبد خرید</b><span class="line"></span></div>
    <div class="step is-now"><span class="n">۲</span><b>اطلاعات ارسال و پرداخت</b><span class="line"></span></div>
    <div class="step"><span class="n">۳</span><b>تأیید و ثبت سفارش</b></div>
  </div>
  <div class="checkout-layout">
    <div>
      <div class="panel checkout-card">
        <h3><span class="n">۱</span> اطلاعات گیرنده</h3>
        <div class="form-grid">{f1}
          <label class="field full"><span class="field-label">نشانی دقیق</span><input class="input" value="تهران، خیابان امیرکبیر، کوچه شهید رضایی، پلاک ۲۴، واحد ۳"></label>
          <label class="field full"><span class="field-label">کد پستی</span><input class="input" value="۱۱۳۶۵۴۳۲۱۱"></label>
        </div>
      </div>
      <div class="panel checkout-card">
        <h3><span class="n">۲</span> روش ارسال</h3>
        <div class="pay-list">
          <div class="pay-option is-active" data-pay><span class="ic">{ic("truck")}</span><div><b>ارسال استاندارد</b><span>تهران ۲۴ ساعته، شهرستان ۱–۳ روز کاری</span></div><span class="radio"></span></div>
          <div class="pay-option" data-pay><span class="ic">{ic("bolt")}</span><div><b>ارسال ویژه (امروز)</b><span>فقط تهران · تا ساعت ۱۴ · ۱۵۰٬۰۰۰ تومان</span></div><span class="radio"></span></div>
        </div>
      </div>
      <div class="panel checkout-card">
        <h3><span class="n">۳</span> روش پرداخت</h3>
        <div class="pay-list">{pays}</div>
      </div>
      <div class="panel checkout-card">
        <h3><span class="n">۴</span> توضیحات سفارش</h3>
        <label class="field"><span class="field-label">یادداشت برای انبار (اختیاری)</span>
          <textarea class="textarea" placeholder="مثلاً: پیش از ارسال با من تماس بگیرید"></textarea></label>
        <label class="check" style="margin-top:10px"><input type="checkbox" checked> فاکتور رسمی می‌خواهم</label>
        <label class="check"><input type="checkbox" checked> شرایط و قوانین سایت را مطالعه کردم</label>
      </div>
    </div>
    <aside class="panel panel-pad checkout-summary">
      <h3 class="h3" style="margin-bottom:14px">سفارش شما</h3>
      <div class="order-mini">{mini}</div>
      <div class="divider"></div>
      <div class="totals">
        <div><span>جمع کالاها</span><b>۲٬۷۱۰٬۰۰۰</b></div>
        <div><span>تخفیف</span><b style="color:var(--danger)">− ۵۴۰٬۰۰۰</b></div>
        <div><span>ارسال</span><b class="free">رایگان</b></div>
        <div class="grand"><span>قابل پرداخت</span><b>۲٬۱۷۰٬۰۰۰ تومان</b></div>
      </div>
      <a class="btn btn-primary btn-block btn-lg" style="margin-top:18px" href="thankyou.html">ثبت و پرداخت سفارش{ic("arrow-l")}</a>
      <p class="text-xs text-muted center" style="margin-top:10px">با ثبت سفارش، <a href="warranty.html"><b>قوانین و شرایط بازگشت</b></a> را می‌پذیرید.</p>
      <div class="pdp-trust" style="grid-template-columns:1fr;margin-top:14px"><div>{ic("shield")}پرداخت امن شاپرک</div></div>
    </aside>
  </div>
</div>'''
    return ("تسویه حساب", body, "تسویه حساب تک‌صفح‌های پارتیاب", "checkout", hero)


def page_account() -> tuple[str, str, str, str, str]:
    hero = page_hero("حساب کاربری", "سلام علی 👋 خلاصه سفارش‌ها، خودروی ذخیره‌شده و آدرسهایت در یک نگاه.",
                     "داشبورد من", [("خانه", "index.html"), ("حساب کاربری", None)],
                     [("۴", "سفارش"), ("۲", "خودرو در گاراژ")])
    kpis = [("truck", "۴", "سفارش ثبت‌شده"), ("clock", "۱", "در حال آماده‌سازی"),
            ("heart", "۶", "علاقه‌مندی"), ("card", "۱۲۰٬۰۰۰", "اعتبار کیف پول")]
    kpi = '<div class="kpi-grid">' + "".join(
        f'<div class="kpi"><span class="ic">{ic(a)}</span><div><b>{b}</b><span>{c}</span></div></div>' for a, b, c in kpis) + "</div>"
    orders = "".join(f'''<tr><td><b>#{n}</b></td><td>{d}</td><td><span class="badge {"badge-success" if s == "تحویل شده" else "badge-warn"}">{s}</span></td>
      <td><b>{p} تومان</b></td><td><a class="link-more" href="track.html">جزئیات{ic("chev-l")}</a></td></tr>'''
        for n, d, s, p in [
            ("PY-1404-1284", "۱۴۰۴/۰۸/۰۲", "در حال آماده‌سازی", "۲٬۱۷۰٬۰۰۰"),
            ("PY-1404-1192", "۱۴۰۴/۰۶/۱۸", "تحویل شده", "۱٬۹۱۰٬۰۰۰"),
            ("PY-1404-0943", "۱۴۰۴/۰۴/۲۷", "تحویل شده", "۵۴۴٬۰۰۰"),
        ])
    body = f'''<div class="container block">
  <div class="acc-layout">
    {acc_menu("account")}
    <div>
      {kpi}
      <div class="garage-card block-sm">
        <span class="ic">{ic("car")}</span>
        <div class="grow"><b>خودروی فعلی: پژو ۲۰۶ تیپ ۲ · ۱۳۹۰</b><span>فقط قطعات سازگار با این خودرو به شما نمایش داده می‌شود</span></div>
        <a class="btn btn-white btn-sm" href="garage.html">مدیریت گاراژ{ic("arrow-l")}</a>
      </div>
      <div class="panel">
        <div class="panel-head"><h2>آخرین سفارش‌ها</h2><a class="link-more" href="orders.html">همه سفارش‌ها{ic("chev-l")}</a></div>
        <div style="overflow-x:auto"><table class="data-table"><thead><tr><th>شماره سفارش</th><th>تاریخ</th><th>وضعیت</th><th>مبلغ</th><th></th></tr></thead><tbody>{orders}</tbody></table></div>
      </div>
      <div class="acc-head block-sm" style="margin-top:22px"><h2 class="h3">آدرسهای من</h2>
        <button class="btn btn-outline btn-sm" data-toast="فرم افزودن آدرس باز شد">{ic("plus")}آدرس جدید</button></div>
      <div class="addr-grid">
        <div class="addr-card"><div class="row-between"><b>خانه <span class="badge badge-soft-orange">پیش‌فرض</span></b>{ic("pin")}</div>
          <p>تهران، خیابان امیرکبیر، کوچه شهید رضایی، پلاک ۲۴، واحد ۳ · ۱۱۳۶۵۴۳۲۱۱</p>
          <div class="row"><button class="btn btn-ghost btn-sm" data-toast="ویرایش آدرس">ویرایش</button></div></div>
        <div class="addr-card"><div class="row-between"><b>محل کار</b>{ic("pin")}</div>
          <p>کرج، خیابان شهید بهشتی، برج اداری آسمان، طبقه ۵ · ۳۱۴۵۶۷۸۹۱۲</p>
          <div class="row"><button class="btn btn-ghost btn-sm" data-toast="ویرایش آدرس">ویرایش</button></div></div>
      </div>
    </div>
  </div>
</div>'''
    return ("حساب کاربری", body, "داشبورد حساب کاربری پارتیاب", "account", hero)


def page_orders() -> tuple[str, str, str, str, str]:
    hero = page_hero("سفارش‌های من", "همه سفارش‌ها، وضعیت لحظه‌ای و کد رهگیری مرسولهها.",
                     "حساب کاربری", [("خانه", "index.html"), ("حساب کاربری", "account.html"), ("سفارش‌ها", None)])
    rows = "".join(f'''<tr><td><b>#{n}</b></td><td>{d}</td><td>{c} کالا</td><td><b>{p} تومان</b></td>
      <td><span class="badge {b}">{s}</span></td><td><a class="link-more" href="track.html">پیگیری{ic("chev-l")}</a></td></tr>'''
        for n, d, c, p, s, b in [
            ("PY-1404-1284", "۱۴۰۴/۰۸/۰۲", "۳", "۲٬۱۷۰٬۰۰۰", "آماده‌سازی", "badge-warn"),
            ("PY-1404-1192", "۱۴۰۴/۰۶/۱۸", "۱", "۱٬۹۱۰٬۰۰۰", "تحویل شده", "badge-success"),
            ("PY-1404-0943", "۱۴۰۴/۰۴/۲۷", "۴", "۵۴۴٬۰۰۰", "تحویل شده", "badge-success"),
            ("PY-1403-2210", "۱۴۰۳/۱۲/۰۴", "۲", "۲٬۳۸۰٬۰۰۰", "تحویل شده", "badge-success"),
        ])
    body = f'''<div class="container block"><div class="acc-layout">
  {acc_menu("orders")}
  <div>
    <div class="acc-head"><h2 class="h3">تاریخچه سفارش‌ها</h2>
      <div class="tabs"><button class="is-active">همه</button><button>در جریان</button><button>تحویل شده</button></div></div>
    <div class="panel"><div style="overflow-x:auto"><table class="data-table">
      <thead><tr><th>شماره سفارش</th><th>تاریخ</th><th>تعداد</th><th>مبلغ</th><th>وضعیت</th><th></th></tr></thead>
      <tbody>{rows}</tbody></table></div></div>
    <div class="note-box" style="margin-top:16px">{ic("info")}<span>کد رهگیری مرسولهها در صفحه <a href="track.html"><b>پیگیری سفارش</b></a> و با وارد کردن شماره سفارش قابل مشاهده است.</span></div>
  </div>
</div></div>'''
    return ("سفارش‌های من", body, "تاریخچه و وضعیت سفارش‌های کاربر", "orders", hero)


def page_wishlist() -> tuple[str, str, str, str, str]:
    hero = page_hero("علاقه‌مندیها", "قطعاتی که ذخیره کردهاید — از تغییر قیمت و موجود شدنشان باخبر می‌شوید.",
                     "حساب کاربری", [("خانه", "index.html"), ("حساب کاربری", "account.html"), ("علاقه‌مندیها", None)])
    rows = []
    for p in [BY_ID[k] for k in ("brake", "belt", "battery", "wiper")]:
        old = f'<span class="old">{p["old"]}</span>' if p.get("old") else ""
        stock_badge = "badge-success" if p["status"] != "out" else "badge-danger"
        stock_text = "موجود" if p["status"] != "out" else "ناموجود"
        rows.append(f'''<div class="p-row" style="margin-bottom:12px">
      <a class="p-media" href="product.html">{art(p["art"], "c-art")}</a>
      <div><div class="pc-brand">{p["brand"]} · کد {p["sku"]}</div>
        <h4 class="h4" style="margin-block:4px"><a href="product.html">{p["title"]}</a></h4>
        <div class="row" style="gap:6px;flex-wrap:wrap"><span class="pc-fit" style="margin:0">{ic("car")}<span>{p["fit"]}</span></span>
          <span class="badge {stock_badge}">{stock_text}</span></div></div>
      <div style="text-align:left"><div class="pc-price" style="align-items:flex-end">
        {old}<b>{p["price"]} <small>تومان</small></b></div>
        <div class="row" style="gap:6px;margin-top:8px;justify-content:flex-end">
          <a class="btn btn-primary btn-sm" href="cart.html">{ic("cart")}افزودن</a>
          <button class="btn btn-outline btn-icon btn-sm" data-toast="از علاقه‌مندیها حذف شد" aria-label="حذف">{ic("plus")}</button></div></div>
    </div>''')
    rows = "".join(rows)
    body = f'''<div class="container block"><div class="acc-layout">
  {acc_menu("wishlist")}
  <div><div class="acc-head"><h2 class="h3">کالاهای ذخیره‌شده</h2><span class="badge badge-navy">۴ کالا</span></div>
    {rows}
    <div class="alert">{ic("bell")}<span>برای کالاهای ناموجود، به محض رسیدن به انبار ایمیل اطلاع‌رسانی ارسال می‌کنیم.</span></div>
  </div>
</div></div>'''
    return ("علاقه‌مندیها", body, "لیست علاقه‌مندیهای کاربر", "wishlist", hero)


def page_garage() -> tuple[str, str, str, str, str]:
    hero = page_hero("خودروی من / گاراژ", "یک بار خودرویت را انتخاب کن؛ در تمام صفحات فقط قطعات سازگار با همان مدل و سال ساخت را می‌بینی.",
                     "تطبیق دقیق قطعه", [("خانه", "index.html"), ("خودروی من", None)],
                     [("۲", "خودرو ذخیره‌شده"), ("۱۲۴", "قطعه سازگار")])
    cars = [
        ("پژو ۲۰۶ تیپ ۲ · ۱۳۹۰", "خودروی فعلی", "is-active"),
        ("سمند EF7 · ۱۳۹۴", "خودروی دوم", ""),
    ]
    car_cards = []
    for n, d, c in cars:
        style = "box-shadow:0 12px 26px -16px rgba(255,106,0,.8)" if c == "is-active" else "opacity:.82"
        badge_cls = "badge-orange" if c else "badge-outline"
        badge_bg = "var(--orange-500)" if c else "rgba(255,255,255,.15)"
        badge_txt = "فعال" if c else "انتخاب"
        count = "۱۲۴" if c else "۹۸"
        car_cards.append(f'''<div class="garage-card" style="{style}">
      <span class="ic">{ic("car")}</span><div class="grow"><b>{n}</b><span>{d} · {count} قطعه سازگار</span></div>
      <span class="badge {badge_cls}" style="background:{badge_bg};color:#fff">{badge_txt}</span>
    </div>''')
    car_cards = "".join(car_cards)
    body = f'''<div class="container block">
  <div class="panel panel-pad">
    <h2 class="h3" style="margin-bottom:6px">{ic("car")} افزودن یا تغییر خودرو</h2>
    <p class="text-sm text-muted" style="margin-bottom:16px">سال ساخت در تطبیق قطعات اهمیت دارد؛ بازه‌های سال در جدول سازگاری بسته در نظر گرفته می‌شود.</p>
    <div class="car-select">
      <label class="field"><span class="field-label">برند خودرو</span><select class="select"><option>ایران خودرو</option><option>سایپا</option><option>مدیران خودرو</option></select></label>
      <label class="field"><span class="field-label">مدل</span><select class="select"><option>پژو ۲۰۶ تیپ ۲</option><option>پژو پارس</option><option>سمند EF7</option><option>دنا پلاس</option></select></label>
      <label class="field"><span class="field-label">سال ساخت</span><select class="select"><option>۱۳۹۰</option><option>۱۳۹۱</option><option>۱۳۹۲</option><option>۱۳۹۴</option></select></label>
      <button class="btn btn-primary" data-confirm-car style="height:44px">ذخیره خودرو</button>
    </div>
  </div>
  <div class="grid-2 block-sm">
    {car_cards}
  </div>
  <div class="compat-result block-sm">
    <div class="compat-state is-ok"><span class="ic">{ic("check")}</span><div><b>سازگار</b><p>قطعه با برند، مدل و سال خودروی شما مطابقت دارد و در سبد خرید بدون هشدار قابل اضافه شدن است.</p></div></div>
    <div class="compat-state is-warn"><span class="ic">{ic("info")}</span><div><b>نامشخص</b><p>برای این ترکیب دادهای ثبت نشده؛ یعنی ناسازگار نیست. پیش از خرید با کارشناسان فنی مشورت کن.</p></div></div>
    <div class="compat-state is-info"><span class="ic">{ic("compare")}</span><div><b>ناسازگار</b><p>قطعه مربوط به تیپ یا سال دیگری است؛ در سبد خرید با هشدار قرمز مشخص می‌شود.</p></div></div>
    <div class="compat-state is-ok"><span class="ic">{ic("verified")}</span><div><b>ضمانت تطبیق</b><p>اگر قطعه‌ای با وجود نشان «سازگار» به خودرویت نخورد، تا ۷ روز بدون هزینه مرجوع می‌شود.</p></div></div>
  </div>
  <div class="note-box block-sm">{ic("info")}<span>قاعده جدول سازگاری: <b>year_from ≤ سال خودرو ≤ year_to</b>؛ اگر تیپ موتور خالی باشد یعنی همه تیپ‌ها. نبود ردیف «نامشخص» است، نه «ناسازگار».</span></div>
</div>
<div class="container block">
  {section_head("قطعات پرمصرف پژو ۲۰۶ تیپ ۲", "بر اساس سرویس‌های دوره‌ای و خرید کاربران همین مدل", "shop.html")}
  {pc_grid([BY_ID[k] for k in ("brake", "oil", "filter", "belt")], cols=4)}
</div>
<div class="container block">
  <div class="grid-2" style="align-items:start">
    <div class="panel panel-pad"><h3 class="h3" style="margin-bottom:14px">برنامه سرویس خودرو</h3>
      <div class="timeline">
        <div class="timeline-item is-done"><span class="dot"></span><b>تعویض روغن و فیلتر — ۱۲۸٬۰۰۰ کیلومتر</b><span>۱۴۰۴/۰۶/۱۸ · فیلتر روغن سرکان</span></div>
        <div class="timeline-item is-now"><span class="dot"></span><b>تعویض لنت ترمز جلو — سررسید</b><span>پیشنهاد: لنت ایساکو 4254-108</span></div>
        <div class="timeline-item"><span class="dot"></span><b>تسمه تایم — ۱۴۰٬۰۰۰ کیلومتر</b><span>حدود ۱۲٬۰۰۰ کیلومتر دیگر</span></div>
        <div class="timeline-item"><span class="dot"></span><b>تعویض شمعها</b><span>زمان تقریبی: زمستان ۱۴۰۴</span></div>
      </div></div>
    <div class="panel panel-pad"><h3 class="h3" style="margin-bottom:14px">چطور کار می‌کند؟</h3>
      <div class="prose"><p>پس از انتخاب خودرو، یک کوکی سبک روی مرورگر ذخیره می‌شود. فیلتر «سازگار با خودروی من» در فروشگاه، جدول سازگاری صفحه محصول و هشدار سبد خرید، همگی از همان داده استفاده می‌کنند.</p>
        <blockquote>یک منبع حقیقت، سه جای مصرف — بدون دوباره‌کاری.</blockquote>
        <a class="btn btn-outline btn-sm" href="guide.html">راهنمای خرید{ic("arrow-l")}</a></div></div>
  </div>
</div>'''
    return ("خودروی من", body, "گاراژ خودرو و بررسی سازگاری قطعات پارتیاب", "garage", hero)


def page_compare() -> tuple[str, str, str, str, str]:
    hero = page_hero("مقایسه محصولات", "تا چهار قطعه را کنار هم بگذار و تفاوت‌های مهم را با هایلایت ببین.",
                     "تصمیم آگاهانه", [("خانه", "index.html"), ("مقایسه", None)], [("۳", "کالا"), ("۹", "ویژگی")])
    ps = [BY_ID[k] for k in ("brake", "disc", "oil")]
    heads = "".join(f'''<th class="c-head"><div class="c-media">{art(p["art"], "c-art")}</div>
      <b>{p["short"]}</b><span class="badge badge-outline">{p["brand"]}</span>
      <div class="row" style="justify-content:center;gap:6px;margin-top:10px">
        <a class="btn btn-primary btn-sm" href="cart.html">خرید</a>
        <button class="btn btn-outline btn-icon btn-sm" data-compare-remove aria-label="حذف">{ic("plus")}</button></div></th>''' for p in ps)
    rows = [
        ("قیمت", [f'<b>{p["price"]}</b> تومان' for p in ps], False),
        ("برند", [p["brand"] for p in ps], True),
        ("کد فنی", [f'<span class="ltr">{p["sku"]}</span>' for p in ps], True),
        ("دسته‌بندی", [p["cat"] for p in ps], True),
        ("سازگار با ۲۰۶", ["<span class=\"yes\">بله</span>"] * 2 + ["<span class=\"no\">خیر</span>"], True),
        ("وضعیت موجودی", ["موجود — ۱۲ عدد", "موجود — ۱۴ عدد", "موجود — ۴۰ عدد"], False),
        ("گارانتی اصالت", ["۲۴ ماه", "۲۴ ماه", "۱۲ ماه"], True),
        ("امتیاز خریداران", [f'<b>{p["rate"]}</b> از ۵' for p in ps], False),
        ("ارسال امروز", ["<span class=\"yes\">بله</span>"] * 3, False),
    ]
    trs = "".join(f'<tr><th>{name}</th>' + "".join(
        f'<td class="{"is-diff" if diff else ""}">{v}</td>' for v in vals) + "</tr>"
        for name, vals, diff in rows)
    body = f'''<div class="container block">
  <div class="row-between" style="margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <div class="row" style="gap:8px;flex-wrap:wrap"><span class="badge badge-navy">۳ کالا</span>
      <span class="text-sm text-muted">تفاوت‌ها با پس‌زمینه نارنجی مشخص شدهاند</span></div>
    <a class="btn btn-outline btn-sm" href="shop.html">{ic("plus")}افزودن کالا</a>
  </div>
  <div class="compare-scroll"><table class="compare-table">
    <thead><tr><th>ویژگی</th>{heads}</tr></thead><tbody>{trs}</tbody></table></div>
  <div class="note-box" style="margin-top:18px">{ic("info")}<span>قیمت‌ها به‌روز و شامل تخفیف‌های فعلیاند. برای قطعات برقی و باتری، پیش از خرید <a href="contact.html"><b>مشاوره فنی</b></a> بگیر.</span></div>
</div>
<div class="container block">{usp()}</div>'''
    return ("مقایسه محصولات", body, "مقایسه مشخصات قطعات یدکی پارتیاب", "compare", hero)


def page_brands() -> tuple[str, str, str, str, str]:
    hero = page_hero("برندهای قطعات", "فقط برندهایی که اصالتشان قابل پیگیری است؛ با فاکتور رسمی و ضمانت کالا.",
                     "فهرست برندها", [("خانه", "index.html"), ("برندها", None)], [("۴۲", "برند"), ("۳۵٬۰۰۰+", "کالا")])
    brands = [
        ("ایساکو", "IS", "۲٬۴۲۰ کالا"), ("والئو", "VA", "۱٬۸۶۰ کالا"), ("بوش", "BO", "۲٬۱۱۰ کالا"),
        ("کروز", "CR", "۱٬۵۴۰ کالا"), ("گیتس", "GA", "۹۸۰ کالا"), ("سرکان", "SE", "۱٬۲۳۰ کالا"),
        ("تکسطار", "TX", "۷۴۰ کالا"), ("کایابا", "KY", "۶۲۰ کالا"), ("صدرا باتری", "SD", "۴۱۰ کالا"),
        ("NGK", "NG", "۸۶۰ کالا"), ("مان", "MN", "۶۹۰ کالا"), ("فیلیپس", "PH", "۵۳۰ کالا"),
    ]
    cards = "".join(f'''<a class="brand-card" href="shop.html"><span class="ic">{abbr}</span>
      <div class="grow"><b>{name}</b><span>{count}</span></div>{ic("chev-l")}</a>''' for name, abbr, count in brands)
    letters = "".join(f'<button class="{"is-active" if i == 0 else ""}">{l}</button>' for i, l in enumerate("همه الف ب پ ت ج چ ح د ر ز س ش ص ع ف ق ک گ م ن و ه ی"))
    body = f'''<div class="container block">
  <div class="letter-bar">{letters}</div>
  <div class="brand-grid">{cards}</div>
  <div class="grid-2 block" style="align-items:center">
    <div class="panel panel-pad"><h3 class="h3">برند مورد نظرت پیدا نشد؟</h3>
      <p class="text-sm text-muted" style="margin-top:8px;line-height:2">شماره فنی یا عکس قطعه را برایمان بفرست؛ اگر در شبکه تأمین باشد، لینک خرید اختصاصی می‌سازیم.</p>
      <a class="btn btn-primary btn-sm" style="margin-top:14px" href="contact.html">درخواست تأمین قطعه{ic("arrow-l")}</a></div>
    <div class="panel panel-pad"><h3 class="h3">چطور اصل بودن را تشخیص دهیم؟</h3>
      <p class="text-sm text-muted" style="margin-top:8px;line-height:2">کد فنی روی جعبه، هولوگرام، فاکتور رسمی و بسته‌بندی سالم. <a href="article.html"><b>راهنمای تشخیص قطعه اصل</b></a> را بخوان.</p>
      <a class="btn btn-outline btn-sm" style="margin-top:14px" href="article.html">مطالعه مقاله{ic("arrow-l")}</a></div>
  </div>
</div>
<div class="container block">{usp()}</div>'''
    return ("برندها", body, "فهرست برندهای معتبر قطعات یدکی در پارتیاب", "brands", hero)


POSTS = [
    dict(icon="brake", cat="سیستم ترمز", title="چه زمانی لنت ترمز را عوض کنیم؟", date="۱۴۰۴/۰۸/۰۱", read="۶ دقیقه",
         excerpt="صدای سوت، لرزش پدال و کاهش ضخامت لنت، سه نشانهایاند که نباید نادیده بگیری. در این راهنما بازه تعویض و نکات ایمنی را مرور می‌کنیم."),
    dict(icon="oil", cat="سرویس دوره‌ای", title="برنامه سرویس دوره‌ای پژو ۲۰۶", date="۱۴۰۴/۰۷/۲۴", read="۹ دقیقه",
         excerpt="از روغن و فیلترها تا تسمه تایم و شمع؛ جدول کامل کیلومتر و زمان هر سرویس برای مدل‌های رایج ایران‌خودرو."),
    dict(icon="verified", cat="راهنمای خرید", title="تشخیص قطعه اصل از طرح اصلی", date="۱۴۰۴/۰۷/۱۲", read="۷ دقیقه",
         excerpt="کد فنی، هولوگرام، بسته‌بندی و فاکتور رسمی؛ چهار چیزی که پیش از پرداخت باید بررسی کنی تا خیالت راحت باشد."),
    dict(icon="electric", cat="برقی و الکترونیک", title="علائم خرابی کویل احتراق", date="۱۴۰۴/۰۶/۲۸", read="۵ دقیقه",
         excerpt="ریپ زدن موتور، مصرف سوخت بالا و روشن شدن چراغ چک؛ نشانه‌هایی که از کویل می‌آیند و روش تست ساده‌اشان."),
    dict(icon="shock", cat="جلوبندی", title="کمک فنر فرسوده را چگونه بشناسیم؟", date="۱۴۰۴/۰۶/۱۰", read="۸ دقیقه",
         excerpt="کشیده شدن فرمان، لاستیک‌سایی و پرش خودرو در دست‌اندازها؛ همراه با نکات تعویض جفتی کمک‌فنرها."),
    dict(icon="card", cat="خرید و پرداخت", title="راهنمای پرداخت اقساطی قطعات", date="۱۴۰۴/۰۵/۲۲", read="۴ دقیقه",
         excerpt="چهار قسط بدون بهره، بدون ضامن و بدون چک؛ شرایط، سقف مبلغ و مراحل فعال‌سازی در تسویه حساب."),
]


def post_grid(posts: list[dict]) -> str:
    return '<div class="post-grid">' + "".join(f'''<article class="post-card" data-reveal style="--d:{i * .06}s">
  <a class="post-art" href="article.html">{ic(p["icon"])}</a>
  <div class="post-body"><div class="post-meta"><span class="badge badge-soft-orange">{p["cat"]}</span>
    <span>{p["date"]}</span><span>· {p["read"]}</span></div>
    <h3><a href="article.html">{p["title"]}</a></h3><p>{p["excerpt"]}</p>
    <a class="link-more" href="article.html">ادامه مقاله{ic("chev-l")}</a></div>
</article>''' for i, p in enumerate(posts)) + "</div>"


def page_blog() -> tuple[str, str, str, str, str]:
    hero = page_hero("دانشنامه فنی پارتیاب", "راهنماهای نگهداری، تشخیص خرابی و انتخاب قطعه — نوشته کارشناسان فنی، به زبان ساده.",
                     "مقالات و راهنماها", [("خانه", "index.html"), ("دانشنامه", None)], [("۴۸", "مقاله"), ("۶", "دسته")])
    body = f'''<div class="container block">
  <div class="row" style="gap:8px;flex-wrap:wrap;margin-bottom:18px">
    <button class="chip is-active">همه</button><button class="chip">سیستم ترمز</button>
    <button class="chip">سرویس دوره‌ای</button><button class="chip">موتور</button>
    <button class="chip">برقی</button><button class="chip">راهنمای خرید</button></div>
  {post_grid(POSTS)}
  <div style="margin-top:22px">{pager()}</div>
</div>
<div class="container block">
  <div class="panel panel-pad row" style="gap:18px;flex-wrap:wrap">
    <span class="ic" style="width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:var(--orange-50);color:var(--orange-600)">{ic("bell")}</span>
    <div class="grow"><b>خبرنامه فنی</b><div class="text-sm text-muted">هر هفته یک راهنمای کوتاه و تخفیفهای قطعات مصرفی — بدون ایمیل اضافه.</div></div>
    <form class="row" style="gap:8px" data-demo-form="ایمیل شما برای خبرنامه ثبت شد" onsubmit="return false">
      <input class="input" type="email" placeholder="ایمیل شما" style="width:220px"><button class="btn btn-primary">عضویت</button></form>
  </div>
</div>'''
    return ("دانشنامه فنی", body, "مقالات فنی و راهنمای نگهداری خودرو پارتیاب", "blog", hero)


def page_article() -> tuple[str, str, str, str, str]:
    toc = ["زمان استاندارد تعویض", "نشانه‌های فرسودگی", "انتخاب لنت مناسب", "نکات پس از تعویض", "پرسش‌های رایج"]
    body = f'''<div class="container block-sm">{crumbs([("خانه", "index.html"), ("دانشنامه", "blog.html"), ("سیستم ترمز", "blog.html"), ("چه زمانی لنت ترمز را عوض کنیم؟", None)])}</div>
<div class="container block">
  <div class="article-layout">
    <article>
      <div class="article-hero">{ic("brake")}</div>
      <div class="row" style="gap:8px;flex-wrap:wrap;margin-top:18px">
        <span class="badge badge-soft-orange">سیستم ترمز</span><span class="text-sm text-muted">۱۴۰۴/۰۸/۰۱ · مطالعه ۶ دقیقه</span>
        <span class="badge badge-outline">بازبینی فنی: تیم پارتیاب</span></div>
      <h1 class="h1" style="margin-top:12px">چه زمانی لنت ترمز را عوض کنیم؟</h1>
      <p class="lead" style="margin-top:10px">لنت ترمز یکی از معدود قطعاتی است که فرسودگی‌اش مستقیم با ایمنی سرنشینان گره خورده. در این راهنما، بازه استاندارد، نشانه‌ها و نکات انتخاب لنت مناسب خودرو‌های رایج ایران را مرور می‌کنیم.</p>
      <div class="panel panel-pad prose" style="margin-top:22px" id="article-body">
        <h2>۱. زمان استاندارد تعویض</h2>
        <p>برای رانندگی شهری در ایران، لنت جلو معمولاً بین <b>۲۵ تا ۴۰ هزار کیلومتر</b> و لنت عقب بین ۴۰ تا ۶۰ هزار کیلومتر نیاز به تعویض پیدا می‌کند. ترافیک سنگین، ترمزگیری‌های پیاپی و بار زیاد، این بازه را کوتاه می‌کند.</p>
        <blockquote>قاعده ساده: ضخامت لنت کمتر از ۳ میلی‌متر یعنی وقت تعویض رسیده، حتی اگر کیلومترش نرسیده باشد.</blockquote>
        <h2>۲. نشانه‌های فرسودگی</h2>
        <ul><li>سوت یا جیغ فلزی هنگام ترمز گرفتن (صدای سنسور فرسودگی)</li>
          <li>لرزش فرمان یا پدال در ترمزگیری‌های شدید</li>
          <li>افزایش مسافت توقف یا پایین رفتن بیش از حد پدال</li>
          <li>بوی سوختگی پس از ترمزگیری طولانی در سرازیری</li>
          <li>روشن شدن چراغ هشدار ترمز در خودرو‌های مجهز به سنسور</li></ul>
        <h2>۳. انتخاب لنت مناسب</h2>
        <p>پیش از خرید، <b>برند خودرو، مدل، تیپ و سال ساخت</b> را مشخص کن. لنت پژو ۲۰۶ تیپ ۲ با نمونه ۴۰۵ یا پراید یکسان نیست و حتی در برخی تیپ‌ها سیستم ترمز متفاوت است. در پارتیاب، جدول سازگاری هر محصول دقیقاً همین تطبیق را انجام می‌دهد.</p>
        <p>لنت‌های نیمه‌فلزی دوام بیشتری دارند و برای رانندگی شهری گزینه متعادلیاند؛ لنت‌های سرامیکی صدای کمتر و گرد کمتری تولید می‌کنند، ولی قیمت بالاتری دارند.</p>
        <div class="alert" style="margin-block:18px">{ic("info")}<span>حتماً لنت‌های یک محور را <b>جفتی</b> تعویض کن؛ تعویض تکی باعث کشیده شدن فرمان و سایش نامتقارن دیسک می‌شود.</span></div>
        <h2>۴. نکات پس از تعویض</h2>
        <p>تا ۲۰۰ کیلومتر اول از ترمزگیری‌های ناگهانی پرهیز کن تا لنت به سطح دیسک بنشیند (آب‌بندی). سطح روغن ترمز را هم بررسی کن؛ پایین آمدن مخزن می‌تواند نشان‌دهنده نشتی یا فرسودگی لنت باشد.</p>
        <h2>۵. پرسش‌های رایج</h2>
        <p>اگر دیسک خط افتاده باشد، تعویض لنت به‌تنهایی کافی نیست و باید دیسک تراش یا تعویض شود. برای خودروی خودت، <a href="garage.html"><b>صفحه گاراژ</b></a> قطعات دقیقاً سازگار را پیشنهاد می‌دهد.</p>
      </div>
      <div class="panel panel-pad block-sm">
        <h3 class="h3" style="margin-bottom:10px">قطعات پیشنهادی این مقاله</h3>
        {rail([BY_ID[k] for k in ("brake", "disc", "pump", "filter")])}
      </div>
    </article>
    <aside>
      <div class="panel panel-pad" style="position:sticky;top:132px">
        <b style="font-size:13px">فهرست مطلب</b>
        <div class="toc-list" style="margin-top:10px">{"".join(f'<a href="#article-body">{t}</a>' for t in toc)}</div>
        <div class="divider"></div>
        <b style="font-size:12.5px">مطمئن نیستی کدام لنت مناسب است؟</b>
        <p class="text-xs text-muted" style="margin-top:6px;line-height:1.9">کارشناسان فنی پارتیاب رایگان راهنمایی می‌کنند.</p>
        <a class="btn btn-outline btn-sm btn-block" style="margin-top:10px" href="contact.html">{ic("headset")}مشاوره فنی</a>
      </div>
    </aside>
  </div>
</div>
<div class="container block">
  {section_head("مقالات مرتبط", "", "blog.html")}{post_grid(POSTS[2:5])}
</div>'''
    return ("چه زمانی لنت ترمز را عوض کنیم؟", body, "راهنمای تشخیص زمان تعویض لنت ترمز", "article", "")


CONTENT_SIDE = f'''<aside>
  <div class="panel panel-pad" style="position:sticky;top:132px">
    <b style="font-size:13px">نیاز به راهنمایی داری؟</b>
    <p class="text-sm text-muted" style="margin-top:8px;line-height:2">کارشناسان فنی پارتیاب هر روز ۹ تا ۱۸ پاسخگوی پرسش‌های مربوط به قطعه و سازگاری خودرو هستند.</p>
    <a class="btn btn-primary btn-sm btn-block" style="margin-top:12px" href="contact.html">{ic("headset")}تماس با کارشناس</a>
    <a class="btn btn-outline btn-sm btn-block" style="margin-top:8px" href="faq.html">{ic("info")}سوالات متداول</a>
    <div class="divider"></div>
    <div class="kpi" style="border:0;padding:0"><span class="ic">{ic("clock")}</span><div><b>۹ تا ۱۸</b><span>شنبه تا پنجشنبه</span></div></div>
  </div>
</aside>'''


def page_about() -> tuple[str, str, str, str, str]:
    hero = page_hero("درباره پارتیاب", "از یک مغازه کوچک در خیابان امیرکبیر تا فروشگاه تخصصی آنلاین قطعات یدکی؛ هدفمان همیشه یکی بوده: قطعه‌ای درست، همان بار اول.",
                     "داستان ما", [("خانه", "index.html"), ("درباره ما", None)],
                     [("۱۳۹۶", "سال تأسیس"), ("۳۵٬۰۰۰+", "قطعه")])
    body = f'''<div class="container block">
  <div class="content-layout">
    <div class="panel panel-pad">
      <div class="prose">
        <h2>چرا پارتیاب شروع شد؟</h2>
        <p>خرید قطعه یدکی در ایران همیشه با یک نگرانی همراه بوده: «آیا این قطعه واقعاً به ماشین من می‌خورد؟» پارتیاب از همین سوال شروع شد. تیمی از مکانیک‌ها و توسعه‌دهندگان کنار هم نشستند تا مدل داده‌ای سازگاری قطعه با خودرو را بسازند و آن را به تجربه خرید سادهای تبدیل کنند.</p>
        <p>امروز هر محصول در پارتیاب با برند، شماره فنی، OEM، بازه سال ساخت و تیپ موتور مشخص می‌شود. یکبار خودرویت را انتخاب می‌کنی و در فروشگاه، صفحه محصول و حتی سبد خرید، همان داده دنبالَت می‌آید.</p>
        <h2>آنچه به آن پایبندیم</h2>
        <ul><li><b>اصالت، نه فقط ادعا:</b> هر کالا با فاکتور رسمی و قابلیت پیگیری تأمین‌کننده عرضه می‌شود.</li>
          <li><b>تخصص قبل از فروش:</b> مشاوره فنی رایگان، حتی اگر در نهایت از ما نخری.</li>
          <li><b>شفافیت:</b> قیمت نهایی، موجودی واقعی انبار و وضعیت سازگاری، پیش از پرداخت.</li>
          <li><b>پشتیبانی پس از خرید:</b> ۷ روز بازگشت در صورت عدم سازگاری قطعه.</li></ul>
        <blockquote>اگر قطعه‌ای با نشان «سازگار» به خودرویت نخورد، تقصیر ماست — نه تو.</blockquote>
        <h2>مسیر ما</h2>
      </div>
      <div class="timeline" style="margin-top:6px">
        <div class="timeline-item is-done"><span class="dot"></span><b>۱۳۹۶ — آغاز فروش حضوری</b><span>تأمین قطعات تعمیرگاه‌های محلی در تهران</span></div>
        <div class="timeline-item is-done"><span class="dot"></span><b>۱۳۹۹ — ساخت جدول سازگاری</b><span>ثبت ۲۰٬۰۰۰ ترکیب قطعه و خودرو</span></div>
        <div class="timeline-item is-done"><span class="dot"></span><b>۱۴۰۲ — فروشگاه آنلاین</b><span>جستجوی شماره فنی و فیلتر «خودروی من»</span></div>
        <div class="timeline-item is-now"><span class="dot"></span><b>۱۴۰۴ — طراحی مجدد v2</b><span>سیستم طراحی یکپارچه و تجربه خرید سریع‌تر</span></div>
      </div>
    </div>
    {CONTENT_SIDE}
  </div>
</div>
<div class="container block">
  {section_head("ارقامی که برایمان مهماند", "", "")}
  <div class="kpi-grid">{"".join(f'<div class="kpi"><span class="ic">{ic(a)}</span><div><b>{b}</b><span>{c}</span></div></div>' for a, b, c in [
    ("verified", "۱۰۰٪", "ضمانت اصالت"), ("truck", "۱–۳ روز", "ارسال سراسری"),
    ("headset", "رایگان", "مشاوره فنی"), ("refresh", "۷ روز", "بازگشت کالا")])}</div>
</div>
<div class="container block">{svc_grid()}</div>'''
    return ("درباره ما", body, "داستان و ارزشهای فروشگاه تخصصی پارتیاب", "about", hero)


def page_contact() -> tuple[str, str, str, str, str]:
    hero = page_hero("تماس با ما", "سوال فنی داری، شماره قطعه‌ای پیدا نکردی یا پیگیر سفارشی؟ هر روز ۹ تا ۱۸ کنارت هستیم.",
                     "پشتیبانی و مشاوره", [("خانه", "index.html"), ("تماس با ما", None)],
                     [("۰۲۱–۵۵۶۶۷۷۸۸", "تلفن فروش"), ("۹ تا ۱۸", "پاسخگویی")])
    channels = [
        ("phone", "تلفن فروش و پشتیبانی", "۰۲۱–۵۵۶۶۷۷۸۸", "شنبه تا پنجشنبه ۹ تا ۱۸"),
        ("whatsapp", "واتس‌اپ", "۰۹۱۲۰۰۰۰۰۰۰", "ارسال عکس قطعه یا کد فنی"),
        ("mail", "ایمیل", "info@part-yab.ir", "پاسخ حداکثر تا یک روز کاری"),
        ("pin", "نشانی فروشگاه", "تهران، امیرکبیر، پاساژ کاشانی، پلاک ۲۱۱", "بازدید حضوری با هماهنگی قبلی"),
    ]
    ch = "".join(f'''<div class="contact-channel"><span class="ic">{ic(a)}</span>
      <div class="grow"><b>{b}</b><span class="ltr" style="display:block">{c}</span><span>{d}</span></div></div>'''
        for a, b, c, d in channels)
    body = f'''<div class="container block">
  <div class="content-layout">
    <div>
      <div class="contact-grid">{ch}</div>
      <div class="panel panel-pad block-sm">
        <h3 class="h3" style="margin-bottom:6px">ارسال پیام</h3>
        <p class="text-sm text-muted" style="margin-bottom:16px">پیامهای فنی را مستقیم به تیم کارشناسی می‌رسانیم.</p>
        <form class="form-grid" data-demo-form="پیام شما ثبت شد — حداکثر تا یک روز کاری پاسخ می‌دهیم" onsubmit="return false">
          <label class="field"><span class="field-label">نام و نام خانوادگی</span><input class="input" placeholder="مثلاً علی محمدی" required></label>
          <label class="field"><span class="field-label">شماره تماس یا ایمیل</span><input class="input" placeholder="۰۹۱۲۳۴۵۶۷۸۹" required></label>
          <label class="field full"><span class="field-label">موضوع</span><select class="select"><option>مشاوره فنی و انتخاب قطعه</option><option>پیگیری سفارش</option><option>درخواست تأمین قطعه</option><option>گارانتی و مرجوعی</option><option>همکاری و فروش عمده</option></select></label>
          <label class="field full"><span class="field-label">متن پیام</span><textarea class="textarea" placeholder="مدل و سال خودرو، شماره فنی یا توضیح مشکل را بنویس…"></textarea></label>
          <div class="full row" style="gap:10px;flex-wrap:wrap"><button class="btn btn-primary btn-lg" type="submit">ارسال پیام{ic("arrow-l")}</button>
            <label class="check"><input type="checkbox" checked> پیامکی از نتیجه پیگیری دریافت کنم</label></div>
        </form>
      </div>
      <div class="panel block-sm" style="overflow:hidden">
        <div class="row" style="gap:16px;padding:20px;flex-wrap:wrap">
          <span class="ic" style="width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:var(--navy-50);color:var(--navy-700)">{ic("pin")}</span>
          <div class="grow"><b>فروشگاه مرکزی</b><p class="text-sm text-muted">تهران، خیابان امیرکبیر، پاساژ کاشانی، پلاک ۲۱۱ — نزدیک چهارراه استانبول</p></div>
          <a class="btn btn-outline btn-sm" href="about.html">درباره ما{ic("chev-l")}</a></div>
        <div style="height:180px;background:radial-gradient(80% 120% at 85% 10%,#1D4074,#0A1B33 70%);position:relative;display:grid;place-items:center;color:#FFB37A">
          <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.06) 1px,transparent 1px);background-size:40px 40px"></div>
          <span style="z-index:1;display:grid;place-items:center;gap:6px">{ic("pin", "")}<b style="font-size:12px;color:#D9E5F5">پارتیاب · امیرکبیر</b></span>
        </div>
      </div>
    </div>
    {CONTENT_SIDE}
  </div>
</div>
<div class="container block">{usp()}</div>'''
    return ("تماس با ما", body, "راههای ارتباط با پارتیاب و فرم ارسال پیام", "contact", hero)


FAQ_ITEMS = [
    ("چطور مطمئن شوم قطعه با خودروی من سازگار است؟", "در صفحه هر قطعه، تب «سازگاری با خودرو» فهرست کامل برند، مدل و بازه سال را نشان می‌دهد. اگر خودرویت را در گاراژ انتخاب کرده باشی، ردیف مربوط به تو با رنگ سبز و نشان «خودروی شما» مشخص می‌شود."),
    ("اگر قطعه سازگار نبود چه کار کنم؟", "تا ۷ روز پس از دریافت، به شرط استفاده‌نشدن کالا و سالم بودن بسته‌بندی، مرجوعی پذیرفته می‌شود. برای قطعات برقی و باتری، تماس با پشتیبانی پیش از ارسال لازم است."),
    ("هزینه و زمان ارسال چقدر است؟", "ارسال در تهران ۲۴ ساعته و در شهرستانها ۱ تا ۳ روز کاری است. سفارش‌های بالای ۵ میلیون تومان ارسال رایگان دارند؛ باقی سفارش‌ها بر اساس وزن و مقصد محاسبه می‌شود."),
    ("امکان پرداخت در محل وجود دارد؟", "در تهران برای سفارش‌های زیر ۵ میلیون تومان پرداخت در محل فعال است. برای شهرستانها پرداخت آنلاین یا کارت‌به‌کارت پیش از ارسال انجام می‌شود."),
    ("ضمانت اصالت کالا یعنی چه؟", "هر قطعه با برند و کد فنی مشخص از تأمین‌کننده معتبر خریداری می‌شود. در صورت اثبات غیراصل بودن، مبلغ کامل بازگردانده و قطعه جایگزین می‌شود."),
    ("فاکتور رسمی صادر می‌شود؟", "بله. در مرحله تسویه، گزینه «فاکتور رسمی» را انتخاب کن و اطلاعات حقوقی را وارد کن. فاکتور همراه سفارش ارسال می‌شود."),
    ("سفارش را چطور پیگیری کنم؟", "از بخش «حساب کاربری ← سفارش‌ها» یا صفحه «پیگیری سفارش» وضعیت لحظه‌ای و کد رهگیری مرسوله را می‌بینی. پیامک اطلاع‌رسانی هم در هر تغییر وضعیت ارسال می‌شود."),
    ("اگر قطعه‌ای در سایت موجود نبود؟", "شماره یا کد فنی قطعه را از طریق واتس‌اپ یا فرم تماس برای ما بفرست؛ در صورت موجود بودن در شبکه تأمین، لینک خرید اختصاصی می‌سازیم."),
]


def page_faq() -> tuple[str, str, str, str, str]:
    hero = page_hero("سوالات متداول", "پاسخ کوتاه و دقیق به پرسش‌هایی که بیشتر از همه پرسیده می‌شوند.",
                     "راهنمای مشتریان", [("خانه", "index.html"), ("سوالات متداول", None)], [("۸", "پرسش"), ("۹ تا ۱۸", "پشتیبانی")])
    items = "".join(f'''<div class="faq-item{" is-open" if i == 0 else ""}">
      <button class="faq-q" type="button"><span>{q}</span>{ic("chev-d")}</button>
      <div class="faq-a"><p>{a}</p></div></div>''' for i, (q, a) in enumerate(FAQ_ITEMS))
    body = f'''<div class="container block">
  <div class="content-layout">
    <div class="panel" style="padding:6px 20px">
      <div class="row" style="gap:8px;flex-wrap:wrap;padding-block:14px">
        <button class="chip is-active">همه</button><button class="chip">خرید و پرداخت</button>
        <button class="chip">ارسال</button><button class="chip">سازگاری</button><button class="chip">گارانتی</button></div>
      {items}
    </div>
    {CONTENT_SIDE}
  </div>
</div>
<div class="container block">
  <div class="panel panel-pad center"><h3 class="h3">پاسخت را پیدا نکردی؟</h3>
    <p class="text-sm text-muted" style="margin-top:8px">مستقیم با کارشناس فنی صحبت کن؛ مشاوره رایگان است.</p>
    <div class="row" style="gap:10px;justify-content:center;flex-wrap:wrap;margin-top:16px">
      <a class="btn btn-primary" href="contact.html">{ic("headset")}تماس با پشتیبانی</a>
      <a class="btn btn-outline" href="guide.html">راهنمای خرید{ic("arrow-l")}</a></div></div>
</div>'''
    return ("سوالات متداول", body, "پاسخ پرسشهای پرتکرار مشتریان پارتیاب", "faq", hero)


def page_warranty() -> tuple[str, str, str, str, str]:
    hero = page_hero("گارانتی، مرجوعی و اصالت", "هر قطعه‌ای که بخری، پشتش ضمانت اصالت و ۷ روز فرصت بازگشت هست — بدون پرسش‌های اضافی.",
                     "خیال راحت", [("خانه", "index.html"), ("گارانتی و مرجوعی", None)], [("۲۴ ماه", "ضمانت اصالت"), ("۷ روز", "بازگشت")])
    body = f'''<div class="container block">
  <div class="steps" style="margin-bottom:24px">
    <div class="step is-done"><span class="n">۱</span><b>ثبت درخواست</b><span class="line"></span></div>
    <div class="step is-now"><span class="n">۲</span><b>بررسی کارشناس</b><span class="line"></span></div>
    <div class="step"><span class="n">۳</span><b>تحویل کالا</b><span class="line"></span></div>
    <div class="step"><span class="n">۴</span><b>بازگشت وجه</b></div>
  </div>
  <div class="content-layout">
    <div class="panel panel-pad">
      <div class="prose">
        <h2>ضمانت اصالت کالا</h2>
        <p>تمام محصولات پارتیاب از تأمین‌کنندگان معتبر و با فاکتور رسمی تهیه می‌شوند. برند، شماره فنی (SKU) و در صورت وجود شماره OEM روی صفحه محصول درج شده است تا بتوانی کالای دریافتی را دقیقاً مطابقت دهی.</p>
        <blockquote>اگر غیراصل بودن کالایی اثبات شود، مبلغ کامل بازگردانده و یک قطعه جایگزین بدون هزینه ارسال می‌کنیم.</blockquote>
        <h2>شرایط مرجوعی ۷ روزه</h2>
        <ul><li>حداکثر ۷ روز کاری از تاریخ تحویل فرصت داری درخواست مرجوعی ثبت کنی.</li>
          <li>کالا استفاده نشده، نصب نشده و بسته‌بندی‌اش سالم باشد.</li>
          <li>فاکتور و تمام متعلقات داخل جعبه همراه کالا برگردانده شود.</li>
          <li>قطعات برقی، باتری و روغنها فقط با هماهنگی قبلی پشتیبانی پذیرفته می‌شوند.</li></ul>
        <h2>خطای تطبیق قطعه</h2>
        <p>اگر قطعه‌ای با نشان «سازگار» خریدهای و به خودرویت نخورد، هزینه ارسال رفت و برگشت با ماست. کافی است مدل، سال ساخت و عکس قطعه را در <a href="contact.html"><b>فرم تماس</b></a> بفرستی.</p>
        <h2>زمان بازگشت وجه</h2>
        <p>پس از دریافت و تأیید کالا در انبار، وجه حداکثر ظرف ۴۸ ساعت به همان روش پرداخت برگردانده می‌شود. در پرداخت‌های اقساطی، اقساط باقی‌مانده لغو می‌شوند.</p>
      </div>
    </div>
    <aside><div class="panel panel-pad" style="position:sticky;top:132px">
      <b style="font-size:13px">مدارک لازم</b>
      <div class="timeline" style="margin-top:14px">
        <div class="timeline-item is-done"><span class="dot"></span><b>شماره سفارش</b><span>در حساب کاربری یا پیامک</span></div>
        <div class="timeline-item is-done"><span class="dot"></span><b>فاکتور همراه کالا</b><span>برچسب کد فنی روی جعبه</span></div>
        <div class="timeline-item is-now"><span class="dot"></span><b>عکس کالا و بسته‌بندی</b><span>پیش از ارسال، از طریق واتس‌اپ</span></div>
      </div>
      <div class="divider"></div>
      <a class="btn btn-primary btn-sm btn-block" href="contact.html">{ic("headset")}ثبت درخواست مرجوعی</a>
      <a class="btn btn-outline btn-sm btn-block" style="margin-top:8px" href="faq.html">سوالات متداول</a>
    </div></aside>
  </div>
</div>
<div class="container block">{usp()}</div>'''
    return ("گارانتی و مرجوعی", body, "شرایط ضمانت اصالت و بازگشت کالای پارتیاب", "warranty", hero)


def page_guide() -> tuple[str, str, str, str, str]:
    hero = page_hero("راهنمای خرید قطعه", "چهار قدم تا خرید مطمئن: خودرو، قطعه، تطبیق و پرداخت. همه‌اش در کمتر از دو دقیقه.",
                     "از انتخاب تا تحویل", [("خانه", "index.html"), ("راهنمای خرید", None)], [("۴", "قدم"), ("۲ دقیقه", "زمان لازم")])
    steps = [
        ("car", "خودرویت را انتخاب کن", "برند، مدل و سال ساخت را در «خودروی من» وارد کن. از این پس سایت فقط قطعات سازگار را پیشنهاد می‌دهد."),
        ("search", "قطعه را پیدا کن", "با نام قطعه، برند یا شماره فنی جستجو کن. ارقام فارسی و لاتین و حتی نوشتن بدون خط تیره هم نتیجه می‌دهد."),
        ("verified", "سازگاری و اصالت را بررسی کن", "تب «سازگاری خودرو» و مشخصات فنی صفحه محصول را بخوان؛ نشان «اصل / OEM» و فاکتور رسمی خیالت را راحت می‌کند."),
        ("cart", "سفارش را نهایی کن", "سبد، تسویه تک‌صفح‌ای و پرداخت امن. وضعیت سفارش و کد رهگیری در حساب کاربری و پیامک اطلاع‌رسانی می‌شود."),
    ]
    cards = "".join(f'''<div class="svc" data-reveal style="--d:{i * .06}s;align-items:flex-start">
      <span class="ic">{ic(a)}</span><div><b>{i + 1}. {b}</b><span style="line-height:1.9;display:block;margin-top:4px">{c}</span></div></div>'''
        for i, (a, b, c) in enumerate(steps))
    body = f'''<div class="container block">
  <div class="panel panel-pad"><h2 class="h2" style="margin-bottom:16px">چهار قدم تا قطعه درست</h2>
    <div class="svc-grid svc-2">{cards}</div></div>
  <div class="grid-2 block" style="align-items:start">
    <div class="panel panel-pad prose"><h2>جستجو با شماره فنی</h2>
      <p>هر قطعه‌ای یک شماره فنی (SKU) دارد؛ مثلاً <span class="ltr">4254-108</span> برای لنت ترمز جلو ۲۰۶. اگر شماره را از روی قطعه قدیمی خواندهای، مستقیم در نوار جستجوی بالا وارد کن.</p>
      <ul><li>ارقام فارسی و لاتین یکسان پردازش می‌شوند</li><li>خط تیره، فاصله و کشیدن حروف تأثیری ندارد</li><li>شماره‌های جایگزین (OEM / Alt) هم نتیجه می‌دهند</li></ul>
      <a class="btn btn-outline btn-sm" style="margin-top:10px" href="search.html">امتحان جستجو{ic("arrow-l")}</a></div>
    <div class="panel panel-pad prose"><h2>اگر دو قطعه شبیه هم بودند</h2>
      <p>از <a href="compare.html"><b>صفحه مقایسه</b></a> استفاده کن. تفاوت برند، کد فنی، سازگاری، گارانتی و موجودی با رنگ مشخص می‌شود تا فقط روی چیزی که مهم است تصمیم بگیری.</p>
      <blockquote>نمیدانی کدام تیپ موتور داری؟ با کارشناسان ما تماس بگیر؛ رایگان کمک می‌کنند.</blockquote>
      <a class="btn btn-outline btn-sm" href="compare.html">مقایسه محصولات{ic("arrow-l")}</a></div>
  </div>
  <div class="note-box">{ic("info")}<span>اولین خریدی؟ <a href="faq.html"><b>سوالات متداول</b></a> و <a href="warranty.html"><b>شرایط بازگشت</b></a> را یک بار مرور کن.</span></div>
</div>
<div class="container block">
  {section_head("پیشنهاد شروع", "قطعات پرمصرف سرویس دوره‌ای", "shop.html")}
  {pc_grid([BY_ID[k] for k in ("oil", "filter", "spark", "brake")], cols=4)}
</div>'''
    return ("راهنمای خرید", body, "راهنمای گامبهگام خرید قطعات یدکی از پارتیاب", "guide", hero)


def page_track() -> tuple[str, str, str, str, str]:
    hero = page_hero("پیگیری سفارش", "شماره سفارش یا کد رهگیری را وارد کن تا آخرین وضعیت مرسولهات را ببینی.",
                     "وضعیت لحظه‌ای", [("خانه", "index.html"), ("پیگیری سفارش", None)], [("#PY-1404-1284", "سفارش نمونه"), ("۱–۳ روز", "ارسال")])
    body = f'''<div class="container block">
  <div class="panel panel-pad" style="margin-bottom:20px">
    <form class="row" style="gap:10px;flex-wrap:wrap" data-demo-form="وضعیت سفارش بهروز شد" onsubmit="return false">
      <label class="field grow" style="min-width:220px"><span class="field-label">شماره سفارش</span>
        <input class="input" value="PY-1404-1284" placeholder="مثلاً PY-1404-1284"></label>
      <label class="field grow" style="min-width:180px"><span class="field-label">شماره تماس</span>
        <input class="input" value="۰۹۱۲۳۴۵۶۷۸۹"></label>
      <button class="btn btn-primary" type="submit" style="height:44px">پیگیری{ic("search")}</button>
    </form>
  </div>
  <div class="grid-2 split-6040">
    <div class="panel panel-pad track-card">
      <div class="row-between" style="flex-wrap:wrap;gap:10px"><div><b style="font-size:15px">سفارش #PY-1404-1284</b>
        <div class="text-sm text-muted">ثبتشده در ۱۴۰۴/۰۸/۰۲ · ۳ کالا · ۲٬۱۷۰٬۰۰۰ تومان</div></div>
        <span class="badge badge-warn">در حال آماده‌سازی</span></div>
      <div class="divider"></div>
      <div class="steps">
        <div class="step is-done"><span class="n">{ic("check")}</span><b>ثبت سفارش</b><span class="line"></span></div>
        <div class="step is-done"><span class="n">{ic("check")}</span><b>تأیید پرداخت</b><span class="line"></span></div>
        <div class="step is-now"><span class="n">۳</span><b>آماده‌سازی</b><span class="line"></span></div>
        <div class="step"><span class="n">۴</span><b>تحویل به پست</b></div>
      </div>
      <div class="divider"></div>
      <div class="timeline">
        <div class="timeline-item is-done"><span class="dot"></span><b>سفارش ثبت شد</b><span>۱۴۰۴/۰۸/۰۲ — ۱۰:۲۴</span></div>
        <div class="timeline-item is-done"><span class="dot"></span><b>پرداخت تأیید شد</b><span>۱۴۰۴/۰۸/۰۲ — ۱۰:۲۶ · درگاه شاپرک</span></div>
        <div class="timeline-item is-now"><span class="dot"></span><b>در حال بسته‌بندی</b><span>انبار مرکزی تهران · امروز تا ساعت ۱۶</span></div>
        <div class="timeline-item"><span class="dot"></span><b>تحویل به شرکت حمل</b><span>پیش‌بینی: فردا صبح</span></div>
        <div class="timeline-item"><span class="dot"></span><b>تحویل به مشتری</b><span>پیش‌بینی: ۱ تا ۳ روز کاری</span></div>
      </div>
    </div>
    <aside class="panel panel-pad">
      <h3 class="h3" style="margin-bottom:12px">اقلام سفارش</h3>
      {"".join(f'<div class="row" style="gap:10px;margin-bottom:12px"><span class="c-media" style="width:54px;height:54px;border-radius:10px;display:grid;place-items:center;background:var(--bg);flex:none">{art(BY_ID[k]["art"], "c-art")}</span><div class="grow"><b style="font-size:12px">{BY_ID[k]["short"]}</b><div class="text-xs text-muted">تعداد {q}</div></div></div>' for k, q in [("brake", "۱"), ("oil", "۲"), ("filter", "۱")])}
      <div class="divider"></div>
      <div class="totals"><div><span>روش ارسال</span><b>استاندارد</b></div>
        <div><span>کد رهگیری</span><b class="ltr">—</b></div>
        <div><span>گیرنده</span><b>علی محمدی</b></div></div>
      <a class="btn btn-outline btn-sm btn-block" style="margin-top:14px" href="contact.html">{ic("headset")}سوال درباره سفارش</a>
    </aside>
  </div>
</div>'''
    return ("پیگیری سفارش", body, "پیگیری لحظه‌ای وضعیت سفارش‌های پارتیاب", "track", hero)


def page_thankyou() -> tuple[str, str, str, str, str]:
    hero = page_hero("سفارش شما ثبت شد", "پرداخت با موفقیت انجام شد. از این لحظه می‌توانی وضعیت سفارش را مرحله به‌مرحله دنبال کنی.",
                     "مرحله ۳ از ۳", [("خانه", "index.html"), ("سبد خرید", "cart.html"), ("تأیید سفارش", None)],
                     [("#PY-1404-1284", "شماره سفارش"), ("۲٬۱۷۰٬۰۰۰", "تومان پرداختشده")])
    body = f'''<div class="container block">
  <div class="panel success-card">
    <span class="ic">{ic("check")}</span>
    <h2 class="h1">ممنون، علی! سفارشت ثبت شد 🎉</h2>
    <p>رسید پرداخت و خلاصه سفارش به شماره ۰۹۱۲۳۴۵۶۷۸۹ پیامک شد.<br>همکاران انبار تا ساعت ۱۶ امروز بسته‌بندی را انجام می‌دهند.</p>
    <div class="row" style="justify-content:center;gap:10px;flex-wrap:wrap;margin-top:18px">
      <span class="badge badge-navy">شماره سفارش: PY-1404-1284</span>
      <span class="badge badge-success">پرداخت موفق</span></div>
    <div class="btn-row">
      <a class="btn btn-primary btn-lg" href="track.html">پیگیری سفارش{ic("arrow-l")}</a>
      <a class="btn btn-outline btn-lg" href="shop.html">ادامه خرید</a></div>
  </div>
  <div class="panel panel-pad block-sm">
    <div class="steps">
      <div class="step is-done"><span class="n">{ic("check")}</span><b>ثبت سفارش</b><span class="line"></span></div>
      <div class="step is-done"><span class="n">{ic("check")}</span><b>تأیید پرداخت</b><span class="line"></span></div>
      <div class="step is-now"><span class="n">۳</span><b>آماده‌سازی و بسته‌بندی</b><span class="line"></span></div>
      <div class="step"><span class="n">۴</span><b>تحویل به پست</b></div>
    </div>
  </div>
  <div class="grid-2 block-sm" style="align-items:start">
    <div class="panel panel-pad"><h3 class="h3" style="margin-bottom:12px">جزئیات سفارش</h3>
      <div class="totals"><div><span>تاریخ</span><b>۱۴۰۴/۰۸/۰۲</b></div>
        <div><span>روش پرداخت</span><b>درگاه آنلاین شاپرک</b></div>
        <div><span>روش ارسال</span><b>استاندارد (رایگان)</b></div>
        <div><span>آدرس تحویل</span><b>تهران، امیرکبیر، پلاک ۲۴</b></div>
        <div class="grand"><span>مبلغ پرداختشده</span><b>۲٬۱۷۰٬۰۰۰ تومان</b></div></div></div>
    <div class="panel panel-pad"><h3 class="h3" style="margin-bottom:12px">بعد از این چه می‌شود؟</h3>
      <div class="timeline">
        <div class="timeline-item is-now"><span class="dot"></span><b>امروز تا ۱۶</b><span>بسته‌بندی و صدور فاکتور</span></div>
        <div class="timeline-item"><span class="dot"></span><b>فردا صبح</b><span>تحویل به شرکت حمل و ارسال کد رهگیری</span></div>
        <div class="timeline-item"><span class="dot"></span><b>۱ تا ۳ روز کاری</b><span>تحویل مرسوله و پیامک رضایت‌سنجی</span></div>
      </div></div>
  </div>
</div>
<div class="container block">
  {section_head("دفعه بعد هم همراه تو هستیم", "قطعات مکمل سرویس بعدی", "shop.html")}
  {rail([BY_ID[k] for k in ("disc", "pump", "spark", "belt", "wiper")])}
</div>'''
    return ("سفارش ثبت شد", body, "صفحه تشکر و تأیید سفارش پارتیاب", "thankyou", hero)


def page_search() -> tuple[str, str, str, str, str]:
    hero = page_hero("نتایج جستجو", "نتایج برای عبارت «<span data-query>لنت ترمز ۲۰۶</span>» — شامل نام قطعه، برند و شماره فنی.",
                     "جستجوی هوشمند", [("خانه", "index.html"), ("جستجو", None)], [("۲۳۴", "نتیجه"), ("۰٫۱۲ ثانیه", "زمان جستجو")])
    body = f'''<div class="container block">
  <div class="row" style="gap:8px;flex-wrap:wrap;margin-bottom:18px">
    <span class="text-sm text-muted">جستجوهای مرتبط:</span>
    <a class="chip" href="search.html">لنت ترمز جلو</a><a class="chip" href="search.html">دیسک ترمز ۲۰۶</a>
    <a class="chip" href="search.html">لنت عقب ۲۰۷</a><a class="chip" href="search.html">4254-108</a></div>
  <div class="shop-toolbar" style="margin-bottom:14px">
    <span class="text-sm text-muted">نمایش ۱ تا ۶ از ۲۳۴ نتیجه برای «<b data-query>لنت ترمز ۲۰۶</b>»</span>
    <div class="tabs"><button class="is-active">مرتبط‌ترین</button><button>ارزان‌ترین</button><button>پرفروش‌ترین</button></div>
  </div>
  {pc_grid([BY_ID[k] for k in ("brake", "disc", "filter", "oil", "spark", "pump")], cols=3, data_grid=True)}
  <div style="margin-top:22px">{pager()}</div>
  <div class="panel panel-pad block" style="background:var(--bg)">
    <h3 class="h3">چیزی پیدا نکردی؟</h3>
    <div class="grid-2" style="margin-top:12px">
      <div class="prose"><p>اگر شماره فنی داری، بدون خط تیره و با حروف لاتین امتحان کن. نرمال‌سازی فارسی ارقام و حروف کشیده را یکسان می‌خواند.</p>
        <a class="btn btn-outline btn-sm" href="garage.html">انتخاب خودرو{ic("arrow-l")}</a></div>
      <div class="prose"><p>درخواست تأمین قطعه بفرست؛ عکس یا کد فنی را از طریق واتس‌اپ ارسال کن تا لینک خرید اختصاصی بسازیم.</p>
        <a class="btn btn-outline btn-sm" href="contact.html">درخواست تأمین{ic("arrow-l")}</a></div>
    </div>
  </div>
</div>'''
    return ("نتایج جستجو", body, "نتایج جستجوی قطعات و شماره فنی در پارتیاب", "search", hero)


def page_404() -> tuple[str, str, str, str, str]:
    body = f'''<div class="container err-404">
  <div class="num404">۴۰۴</div>
  <h1>این صفحه پیدا نشد</h1>
  <p>لینک ممکن است قدیمی باشد یا قطعه‌ای که دنبالشی دیگر در فروشگاه نباشد. از جستجو یا گاراژ خودرو شروع کن.</p>
  <form class="row" style="justify-content:center;gap:8px;max-width:520px;margin:22px auto 0" action="search.html" method="get">
    <input class="input grow" name="q" placeholder="نام قطعه یا شماره فنی…"><button class="btn btn-primary">جستجو{ic("search")}</button></form>
  <div class="btn-row">
    <a class="btn btn-primary" href="index.html">بازگشت به خانه{ic("arrow-l")}</a>
    <a class="btn btn-outline" href="shop.html">همه قطعات</a>
    <a class="btn btn-ghost" href="contact.html">گزارش مشکل</a></div>
</div>
<div class="container block">
  {section_head("شاید دنبال اینها باشی", "دسته‌های پرطرفدار", "shop.html")}{cat_grid()}
</div>'''
    return ("صفحه پیدا نشد", body, "خطای ۴۰۴ پارتیاب", "404", "")


def page_sitemap() -> tuple[str, str, str, str, str]:
    hero = page_hero("نقشه صفحات", "همه صفحات سایت، از نو و با سیستم طراحی v2 — یکدست، راست‌به‌چپ و واکنش‌گرا.",
                     "طراحی مجدد کامل", [("خانه", "index.html"), ("نقشه صفحات", None)],
                     [(str(len(PAGES)), "صفحه"), ("v2.0", "نسخه طراحی")])
    cards = "".join(f'''<a class="map-card" href="{f}"><span class="ic">{ic(a)}</span>
      <div class="grow"><b>{t}</b><span>{d}</span></div>{ic("chev-l")}</a>'''
        for f, t, d, a, _ in PAGES)
    body = f'''<div class="container block">
  <div class="map-grid">{cards}</div>
  <div class="note-box" style="margin-top:20px">{ic("info")}<span>منبع این طراحی، فایل <a href="styleguide.html"><b>سیستم طراحی پارتیاب v1.0 + بسته‌های v1.1 و v1.2</b></a> است؛ تمام توکنها، کامپوننتها، حرکتها و آیکونها از همان فایل استخراج شدهاند.</span></div>
</div>
<div class="container block">{usp()}</div>'''
    return ("نقشه صفحات", body, "فهرست تمام صفحات بازطراحیشده پارتیاب", "sitemap", hero)


BUILDERS = {
    "home": page_home, "shop": page_shop, "product": page_product, "cart": page_cart,
    "checkout": page_checkout, "account": page_account, "orders": page_orders,
    "wishlist": page_wishlist, "garage": page_garage, "compare": page_compare,
    "brands": page_brands, "blog": page_blog, "article": page_article, "about": page_about,
    "contact": page_contact, "faq": page_faq, "warranty": page_warranty, "guide": page_guide,
    "track": page_track, "thankyou": page_thankyou, "search": page_search,
    "404": page_404, "sitemap": page_sitemap,
}


def main() -> None:
    for filename, label, desc, icon, key in PAGES:
        title, body, meta, active, hero = BUILDERS[key]()
        html = shell(title, body, desc=meta, active=active, hero=hero)
        (ROOT / filename).write_text(html, encoding="utf-8")
        print(f"{filename:18} {len(html):7,} bytes  {label}")
    print(f"\n{len(PAGES)} صفحه ساخته شد ✓")


if __name__ == "__main__":
    main()
