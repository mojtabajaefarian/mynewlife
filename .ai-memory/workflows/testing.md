# 🧪 Testing Workflow

> **قبل از هر تحویل به کاربر**

---

## Checklist اجباری

### قبل از تست

□ همه فایل‌ها ذخیره شده‌اند
□ dependencies نصب هستند
□ محیط تست آماده است

### تست‌ها

□ PHP syntax check
php -l config.php
php -l api.php
...
□ JS syntax check
node --check assets/app.js
□ Unit tests
(اگر وجود دارند)
□ Integration tests
API endpoints
Auth flow
Data flow
□ E2E tests (manual)
Login
Task submission
Day transition
Stats display
□ Coaching compliance
No blame language
Survival mode works
Streak preservation
□ Browser console
No errors
No warnings (critical)
□ Network tab
No 4xx/5xx
All requests successful

### بعد از تست

□ Test report بنویس
□ Issues را گزارش بده
□ اگر همه pass → Approve
□ اگر fail → به Orchestrator اطلاع بده

---

## Test Report Template

```markdown
# 🧪 Test Report

**Date:** 1405/05/28
**Tester:** Test Agent
**Status:** ✅ PASSED / ❌ FAILED

## Summary

- Tests run: 15
- Passed: 14
- Failed: 1
- Warnings: 2

## Details

...

## Issues Found

...

## Recommendation

[ ] Approve
[ ] Fix and re-test
[ ] Reject

پایان testing.md
```
