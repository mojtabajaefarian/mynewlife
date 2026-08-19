# ADR-004: CSRF نرم (Soft CSRF)

> **تاریخ:** 1405/05/28
> **وضعیت:** ✅ تصویب شده

---

## زمینه

CSRF protection نیاز است، ولی سیستم strict ممکن است UX را خراب کند.

## تصمیم

استفاده از **Soft CSRF**:

- توکن در session ذخیره می‌شود
- هر request باید توکن داشته باشد
- اگر توکن نامعتبر، فقط warning (نه block)
- مگر در موارد حساس (login, delete)

## پیاده‌سازی

```php
// تولید
function generate_csrf() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// بررسی
function verify_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

پایان ADR-004
```
