# 📍 Master Index — مغز پروژه + کد اصلی

> **نسخه:** 2.0.0
> **آخرین بروزرسانی:** 1405/05/28
> **Base URL:** `https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/`
> **Repository:** https://github.com/mojtabajaefarian/mynewlife
> **Live URL:** https://samfon.ir/FatehNe/MyNewLife/

---

## 🎯 نقطه شروع اجباری برای AI

اگر اولین بار است با این پروژه کار می‌کنی، **به این ترتیب** بخوان:

| # | فایل | لینک Raw | چرا |
|---|------|----------|------|
| 1 | **🧠 BRAIN** (مغز پروژه) | [BRAIN.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/BRAIN.md) | هویت، اصول کوچینگ، قوانین طلایی |
| 2 | **📊 وضعیت فعلی** | [current-status.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/current-status.md) | کجای پروژه‌ایم |
| 3 | **📋 ایندکس** (همین فایل) | [_index.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/_index.md) | نقشهٔ راه همه فایل‌ها |
| 4 | **👤 پروفایل کاربر** | [user-profile.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/user-profile.md) | مجتبی کیست؟ شرایطش چیست؟ |
| 5 | **🏗️ معماری** | [overview.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/overview.md) | نمای کلی سیستم |

---

## 🔥 فایل‌های حیاتی کد (Backend + Frontend)

> این‌ها فایل‌های در حال اجرا هستند. قبل از هر تغییر، محتوای فعلی‌شان را بخوان.

### Backend (PHP)

| فایل | لینک Raw | نقش |
|------|----------|------|
| `index.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/index.php) | پوسته HTML + روتر |
| `api.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/api.php) | API اصلی: state, tick, val, brush, note, test, report, export, import |
| `auth.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/auth.php) | احراز هویت (Argon2id/bcrypt + CSRF + Rate Limit) |
| `config.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/config.php) | ثابت‌ها، اوقات شرعی، تاریخ شمسی، دیتابیس JSON |
| `data.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/data.php) | تعاریف: بلوک‌ها، تسک‌ها، فازها، جملات، قوانین، پروتکل‌ها |
| `engine.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/engine.php) | موتور ورود جزئی + امتیاز رفتاری (qty/win/chk/lv) |
| `admin.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/admin.php) | پنل مدیریت |
| `login.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/login.php) | صفحه ورود |
| `logout.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/logout.php) | خروج از سیستم |
| `seed.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/seed.php) | نصب اولیه (یک‌بار) |
| `settings.php` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/settings.php) | لودر تنظیمات + نرمال‌سازی |
| `settings.json` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/settings.json) | تنظیمات پویا (runtime) |
| `.gitignore` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.gitignore) | قوانین Git |
| `AGENTS.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/AGENTS.md) | پروتکل Multi-Agent |
| `AI_CONTEXT.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/AI_CONTEXT.md) | راهنمای AI جدید |

### Frontend (JS/CSS)

| فایل | لینک Raw | نقش |
|------|----------|------|
| `assets/app.js` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/assets/app.js) | منطق کلاینت (SPA) + رندر + API calls |
| `assets/store.js` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/assets/store.js) | ابزارها + تاریخ شمسی + localStorage fallback |
| `assets/style.css` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/assets/style.css) | استایل RTL + موبایل‌اول + تیره |

### DevOps

| فایل | لینک Raw | نقش |
|------|----------|------|
| `.github/workflows/deploy.yml` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.github/workflows/deploy.yml) | GitHub Actions FTP Deploy |

---

## 🧠 مغز پروژه (`.ai-memory/`)

### 🏢 `project/` — هویت و اهداف

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `identity.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/identity.md) | نام، اهداف، چشم‌انداز، ارزش‌ها |
| `user-profile.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/user-profile.md) | پروفایل کامل مجتبی |
| `requirements.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/requirements.md) | نیازمندی‌های عملکردی |
| `constraints.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/constraints.md) | محدودیت‌های فنی و کسب‌وکاری |

### 🏗️ `architecture/` — معماری سیستم

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `overview.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/overview.md) | نمای کلی معماری |
| `data-flow.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/data-flow.md) | جریان داده در سیستم |
| `tech-stack.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/tech-stack.md) | فناوری‌ها و ابزارها |
| `file-structure.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/file-structure.md) | ساختار دقیق فایل‌ها |

### 📝 `decisions/` — تصمیمات اتخاذ شده (ADR)

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `_index.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/_index.md) | لیست همه ADRها |
| `ADR-001-login-system.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-001-login-system.md) | سیستم لاگین |
| `ADR-002-partial-input.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-002-partial-input.md) | ورود جزئی |
| `ADR-003-reminder-ics.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-003-reminder-ics.md) | یادآور ICS |
| `ADR-004-csrf-soft.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-004-csrf-soft.md) | CSRF نرم |

### 🗺️ `code-map/` — نقشه کد

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `backend.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/code-map/backend.md) | نقشه کد PHP + آدرس‌دهی |
| `frontend.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/code-map/frontend.md) | نقشه کد JS/CSS |
| `data-schema.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/code-map/data-schema.md) | ساختار JSON |
| `api-endpoints.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/code-map/api-endpoints.md) | مستندات API |

### 📊 `progress/` — پیشرفت

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `current-status.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/current-status.md) | وضعیت فعلی |
| `changelog.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/changelog.md) | تاریخچه تغییرات |
| `known-issues.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/known-issues.md) | مشکلات شناخته‌شده |

### ✅ `tasks/` — مدیریت تسک

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `backlog.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/tasks/backlog.md) | تسک‌های آتی |
| `in-progress.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/tasks/in-progress.md) | در حال انجام |
| `done.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/tasks/done.md) | انجام‌شده |

### 🤖 `agents/` — ایجنت‌های هوشمند

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `_orchestrator.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/_orchestrator.md) | هماهنگ‌کننده |
| `github-agent.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/github-agent.md) | ایجنت گیت‌هاب |
| `test-agent.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/test-agent.md) | ایجنت تست |
| `code-reviewer.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/code-reviewer.md) | بازبینی کد |
| `coach-agent.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/coach-agent.md) | ایجنت کوچینگ |

### 🔄 `workflows/` — گردش کارها

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `onboarding.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/workflows/onboarding.md) | ورود AI جدید |
| `change-request.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/workflows/change-request.md) | درخواست تغییر |
| `testing.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/workflows/testing.md) | فرآیند تست |
| `deployment.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/workflows/deployment.md) | استقرار |

### 📚 `references/` — منابع مرجع

| فایل | لینک Raw | محتوا |
|------|----------|-------|
| `coaching-principles.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/references/coaching-principles.md) | اصول کوچینگ |
| `habit-formation.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/references/habit-formation.md) | اصول عادت‌سازی |
| `php-patterns.md` | [raw](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/references/php-patterns.md) | الگوهای PHP |

---

## 🚀 مسیرهای سریع برای AI

### مسیر ۱: «می‌خواهم یک فیچر جدید اضافه کنم»

requirements.md → نیازمندی چیست؟
decisions/_index.md → آیا ADR مرتبط هست؟
architecture/overview.md → کجای معماری جا می‌گیرد؟
code-map/backend.md → کدام فایل‌ها را تغییر دهم؟
workflows/change-request.md → گردش کار تغییر


### مسیر ۲: «یک باگ پیدا کردم»

progress/known-issues.md → آیا شناخته‌شده است؟
architecture/data-flow.md → جریان داده را بفهم
code-map/backend.md → فایل‌های مرتبط
workflows/testing.md → تست و رفع



### مسیر ۳: «می‌خواهم تصمیم مهمی بگیرم»

decisions/_index.md → لیست ADRهای قبلی
project/constraints.md → محدودیت‌ها
ADR جدید بساز → مستندسازی تصمیم


### مسیر ۴: «اولین بار است با پروژه کار می‌کنم»
BRAIN.md ← شما اینجا هستید (بعد از _index)
این فایل (_index.md) ← همین فایل
project/identity.md → چیستی پروژه
project/user-profile.md → پروفایل کاربر
architecture/overview.md → معماری
workflows/onboarding.md → مراحل ورود


---

## 🔍 جستجو بر اساس موضوع

### موضوع: احراز هویت و امنیت
- [ADR-001-login-system.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-001-login-system.md)
- [auth.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/auth.php)
- [login.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/login.php)

### موضوع: ورود جزئی (Partial Input)
- [ADR-002-partial-input.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-002-partial-input.md)
- [engine.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/engine.php)
- [api.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/api.php) (اکشن `val`)

### موضوع: یادآوری و Notification
- [ADR-003-reminder-ics.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/decisions/ADR-003-reminder-ics.md)
- [project/requirements.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/requirements.md)

### موضوع: نماز و اوقات شرعی
- [config.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/config.php) (متغیر `$PRAYERS`)
- [data.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/data.php) (بلوک‌های نماز)

### موضوع: ورزش و حرکات اصلاحی
- [data.php](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/data.php) (پروتکل‌ها و حرکات)
- [project/user-profile.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/user-profile.md) (مشکلات جسمی)

### موضوع: اصول کوچینگ (غیرقابل مذاکره)
- [BRAIN.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/BRAIN.md)
- [references/coaching-principles.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/references/coaching-principles.md)
- [agents/coach-agent.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/coach-agent.md)

---

## 📞 کمک فوری

| سوال | برو به |
|------|--------|
| پروژه چیست؟ | [identity.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/identity.md) |
| کاربر کیست؟ | [user-profile.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/project/user-profile.md) |
| معماری چگونه است؟ | [overview.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/architecture/overview.md) |
| کد کجاست؟ | [code-map/backend.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/code-map/backend.md) |
| الان کجاییم؟ | [current-status.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/current-status.md) |
| چه کاری باید بکنم؟ | [in-progress.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/tasks/in-progress.md) |
| چه مشکلاتی هست؟ | [known-issues.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/progress/known-issues.md) |
| چگونه commit کنم؟ | [github-agent.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/github-agent.md) |
| چگونه تست کنم؟ | [test-agent.md](https://raw.githubusercontent.com/mojtabajaefarian/mynewlife/main/.ai-memory/agents/test-agent.md) |

---

## 🔗 آدرس‌های کلیدی

| مورد | آدرس |
|------|------|
| **Repository** | https://github.com/mojtabajaefarian/mynewlife |
| **Live Production** | https://samfon.ir/FatehNe/MyNewLife/ |
| **API State (Live)** | https://samfon.ir/FatehNe/MyNewLife/api.php?a=state |
| **API Ping (Live)** | https://samfon.ir/FatehNe/MyNewLife/api.php?a=ping |
| **GitHub Actions** | https://github.com/mojtabajaefarian/mynewlife/actions |

---

## 📜 تعهد AI

وقتی با این پروژه کار می‌کنی، متعهد می‌شوی:

- ✅ قبل از هر تغییر، فایل‌های مرتبط را از لینک‌های raw بخوانی
- ✅ اصول کوچینگ را از `BRAIN.md` رعایت کنی
- ✅ کد را قبل از ارسال تست کنی
- ✅ تصمیمات را در `decisions/` مستند کنی
- ✅ `changelog.md` را آپدیت کنی
- ✅ به کاربر با احترام و همدلی پاسخ بدهی

---

## ⚠️ نکات مهم برای AI

1. **لینک‌های raw همیشه محتوای روز را می‌دهند** — اگر فایلی در ریپو تغییر کرد، لینک raw همان نسخهٔ جدید را برمی‌گرداند.
2. **قبل از ویرایش هر فایل، حتماً آخرین نسخه‌اش را از لینک raw بخوان** — ممکن است کاربر یا ایجنت دیگر تغییر داده باشد.
3. **هرگز چند فایل را همزمان تغییر نده** — هر ایجنت فقط روی یک فایل در یک زمان کار می‌کند.
4. **برای تغییرات بزرگ، اول ADR بساز** — تصمیم را ثبت کن، بعد کد بزن.
5. **فایل‌های `data/*.json` در ریپو نیستند** (در `.gitignore`) — این‌ها داده‌های زنده کاربر هستند.

---

**پایان `_index.md`** — حالا بر اساس نیازت، فایل‌های مرتبط را از لینک‌های raw بخوان.