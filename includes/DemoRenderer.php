<?php
/** Reusable renderer for all portfolio demos; content lives in config/demos.php. */
class DemoRenderer
{
    private array $demos;
    public function __construct() { $this->demos = require BASE_PATH . '/config/demos.php'; }
    public function isDemo(string $slug): bool { return isset($this->demos[$slug]); }
    public function demo(string $slug, string $subpage = ''): void
    {
        if (!$this->isDemo($slug)) { http_response_code(404); return; }
        $demo = $this->demos[$slug];
        $title = ($subpage ? $this->subpageTitle($subpage) . ' | ' : '') . 'نمونه طراحی ' . $demo['label'] . ' | مجید منصوری';
        $description = $demo['description'];
        require VIEWS_PATH . '/demo.php';
    }
    public function portfolio(): void { $demos = $this->demos; require VIEWS_PATH . '/portfolio.php'; }
    private function subpageTitle(string $page): string { return ['products'=>'محصولات','cart'=>'سبد خرید','checkout'=>'تکمیل سفارش','product'=>'جزئیات محصول','events'=>'رویدادها','event'=>'جزئیات رویداد','menu'=>'منو','gallery'=>'گالری','contact'=>'تماس با ما'][$page] ?? 'دمو'; }
}
