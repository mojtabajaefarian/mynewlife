# 📍 نقشه راه مغز پروژه

> **نسخه:** 1.0.0
> **آخرین بروزرسانی:** 1405/05/28

---

## 🎯 نقطه شروع

**اگر اولین بار است با این پروژه کار می‌کنی:**

1. [`BRAIN.md`](./BRAIN.md) - دستورالعمل اصلی
2. [`project/identity.md`](./project/identity.md) - چیستی پروژه
3. [`project/user-profile.md`](./project/user-profile.md) - پروفایل کاربر
4. [`architecture/overview.md`](./architecture/overview.md) - معماری

---

## 📂 راهنمای پوشه‌ها

### 🏢 project/ - هویت و اهداف

| فایل                                           | محتوا                          | کی بخوانم؟ |
| ---------------------------------------------- | ------------------------------ | ---------- |
| [`identity.md`](./project/identity.md)         | نام، اهداف، چشم‌انداز، ارزش‌ها | همیشه اول  |
| [`user-profile.md`](./project/user-profile.md) | پروفایل کامل مجتبی             | درک کاربر  |
| [`requirements.md`](./project/requirements.md) | نیازمندی‌های عملکردی           | توسعه فیچر |
| [`constraints.md`](./project/constraints.md)   | محدودیت‌های فنی و کسب‌وکاری    | تصمیم‌گیری |

### 🏗️ architecture/ - معماری سیستم

| فایل                                                    | محتوا           | کی بخوانم؟   |
| ------------------------------------------------------- | --------------- | ------------ |
| [`overview.md`](./architecture/overview.md)             | نمای کلی معماری | شروع کار     |
| [`data-flow.md`](./architecture/data-flow.md)           | جریان داده      | دیباگ        |
| [`tech-stack.md`](./architecture/tech-stack.md)         | فناوری‌ها       | انتخاب ابزار |
| [`file-structure.md`](./architecture/file-structure.md) | ساختار فایل‌ها  | ناوبری کد    |

### 📝 decisions/ - تصمیمات اتخاذ شده (ADR)

| فایل                                 | محتوا          | کی بخوانم؟       |
| ------------------------------------ | -------------- | ---------------- |
| [`_index.md`](./decisions/_index.md) | لیست همه ADRها | تصمیم جدید       |
| `ADR-001-*.md`                       | سیستم لاگین    | تغییر احراز هویت |
| `ADR-002-*.md`                       | ورود جزئی      | تغییر مدل تسک    |
| `ADR-003-*.md`                       | یادآور ICS     | تغییر reminder   |
| `ADR-004-*.md`                       | CSRF نرم       | تغییر امنیت      |

### 🗺️ code-map/ - نقشه کد

| فایل                                              | محتوا                  | کی بخوانم؟      |
| ------------------------------------------------- | ---------------------- | --------------- |
| [`backend.md`](./code-map/backend.md)             | نقشه کد PHP + آدرس‌دهی | کار با PHP      |
| [`frontend.md`](./code-map/frontend.md)           | نقشه کد JS/CSS         | کار با frontend |
| [`data-schema.md`](./code-map/data-schema.md)     | ساختار JSON            | تغییر دیتا      |
| [`api-endpoints.md`](./code-map/api-endpoints.md) | مستندات API            | توسعه API       |

### 📊 progress/ - پیشرفت

| فایل                                                | محتوا             | کی بخوانم؟   |
| --------------------------------------------------- | ----------------- | ------------ |
| [`current-status.md`](./progress/current-status.md) | وضعیت فعلی        | هر بار شروع  |
| [`changelog.md`](./progress/changelog.md)           | تاریخچه تغییرات   | بعد از تغییر |
| [`known-issues.md`](./progress/known-issues.md)     | مشکلات شناخته‌شده | دیباگ        |

### ✅ tasks/ - مدیریت تسک

| فایل                                       | محتوا        | کی بخوانم؟  |
| ------------------------------------------ | ------------ | ----------- |
| [`backlog.md`](./tasks/backlog.md)         | تسک‌های آتی  | برنامه‌ریزی |
| [`in-progress.md`](./tasks/in-progress.md) | در حال انجام | شروع کار    |
| [`done.md`](./tasks/done.md)               | انجام‌شده    | گزارش       |

### 🤖 agents/ - ایجنت‌های هوشمند

| فایل                                            | محتوا         | کی بخوانم؟   |
| ----------------------------------------------- | ------------- | ------------ |
| [`_orchestrator.md`](./agents/_orchestrator.md) | هماهنگ‌کننده  | هر بار       |
| [`github-agent.md`](./agents/github-agent.md)   | ایجنت گیت‌هاب | commit/push  |
| [`test-agent.md`](./agents/test-agent.md)       | ایجنت تست     | قبل از ارسال |
| [`code-reviewer.md`](./agents/code-reviewer.md) | بازبینی کد    | قبل از merge |
| [`coach-agent.md`](./agents/coach-agent.md)     | ایجنت کوچینگ  | بررسی اصول   |

### 🔄 workflows/ - گردش کارها

| فایل                                                 | محتوا         | کی بخوانم؟         |
| ---------------------------------------------------- | ------------- | ------------------ |
| [`onboarding.md`](./workflows/onboarding.md)         | ورود AI جدید  | اولین بار          |
| [`change-request.md`](./workflows/change-request.md) | درخواست تغییر | تغییر کد           |
| [`testing.md`](./workflows/testing.md)               | فرآیند تست    | قبل از ارسال       |
| [`deployment.md`](./workflows/deployment.md)         | استقرار       | push به production |

### 📚 references/ - منابع مرجع

| فایل                                                            | محتوا          | کی بخوانم؟ |
| --------------------------------------------------------------- | -------------- | ---------- |
| [`coaching-principles.md`](./references/coaching-principles.md) | اصول کوچینگ    | طراحی فیچر |
| [`habit-formation.md`](./references/habit-formation.md)         | اصول عادت‌سازی | طراحی فیچر |
| [`php-patterns.md`](./references/php-patterns.md)               | الگوهای PHP    | توسعه PHP  |

---

## 🎯 مسیرهای سریع

### مسیر ۱: "می‌خواهم یک فیچر جدید اضافه کنم"

project/requirements.md → نیازمندی چیست؟
decisions/\_index.md → آیا ADR مرتبط هست؟
architecture/overview.md → کجای معماری جا می‌گیرد؟
code-map/backend.md → کدام فایل‌ها را تغییر دهم؟
workflows/change-request.md → گردش کار تغییر
agents/test-agent.md → تست کن
agents/github-agent.md → commit و push

### مسیر ۲: "یک باگ پیدا کردم"

progress/known-issues.md → آیا شناخته‌شده است؟
architecture/data-flow.md → جریان داده را بفهم
code-map/backend.md → فایل‌های مرتبط را پیدا کن
workflows/testing.md → تست و رفع
progress/changelog.md → ثبت در changelog

### مسیر ۳: "می‌خواهم تصمیم مهمی بگیرم"

decisions/\_index.md → لیست ADRهای قبلی
project/constraints.md → محدودیت‌ها
project/requirements.md → نیازمندی‌ها
ADR جدید بساز → مستندسازی تصمیم

### مسیر ۴: "اولین بار است با پروژه کار می‌کنم"

BRAIN.md ← شما اینجا هستید
\_index.md ← همین فایل
project/identity.md → چیستی پروژه
project/user-profile.md → پروفایل کاربر
architecture/overview.md → معماری
workflows/onboarding.md → مراحل ورود

---

## 🔍 جستجو بر اساس موضوع

### موضوع: احراز هویت و امنیت

- [`decisions/ADR-001-login-system.md`](./decisions/ADR-001-login-system.md)
- [`code-map/backend.md#auth-php`](./code-map/backend.md#auth-php)
- [`code-map/backend.md#login-php`](./code-map/backend.md#login-php)

### موضوع: ورود جزئی (Partial Input)

- [`decisions/ADR-002-partial-input.md`](./decisions/ADR-002-partial-input.md)
- [`code-map/backend.md#engine-php`](./code-map/backend.md#engine-php)
- [`code-map/api-endpoints.md#val`](./code-map/api-endpoints.md#val)

### موضوع: یادآوری و Notification

- [`decisions/ADR-003-reminder-ics.md`](./decisions/ADR-003-reminder-ics.md)
- [`project/requirements.md#reminders`](./project/requirements.md#reminders)

### موضوع: نماز و اوقات شرعی

- [`project/user-profile.md#prayers`](./project/user-profile.md#prayers)
- [`code-map/backend.md#prayer-times`](./code-map/backend.md#prayer-times)
- [`code-map/data-schema.md#prayers`](./code-map/data-schema.md#prayers)

### موضوع: ورزش و حرکات اصلاحی

- [`project/user-profile.md#health`](./project/user-profile.md#health)
- [`code-map/data-schema.md#exercises`](./code-map/data-schema.md#exercises)
- [`code-map/data-schema.md#workouts`](./code-map/data-schema.md#workouts)

---

## 📞 کمک فوری

| سوال               | برو به                                                       |
| ------------------ | ------------------------------------------------------------ |
| پروژه چیست؟        | [`project/identity.md`](./project/identity.md)               |
| کاربر کیست؟        | [`project/user-profile.md`](./project/user-profile.md)       |
| معماری چگونه است؟  | [`architecture/overview.md`](./architecture/overview.md)     |
| کد کجاست؟          | [`code-map/backend.md`](./code-map/backend.md)               |
| الان کجاییم؟       | [`progress/current-status.md`](./progress/current-status.md) |
| چه کاری باید بکنم؟ | [`tasks/in-progress.md`](./tasks/in-progress.md)             |
| چه مشکلاتی هست؟    | [`progress/known-issues.md`](./progress/known-issues.md)     |
| چگونه commit کنم؟  | [`agents/github-agent.md`](./agents/github-agent.md)         |
| چگونه تست کنم؟     | [`agents/test-agent.md`](./agents/test-agent.md)             |

---

**پایان \_index.md** - حالا بر اساس نیازت، فایل‌های مرتبط را بخوان.
