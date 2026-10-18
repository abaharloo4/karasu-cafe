<?php

namespace Database\Seeders;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class RealMenuAndSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $cafe = Cafe::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'بی کافه شیراز (B Cafe)',
                'address' => 'شیراز، بلوار ارم، نبش کوچه ۱۵',
                'phone' => '07136280000',
            ]
        );

        // Clean up empty or obsolete categories
        Category::where('cafe_id', $cafe->id)->where('name', 'سایر موارد')->delete();
        SubscriptionPlan::where('cafe_id', $cafe->id)->whereNotIn('name', [
            'اشتراک روزانه اسپرسو بار (۳۰ روزه)',
            'اشتراک قهوه شیری و لاته (۳۰ روزه)',
            'اشتراک باریستا پریمیوم (۶۰ روزه)',
            'اشتراک VIP سلطنتی بی کافه (۱۰۰ روزه)',
        ])->delete();

        $categoryNames = [
            'hot_drinks' => 'قهوه و نوشیدنی‌های گرم',
            'cold_drinks' => 'نوشیدنی‌های سرد و شیک',
            'pastries' => 'کیک و دسر',
            'retail' => 'قهوه تخصصی و اقلام همراه',
        ];

        $categoryModels = [];
        foreach ($categoryNames as $key => $catName) {
            $categoryModels[$key] = Category::updateOrCreate(
                ['cafe_id' => $cafe->id, 'name' => $catName],
                [
                    'status' => 'active',
                ]
            );
        }

        // 2. Products Data
        $products = [
            // Category: قهوه و نوشیدنی‌های گرم
            [
                'cat' => 'hot_drinks',
                'name' => 'اسپرسو سینگل',
                'type' => 'coffee',
                'price' => 55000,
                'prep_time' => 5,
                'tasting_notes' => 'آجیلی، کرمای طلایی مخملی، اسیدیته ملایم',
                'bean_type' => '۱۰۰٪ عربیکا کلمبیا سوپریمو',
                'serving_type' => 'سرو داخل فنجان سرامیکی / لیوان بیرون‌بر دوجداره',
                'description' => 'عصاره‌گیری استاندارد ۳۰ میلی‌لیتر از دانه‌های تازه برشته‌شده ۱۰۰٪ عربیکا با بادی غنی، اسیدیته ملایم و کرمای طلایی مخملی.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'اسپرسو دابل',
                'type' => 'coffee',
                'price' => 75000,
                'prep_time' => 5,
                'tasting_notes' => 'کاکائو تلخ، کاراملی، بادی پر و سنگین',
                'bean_type' => 'بلِند اختصاصی بی کافه (۸۰/۲۰)',
                'serving_type' => 'سرو دابل شات ۶۰ میلی‌لیتر',
                'description' => 'دو شات اسپرسو خالص (۶۰ میلی‌لیتر) با طعم‌یاد کاکائو تلخ و آجیلی، ایده‌آل برای شروع پرانرژی روز.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'آمریکانو اصیل',
                'type' => 'coffee',
                'price' => 85000,
                'prep_time' => 6,
                'tasting_notes' => 'شکلات تلخ، معطر و سبک',
                'bean_type' => '۱۰۰٪ عربیکا کلمبیا',
                'serving_type' => '۲۵۰ میلی‌لیتر فنجان بزرگ',
                'description' => 'ترکیب دابل اسپرسو تخصصی همراه با آب جوش تصفیه‌شده، طعمی سبک‌تر با حفظ کامل رایحه اصیل قهوه.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'لاته آرت تخصصی',
                'type' => 'coffee',
                'price' => 98000,
                'prep_time' => 7,
                'tasting_notes' => 'شیرین طبیعی، وانیلی، مخملی',
                'bean_type' => 'عربیکا گواتمالا آنتیگوا',
                'serving_type' => '۳۰۰ میلی‌لیتر فنجان لاته سرامیکی',
                'description' => 'ترکیب اسپرسو تک‌خاستگاه با شیر بخاردیده تازه و فوم نازک ابریشمی به همراه طراحی لته‌آرت حرفه‌ای باریستا.',
                'addons' => [
                    ['name' => 'شات اسپرسو اضافه (Extra Shot)', 'price' => 30000],
                    ['name' => 'سیروپ وانیل ماداگاسکار', 'price' => 25000],
                    ['name' => 'سیروپ فندق تفت‌داده', 'price' => 25000],
                    ['name' => 'شیر جو دوسر گیاهی (Oat Milk)', 'price' => 25000],
                ],
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'کاپوچینو ایتالیایی',
                'type' => 'coffee',
                'price' => 95000,
                'prep_time' => 7,
                'tasting_notes' => 'فوم خامه‌ای متراکم، تلخی متعادل با گرد دارچین',
                'bean_type' => 'بلِند اختصاصی اسپرسو',
                'serving_type' => '۲۲۰ میلی‌لیتر فنجان کلاسیک ایتالیایی',
                'description' => 'تعادل کلاسیک یک سوم اسپرسو، یک سوم شیر گرم و یک سوم میکروفوم غلیظ و خامه‌ای به همراه پودر دارچین یا کاکائو.',
                'addons' => [
                    ['name' => 'شات اسپرسو اضافه', 'price' => 30000],
                    ['name' => 'سیروپ دارچین سیلان', 'price' => 20000],
                ],
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'کارامل ماکیاتو',
                'type' => 'coffee',
                'price' => 110000,
                'prep_time' => 8,
                'tasting_notes' => 'شیرینی کارامل دست‌ساز، وانیل ماداگاسکار و اسپرسو تیره',
                'bean_type' => 'بلِند مدیوم رست عربیکا',
                'serving_type' => 'لیوان شیشه‌ای لایه‌ای ۳۵۰ میلی‌لیتر',
                'description' => 'لایه‌های شیر داغ فوم‌دار با سیروپ وانیل خانگی، شات اسپرسوی غلیظ و پوشش سخاوتمندانه سس کارامل دست‌ساز بی کافه.',
                'addons' => [
                    ['name' => 'شات اسپرسو اضافه', 'price' => 30000],
                    ['name' => 'سس کارامل دوبل اضافه', 'price' => 20000],
                    ['name' => 'شیر جو دوسر گیاهی (Oat Milk)', 'price' => 25000],
                ],
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'فلت وایت استرالیایی',
                'type' => 'coffee',
                'price' => 105000,
                'prep_time' => 7,
                'tasting_notes' => 'قوی‌تر از لاته، کرمی و بسیار نرم',
                'bean_type' => 'دابل ریسترتو ۱۰۰٪ عربیکا',
                'serving_type' => '۲۰۰ میلی‌لیتر فنجان سرامیکی تخت',
                'description' => 'دو شات ریسترتو متمرکز با لایه‌ای ابریشمی از میکروفوم داغ بدون حباب، برای عاشقان طعم اصیل و قوی قهوه با شیر.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'کورتادو کلاسیک',
                'type' => 'coffee',
                'price' => 90000,
                'prep_time' => 6,
                'tasting_notes' => 'تعادل دقیق ۵۰/۵۰ اسپرسو و شیر داغ',
                'bean_type' => 'عربیکا برزیل سرادو',
                'serving_type' => 'گیلاس شیشه‌ای ژیبرالتار ۱۳۰ میلی‌لیتر',
                'description' => 'نسبت برابر شات دابل اسپرسو و شیر ملایم بخاردیده که اسیدیته قهوه را به زیباترین شکل ممکن تلطیف می‌کند.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'موکا شکلاتی بلژیکی',
                'type' => 'coffee',
                'price' => 115000,
                'prep_time' => 8,
                'tasting_notes' => 'شکلات ۷۰٪ ذوب‌شده، خامه و اسپرسو عمیق',
                'bean_type' => 'بلِند عربیکا اسپرسو',
                'serving_type' => '۳۰۰ میلی‌لیتر ماگ سرامیکی',
                'description' => 'ترکیب بی‌نظیر شکلات تلخ بلژیکی ذوب‌شده در بار با شیر تازه بخاردیده و اسپرسوی دوبل با تزئین پودر کاکائوی هلندی.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'هات چاکلت غلیظ فرانسوی',
                'type' => 'other',
                'price' => 100000,
                'prep_time' => 8,
                'tasting_notes' => 'گاناش کاکائو، بسیار غلیظ، شیرین و آرامش‌بخش',
                'bean_type' => 'شکلات تلخ و شیری دست‌ساز',
                'serving_type' => '۲۵۰ میلی‌لیتر فنجان گرم با خامه زده‌شده',
                'description' => 'هات چاکلت با قوام غلیظ به سبک پاریسی، تهیه شده از قطعات واقعی شکلات دست‌ساز بدون افزودنی نشاسته صنعتی.',
            ],
            [
                'cat' => 'hot_drinks',
                'name' => 'چای ماسالا اعلای هندی',
                'type' => 'tea',
                'price' => 85000,
                'prep_time' => 7,
                'tasting_notes' => 'هل سبز، چوب دارچین سیلان، زنجبیل تازه و میخک',
                'bean_type' => 'چای آسام با شیر جوشیده و عسل طبیعی',
                'serving_type' => '۲۸۰ میلی‌لیتر لیوان دوجداره',
                'description' => 'دم‌آوری سنتی چای آسام سیاه با ادویه‌های کوبیده تازه و شیر تازه پرچرب، گرمابخش و تقویت‌کننده سیستم ایمنی.',
            ],

            // Category: نوشیدنی‌های سرد و شیک
            [
                'cat' => 'cold_drinks',
                'name' => 'آیس آمریکانو شیراز',
                'type' => 'coffee',
                'price' => 90000,
                'prep_time' => 5,
                'tasting_notes' => 'خنک، طراوت‌بخش، میوه‌ای و کاکائویی',
                'bean_type' => '۱۰۰٪ عربیکا کلمبیا',
                'serving_type' => 'لیوان ۴۰۰ میلی‌لیتر با تکه‌های یخ کریستالی',
                'description' => 'دابل اسپرسو داغ که مستقیماً روی کوهی از یخ‌های قالبی شفاف و آب معدنی سرد ریخته می‌شود، بسیار سبک و نشاط‌آور.',
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'آیس لاته کاراملی',
                'type' => 'coffee',
                'price' => 108000,
                'prep_time' => 6,
                'tasting_notes' => 'شیر سرد تازه، طعم‌یاد کارامل نمکی و اسپرسو',
                'bean_type' => 'بلِند عربیکا اسپرسو',
                'serving_type' => 'لیوان بلند ۴۰۰ میلی‌لیتر',
                'description' => 'شیر تازه خنک همراه یخ قالبی، سیروپ کارامل دست‌ساز و شات اسپرسوی داغ که به آرامی روی لایه‌ها شناور می‌شود.',
                'addons' => [
                    ['name' => 'شات اسپرسو اضافه', 'price' => 30000],
                    ['name' => 'سیروپ کارامل نمکی اضافه', 'price' => 25000],
                    ['name' => 'سیروپ فندق تفت‌داده', 'price' => 25000],
                    ['name' => 'شیر جو دوسر گیاهی (Oat Milk)', 'price' => 25000],
                    ['name' => 'خامه فرم‌گرفته قنادی', 'price' => 15000],
                ],
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'کلدبرو تخصصی کلمبیا (دم‌آوری سرد)',
                'type' => 'coffee',
                'price' => 125000,
                'prep_time' => 3,
                'tasting_notes' => 'نوت‌های انگور سیاه، مرکبات ملس و شیرینی ماندگار',
                'bean_type' => '۱۰۰٪ عربیکا کلمبیا گیشا تخصصی',
                'serving_type' => 'بطری شیشه‌ای ویژه بی کافه با یخ توپی',
                'description' => '۱۶ ساعت دم‌آوری قطره‌ای آهسته با آب سرد و یخ، اسیدیته بسیار پایین و کافئین ملایم با حداکثر شفافیت طعمی.',
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'موکا فراپاچینو بی کافه',
                'type' => 'coffee',
                'price' => 135000,
                'prep_time' => 8,
                'tasting_notes' => 'اسپرسو بِلِند با شکلات، یخ خردشده و خامه فرم‌گرفته',
                'bean_type' => 'دابل شات اسپرسو و گاناش بلژیکی',
                'serving_type' => 'لیوان اسموتی ۴۵۰ میلی‌لیتر با سس شکلات',
                'description' => 'نوشیدنی اسموتی پرطرفدار از ترکیب اسپرسوی غلیظ، شیر، شکلات تیره، سس موکا و یخ ریز خرد شده همراه با خامه تازه قنادی.',
                'addons' => [
                    ['name' => 'شات اسپرسو اضافه', 'price' => 30000],
                    ['name' => 'خامه فرم‌گرفته اضافه', 'price' => 15000],
                    ['name' => 'سس شکلات بلژیکی دوبل', 'price' => 20000],
                ],
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'شیک نوتلا فندقی ایتالیایی',
                'type' => 'other',
                'price' => 145000,
                'prep_time' => 8,
                'tasting_notes' => 'نوتلا اصل ایتالیا، بستنی خامه و مغز فندق کرانچی',
                'bean_type' => 'بستنی خامه‌ای درجه یک با شیر پرچرب',
                'serving_type' => 'لیوان شیک ۴۰۰ میلی‌لیتر با دیواره نوتلایی',
                'description' => 'شیک فوق‌العاده متراکم و خامه‌ای از ترکیب بستنی وانیلی خالص، دو قاشق پر نوتلای اصل و خرده‌های فندق تُست‌شده داغ.',
                'addons' => [
                    ['name' => 'شات اسپرسو (افوگاتو استایل)', 'price' => 30000],
                    ['name' => 'مغز فندق و بادام تفت‌داده اضافه', 'price' => 25000],
                    ['name' => 'خامه قنادی اضافه', 'price' => 15000],
                ],
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'شیک لوتوس بلژیکی کارامل',
                'type' => 'other',
                'price' => 150000,
                'prep_time' => 8,
                'tasting_notes' => 'کرم بیسکویت لوتوس بیسکوف، عطر دارچین کاراملی',
                'bean_type' => 'بیسکویت و کرم اورجینال لوتوس بلژیک',
                'serving_type' => 'لیوان شیک با لبه کرانچ لوتوس',
                'description' => 'شیک بسیار محبوب و پرطرفدار بی کافه تهیه شده از کره بیسکویت لوتوس اصل بلژیک، بستنی سنتی خامه‌ای و سس تافی دست‌ساز.',
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'موهیتو نعناع و لیمو شیرازی طبیعی',
                'type' => 'other',
                'price' => 89000,
                'prep_time' => 6,
                'tasting_notes' => 'طراوت نعناع باغی شیراز، لیموترش تازه و سودا گازدار',
                'bean_type' => 'صددرصد طبیعی، بدون اسانس شیمیایی',
                'serving_type' => 'ماگ شیشه‌ای ۴۰۰ میلی‌لیتر یخ‌زده',
                'description' => 'برگ‌های تازه و شسته نعناع باغی کوبیده‌شده با آب لیمو ترش تازه شیراز، شربت نعناع خانگی و آب گازدار سودای خنک.',
            ],
            [
                'cat' => 'cold_drinks',
                'name' => 'اسموتی بری میکس جنگلی',
                'type' => 'other',
                'price' => 115000,
                'prep_time' => 7,
                'tasting_notes' => 'بلوبری، شاتوت، تمشک قرمز و یخ میوه‌ای',
                'bean_type' => 'میوه‌های یخ‌زده طبیعی غنی از آنتی‌اکسیدان',
                'serving_type' => 'لیوان اسموتی بلند با میوه کامل تزئینی',
                'description' => 'انفجار ویتامین و انرژی طبیعی از ترکیب بلوبری وحشی، شاتوت، تمشک و سس آلبالوی ترش بدون شکر مصنوعی اضافه.',
            ],

            // Category: کیک و دسر
            [
                'cat' => 'pastries',
                'name' => 'چیزکیک سن سباستین با سس شکلات',
                'type' => 'pastry',
                'price' => 140000,
                'prep_time' => 4,
                'tasting_notes' => 'پوسته کاراملیزه سوخته، بافت میانی ابریشمی و نرم',
                'bean_type' => 'پنیر ماسکارپونه و خامه اعلای حیوانی',
                'serving_type' => 'یک اسلایس بزرگ به همراه شات سس شکلات داغ',
                'description' => 'چیزکیک مشهور باسکی سن سباستین با رویه برشته و داخل کاملاً کرمی و ذوب‌شونده، سرو شده با گاناش گرم شکلات تلخ.',
            ],
            [
                'cat' => 'pastries',
                'name' => 'چیزکیک نیویورکی با کرم لوتوس',
                'type' => 'pastry',
                'price' => 145000,
                'prep_time' => 4,
                'tasting_notes' => 'کراست بیسکویتی ترد، پنیر غنی و لایه لوتوس گرم',
                'bean_type' => 'کرم بیسکویت لوتوس بلژیک',
                'serving_type' => 'یک اسلایس در بشقاب سرامیکی',
                'description' => 'پایه بیسکویت کره‌ای با لایه فشرده و غنی پنیر خامه نیویورکی که با یک لایه سخاوتمندانه کرم لوتوس تزئین شده است.',
            ],
            [
                'cat' => 'pastries',
                'name' => 'کرواسان کره‌ای فرانسوی پخت روز',
                'type' => 'pastry',
                'price' => 78000,
                'prep_time' => 3,
                'tasting_notes' => 'کره حیوانی فرانسوی، هزارلا، بسیار ترد و توخالی',
                'bean_type' => 'آرد غنی فرانسوی و کره طبیعی ۸۲٪',
                'serving_type' => 'سرو گرم با مربای توت‌فرنگی یا کره عسل',
                'description' => 'پخت روزانه هر روز صبح در آشپزخانه بی کافه با بوی اشتهاآور کره، پوسته فوق‌العاده برشته و بافت هزارلای طلایی سبک.',
            ],
            [
                'cat' => 'pastries',
                'name' => 'کرواسان نوتلا و موز تازه',
                'type' => 'pastry',
                'price' => 98000,
                'prep_time' => 5,
                'tasting_notes' => 'نوتلا ذوب‌شده، حلقه‌های موز خنک و پودر قند',
                'bean_type' => 'کرواسان روزانه و شکلات فندقی نوتلا',
                'serving_type' => 'سرو گرم تافی شکلاتی',
                'description' => 'کرواسان کره‌ای پرشده از شکلات نوتلای گرم و اسلایس‌های تازه موز که با پودر پسته و قند الک‌شده پوشش داده شده است.',
            ],
            [
                'cat' => 'pastries',
                'name' => 'کیک هویج و گردوی خانگی بی کافه',
                'type' => 'pastry',
                'price' => 95000,
                'prep_time' => 3,
                'tasting_notes' => 'دارچین، جوز هندی، مغز گردوی تازه و فراستینگ پنیری',
                'bean_type' => 'کیک سنتی با آرد سبوس‌دار و هویج تازه',
                'serving_type' => 'یک اسلایس سخاوتمندانه با مغز گردوی درسته',
                'description' => 'دستور پخت اختصاصی بی کافه با بافت مرطوب و نرم، گردوی فراوان تویسرکان و کرم پنیر خامه‌ای ترش و شیرین متعادل.',
            ],
            [
                'cat' => 'pastries',
                'name' => 'کوکی دابل چاکلت داغ',
                'type' => 'pastry',
                'price' => 68000,
                'prep_time' => 5,
                'tasting_notes' => 'شکلات چیپسی بلژیکی، مغز شکلاتی روان، لبه‌های ترد',
                'bean_type' => 'شکلات ۵۵٪ و ۷۰٪ با شکر قهوه‌ای',
                'serving_type' => 'کوکی بزرگ ۱۲۰ گرمی سرو شده گرم',
                'description' => 'کوکی غول‌پیکر تازه پخت شده با بافت بیرونی کرانچی و مرکز نرم و مرطوب همراه با تکه‌های شکلات تلخ ذوب‌شده.',
            ],

            // Category: قهوه تخصصی و اقلام همراه
            [
                'cat' => 'retail',
                'name' => 'دانه قهوه ۲۵۰ گرمی بلِند اختصاصی بی کافه',
                'type' => 'retail',
                'price' => 340000,
                'prep_time' => 2,
                'tasting_notes' => 'شکلاتی، فندقی، بادی متراکم با اسیدیته پایین',
                'bean_type' => '۸۰٪ عربیکا برزیل/کلمبیا و ۲۰٪ روبوستا اوگاندا',
                'serving_type' => 'بسته زیپ‌کیپ سوپاپ‌دار ۲۵۰ گرمی با امکان آسیاب دلخواه',
                'description' => 'بهترین ترکیب برای اسپرسوساز خانگی، موکاپات و فرنچ‌پرس با کرمای پایدار و رست تازه هفتگی در کارگاه بی کافه.',
            ],
            [
                'cat' => 'retail',
                'name' => 'دانه قهوه تک‌خاستگاه اتیوپی یرگاچف ۲۵۰ گرمی',
                'type' => 'retail',
                'price' => 430000,
                'prep_time' => 2,
                'tasting_notes' => 'شکوفه پرتقال، یاسمن، لیمو و توت‌فرنگی وحشی',
                'bean_type' => '۱۰۰٪ عربیکا تخصصی (اسپیشالتی امتیاز ۸۸+)',
                'serving_type' => 'بسته تخصصی کرافت سوپاپ‌دار',
                'description' => 'قهوه تخصصی فراوری طبیعی از خاستگاه افسانه‌ای یرگاچف اتیوپی، ایده‌آل برای دم‌آوری V60، کمکس و ائروپرس با طعم میوه‌ای شگفت‌انگیز.',
            ],
            [
                'cat' => 'retail',
                'name' => 'سیروپ کارامل نمکی خانگی ۲۵۰ میلی‌لیتر',
                'type' => 'retail',
                'price' => 125000,
                'prep_time' => 2,
                'tasting_notes' => 'کره ذوب‌شده، شکر سوخته و نمک دریایی تصفیه‌شده',
                'bean_type' => 'عصاره کاملاً دست‌ساز بدون طعم‌دهنده سنتتیک',
                'serving_type' => 'شیشه قطره‌چکانی ۲۵۰ میلی‌لیتری',
                'description' => 'سیروپ دست‌ساز باریستاهای بی کافه برای استفاده خانگی در لاته، بستنی، پنکیک و وافل با طعمی متمایز و ماندگار.',
            ],
            [
                'cat' => 'retail',
                'name' => 'شات اضافه اسپرسو تخصصی (Extra Shot)',
                'type' => 'addon',
                'price' => 25000,
                'prep_time' => 2,
                'tasting_notes' => 'عصاره‌گیری تازه برای غلیظ‌تر کردن نوشیدنی شما',
                'bean_type' => '۱۰۰٪ عربیکا',
                'serving_type' => 'افزوده به نوشیدنی انتخابی',
                'description' => 'یک شات عصاره‌گیری اضافی اسپرسو جهت بالا بردن انرژی و دوز کافئین نوشیدنی مورد نظر شما.',
            ],
        ];

        foreach ($products as $p) {
            $cat = $categoryModels[$p['cat']];
            Product::updateOrCreate(
                ['cafe_id' => $cafe->id, 'name' => $p['name']],
                [
                    'category_id' => $cat->id,
                    'type' => $p['type'],
                    'price' => $p['price'],
                    'prep_time' => $p['prep_time'],
                    'tasting_notes' => $p['tasting_notes'],
                    'bean_type' => $p['bean_type'],
                    'serving_type' => $p['serving_type'],
                    'description' => $p['description'],
                    'addons' => $p['addons'] ?? null,
                    'status' => 'active',
                ]
            );
        }

        // 3. Subscription Plans (Authentic Shiraz Cafe Subscription Models)
        $plans = [
            [
                'name' => 'اشتراک روزانه اسپرسو بار (۳۰ روزه)',
                'total_items' => 30,
                'duration_days' => 30,
                'price' => 1350000,
                'allowed_names' => ['اسپرسو سینگل', 'اسپرسو دابل', 'آمریکانو اصیل', 'آیس آمریکانو شیراز'],
            ],
            [
                'name' => 'اشتراک قهوه شیری و لاته (۳۰ روزه)',
                'total_items' => 30,
                'duration_days' => 30,
                'price' => 2100000,
                'allowed_names' => ['لاته آرت تخصصی', 'کاپوچینو ایتالیایی', 'فلت وایت استرالیایی', 'کورتادو کلاسیک', 'کارامل ماکیاتو'],
            ],
            [
                'name' => 'اشتراک باریستا پریمیوم (۶۰ روزه)',
                'total_items' => 60,
                'duration_days' => 60,
                'price' => 3900000,
                'allowed_names' => [
                    'اسپرسو سینگل', 'اسپرسو دابل', 'آمریکانو اصیل', 'آیس آمریکانو شیراز',
                    'لاته آرت تخصصی', 'کاپوچینو ایتالیایی', 'کلدبرو تخصصی کلمبیا (دم‌آوری سرد)',
                    'موکا فراپاچینو بی کافه', 'موکا شکلاتی بلژیکی'
                ],
            ],
            [
                'name' => 'اشتراک VIP سلطنتی بی کافه (۱۰۰ روزه)',
                'total_items' => 100,
                'duration_days' => 100,
                'price' => 5900000,
                'allowed_names' => [
                    'اسپرسو سینگل', 'اسپرسو دابل', 'آمریکانو اصیل', 'آیس آمریکانو شیراز',
                    'لاته آرت تخصصی', 'کاپوچینو ایتالیایی', 'کارامل ماکیاتو', 'فلت وایت استرالیایی',
                    'کلدبرو تخصصی کلمبیا (دم‌آوری سرد)', 'موکا فراپاچینو بی کافه', 'شیک نوتلا فندقی ایتالیایی'
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::updateOrCreate(
                ['cafe_id' => $cafe->id, 'name' => $planData['name']],
                [
                    'total_items' => $planData['total_items'],
                    'duration_days' => $planData['duration_days'],
                    'price' => $planData['price'],
                    'status' => 'active',
                ]
            );

            $productIds = Product::whereIn('name', $planData['allowed_names'])->pluck('id');
            if ($productIds->isNotEmpty()) {
                $plan->products()->sync($productIds);
            }
        }

        // 4. Authentic Test Customer with Subscription & Tier
        $customer = \App\Models\Customer::updateOrCreate(
            ['phone' => '09171234567'],
            [
                'cafe_id' => $cafe->id,
                'name' => 'امیرحسین رضایی',
                'email' => 'amir@bcafe.ir',
                'address' => 'شیراز، خیابان ارم، کوچه ۱۲، پلاک ۴',
                'birthdate' => '1996-05-15',
                'tier_id' => 2, // نقره‌ای
                'total_spent' => 3450000,
                'wallet_balance' => 85000,
            ]
        );

        // Ensure active subscription for customer
        $samplePlan = \App\Models\SubscriptionPlan::where('name', 'اشتراک روزانه اسپرسو بار (۳۰ روزه)')->first();
        if ($samplePlan) {
            \App\Models\Subscription::updateOrCreate(
                ['customer_id' => $customer->id, 'status' => 'active'],
                [
                    'cafe_id' => $cafe->id,
                    'subscription_plan_id' => $samplePlan->id,
                    'total_items' => 30,
                    'remaining_items' => 22,
                    'start_date' => now()->subDays(8)->toDateString(),
                    'expire_date' => now()->addDays(22)->toDateString(),
                    'status' => 'active',
                ]
            );
        }

        // Add a recent order for this customer
        \App\Models\Order::firstOrCreate(
            ['order_number' => 'BC-SHZ-7821'],
            [
                'cafe_id' => $cafe->id,
                'customer_id' => $customer->id,
                'type' => 'normal',
                'order_type' => 'courier',
                'status' => 'processing',
                'payment_method' => 'zarinpal',
                'total_price' => 235000,
                'discount_amount' => 0,
                'delivery_fee' => 25000,
                'final_payable' => 260000,
            ]
        );
    }
}
