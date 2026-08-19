# 📁 ساختار فایل‌ها

> **نسخه:** 1.0.0
> **آخرین بروزرسانی:** 1405/05/28

---

## ۱. ساختار کامل

MyNewLife/
│
├── 🌐 Entry Points (قابل دسترسی از وب)
│ ├── index.php # صفحه اصلی (SPA Shell)
│ ├── login.php # صفحه ورود
│ ├── admin.php # پنل مدیریت
│ ├── seed.php # نصب اولیه (یکبار)
│ └── logout.php # خروج
│
├── ⚙️ Backend Logic
│ ├── config.php # ثابت‌ها، helpers، time zone
│ ├── data.php # ساختار داده (TASKS, ANCHORS, ...)
│ ├── api.php # Router + handlers
│ ├── auth.php # توابع احراز هویت
│ ├── engine.php # منطق کسب‌وکار
│ └── settings.php # (deprecated → settings.json)
│
├── 🎨 Frontend Assets
│ └── assets/
│ ├── app.js # منطق کلاینت (SPA)
│ ├── store.js # ابزارها + localStorage
│ ├── style.css # ظاهر + RTL
│ ├── sw.js # Service Worker (آفلاین)
│ └── manifest.json # PWA manifest
│
├── 💾 Data Storage
│ └── data/
│ ├── db.json # داده‌های روزانه کاربر
│ ├── settings.json # تنظیمات (رمز، تاریخ شروع)
│ ├── rate_limit.json # شمارنده درخواست‌ها
│ ├── .htaccess # محافظت از پوشه
│ └── backups/
│ ├── db_2026-08-15.json
│ ├── db_2026-08-16.json
│ └── ...
│
├── 📦 Exports
│ └── exports/
│ ├── export_20260815_153834_fdc137/
│ │ ├── part1.txt
│ │ └── part2.txt
│ └── ...
│
├── 🤖 AI Memory (Project Brain)
│ └── .ai-memory/
│ ├── BRAIN.md # نقطه ورود AI
│ ├── \_index.md # نقشه راه
│ ├── project/
│ ├── architecture/
│ ├── decisions/
│ ├── code-map/
│ ├── progress/
│ ├── tasks/
│ ├── agents/
│ ├── workflows/
│ └── references/
│
├── ⚙️ Configuration
│ ├── .gitignore # Git ignore rules
│ ├── .github/
│ │ └── workflows/
│ │ └── deploy.yml # GitHub Actions
│ └── README.md # مستندات کاربر
│
└── 🧪 Tests (فاز بعدی)
└── tests/
├── unit/
│ └── engine.test.php
└── integration/
└── api.test.php

---

## ۲. دسترسی‌ها (Permissions)

| فایل/پوشه        | Permission | توضیح               |
| ---------------- | ---------- | ------------------- |
| `*.php`          | 644        | Read-only برای web  |
| `assets/*`       | 644        | Public              |
| `data/`          | 755        | Read+Execute        |
| `data/*.json`    | 664        | Read+Write برای PHP |
| `data/backups/`  | 755        | Read+Execute        |
| `data/.htaccess` | 644        | محافظت              |

---

## ۳. فایل‌های محافظت‌شده

### `.htaccess` در `data/`

```apache
Order Deny,Allow
Deny from all
```

.gitignore

# Data (هرگز commit نشود)

/data/db.json
/data/settings.json
/data/rate_limit.json
/data/backups/

# Logs

\*.log
error_log

# IDE

.vscode/
.idea/

# OS

.DS_Store
Thumbs.db

# Env

.env
.env.\*

# Exports (too large)

/exports/

۴. نقش هر فایل (خلاصه)
فایل
نقش
آدرس‌دهی سریع
index.php
Shell + load
line 1-30
config.php
Helpers
line 1-50
data.php
TASKS array
line 10-200
api.php
Router
line 20-50
auth.php
Auth functions
line 10-80
engine.php
Business logic
line 10-150
app.js
SPA
line 1-500
style.css
UI
line 1-800
پایان file-structure.md
