# 🔄 Change Request Workflow

> **برای:** هر درخواست تغییر در پروژه

---

## مراحل

### 1️⃣ دریافت درخواست

کاربر: "می‌خواهم فیچر X اضافه شود"

### 2️⃣ تحلیل توسط Orchestrator

چه فایل‌هایی تغییر می‌کنند؟
چه ایجنت‌هایی لازم است؟
آیا ADR نیاز است؟
چه ریسک‌هایی وجود دارد؟

### 3️⃣ بررسی توسط Coach Agent

□ انطباق با اصول کوچینگ
□ عدم ایجاد فشار
□ پشتیبانی از بقا
□ عدم کمال‌گرایی

### 4️⃣ پیاده‌سازی

کد را بنویس
مستندات را آپدیت کن
ADR بساز اگر لازم است
changelog را آپدیت کن

### 5️⃣ بازبینی توسط Code Reviewer

□ کیفیت کد
□ Best practices
□ Security
□ Performance

### 6️⃣ تست توسط Test Agent

□ Syntax check
□ Unit tests
□ Integration tests
□ E2E tests
□ Coaching compliance

### 7️⃣ Commit و Push توسط GitHub Agent

□ Conventional commit
□ Push to branch
□ Create PR (if needed)
□ Notify user

### 8️⃣ گزارش نهایی

✅ تغییر اعمال شد
📝 خلاصه:
[تغییرات]
🧪 تست‌ها: همه پاس شدند
🐙 Commit: abc123
🌐 Deploy: موفق
🔗 Links:
Commit: ...
ADR: ...

---

**پایان change-request.md**
