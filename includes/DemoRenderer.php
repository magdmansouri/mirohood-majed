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
        $demo = $this->demos[$slug];
        $title = ($subpage ? $this->subpageTitle($subpage) . ' | ' : '') . 'نمونه طراحی ' . $demo['label'] . ' | مجید منصوری';
        $description = $demo['description'];
        require VIEWS_PATH . '/demo.php';
    }
    public function portfolio() { $demos = $this->demos; require VIEWS_PATH . '/portfolio.php'; }

    /** Public, read-only showcase of the management dashboard design. */
    public function dashboardDemo() { require VIEWS_PATH . '/dashboard-demo.php'; }

    private function subpageTitle($page) { return ['products'=>'محصولات','cart'=>'سبد خرید','checkout'=>'تکمیل سفارش','product'=>'جزئیات محصول','events'=>'رویدادها','event'=>'جزئیات رویداد','menu'=>'منو','gallery'=>'گالری','contact'=>'تماس با ما'][$page] ?? 'دمو'; }
}
