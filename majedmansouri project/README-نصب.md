# Majed Hub — فروشگاه لباس + کافه با PHP/MySQL

این پروژه جایگزین WordPress می‌شود و در روت دامنه اجرا می‌گردد:

- `/` پورتفولیوی شخصی Majed Mansouri و معرفی مهارت‌ها
- `/shop` فروشگاه لباس مستقل
- `/cafe` منوی کامل کافه مستقل
- `/admin/login` پنل مدیریت مشترک

## قبل از نصب

1. از WordPress فعلی بکاپ بگیر.
2. در cPanel یک Database و User بساز و به User دسترسی `ALL PRIVILEGES` بده.
3. فایل ZIP را در Document Root دامنه (معمولاً `public_html`) Upload و Extract کن.
4. فایل‌ها باید مستقیماً در Root باشند؛ نه داخل پوشهٔ اضافه.

## نصب یک‌مرحله‌ای

بعد از Upload، این لینک را باز کن:

```text
https://majedmansouri.ir/setup/?token=rJ4pyUPX1fr-ExTlnQsJjAB-FzkrQx2Q
```

فرم Database و Admin را پر کن. Installer این کارها را انجام می‌دهد:

- نوشتن `config.php`
- ساخت تمام جدول‌ها
- ساخت Admin
- ساخت محصولات دمو فروشگاه
- ساخت دسته‌بندی و آیتم‌های دمو کافه
- قفل‌کردن Installer برای امنیت

## مسیرها

```text
https://majedmansouri.ir/
https://majedmansouri.ir/shop
https://majedmansouri.ir/cafe
https://majedmansouri.ir/admin/login
```

## نکات امنیتی

- پس از نصب Installer قفل می‌شود.
- رمز Database را در هیچ صفحه یا چتی قرار نده.
- `config.php` و فایل‌های SQL از طریق `.htaccess` مسدود هستند.
- قبل از فروش واقعی، درگاه پرداخت، ارسال، قوانین مرجوعی و SSL را تنظیم کن.
