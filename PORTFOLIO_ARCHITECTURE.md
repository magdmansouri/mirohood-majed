# معماری پورتفولیوی ماجد منصوری

## بررسی ساختار موجود

این پروژه یک اپلیکیشن PHP با router مرکزی در `index.php` است، نه Next.js. بنابراین ساختار پیشنهادی `app/` به‌صورت معادل و سازگار با پروژه پیاده‌سازی شده است:

- `config/demos.php`: داده‌های مستقل هر دمو
- `includes/DemoRenderer.php`: renderer مشترک و قابل استفاده مجدد
- `views/portfolio.php`: صفحه اصلی پورتفولیو
- `views/demo.php`: قالب مشترک تمام دموها
- `assets/css/portfolio.css`: design system مشترک
- `assets/js/portfolio.js`: تعاملات مشترک و Guide Mode

## Design System حفظ‌شده

سیستم موجود Mirohood این مشخصات را داشت و هسته بصری دموها بر همان پایه ساخته شد:

- پس‌زمینه تیره: `#0a0908`
- رنگ تاکیدی شامپاینی: `#c8a862` و `#a8893a`
- متن روشن: `#f4f1ea`، متن ثانویه: `#a49e95`
- کارت‌های تیره با border ظریف طلایی و radius نرم
- دکمه‌های pill شکل با gradient طلایی
- حرکت‌های نرم `cubic-bezier(.22,1,.36,1)` و hover با جابه‌جایی محدود
- حداکثر عرض محتوا در بازه ۱۱۸۰ تا ۱۲۸۰ پیکسل و responsive grid
- فونت فارسی Vazirmatn و فونت نمایشی Cormorant Garamond

## Componentهای مشترک

از آنجا که template engine پروژه PHP است، componentها به‌جای فایل‌های JSX به‌شکل بلوک‌های مشترک در `views/demo.php` و renderer پیاده‌سازی شده‌اند: Navbar، Hero، Image Placeholder، Section Heading، Item Card، CTA Form، FAQ، Footer و Guide Mode.

## مسیرها

`/`، `/shop`، `/cafe`، `/ticket`، `/company`، `/restaurant`، `/realestate`، `/hotel` و `/medical` توسط router مرکزی هندل می‌شوند. مسیرهای داخلی مانند `/shop/products`، `/shop/product/[id]`، `/shop/cart`، `/shop/checkout`، `/ticket/events` و `/ticket/event/[id]` نیز به همان renderer وصل هستند.

## تعاملات بدون Backend

سبد خرید فروشگاه با `localStorage` نگهداری می‌شود؛ افزودن کالا، شمارنده، حذف و وضعیت خالی قابل استفاده است. فرم‌ها پاسخ نمایشی فارسی دارند و Guide Mode از URL (`?guide=1`) یا دکمه header فعال می‌شود.
