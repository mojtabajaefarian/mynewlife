# 🧪 Test Agent

> **نقش:** تست کامل قبل از ارسال کد/محتوا به کاربر
> **نسخه:** 1.0.0

---

## ۱. فلسفه

> **قانون طلایی:** هرگز چیزی را به کاربر تحویل نده مگر ۱۰۰٪ تست شده باشد.

---

## ۲. سطوح تست

### Level 1: Syntax Validation

```bash
# PHP syntax check
php -l config.php
php -l data.php
php -l api.php
php -l engine.php
php -l auth.php

# JavaScript syntax check
node --check assets/app.js
Expected: No syntax errors detected
Level 2: Unit Tests

// مثال تست برای engine.php
function test_day_stats() {
    $db = ['days' => ['2026-08-15' => [...]]];
    $stats = day_stats($db, '2026-08-15', 1);

    assert($stats['total'] > 0, 'total should be > 0');
    assert($stats['pct'] >= 0 && $stats['pct'] <= 100, 'pct in range');
    assert(isset($stats['label']), 'label must exist');
}

Level 3: Integration Tests

// تست API endpoint
async function test_val_endpoint() {
    const res = await fetch('?api=val', {
        method: 'POST',
        body: JSON.stringify({k: 'walk', v: 7}),
        headers: {'Content-Type': 'application/json'}
    });
    const data = await res.json();

    assert(data.ok === true, 'should succeed');
    assert(data.stats.done > 0, 'should increment done');
}

Level 4: E2E Tests (Manual)

1. صفحه login باز می‌شود
2. رمز صحیح → redirect به index
3. رمز اشتباه → error message
4. تسک کلیک → مقدار ذخیره می‌شود
5. همه تسک‌ها → stats آپدیت می‌شود
6. روز جدید → داده قبلی حفظ می‌شود

۳. Test Checklist قبل از تحویل
۳.۱. برای تغییر کد
[ ] PHP syntax check (همه فایل‌های تغییرکرده)
[ ] JS syntax check (app.js, store.js)
[ ] Unit tests پاس شده
[ ] Integration tests پاس شده
[ ] E2E تست دستی (حداقل ۳ سناریو)
[ ] Browser console بدون خطا
[ ] Network tab بدون 4xx/5xx

۳.۲. برای تغییر محتوا
[ ] لحن همدلانه و بدون سرزنش
[ ] عدم استفاده از کلمات ممنوع
[ ] انطباق با اصول کوچینگ
[ ] شفافیت و قابل فهم بودن
[ ] عدم ایجاد فشار کمال‌گرایی

۳.۳. برای تغییر UI
[ ] روی موبایل تست شده
[ ] روی دسکتاپ تست شده
[ ] RTL درست کار می‌کند
[ ] اعداد فارسی هستند
[ ] تاریخ شمسی درست است
[ ] فونت وزیر load می‌شود
[ ] کنتراست کافی است

۴. Test Scenarios
۴.۱. سناریوی روز عادی
Given: کاربر لاگین کرده
When: 50% تسک‌ها را انجام می‌دهد
Then: level = "معمول"
And: stats.pct = 50
And: no error in console

۴.۲. سناریوی روز بقا
Given: کاربر لاگین کرده
When: فقط minimums را انجام می‌دهد
Then: level = "بقا"
And: streak continues (not reset)
And: encouraging message shown

۴.۳. سناریوی روز عالی
Given: کاربر لاگین کرده
When: همه stars را انجام می‌دهد + امتیاز بالا
Then: level = "عالی"
And: streak incremented
And: celebration animation

۴.۴. سناریوی روز صفر
Given: کاربر لاگین کرده
When: هیچ تسکی انجام نمی‌شود
Then: level = "شروع نشده"
And: no blame message
And: "قانون هرگز دو روز" reminder

۵. Common Test Patterns
۵.۱. Testing Scoring
// تست همه انواع تسک
const testCases = [
    {k: 'walk', v: 7000, expected: 'عالی'},
    {k: 'walk', v: 3500, expected: 'معمول'},
    {k: 'walk', v: 1000, expected: 'بقا'},
    {k: 'fajr_pray', v: 'prime', expected: 100},
    {k: 'fajr_pray', v: 'miss', expected: 0},
];

for (const tc of testCases) {
    await test_val_endpoint(tc.k, tc.v, tc.expected);
}

۵.۲. Testing Streak
// تست استریک در روزهای مختلف
async function test_streak() {
    // Day 1: all stars → streak = 1
    await complete_all_stars('2026-08-15');
    assert(streak.current === 1);

    // Day 2: all stars → streak = 2
    await complete_all_stars('2026-08-16');
    assert(streak.current === 2);

    // Day 3: survival only → streak = 3 (not reset!)
    await survival_only('2026-08-17');
    assert(streak.current === 3);

    // Day 4: zero → streak = 0
    await zero_day('2026-08-18');
    assert(streak.current === 0);
}

۶. Test Report Template
# 🧪 Test Report

## Summary
- **Status:** ✅ PASSED / ❌ FAILED
- **Tests Run:** X
- **Passed:** Y
- **Failed:** Z

## Tests Executed

### Unit Tests
- ✅ test_day_stats
- ✅ test_streak_calculation
- ❌ test_coaching_eval (FAILED)

### Integration Tests
- ✅ test_val_endpoint
- ✅ test_load_endpoint
- ✅ test_auth_flow

### E2E Tests
- ✅ Login flow
- ✅ Task submission
- ⚠️ Day transition (manual check needed)

## Issues Found

### Critical
- ❌ [Issue #1]: Description

### Warning
- ⚠️ [Issue #2]: Description

## Recommendations
- [ ] Fix critical issues before deployment
- [ ] Review warnings
- [ ] Add regression tests for fixed issues

## Coaching Compliance
- ✅ No blame language
- ✅ Survival mode supported
- ✅ No perfectionism
- ✅ Empathetic tone

## Sign-off
- Test Agent: ✅ APPROVED
- Ready for GitHub Agent: ✅ YES
۷. Failure Handling
اگر تست fail شد
متوقف کن - ادامه نده
گزارش بده - دقیق به Orchestrator
پیشنهاد fix - اگر می‌توانی
منتظر بمان - تا fix شود
تست مجدد - بعد از fix
Severity Levels
Level
Action
Critical
❌ متوقف کن، فوراً گزارش بده
High
⚠️ گزارش بده، منتظر تایید
Medium
ℹ️ گزارش بده، ادامه بده
Low
📝 فقط یادداشت کن
۸. Integration
Before GitHub Agent Push
1. GitHub Agent: "Ready to commit"
2. Test Agent: "Running tests..."
3. Test Agent: "All tests passed ✅"
4. GitHub Agent: "Committing and pushing"

If Tests Fail
1. GitHub Agent: "Ready to commit"
2. Test Agent: "Tests failed ❌"
3. Test Agent: "Issue: [details]"
4. Orchestrator: "Assigning fix to..."
5. [Fix applied]
6. Test Agent: "Re-running tests..."
7. Test Agent: "All tests passed ✅"
8. GitHub Agent: "Committing and pushing"

۹. Anti-Patterns to Avoid
❌ Don't
✅ Do Instead
Skip tests "because it's small"
Test everything
Assume it works
Verify it works
Test only happy path
Test edge cases too
Ignore warnings
Investigate warnings
Test in production
Test locally first
۱۰. Metrics
Metric
Goal
Test coverage
> 80% (code) / 100% (coaching)
False positives
< 5%
Test execution time
< 30 seconds
Critical issues caught
100%
پایان test-agent.md
```
