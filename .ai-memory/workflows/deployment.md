# 🚀 Deployment Workflow

> **برای:** انتقال کد به production (هاست)

---

## معماری Deploy

Local System (Source of Truth)
↓
Git Commit + Push
↓
GitHub Repository
↓
GitHub Actions (Self-hosted Runner)
↓
FTP Deploy to cPanel
↓
Production (samfon.ir)

---

## مراحل

### 1️⃣ قبل از Deploy

□ همه تست‌ها پاس شده‌اند
□ changelog آپدیت شده
□ بکاپ گرفته شده (اختیاری)
□ Test Agent تأیید کرده

### 2️⃣ Commit و Push

```powershell
git add .
git commit -m "feat: ..."
git push origin main

3️⃣ GitHub Actions
Workflow خودکار اجرا می‌شود:
1. checkout
2. FTP Deploy
3. Verify (optional)

4️⃣ بررسی Production
□ سایت بالا می‌آید؟
□ Login کار می‌کند؟
□ API پاسخ می‌دهد؟
□ Console بدون خطا؟

✅ Deploy موفق

🌐 Production: https://samfon.ir/FatehNe/MyNewLife/
🐙 Commit: abc123
📊 Status: 200 OK
⏱️ Duration: 1m 30s

تغییرات:
- [لیست تغییرات]

```

Rollback Procedure
اگر deploy مشکل ایجاد کرد:

# 1. شناسایی commit مشکل‌دار

git log --oneline -10

# 2. Revert

git revert <commit-hash>

# 3. Push

git push origin main

# GitHub Actions خودکار deploy می‌کند

Troubleshooting
FTP Timeout
علت: پورت داده FTP بسته است
راه‌حل: استفاده از Self-hosted runner

Permission Denied
علت: .htaccess مشکل دارد
راه‌حل: بررسی permissions

پایان deployment.md
