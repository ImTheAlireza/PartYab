/* =========================================================
   PartYab — demo data
   داده‌های این فایل نمونه/نمایشی هستند و برای سفارش واقعی
   قابل اتکا نیستند.
   اولویت بازار: خودروهای تولید/مونتاژ ایران، سپس وارداتی‌ها.
   ========================================================= */

const MAKES = [
  {
    id: 'ikco', name: 'ایران‌خودرو', latin: 'IKCO', group: 'iran',
    models: [
      { id: '206', name: 'پژو ۲۰۶', years: [1380, 1403], engines: ['۱.۴ لیتری', '۱.۶ لیتری TU5'], keywords: ['206', 'پژو', 'peugeot', 'tu5', 'تیپ ۵'] },
      { id: '206sd', name: 'پژو ۲۰۶ صندوقدار', years: [1385, 1403], engines: ['۱.۶ لیتری TU5'], keywords: ['206 sd', 'صندوقدار', 'sd'] },
      { id: '207', name: 'پژو ۲۰۷', years: [1390, 1403], engines: ['۱.۶ لیتری TU5'], keywords: ['207', 'peugeot'] },
      { id: 'samand', name: 'سمند', years: [1381, 1402], engines: ['۱.۸ لیتری XU7', '۱.۶ لیتری EF7'], keywords: ['samand', 'ef7', 'xu7', 'سمند'] },
      { id: 'dena', name: 'دنا', years: [1393, 1403], engines: ['۱.۶ لیتری EF7', '۱.۶ لیتری TU5'], keywords: ['dena', 'دنا'] },
      { id: 'rana', name: 'رانا', years: [1390, 1400], engines: ['۱.۶ لیتری TU5'], keywords: ['rana', 'رانا'] },
      { id: 'pars', name: 'پژو پارس', years: [1382, 1403], engines: ['۱.۸ لیتری XU7', '۱.۶ لیتری ELX'], keywords: ['pars', '405', 'پارس'] },
      { id: 'tara', name: 'تارا', years: [1399, 1403], engines: ['۱.۶ لیتری TU5P'], keywords: ['tara', 'تارا'] },
      { id: 'arisun', name: 'آریسان', years: [1394, 1403], engines: ['۱.۸ لیتری XU7'], keywords: ['arisun', 'آریسان'] }
    ]
  },
  {
    id: 'saipa', name: 'سایپا', latin: 'SAIPA', group: 'iran',
    models: [
      { id: 'pride', name: 'پراید', years: [1372, 1398], engines: ['۱.۳ لیتری', '۱.۵ لیتری'], keywords: ['pride', 'پراید', '131', '111'] },
      { id: 'tiba', name: 'تیبا', years: [1387, 1400], engines: ['۱.۵ لیتری'], keywords: ['tiba', 'تیبا'] },
      { id: 'saina', name: 'ساینا', years: [1394, 1403], engines: ['۱.۵ لیتری'], keywords: ['saina', 'ساینا'] },
      { id: 'quick', name: 'کوییک', years: [1397, 1403], engines: ['۱.۵ لیتری'], keywords: ['quick', 'کوییک'] },
      { id: 'shahin', name: 'شاهین', years: [1399, 1403], engines: ['۱.۵ لیتری توربو'], keywords: ['shahin', 'شاهین'] }
    ]
  },
  {
    id: 'mvm', name: 'مدیران خودرو', latin: 'MVM', group: 'iran',
    models: [
      { id: 'tiggo7', name: 'تیگو ۷', years: [1397, 1403], engines: ['۱.۵ لیتری توربو'], keywords: ['tiggo', 'چری', 'chery'] },
      { id: 'tiggo8', name: 'تیگو ۸', years: [1399, 1403], engines: ['۱.۶ لیتری توربو'], keywords: ['tiggo 8', 'چری'] },
      { id: 'arrizo5', name: 'آریزو ۵', years: [1396, 1403], engines: ['۱.۵ لیتری توربو'], keywords: ['arrizo', 'آریزو'] }
    ]
  },
  {
    id: 'kerman', name: 'کرمان موتور', latin: 'KERMAN', group: 'iran',
    models: [
      { id: 'j5', name: 'جک J5', years: [1394, 1400], engines: ['۱.۵ لیتری'], keywords: ['j5', 'jac', 'جک'] },
      { id: 'haima7', name: 'هایما S7', years: [1396, 1403], engines: ['۱.۸ لیتری توربو'], keywords: ['haima', 'هایما', 's7'] }
    ]
  },
  {
    id: 'bahman', name: 'بهمن موتور', latin: 'BAHMAN', group: 'iran',
    models: [
      { id: 'dignity', name: 'دیگنیتی', years: [1399, 1403], engines: ['۱.۵ لیتری توربو'], keywords: ['dignity', 'دیگنیتی'] },
      { id: 'fidelity', name: 'فیدلیتی', years: [1399, 1403], engines: ['۱.۵ لیتری توربو'], keywords: ['fidelity', 'فیدلیتی'] }
    ]
  },
  {
    id: 'renault', name: 'رنو', latin: 'RENAULT', group: 'iran',
    models: [
      { id: 'l90', name: 'ال ۹۰', years: [1385, 1392], engines: ['۱.۶ لیتری K7M'], keywords: ['l90', 'رنو', 'renault', 'تندر'] },
      { id: 'tondar90', name: 'تندر ۹۰', years: [1385, 1393], engines: ['۱.۶ لیتری K4M'], keywords: ['tondar', 'تندر'] }
    ]
  },
  {
    id: 'toyota', name: 'تویوتا', latin: 'TOYOTA', group: 'foreign',
    models: [
      { id: 'corolla', name: 'کرولا', years: [1395, 1403], engines: ['۱.۶ لیتری', '۱.۸ لیتری هیبرید'], keywords: ['corolla', 'کرولا', 'toyota'] },
      { id: 'yaris', name: 'یاریس', years: [1395, 1400], engines: ['۱.۵ لیتری'], keywords: ['yaris', 'یاریس'] },
      { id: 'camry', name: 'کمری', years: [1397, 1403], engines: ['۲.۵ لیتری هیبرید'], keywords: ['camry', 'کمری'] }
    ]
  },
  {
    id: 'hyundai', name: 'هیوندای', latin: 'HYUNDAI', group: 'foreign',
    models: [
      { id: 'tucson', name: 'توسان', years: [1394, 1403], engines: ['۲.۰ لیتری', '۲.۴ لیتری'], keywords: ['tucson', 'توسان'] },
      { id: 'elantra', name: 'النترا', years: [1395, 1403], engines: ['۱.۶ لیتری', '۲.۰ لیتری'], keywords: ['elantra', 'النترا'] },
      { id: 'santafe', name: 'سانتافه', years: [1395, 1403], engines: ['۲.۴ لیتری'], keywords: ['santa fe', 'سانتافه'] }
    ]
  },
  {
    id: 'kia', name: 'کیا', latin: 'KIA', group: 'foreign',
    models: [
      { id: 'cerato', name: 'سراتو', years: [1394, 1403], engines: ['۱.۶ لیتری', '۲.۰ لیتری'], keywords: ['cerato', 'سراتو'] },
      { id: 'sportage', name: 'اسپورتیج', years: [1395, 1403], engines: ['۲.۰ لیتری'], keywords: ['sportage', 'اسپورتیج'] },
      { id: 'sorento', name: 'سورنتو', years: [1395, 1403], engines: ['۲.۴ لیتری'], keywords: ['sorento', 'سورنتو'] }
    ]
  },
  {
    id: 'bmw', name: 'بی‌ام‌و', latin: 'BMW', group: 'foreign',
    models: [
      { id: '320i', name: 'سری ۳ (۳۲۰i)', years: [1395, 1403], engines: ['۲.۰ لیتری توربو'], keywords: ['320', 'bmw', 'بی ام و'] }
    ]
  }
];

const GROUP_LABEL = { iran: 'خودروهای ایرانی', foreign: 'خودروهای وارداتی' };

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

/* محصولات
   fits[].status: fit (تأییدشده) | conditional (سازگار با شرط)
   fitmentKnown=false → اطلاعات سازگاری ثبت نشده
   universal=true → قطعهٔ عمومی (وابسته به خودروی خاص)
   equivalents → کدهای فنی معادل
   kit → شناسهٔ قطعاتی که معمولاً همراه این قطعه تعویض می‌شوند */
const PRODUCTS = [
  {
    id: 'p1', name: 'لنت ترمز جلو پژو ۲۰۶ / سمند / دنا', brand: 'TRW', sku: 'BRK-206-4415',
    oem: '4253.42', quality: 'orig', engines: ['TU5','XU7'],
    cat: 'brake', price: 1850000, oldPrice: 2150000,
    stock: 12, rating: 4.7, reviews: 38, img: 'p-brake.jpg', featured: true,
    fitmentKnown: true, verified: true, equivalents: ['4253.42', '4253.43', 'BP-1134'],
    kit: ['p9', 'p13'], kitNote: 'تعویض لنت بدون بررسی دیسک و روغن ترمز توصیه نمی‌شود.',
    fits: [
      { model: '206', from: 1380, to: 1403, status: 'conditional', note: 'فقط تیپ ۵ و ۶ با ترمز ABS؛ برای تیپهای پایینتر کد 4253.41' },
      { model: 'samand', from: 1381, to: 1402, status: 'fit' },
      { model: 'dena', from: 1393, to: 1403, status: 'fit' },
      { model: 'rana', from: 1390, to: 1400, status: 'fit' },
      { model: 'pars', from: 1382, to: 1403, status: 'fit' }
    ],
    desc: 'لنت ترمز جلو با ترکیب نیمه‌فلزی و ضریب اصطکاک پایدار، مناسب رانندگی شهری و جاده‌ای. صفحهٔ پشتی فولادی با پوشش ضدزنگ و شیار تخلیهٔ حرارت.',
    specs: { 'نوع': 'لنت ترمز دیسکی', 'محور': 'جلو', 'جنس': 'نیمه‌فلزی', 'تعداد در بسته': '۴ عدد', 'کد فنی': '4253.42', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p2', name: 'فیلتر روغن MANN مناسب موتور TU5', brand: 'MANN-FILTER', sku: 'OIL-MF-7700',
    oem: '1109.A7', quality: 'orig', engines: ['TU5'],
    cat: 'filter', price: 420000, oldPrice: 480000,
    stock: 54, rating: 4.8, reviews: 126, img: 'p-oilfilter.jpg', featured: true,
    fitmentKnown: true, verified: true, equivalents: ['1109.A7', '1109.CL', 'HU-7011'],
    kit: ['p11', 'p3'], kitNote: 'فیلتر روغن را همیشه همراه روغن موتور تعویض کنید.',
    fits: [
      { model: '206', from: 1380, to: 1403, status: 'fit' },
      { model: 'samand', from: 1381, to: 1402, status: 'fit' },
      { model: 'dena', from: 1393, to: 1403, status: 'fit' },
      { model: 'rana', from: 1390, to: 1400, status: 'fit' },
      { model: 'pars', from: 1382, to: 1403, status: 'fit' },
      { model: 'tara', from: 1399, to: 1403, status: 'fit' },
      { model: 'quick', from: 1397, to: 1403, status: 'fit' },
      { model: 'saina', from: 1394, to: 1403, status: 'fit' },
      { model: 'shahin', from: 1399, to: 1403, status: 'fit' }
    ],
    desc: 'فیلتر روغن با کاغذ صافی چندلایه و شیر یک‌طرفه، جریان روغن پایدار و حفظ ذرات ریز را تضمین می‌کند. مناسب سرویس دوره‌ای ۱۰٬۰۰۰ کیلومتری.',
    specs: { 'نوع': 'فیلتر روغن پیچی', 'ارتفاع': '۷۹ میلی‌متر', 'قطر رزوه': 'M20×1.5', 'کد فنی': '1109.A7', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p3', name: 'فیلتر هوا پژو ۲۰۶ و پارس', brand: 'BOSCH', sku: 'AIR-BS-1402',
    oem: '1444.F7', quality: 'after', engines: ['TU5','XU7'],
    cat: 'filter', price: 380000, oldPrice: 450000,
    stock: 31, rating: 4.6, reviews: 74, img: 'p-airfilter.jpg', featured: true,
    fitmentKnown: true, verified: true, equivalents: ['1444.F7', '1444.F8'],
    kit: ['p2', 'p11'], kitNote: 'فیلتر هوا و روغن را در یک سرویس تعویض کنید.',
    fits: [
      { model: '206', from: 1380, to: 1403, status: 'fit' },
      { model: 'pars', from: 1382, to: 1403, status: 'fit' },
      { model: 'samand', from: 1381, to: 1402, status: 'fit' },
      { model: 'arisun', from: 1394, to: 1403, status: 'fit' }
    ],
    desc: 'فیلتر هوای پنلی با کاغذ آکاردئونی و درزگیر لاستیکی، مانع ورود گرد و غبار به موتور می‌شود و افت جریان هوا را کم می‌کند.',
    specs: { 'نوع': 'فیلتر هوای پنلی', 'طول': '۲۳۵ میلی‌متر', 'عرض': '۱۶۵ میلی‌متر', 'کد فنی': '1444.F7', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p4', name: 'شمع ایریدیوم NGK مناسب TU5', brand: 'NGK', sku: 'SPK-NGK-ILZ',
    oem: '5960.9F', quality: 'orig', engines: ['TU5','EF7'],
    cat: 'engine', price: 1250000, oldPrice: 0,
    stock: 18, rating: 4.9, reviews: 61, img: 'p-sparkplug.jpg', featured: true,
    fitmentKnown: true, verified: true, equivalents: ['5960.9F', 'ILZKR7B11', '267700-6160'],
    kit: ['p2', 'p11'], kitNote: 'شمع را همراه فیلتر هوا و روغن تعویض کنید.',
    fits: [
      { model: '206', from: 1385, to: 1403, status: 'fit' },
      { model: 'dena', from: 1393, to: 1403, status: 'fit' },
      { model: 'rana', from: 1390, to: 1400, status: 'fit' },
      { model: 'tara', from: 1399, to: 1403, status: 'fit' },
      { model: 'shahin', from: 1399, to: 1403, status: 'fit' },
      { model: 'saina', from: 1394, to: 1403, status: 'fit' }
    ],
    desc: 'ست شمع ایریدیوم با الکترود نازک ۰.۶ میلی‌متری؛ احتراق کامل‌تر، استارت سریع‌تر و عمر مفید بالاتر نسبت به شمع‌های مسی.',
    specs: { 'نوع': 'ایریدیوم', 'تعداد در بسته': '۴ عدد', 'فاصله الکترود': '۰.۸ میلی‌متر', 'کد فنی': '5960.9F', 'کشور سازنده': 'ژاپن' }
  },
  {
    id: 'p5', name: 'کمک فنر جلو گازی پژو ۲۰۶', brand: 'KYB', sku: 'SHK-KYB-3390',
    oem: '5202.7V', quality: 'plan', engines: ['TU5','XU7'],
    cat: 'suspension', price: 3850000, oldPrice: 4300000,
    stock: 6, rating: 4.5, reviews: 22, img: 'p-shock.jpg', featured: false,
    fitmentKnown: true, verified: true, equivalents: ['5202.7V', '5202.7W'],
    kit: ['p1'], kitNote: 'پس از تعویض کمک فنر، تراز و بررسی لنت‌ها توصیه می‌شود.',
    fits: [
      { model: '206', from: 1380, to: 1403, status: 'fit' },
      { model: 'rana', from: 1390, to: 1400, status: 'fit' }
    ],
    desc: 'کمک فنر گازی جلو با میراکنندهٔ روغنی، کاهش نوسان بدنه و حفظ تماس لاستیک با جاده. نصب جفتی (چپ و راست) توصیه می‌شود.',
    specs: { 'نوع': 'گازی', 'محور': 'جلو', 'نصب': 'جفتی توصیه می‌شود', 'کد فنی': '5202.7V', 'کشور سازنده': 'ژاپن' }
  },
  {
    id: 'p6', name: 'چراغ جلوی راست پژو ۲۰۶', brand: 'TYC', sku: 'LGT-HL-206R',
    oem: '6205.P2', quality: 'after', engines: ['TU5'], priceOnRequest: true,
    cat: 'electrical', price: 2950000, oldPrice: 0,
    stock: 4, rating: 4.3, reviews: 15, img: 'p-headlight.jpg', featured: false,
    fitmentKnown: true, verified: false, equivalents: ['6205.P2', '6205.P3'],
    kit: [],
    fits: [
      { model: '206', from: 1385, to: 1403, status: 'conditional', note: 'فقط نسخههای بعد از ۱۳۸۸ با چراغ شفاف' }
    ],
    desc: 'چراغ جلوی سمت راست با شیشهٔ پلی‌کربنات و لنز شفاف. تحویل همراه با پایهٔ نصب؛ لامپ جداگانه فروخته می‌شود.',
    specs: { 'سمت': 'راست', 'نوع لامپ': 'H7', 'جنس شیشه': 'پلی‌کربنات', 'کد فنی': '6205.P2', 'کشور سازنده': 'تایوان' }
  },
  {
    id: 'p7', name: 'تیغه برف‌پاک‌کن Bosch Aerotwin', brand: 'BOSCH', sku: 'WPR-BS-A555',
    oem: '3397.00', quality: 'after', engines: [],
    cat: 'accessory', price: 890000, oldPrice: 1050000,
    stock: 40, rating: 4.6, reviews: 53, img: 'p-wiper.jpg', featured: false,
    fitmentKnown: false, verified: false, universal: true,
    universalNote: 'قطعهٔ عمومی با اتصال چندگانه؛ اندازه را با تیغهٔ فعلی خودرو مقایسه کنید.',
    equivalents: ['3397.00', 'A555S'], kit: [],
    fits: [],
    desc: 'تیغه برف‌پاک‌کن تخت با لاستیک گرافیتی و اتصال چندگانه؛ پاک‌کنندگی بی‌صدا در سرعت بالا.',
    specs: { 'طول': '۵۵ سانتی‌متر', 'نوع اتصال': 'چندگانه', 'جنس': 'لاستیک گرافیتی', 'کد فنی': '3397.00', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p8', name: 'کیت تسمه تایم گیتس + تسمه دینام', brand: 'GATES', sku: 'TBK-GT-K015603',
    oem: '0831.J0', quality: 'orig', engines: ['TU5'], priceOnRequest: true,
    cat: 'engine', price: 4650000, oldPrice: 5200000,
    stock: 8, rating: 4.7, reviews: 19, img: 'p-timingbelt.jpg', featured: false,
    fitmentKnown: false, verified: false, equivalents: ['0831.J0', 'K015603XS'],
    kit: [], fits: [],
    desc: 'کیت کامل تسمه تایم همراه با هرزگرد و تسمه دینام. تعویض در بازهٔ توصیه‌شدهٔ سازنده از پارگی تسمه و آسیب موتور جلوگیری می‌کند.',
    specs: { 'تعداد دندانه': '۱۳۷', 'محتویات': 'تسمه + هرزگرد', 'نوع': 'تایم', 'کد فنی': '0831.J0', 'کشور سازنده': 'بلژیک' }
  },
  {
    id: 'p9', name: 'دیسک ترمز جلو پژو ۲۰۶ (جفت)', brand: 'TRW', sku: 'DSC-TRW-2401',
    oem: '4246.9Q', quality: 'orig', engines: ['TU5','XU7'],
    cat: 'brake', price: 2650000, oldPrice: 0,
    stock: 10, rating: 4.6, reviews: 27, img: 'p-brake.jpg', featured: false,
    fitmentKnown: true, verified: true, equivalents: ['4246.9Q', '4246.9R', 'DF-4184'],
    kit: ['p1', 'p13'], kitNote: 'دیسک و لنت را همیشه با هم تعویض کنید.',
    fits: [
      { model: '206', from: 1380, to: 1403, status: 'conditional', note: 'نسخهٔ ۲۴۷ میلی‌متری؛ برای نسخهٔ ۲۶۶ میلی‌متری کد 4246.9R' },
      { model: 'rana', from: 1390, to: 1400, status: 'fit' }
    ],
    desc: 'دیسک ترمز جلو از چدن خاکستری با پرداخت دقیق سطح، کاهش لرزش و افزایش عمر لنت. فروش به‌صورت جفت.',
    specs: { 'قطر': '۲۴۷ میلی‌متر', 'ضخامت': '۲۰.۴ میلی‌متر', 'تعداد': '۲ عدد', 'کد فنی': '4246.9Q', 'کشور سازنده': 'آلمان' }
  },
  {
    id: 'p10', name: 'فیلتر کابین کربن‌اکتیو پراید و تیبا', brand: 'VALEO', sku: 'CAB-VL-1102',
    oem: '6447.G2', quality: 'after', engines: [],
    cat: 'filter', price: 340000, oldPrice: 395000,
    stock: 25, rating: 4.4, reviews: 33, img: 'p-airfilter.jpg', featured: false,
    fitmentKnown: true, verified: true, equivalents: ['6447.G2'],
    kit: ['p11'], kitNote: 'همراه سرویس روغن تعویض شود.',
    fits: [
      { model: 'pride', from: 1380, to: 1398, status: 'fit' },
      { model: 'tiba', from: 1387, to: 1400, status: 'fit' },
      { model: 'saina', from: 1394, to: 1403, status: 'fit' },
      { model: 'quick', from: 1397, to: 1403, status: 'fit' }
    ],
    desc: 'فیلتر کابین با لایهٔ کربن فعال برای کاهش بو، گرده و ذرات معلق هوای ورودی به کابین.',
    specs: { 'نوع': 'کربن‌اکتیو', 'ابعاد': '۲۰۵×۱۹۰ میلی‌متر', 'کد فنی': '6447.G2', 'کشور سازنده': 'فرانسه' }
  },
  {
    id: 'p11', name: 'روغن موتور تمام سنتتیک ۵W-30', brand: 'TOTAL', sku: 'OIL-TT-5W30',
    oem: '—', quality: 'orig', engines: [],
    cat: 'filter', price: 2150000, oldPrice: 2400000,
    stock: 60, rating: 4.8, reviews: 147, img: 'p-oilfilter.jpg', featured: false,
    fitmentKnown: false, verified: false, universal: true,
    universalNote: 'گرید را با دفترچهٔ خودرو تطبیق دهید؛ برای موتورهای بنزینی مدرن مناسب است.',
    equivalents: [], kit: [], fits: [],
    desc: 'روغن موتور تمام سنتتیک با ویسکوزیتهٔ ۵W-30، مناسب موتورهای بنزینی مدرن و شرایط ترافیک شهری.',
    specs: { 'گرید': '۵W-30', 'نوع': 'تمام سنتتیک', 'حجم': '۴ لیتر', 'استاندارد': 'API SN', 'کشور سازنده': 'فرانسه' }
  },
  {
    id: 'p12', name: 'کمک فنر عقب سمند و پارس', brand: 'MONROE', sku: 'SHK-MN-7721',
    oem: '5206.3E', quality: 'plan', engines: ['XU7'],
    cat: 'suspension', price: 3200000, oldPrice: 3600000,
    stock: 3, rating: 4.4, reviews: 12, img: 'p-shock.jpg', featured: false,
    fitmentKnown: true, verified: true, equivalents: ['5206.3E'],
    kit: [], fits: [
      { model: 'samand', from: 1381, to: 1402, status: 'fit' },
      { model: 'pars', from: 1382, to: 1403, status: 'fit' },
      { model: 'dena', from: 1393, to: 1403, status: 'fit' }
    ],
    desc: 'کمک فنر عقب روغنی با میراکنندگی متعادل برای بار و سرنشین معمول. نصب جفتی توصیه می‌شود.',
    specs: { 'نوع': 'روغنی', 'محور': 'عقب', 'نصب': 'جفتی توصیه می‌شود', 'کد فنی': '5206.3E', 'کشور سازنده': 'بلژیک' }
  },
  {
    id: 'p13', name: 'روغن ترمز DOT4 (۵۰۰ میلی‌لیتر)', brand: 'BOSCH', sku: 'FLU-BS-DOT4',
    oem: '1 987 479 108', quality: 'after', engines: [],
    cat: 'brake', price: 290000, oldPrice: 0,
    stock: 42, rating: 4.7, reviews: 41, img: 'p-brakefluid.jpg', featured: true,
    fitmentKnown: false, verified: false, universal: true,
    universalNote: 'برای اکثر خودروها؛ نوع DOT را با دفترچهٔ خودرو تطبیق دهید.',
    equivalents: ['1 987 479 108', 'DOT4-500'], kit: ['p1', 'p9'], kitNote: 'همراه تعویض لنت/دیسک، روغن ترمز را نیز بررسی کنید.',
    fits: [],
    desc: 'روغن ترمز با نقطهٔ جوش بالا و جذب رطوبت کنترل‌شده؛ مناسب سیستم‌های ABS. هر دو سال یا پس از بازکردن سیستم تعویض شود.',
    specs: { 'استاندارد': 'DOT 4', 'حجم': '۵۰۰ میلی‌لیتر', 'نقطه جوش': '۲۶۰ درجه', 'کد فنی': '1 987 479 108', 'کشور سازنده': 'آلمان' }
  }
];

const GUIDES = [
  { icon: 'car', tag: 'راهنمای خرید', title: 'چطور خودروی خود را درست انتخاب کنیم؟', text: 'تفاوت مدل، سال ساخت و تیپ موتور چه تأثیری بر سازگاری قطعه دارد.', meta: '۶ دقیقه مطالعه' },
  { icon: 'search', tag: 'کد فنی', title: 'کد OEM چیست و کجای قطعه نوشته شده؟', text: 'راهنمای پیدا کردن شمارهٔ فنی روی قطعه، بسته‌بندی و کارت گارانتی.', meta: '۴ دقیقه مطالعه' },
  { icon: 'shield', tag: 'اطمینان', title: 'وقتی سازگاری ثبت نشده باشد چه کنیم؟', text: 'معنی نشان‌های سازگاری در پارت‌یاب و راه درست استعلام پیش از خرید.', meta: '۳ دقیقه مطالعه' }
];
