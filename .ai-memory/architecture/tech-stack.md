# 🛠️ فناوری‌ها و ابزارها

> **نسخه:** 1.0.0
> **آخرین بروزرسانی:** 1405/05/28

---

## ۱. Backend

| فناوری       | نسخه   | هدف             |
| ------------ | ------ | --------------- |
| **PHP**      | 7.4+   | زبان اصلی       |
| **JSON**     | -      | ذخیره‌سازی داده |
| **Sessions** | Native | احراز هویت      |
| **BCrypt**   | Native | رمزنگاری رمز    |

### توابع PHP استفاده‌شده

```php
// Date/Time
date_default_timezone_set('Asia/Tehran');
jdate() // شمسی (custom)

// Security
password_hash($password, PASSWORD_BCRYPT);
password_verify($input, $hash);
random_bytes(32);

// File I/O
file_get_contents()
file_put_contents()
flock()

// JSON
json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
json_decode($json, true)
```

۲. Frontend
فناوری
نسخه
هدف
HTML5

- ساختار
  CSS3
- ظاهر + RTL
  Vanilla JS
  ES6+
  منطق کلاینت
  Chart.js
  3.x
  نمودار
  Font Awesome
  6.x
  آیکون
  Vazir Font
  Latest
  فونت فارسی
  الگوهای JS استفاده‌شده

// Async/Await
async function loadData() {
const res = await fetch('?api=load');
return await res.json();
}

// Event Delegation
document.body.addEventListener('click', (e) => {
if (e.target.matches('.task')) { ... }
});

// LocalStorage
localStorage.setItem('cache', JSON.stringify(data));

// Template Literals
html = `<div class="task">${name}</div>`;
۳. Development Tools
ابزار
هدف
VS Code
ادیتور
Laragon
محیط توسعه محلی
Git
کنترل نسخه
GitHub
مخزن کد
GitHub Actions
CI/CD + Deploy
۴. Deployment
ابزار
هدف
cPanel
هاست اشتراکی
FTP
انتقال فایل
GitHub Actions
اتوماسیون deploy
Self-hosted Runner
روی سیستم محلی
۵. External Services
سرویس
هدف
Google Calendar
یادآوری (ICS)
CDN Fonts
فونت وزیر
CDN Chart.js
کتابخانه نمودار
۶. محدودیت‌های فناوری
محدودیت
توضیح
بدون SSH
deploy با FTP
بدون Database
JSON فایل
بدون Redis
Rate limit در فایل
بدون Node.js
فقط PHP
بدون Composer
autoload دستی

پایان tech-stack.md
