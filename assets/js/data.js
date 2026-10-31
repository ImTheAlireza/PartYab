/* =========================================================
   PartYab — demo data
   داده‌های این فایل نمونه/نمایشی هستند و برای سفارش واقعی
   قابل اتکا نیستند.
   ========================================================= */

const MAKES = [
  {
    id: 'ikco', name: 'ایران‌خودرو', latin: 'IKCO',
    models: [
      { id: '206', name: 'پژو ۲۰۶', years: [1380, 1403], engines: ['۱.۴ لیتری', '۱.۶ لیتری TU5'] },
      { id: 'samand', name: 'سمند', years: [1381, 1402], engines: ['۱.۸ لیتری XU7', '۱.۶ لیتری EF7'] },
      { id: 'dena', name: 'دنا', years: [1393, 1403], engines: ['۱.۶ لیتری EF7', '۱.۶ لیتری TU5'] },
      { id: 'rana', name: 'رانا', years: [1390, 1400], engines: ['۱.۶ لیتری TU5'] },
      { id: 'pars', name: 'پژو پارس', years: [1382, 1403], engines: ['۱.۸ لیتری XU7', '۱.۶ لیتری ELX'] },
      { id: 'tara', name: 'تارا', years: [1399, 1403], engines: ['۱.۶ لیتری TU5P'] }
    ]
  },
  {
    id: 'saipa', name: 'سایپا', latin: 'SAIPA',
    models: [
      { id: 'pride', name: 'پراید', years: [1372, 1398], engines: ['۱.۳ لیتری', '۱.۵ لیتری'] },
      { id: 'tiba', name: 'تیبا', years: [1387, 1400], engines: ['۱.۵ لیتری'] },
      { id: 'quick', name: 'کوییک', years: [1397, 1403], engines: ['۱.۵ لیتری'] },
      { id: 'shahin', name: 'شاهین', years: [1399, 1403], engines: ['۱.۵ لیتری توربو'] }
    ]
  },
  {
    id: 'renault', name: 'رنو', latin: 'RENAULT',
    models: [
      { id: 'l90', name: 'ال ۹۰', years: [1385, 1392], engines: ['۱.۶ لیتری K7M'] },
      { id: 'tondar90', name: 'تندر ۹۰', years: [1385, 1393], engines: ['۱.۶ لیتری K4M'] }
    ]
  },
  {
    id: 'hyundai', name: 'هیوندای', latin: 'HYUNDAI',
    models: [
      { id: 'tucson', name: 'توسان', years: [1394, 1403], engines: ['۲.۰ لیتری', '۲.۴ لیتری'] },
      { id: 'cerato', name: 'سراتو', years: [1394, 1403], engines: ['۱.۶ لیتری', '۲.۰ لیتری'] }
    ]
  },
  {
    id: 'toyota', name: 'تویوتا', latin: 'TOYOTA',
    models: [
      { id: 'corolla', name: 'کرولا', years: [1395, 1403], engines: ['۱.۶ لیتری', '۱.۸ لیتری هیبرید'] },
      { id: 'yaris', name: 'یاریس', years: [1395, 1400], engines: ['۱.۵ لیتری'] }
    ]
  }
];

const CATEGORIES = [
  { id: 'engine', name: 'موتور و انتقال قدرت', icon: 'engine' },
  { id: 'filter', name: 'فیلترها و روغن', icon: 'filter' },
  { id: 'brake', name: 'ترمز و ABS', icon: 'brake' },
  { id: 'suspension', name: 'جلوبندی و تعلیق', icon: 'shock' },
  { id: 'electrical', name: 'برق و روشنایی', icon: 'light' },
  { id: 'body', name: 'بدنه و آینه', icon: 'car' },
  { id: 'tire', name: 'لاستیک و رینگ', icon: 'tire' },
  { id: 'accessory', name: 'لوازم جانبی', icon: 'accessory' }
];

const BRANDS = ['MANN-FILTER', 'BOSCH', 'TRW', 'VALEO', 'NGK', 'KYB', 'HELLA', 'GATES', 'DENSO', 'MONROE'];

/* هر محصول: fits = لیست خودروهای تأییدشده در فروشگاه
   fitmentKnown=false یعنی فروشگاه هنوز داده‌ای ثبت نکرده است */
const PRODUCTS = [
  {
    id: 'p1', name: 'لنت ترمز جلو پژو ۲۰۶ / سمند / دنا', brand: 'TRW', sku: 'BRK-206-4415',
    oem: '4253.42', cat: 'brake', price: 1850000, oldPrice: 2150000,
    stock: 12, rating: 4.7, reviews: 38, img: 'p-brake.jpg', featured: true,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1380, to: 1403 },
      { model: 'samand', from: 1381, to: 1402 },
      { model: 'dena', from: 1393, to: 1403 },
      { model: 'rana', from: 1390, to: 1400 },
      { model: 'pars', from: 1382, to: 1403 }
    ],
    desc: 'لنت ترمز جلو با ترکیب نیمه‌فلزی و ضریب اصطکاک پایدار، مناسب رانندگی شهری و جاده‌ای. صفحه پشتی فولادی با پوشش ضدزنگ و شیار تخلیه حرارت.',
    specs: { 'نوع': 'لنت ترمز دیسکی', 'محور': 'جلو', 'جنس': 'نیمه‌فلزی', 'تعداد در بسته': '۴ عدد', 'کد فنی': '4253.42', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p2', name: 'فیلتر روغن MANN مناسب موتور TU5', brand: 'MANN-FILTER', sku: 'OIL-MF-7700',
    oem: '1109.A7', cat: 'filter', price: 420000, oldPrice: 480000,
    stock: 54, rating: 4.8, reviews: 126, img: 'p-oilfilter.jpg', featured: true,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1380, to: 1403 },
      { model: 'samand', from: 1381, to: 1402 },
      { model: 'dena', from: 1393, to: 1403 },
      { model: 'rana', from: 1390, to: 1400 },
      { model: 'pars', from: 1382, to: 1403 },
      { model: 'tara', from: 1399, to: 1403 },
      { model: 'quick', from: 1397, to: 1403 },
      { model: 'shahin', from: 1399, to: 1403 }
    ],
    desc: 'فیلتر روغن با کاغذ صافی چندلایه و شیر یک‌طرفه، جریان روغن پایدار و حفظ ذرات ریز را تضمین می‌کند. مناسب سرویس دوره‌ای ۱۰٬۰۰۰ کیلومتری.',
    specs: { 'نوع': 'فیلتر روغن پیچی', 'ارتفاع': '۷۹ میلی‌متر', 'قطر رزوه': 'M20×1.5', 'کد فنی': '1109.A7', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p3', name: 'فیلتر هوا پژو ۲۰۶ و پارس', brand: 'BOSCH', sku: 'AIR-BS-1402',
    oem: '1444.F7', cat: 'filter', price: 380000, oldPrice: 450000,
    stock: 31, rating: 4.6, reviews: 74, img: 'p-airfilter.jpg', featured: true,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1380, to: 1403 },
      { model: 'pars', from: 1382, to: 1403 },
      { model: 'samand', from: 1381, to: 1402 }
    ],
    desc: 'فیلتر هوای پنلی با کاغذ آکاردئونی و درزگیر لاستیکی، مانع ورود گرد و غبار به موتور می‌شود و افت جریان هوا را کم می‌کند.',
    specs: { 'نوع': 'فیلتر هوای پنلی', 'طول': '۲۳۵ میلی‌متر', 'عرض': '۱۶۵ میلی‌متر', 'کد فنی': '1444.F7', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p4', name: 'شمع ایریدیوم NGK مناسب TU5', brand: 'NGK', sku: 'SPK-NGK-ILZ',
    oem: '5960.9F', cat: 'engine', price: 1250000, oldPrice: 0,
    stock: 18, rating: 4.9, reviews: 61, img: 'p-sparkplug.jpg', featured: true,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1385, to: 1403 },
      { model: 'dena', from: 1393, to: 1403 },
      { model: 'rana', from: 1390, to: 1400 },
      { model: 'tara', from: 1399, to: 1403 },
      { model: 'shahin', from: 1399, to: 1403 }
    ],
    desc: 'ست شمع ایریدیوم با الکترود نازک ۰.۶ میلی‌متری؛ احتراق کامل‌تر، استارت سریع‌تر و عمر مفید بالاتر نسبت به شمع‌های مسی.',
    specs: { 'نوع': 'ایریدیوم', 'تعداد در بسته': '۴ عدد', 'فاصله الکترود': '۰.۸ میلی‌متر', 'کد فنی': '5960.9F', 'کشور سازنده': 'ژاپن' }
  },
  {
    id: 'p5', name: 'کمک فنر جلو گازی پژو ۲۰۶', brand: 'KYB', sku: 'SHK-KYB-3390',
    oem: '5202.7V', cat: 'suspension', price: 3850000, oldPrice: 4300000,
    stock: 6, rating: 4.5, reviews: 22, img: 'p-shock.jpg', featured: false,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1380, to: 1403 },
      { model: 'rana', from: 1390, to: 1400 }
    ],
    desc: 'کمک فنر گازی جلو با میراکننده روغنی، کاهش نوسان بدنه و حفظ تماس لاستیک با جاده. مناسب جایگزینی جفت (چپ و راست).',
    specs: { 'نوع': 'گازی', 'محور': 'جلو', 'نصب': 'جفتی توصیه می‌شود', 'کد فنی': '5202.7V', 'کشور سازنده': 'ژاپن' }
  },
  {
    id: 'p6', name: 'چراغ جلوی راست پژو ۲۰۶', brand: 'TYC', sku: 'LGT-HL-206R',
    oem: '6205.P2', cat: 'electrical', price: 2950000, oldPrice: 0,
    stock: 4, rating: 4.3, reviews: 15, img: 'p-headlight.jpg', featured: false,
    fitmentKnown: true, verified: false,
    fits: [
      { model: '206', from: 1385, to: 1403 }
    ],
    desc: 'چراغ جلوی سمت راست با شیشه پلی‌کربنات و لنز شفاف. تحویل همراه با پایه نصب؛ لامپ به‌صورت جداگانه فروخته می‌شود.',
    specs: { 'سمت': 'راست', 'نوع لامپ': 'H7', 'جنس شیشه': 'پلی‌کربنات', 'کد فنی': '6205.P2', 'کشور سازنده': 'تایوان' }
  },
  {
    id: 'p7', name: 'تیغه برف‌پاک‌کن بورس Bosch Aerotwin', brand: 'BOSCH', sku: 'WPR-BS-A555',
    oem: '3397.00', cat: 'accessory', price: 890000, oldPrice: 1050000,
    stock: 40, rating: 4.6, reviews: 53, img: 'p-wiper.jpg', featured: false,
    fitmentKnown: false, verified: false,
    fits: [],
    desc: 'تیغه برف‌پاک‌کن تخت با لاستیک گرافیتی و اتصال چندگانه؛ پاک‌کنندگی بی‌صدا در سرعت بالا. سازگاری با خودروهای مختلف باید با کد فنی بررسی شود.',
    specs: { 'طول': '۵۵ سانتی‌متر', 'نوع اتصال': 'چندگانه', 'جنس': 'لاستیک گرافیتی', 'کد فنی': '3397.00', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p8', name: 'کیت تسمه تایم گیتس + تسمه دینام', brand: 'GATES', sku: 'TBK-GT-K015603',
    oem: '0831.J0', cat: 'engine', price: 4650000, oldPrice: 5200000,
    stock: 8, rating: 4.7, reviews: 19, img: 'p-timingbelt.jpg', featured: false,
    fitmentKnown: false, verified: false,
    fits: [],
    desc: 'کیت کامل تسمه تایم همراه با هرزگرد و تسمه دینام. تعویض در بازه توصیه‌شده سازنده از پارگی تسمه و آسیب موتور جلوگیری می‌کند.',
    specs: { 'تعداد دندانه': '۱۳۷', 'محتویات': 'تسمه + هرزگرد', 'نوع': 'تایم', 'کد فنی': '0831.J0', 'کشور سازنده': 'بلژیک' }
  },
  {
    id: 'p9', name: 'دیسک ترمز جلو پژو ۲۰۶ (جفت)', brand: 'TRW', sku: 'DSC-TRW-2401',
    oem: '4246.9Q', cat: 'brake', price: 2650000, oldPrice: 0,
    stock: 10, rating: 4.6, reviews: 27, img: 'p-brake.jpg', featured: false,
    fitmentKnown: true, verified: true,
    fits: [
      { model: '206', from: 1380, to: 1403 },
      { model: 'rana', from: 1390, to: 1400 }
    ],
    desc: 'دیسک ترمز جلو از چدن خاکستری با پرداخت دقیق سطح، کاهش لرزش و افزایش عمر لنت. فروش به‌صورت جفت.',
    specs: { 'قطر': '۲۴۷ میلی‌متر', 'ضخامت': '۲۰.۴ میلی‌متر', 'تعداد': '۲ عدد', 'کد فنی': '4246.9Q', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p10', name: 'فیلتر کابین کربن‌اکتیو پراید و تیبا', brand: 'VALEO', sku: 'CAB-VL-1102',
    oem: '6447.G2', cat: 'filter', price: 340000, oldPrice: 395000,
    stock: 25, rating: 4.4, reviews: 33, img: 'p-airfilter.jpg', featured: false,
    fitmentKnown: true, verified: true,
    fits: [
      { model: 'pride', from: 1380, to: 1398 },
      { model: 'tiba', from: 1387, to: 1400 },
      { model: 'quick', from: 1397, to: 1403 }
    ],
    desc: 'فیلتر کابین با لایه کربن فعال برای کاهش بو، گرده و ذرات معلق هوای ورودی به کابین.',
    specs: { 'نوع': 'کربن‌اکتیو', 'ابعاد': '۲۰۵×۱۹۰ میلی‌متر', 'کد فنی': '6447.G2', 'کشور سازنده': 'فرانسه' }
  },
  {
    id: 'p11', name: 'روغن موتور تمام سنتتیک ۵W-30', brand: 'TOTAL', sku: 'OIL-TT-5W30',
    oem: '—', cat: 'filter', price: 2150000, oldPrice: 2400000,
    stock: 60, rating: 4.8, reviews: 147, img: 'p-oilfilter.jpg', featured: false,
    fitmentKnown: false, verified: false,
    fits: [],
    desc: 'روغن موتور تمام سنتتیک با ویسکوزیته ۵W-30، مناسب موتورهای بنزینی مدرن و شرایط ترافیک شهری. گرید را با دفترچه خودرو تطبیق دهید.',
    specs: { 'گرید': '۵W-30', 'نوع': 'تمام سنتتیک', 'حجم': '۴ لیتر', 'استاندارد': 'API SN', 'کشور سازنده': 'فرانسه' }
  },
  {
    id: 'p12', name: 'کمک فنر عقب سمند و پارس', brand: 'MONROE', sku: 'SHK-MN-7721',
    oem: '5206.3E', cat: 'suspension', price: 3200000, oldPrice: 3600000,
    stock: 3, rating: 4.4, reviews: 12, img: 'p-shock.jpg', featured: false,
    fitmentKnown: true, verified: true,
    fits: [
      { model: 'samand', from: 1381, to: 1402 },
      { model: 'pars', from: 1382, to: 1403 },
      { model: 'dena', from: 1393, to: 1403 }
    ],
    desc: 'کمک فنر عقب روغنی با میراکنندگی متعادل برای بار و سرنشین معمول. نصب جفتی توصیه می‌شود.',
    specs: { 'نوع': 'روغنی', 'محور': 'عقب', 'نصب': 'جفتی توصیه می‌شود', 'کد فنی': '5206.3E', 'کشور سازنده': 'بلژیک' }
  }
];

/* محصولات مرتبط/دسته‌های صفحه اصلی */
const GUIDES = [
  { icon: 'car', tag: 'راهنمای خرید', title: 'چطور خودروی خود را درست انتخاب کنیم؟', text: 'تفاوت مدل، سال ساخت و تیپ موتور چه تأثیری بر سازگاری قطعه دارد.', meta: '۶ دقیقه مطالعه' },
  { icon: 'search', tag: 'کد فنی', title: 'کد OEM چیست و کجای قطعه نوشته شده؟', text: 'راهنمای پیدا کردن شماره فنی روی قطعه، بسته‌بندی و کارت گارانتی.', meta: '۴ دقیقه مطالعه' },
  { icon: 'shield', tag: 'اطمینان', title: 'وقتی سازگاری ثبت نشده باشد چه کنیم؟', text: 'معنی نشان‌های سازگاری در پارت‌یاب و راه درست استعلام پیش از خرید.', meta: '۳ دقیقه مطالعه' }
];
