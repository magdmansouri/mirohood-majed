<?php
/** Reusable renderer for all portfolio demos; content lives in config/demos.php. */
class DemoRenderer
{
    private $demos;
    public function __construct() { $this->demos = require BASE_PATH . '/config/demos.php'; }
    public function isDemo($slug) { return isset($this->demos[$slug]); }
    public function demo($slug, $subpage = '')
    {
        if (!$this->isDemo($slug)) { http_response_code(404); return; }
        if ($subpage === 'dashboard') { $this->industryDashboard($slug); return; }
        if ($subpage === 'case-study') { $this->caseStudy($slug); return; }
        $demo = $this->demos[$slug];
        $title = ($subpage ? $this->subpageTitle($subpage) . ' | ' : '') . 'نمونه طراحی ' . $demo['label'] . ' | ماجد منصوری';
        $description = $demo['description'];
        require VIEWS_PATH . '/demo.php';
    }

    public function caseStudy($slug) { if (!$this->isDemo($slug)) { http_response_code(404); return; } $demo=$this->demos[$slug]; require VIEWS_PATH . '/case-study.php'; }
    public function tool($tool) { $demos=$this->demos; require VIEWS_PATH . '/portfolio-tool.php'; }

    /** Public dashboard mock tailored to the operating model of each demo. */
    public function industryDashboard($slug)
    {
        if (!$this->isDemo($slug)) { http_response_code(404); return; }
        $profiles = [
            'shop' => ['title'=>'مدیریت فروشگاه آتریوم','eyebrow'=>'فروش و سفارش‌ها','accent'=>'#caa45f','metrics'=>[['فروش امروز','۲۸٬۴۵۰٬۰۰۰ تومان','+۱۸٪'],['سفارش جدید','۲۴','+۶ سفارش'],['سبد رهاشده','۷','نیازمند پیگیری']], 'queue'=>'سفارش‌های تازه','action'=>'ثبت محصول جدید','rows'=>[['آوا محمدی','کت شامپاینی آتریوم','۸٬۹۸۰٬۰۰۰','در حال بسته‌بندی'],['سام رستمی','شلوار نوآر','۲٬۴۵۰٬۰۰۰','پرداخت شده'],['نگین صادقی','پیراهن آبسیدین','۳٬۷۶۰٬۰۰۰','ارسال شده']]],
            'cafe' => ['title'=>'مدیریت کافه روبرو','eyebrow'=>'میزها و سفارش‌ها','accent'=>'#bd7650','metrics'=>[['فروش امروز','۱۲٬۸۰۰٬۰۰۰ تومان','+۹٪'],['رزرو امشب','۱۸ میز','۴ میز آزاد'],['موجودی حساس','۳ قلم','نیازمند خرید']], 'queue'=>'رزروهای امروز','action'=>'ثبت رزرو میز','rows'=>[['نسترن احمدی','میز ۴ · ۲ نفر','۱۹:۳۰','تأیید شده'],['آرین نادری','میز ۸ · ۴ نفر','۲۰:۱۵','در انتظار تماس'],['شراره کیانی','میز ۲ · ۲ نفر','۲۱:۰۰','تأیید شده']]],
            'ticket' => ['title'=>'مدیریت رویدادینو','eyebrow'=>'رویداد و فروش بلیت','accent'=>'#8d70df','metrics'=>[['فروش امروز','۳۶۰ بلیت','+۲۴٪'],['رویداد فعال','۸','۲ رویداد امروز'],['صندلی باقی‌مانده','۴۲','از ۷۲۰']], 'queue'=>'رویدادهای نزدیک','action'=>'ساخت رویداد','rows'=>[['کنسرت چارتار','برج میلاد','جمعه ۲۴ مرداد','۸۷٪ فروش'],['دشمن مردم','تئاتر شهر','پنج‌شنبه ۲۳ مرداد','۶۲٪ فروش'],['اکران شمال','پردیس کوروش','امشب','۷۴٪ فروش']]],
            'company' => ['title'=>'مدیریت نقطه‌نو','eyebrow'=>'سرنخ و پروژه‌ها','accent'=>'#639bc5','metrics'=>[['سرنخ تازه','۱۸','+۵ این هفته'],['پروژه در جریان','۶','۲ نزدیک تحویل'],['ارزش قرارداد','۸۴۰ میلیون','این فصل']], 'queue'=>'پیگیری‌های امروز','action'=>'ثبت سرنخ','rows'=>[['گروه آبان','جلسه شناخت','۱۰:۰۰','تأیید شده'],['فروشگاه پرنیان','ارسال پروپوزال','۱۲:۳۰','نیازمند پیگیری'],['استودیو شفق','بازبینی قرارداد','۱۵:۰۰','برنامه‌ریزی شده']]],
            'restaurant' => ['title'=>'مدیریت رستوران گندم','eyebrow'=>'سالن و آشپزخانه','accent'=>'#d59c4c','metrics'=>[['فروش امروز','۱۸٬۶۰۰٬۰۰۰ تومان','+۱۱٪'],['میزهای رزرو','۲۶','تا پایان شب'],['سفارش آشپزخانه','۹','۳ آماده تحویل']], 'queue'=>'سفارش‌های سالن','action'=>'ثبت رزرو','rows'=>[['میز ۱۲','۴ نفر · شام','۲۰:۰۰','در حال آماده‌سازی'],['میز ۵','۲ نفر · شام','۲۰:۱۵','تأیید شده'],['میز ۱۸','۶ نفر · مهمانی','۲۱:۰۰','در انتظار']]],
            'realestate' => ['title'=>'مدیریت خانه‌رو','eyebrow'=>'فایل‌ها و مشتری‌ها','accent'=>'#88ae7b','metrics'=>[['فایل فعال','۱۲۸','+۷ این هفته'],['درخواست بازدید','۳۶','امروز'],['سرنخ جدید','۱۴','نیازمند تماس']], 'queue'=>'بازدیدهای امروز','action'=>'ثبت فایل ملک','rows'=>[['آپارتمان زعفرانیه','بازدید با خانواده رضوی','۱۰:۳۰','تأیید شده'],['ویلای لواسان','بازدید با آقای راد','۱۳:۰۰','نیازمند تماس'],['دفتر ونک','بازدید سازمانی','۱۷:۳۰','برنامه‌ریزی شده']]],
            'hotel' => ['title'=>'مدیریت هتل آبان','eyebrow'=>'رزرو و اقامت','accent'=>'#6eb4b1','metrics'=>[['نرخ اشغال','۸۲٪','+۶٪'],['ورود امروز','۲۸ مهمان','۹ اتاق'],['خروج امروز','۱۹ مهمان','۷ اتاق']], 'queue'=>'ورودهای امروز','action'=>'رزرو جدید','rows'=>[['خانواده شریفی','سوئیت جونیور','۱۴:۰۰','ورود امروز'],['فرزاد صابری','اتاق دلوکس','۱۵:۰۰','پرداخت شده'],['مهتاب صالحی','اتاق باغ','۱۶:۳۰','در انتظار']]],
            'medical' => ['title'=>'مدیریت مرکز سپید','eyebrow'=>'نوبت و پذیرش','accent'=>'#dc8f9c','metrics'=>[['نوبت امروز','۴۸','۶ نوبت باقی'],['پذیرش‌شده','۳۲','تا این ساعت'],['پزشک فعال','۷','در ۴ تخصص']], 'queue'=>'نوبت‌های نزدیک','action'=>'ثبت نوبت','rows'=>[['نسترن حیدری','دکتر آوا فرهمند','۱۰:۳۰','پذیرش شده'],['پیمان فلاح','دکتر سامان راد','۱۱:۰۰','در انتظار پذیرش'],['الهام نوری','کلینیک خانواده','۱۱:۳۰','تأیید شده']]],
        ];
        $demo = $this->demos[$slug];
        $profile = $profiles[$slug];
        require VIEWS_PATH . '/industry-dashboard.php';
    }
    public function portfolio() { $demos = $this->demos; require VIEWS_PATH . '/portfolio.php'; }

    /** Public, read-only showcase of the management dashboard design. */
    public function dashboardDemo() { require VIEWS_PATH . '/dashboard-demo.php'; }

    private function subpageTitle($page) { return ['products'=>'محصولات','cart'=>'سبد خرید','checkout'=>'تکمیل سفارش','product'=>'جزئیات محصول','events'=>'رویدادها','event'=>'جزئیات رویداد','menu'=>'منو','gallery'=>'گالری','contact'=>'تماس با ما'][$page] ?? 'دمو'; }
}
