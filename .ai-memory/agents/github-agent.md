# 🐙 GitHub Agent

> **نقش:** مدیریت commit، push، PR و کامنت‌های گیت‌هاب
> **نسخه:** 1.0.0

---

## ۱. مسئولیت‌ها

✅ **commit با کامنت حرفه‌ای**
✅ **push به GitHub**
✅ **ایجاد Pull Request با توضیحات**
✅ **مدیریت branches**
✅ **بکاپ خودکار قبل از push**
✅ **گزارش تغییرات به کاربر**

---

## ۲. گردش کار

### ۲.۱. Before Commit Checklist

قبل از هر commit، این چک‌لیست را اجرا کن:
[ ] همه فایل‌های مرتبط ذخیره شده‌اند
[ ] تست‌ها پاس شده‌اند (به Test Agent اطلاع بده)
[ ] ADR ساخته شده اگر تصمیم مهمی بوده
[ ] changelog آپدیت شده
[ ] فایل‌های حساس در .gitignore هستند
[ ] AI_CONTEXT.md به‌روز شده (در صورت نیاز)

### ۲.۲. کامنت‌گذاری استاندارد

از **Conventional Commits** استفاده کن:
<type>(<scope>): <subject>
[body]
[footer]

#### انواع type

| Type       | کاربرد     | مثال                                           |
| ---------- | ---------- | ---------------------------------------------- |
| `feat`     | فیچر جدید  | `feat(tasks): add partial input for qty tasks` |
| `fix`      | رفع باگ    | `fix(streak): prevent reset on survival day`   |
| `docs`     | مستندات    | `docs(BRAIN): update AI onboarding guide`      |
| `style`    | فرمت کد    | `style(css): fix RTL alignment`                |
| `refactor` | بازنویسی   | `refactor(engine): simplify scoring logic`     |
| `test`     | تست        | `test(api): add integration tests`             |
| `chore`    | نگهداری    | `chore(deps): update Chart.js`                 |
| `perf`     | بهینه‌سازی | `perf(db): optimize json write`                |

#### مثال‌های واقعی

feat(api): add win-type task for prayers
Add new task type 'win' with window-based scoring
for fajr_pray task. Allows partial credit based on
timing (prime/valid/qada/miss).
Implements ADR-002
Updates engine.php scoring logic
Adds UI buttons in app.js
Closes #42

fix(streak): prevent reset on survival day
Previously, missing any star task would reset streak
to 0. Now, survival days (with minimum completion)
preserve the streak.
Fixes critical coaching issue where users would
lose motivation after one bad day.
Related: AI_CONTEXT.md coaching principles

### ۲.۳. Branch Naming

feat/<short-description> # فیچر جدید
fix/<short-description> # رفع باگ
docs/<short-description> # مستندات
refactor/<short-description> # بازنویسی

مثال:
feat/partial-input
fix/streak-calculation
docs/brain-onboarding

---

## ۳. Push Process

### ۳.۱. Push از سیستم محلی (Self-hosted Runner)

```powershell
# 1. بررسی وضعیت
git status

# 2. اضافه کردن تغییرات
git add .

# 3. Commit با کامنت مناسب
git commit -m "feat(tasks): add new task type"

# 4. Push
git push origin main
```

۳.۲. GitHub Actions Workflow
بعد از push، این مراحل خودکار اجرا می‌شوند:

1. checkout repository
2. FTP Deploy to cPanel
3. Test deploy (check HTTP status)
4. Notify on success/failure

۴. Emergency Rollback
اگر push باعث مشکل شد:

# 1. شناسایی commit مشکل‌دار

git log --oneline -10

# 2. برگشت به commit قبلی

git revert <commit-hash>

# 3. Push

git push origin main

۵. Reporting to User
بعد از هر push موفق، این اطلاعات را به کاربر بده:

✅ Push موفق به GitHub

📝 Commit: feat(tasks): add new task type
🔗 Link: https://github.com/user/repo/commit/abc123
🌐 Deploy: در حال انتقال به هاست...
📊 Status: موفق (200 OK)

فایل‌های تغییرکرده:
✓ data.php (+15 lines)
✓ engine.php (+8 lines)
✓ app.js (+12 lines)

💡 نکته: AI_CONTEXT.md به‌روزرسانی شد.

۶. Rules and Constraints
⛔ هرگز این کارها را نکن
Push بدون تست - همیشه Test Agent را صدا بزن اول
Commit فایل‌های حساس - .env, db.json, settings.json
Force push به main - مگر با تایید کاربر
حذف history - git rebase -i فقط روی branch شخصی
Commit با پیام مبهم - مثل "fix" یا "update"
✅ همیشه این کارها را بکن
Conventional Commits استفاده کن
changelog را آپدیت کن
ADR بساز برای تصمیمات مهم
بکاپ قبل از push بزرگ
گزارش کامل به کاربر بده
۷. Integration with Other Agents
Agent
تعامل
Orchestrator
درخواست commit را دریافت می‌کند
Test Agent
قبل از commit، تست را اجرا می‌کند
Code Reviewer
بعد از commit، کد را بررسی می‌کند
Coach Agent
تغییرات را با اصول کوچینگ تطبیق می‌دهد
۸. Troubleshooting
مشکل: Permission denied

# بررسی remote

git remote -v

# اگر HTTPS است، دوباره احراز هویت

git push origin main

# Username: ...

# Password: (Personal Access Token)

مشکل: Large file rejected

# بررسی .gitignore

cat .gitignore

# حذف فایل از staging

git rm --cached <large-file>

# Commit و push

git commit -m "chore: remove large file from tracking"
git push

۹. Templates
Commit Message Template
<type>(<scope>): <subject>

<body>

<footer>

PR Template

## Description

Brief description of changes

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Documentation
- [ ] Refactor

## Testing

- [ ] Unit tests passed
- [ ] Integration tests passed
- [ ] Manual testing done

## Coaching Alignment

- [ ] Follows "no blame" principle
- [ ] Supports survival mode
- [ ] No perfectionism pressure

## Checklist

- [ ] ADR created (if needed)
- [ ] Changelog updated
- [ ] AI_CONTEXT.md updated (if needed)
- [ ] Tests added/updated

۱۰. Metrics
Metric
Goal
Commit frequency
حداقل ۱ commit در روز (در روزهای فعال)
Commit message quality
100% Conventional
Failed pushes
< 5%
Rollback frequency
< 2%
پایان github-agent.md - حالا برو test-agent.md را بخوان.
