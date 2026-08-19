# 🏗️ نمای کلی معماری

> **نسخه:** 1.0.0
> **آخرین بروزرسانی:** 1405/05/28

---

## ۱. الگوی معماری

**الگوی اصلی:** Modular Monolith (تک‌پارچه ماژولار)
┌─────────────────────────────────────────┐
│ Frontend (Browser) │
│ ┌──────────┐ ┌──────────┐ ┌────────┐ │
│ │ index.php│ │ admin.php│ │seed.php│ │
│ └────┬─────┘ └────┬─────┘ └───┬────┘ │
│ │ │ │ │
│ ▼ ▼ ▼ │
│ ┌────────────────────────────────┐ │
│ │ app.js (SPA Engine) │ │
│ └──────────────┬─────────────────┘ │
└─────────────────┼───────────────────────┘
│
│ Fetch API (JSON)
▼
┌─────────────────────────────────────────┐
│ Backend (PHP) │
│ ┌────────────────────────────────┐ │
│ │ api.php (Router) │ │
│ └──────────────┬─────────────────┘ │
│ │ │
│ ┌──────────┐ ┌──────────┐ ┌────────┐ │
│ │config.php│ │ data.php │ │engine. │ │
│ │ │ │ │ │ php │ │
│ └──────────┘ └──────────┘ └────────┘ │
│ │ │
│ ┌──────────────┴─────────────────┐ │
│ │ data/ (JSON + backups) │ │
│ │ - db.json │ │
│ │ - settings.json │ │
│ │ - rate_limit.json │ │
│ └────────────────────────────────┘ │
└─────────────────────────────────────────┘

---

## ۲. لایه‌های معماری

### لایه ۱: ارائه (Presentation)

| فایل               | مسئولیت                             |
| ------------------ | ----------------------------------- |
| `index.php`        | اسکلت HTML + بارگذاری config و data |
| `admin.php`        | پنل مدیریت (فقط با لاگین)           |
| `login.php`        | صفحه ورود                           |
| `seed.php`         | نصب اولیه (فقط با رمز)              |
| `assets/app.js`    | منطق کلاینت + رندر + ارتباط با API  |
| `assets/style.css` | ظاهر + RTL + موبایل‌اول             |
| `assets/store.js`  | ابزارها + تاریخ شمسی + localStorage |

### لایه ۲: کنترل (Controller)

| فایل       | مسئولیت                                     |
| ---------- | ------------------------------------------- |
| `api.php`  | Router + احراز هویت + CSRF + Rate Limit     |
| `auth.php` | توابع احراز هویت (session, token, password) |

### لایه ۳: منطق (Business Logic)

| فایل         | مسئولیت                                     |
| ------------ | ------------------------------------------- |
| `engine.php` | محاسبه امتیاز، فاز، استریک، گزارش کوچ       |
| `config.php` | ثابت‌ها، time zone، helper functions        |
| `data.php`   | ساختار داده (tasks, anchors, phases, tests) |

### لایه ۴: ذخیره‌سازی (Storage)

| فایل/پوشه              | مسئولیت                   |
| ---------------------- | ------------------------- |
| `data/db.json`         | داده‌های روزانه کاربر     |
| `data/settings.json`   | تنظیمات (رمز، تاریخ شروع) |
| `data/backups/`        | بکاپ‌های روزانه           |
| `data/rate_limit.json` | شمارنده درخواست‌ها        |

---

## ۳. جریان درخواست (Request Flow)

### ۳.۱. بارگذاری صفحه

Browser Request
↓
index.php
↓
require config.php → ثابت‌ها، helpers
↓
require data.php → TASKS, ANCHORS, PHASES
↓
db_read() → data/db.json
↓
Render HTML
↓
app.js executes → fetch('?api=load')
↓
api.php → JSON response
↓
Render UI

123
User clicks task
↓
app.js → fetch('?api=val', {k, v})
↓
api.php
↓
├─ verify_session()
├─ verify_csrf()
├─ rate_limit()
└─ action: val
↓
engine.php
├─ calculate_score()
├─ calculate_streak()
└─ generate_report()
↓
db_write() → data/db.json
↓
JSON response → {ok, stats, streak, eval, rec}
↓
app.js updates UI

### ۳.۳. لاگین

User submits form
↓
login.php
↓
auth.php::login_attempt()
├─ verify_password()
├─ check_rate_limit()
└─ create_session()
↓
Redirect to index.php

---

## ۴. ماژول‌های اصلی

### ۴.۱. ماژول احراز هویت (`auth.php`)

Functions:
login_attempt($password): bool
logout(): void
is_authenticated(): bool
verify_csrf($token): bool
generate_csrf(): string
verify_password($input, $hash): bool

### ۴.۲. ماژول موتور (`engine.php`)

Functions:
day_stats($db, $date, $phase): array
streak($db, $start, $today): array
phase_of($day_number): int
day_number($start, $date): int
coaching_eval($stats, $streak): array
recommendations($stats, $phase): array

### ۴.۳. ماژول API (`api.php`)

Actions:
load → بارگذاری داده روز
val → ذخیره مقدار تسک
chk → ذخیره چک‌لیست
note → ذخیره یادداشت روز
export → خروجی JSON
import → ورود JSON
stats → آمار کلی
report → گزارش کوچ
settings → تغییر تنظیمات (admin)
seed → نصب اولیه

---

## ۵. مدل داده

### ۵.۱. ساختار `db.json`

```json
{
  "start": "2026-08-15",
  "days": {
    "2026-08-15": {
      "tasks": {
        "fajr_pray": {"v": 3, "t": 1692000000},
        "walk": {"v": 7, "t": 1692003600}
      },
      "note": "روز خوبی بود",
      "level": "عادی"
    }
  }
}

### ۵.۲. ساختار settings.json
{
  "password_hash": "$2y$10$...",
  "start_date": "2026-08-15",
  "timezone": "Asia/Tehran",
  "theme": "dark",
  "reminders": {
    "ics_enabled": true,
    "calendar_url": "..."
  }
}
### ۵.۳. ساختار TASK در data.php
[
  'id' => 'fajr_pray',
  'type' => 'win',           // qty|win|chk|lv
  'block' => 'b1',
  'phase' => 3,
  'star' => true,
  'weight' => 3,
  'window' => [
    'prime' => ['n' => 'اول وقت', 'lv' => 3, 'p' => 100],
    'valid' => ['n' => 'داخل وقت', 'lv' => 2, 'p' => 70],
    'qada'  => ['n' => 'قضا', 'lv' => 1, 'p' => 35],
    'miss'  => ['n' => 'نشد', 'lv' => 0, 'p' => 0]
  ]
]

## ۶. الگوهای طراحی استفاده‌شده
الگو
کاربرد
Router Pattern
api.php به‌عنوان dispatcher
Repository Pattern
db_read() / db_write()
Strategy Pattern
انواع تسک (qty, win, chk, lv)
Facade Pattern
engine.php پیچیدگی را پنهان می‌کند
Observer Pattern
app.js به تغییرات API واکنش می‌دهد
۷. وابستگی‌ها (Dependencies)
۷.۱. وابستگی‌های داخلی
index.php
  ├── config.php
  ├── data.php
  └── app.js (کلاینت)

api.php
  ├── config.php
  ├── data.php
  ├── auth.php
  └── engine.php

admin.php
  ├── config.php
  ├── data.php
  ├── auth.php
  └── engine.php

  ۷.۲. وابستگی‌های خارجی
وابستگی
نسخه
هدف
PHP
7.4+
Runtime
فونت وزیر
CDN
UI
Chart.js
3.x
نمودار
Font Awesome
6.x
آیکون
۸. مقیاس‌پذیری
۸.۱. فعلی
تک‌کاربره
حداکثر ۱۰۰۰ روز داده (≈ ۵۰۰ کیلوبایت)
بدون caching
۸.۲. آینده (فازهای بعدی)
تبدیل به دیتابیس (SQLite یا MySQL)
اضافه کردن Redis برای caching
Web Push Notification
Multi-user support (با roles)
۹. امنیت
۹.۱. لایه‌های امنیتی
┌─────────────────────────┐
│  1. HTTPS (هاست)        │
├─────────────────────────┤
│  2. Session Management  │
├─────────────────────────┤
│  3. CSRF Token          │
├─────────────────────────┤
│  4. Rate Limiting       │
├─────────────────────────┤
│  5. Password Hashing    │
├─────────────────────────┤
│  6. Input Validation    │
├─────────────────────────┤
│  7. .htaccess (data/)   │
└─────────────────────────┘

۹.۲. تهدیدهای شناخته‌شده
تهدید
وضعیت
XSS
✅ Sanitize در خروجی
CSRF
✅ توکن در هر request
Brute Force
✅ Rate Limit (۵ تلاش/۱۵ دقیقه)
SQL Injection
N/A (بدون DB)
File Inclusion
⚠️ بررسی شود
۱۰. تست‌پذیری
۱۰.۱. تست‌های فعلی
تست دستی در مرورگر
Console output در app.js
۱۰.۲. تست‌های مورد نیاز (فاز بعد)
Unit tests برای engine.php
Integration tests برای api.php
E2E tests با Puppeteer
پایان overview.md - حالا برو data-flow.md را بخوان.

```
