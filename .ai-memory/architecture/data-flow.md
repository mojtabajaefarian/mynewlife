# 🌊 جریان داده در سیستم

> **نسخه:** 1.0.0
> **آخرین بروزرسانی:** 1405/05/28

---

## ۱. نمودار جریان کلی

┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ User │─────▶│ Frontend │─────▶│ API │─────▶│ Engine │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
│ │ │
│ │ │
▼ ▼ ▼
┌──────────┐ ┌──────────┐ ┌──────────┐
│ UI │ │ Auth │ │ Data │
│ Render │ │ Check │ │ Layer │
└──────────┘ └──────────┘ └──────────┘

---

## ۲. جریان‌های اصلی

### ۲.۱. بارگذاری اولیه (Page Load)

┌─────────┐
│ Browser │
└────┬────┘
│ 1. GET /index.php
▼
┌─────────┐
│ index │
│ .php │
└────┬────┘
│ 2. require config.php
│ 3. require data.php
│ 4. db_read()
│ 5. Render HTML
▼
┌─────────┐
│ Browser │ ──▶ HTML + CSS + JS
└────┬────┘
│ 6. app.js → fetch('?api=load')
▼
┌─────────┐
│ api.php │
└────┬────┘
│ 7. verify_session()
│ 8. load_today()
│ 9. day_stats()
│10. coaching_eval()
▼
┌─────────┐
│ Browser │ ◀── JSON Response
└────┬────┘
│ 11. render_ui(data)
▼
┌─────────┐
│ UI │
└─────────┘

### ۲.۲. ثبت تسک (Task Submission)

┌─────────┐
│ User │
└────┬────┘
│ 1. Click on task
▼
┌─────────┐
│ app.js │
└────┬────┘
│ 2. fetch('?api=val', {k, v, csrf})
▼
┌─────────┐
│ api.php │
└────┬────┘
│ 3. verify_session()
│ 4. verify_csrf()
│ 5. rate_limit()
│ 6. action_val(k, v)
▼
┌─────────┐
│ engine │
│ .php │
└────┬────┘
│ 7. calculate_score(task, value)
│ 8. update_day_stats()
│ 9. recalculate_streak()
│10. generate_eval()
▼
┌─────────┐
│ db.json │
└────┬────┘
│ 11. db_write()
▼
┌─────────┐
│ Browser │ ◀── {ok, stats, streak, eval, rec}
└────┬────┘
│ 12. update_ui(response)
│ 13. show_reward_if_needed()
▼
┌─────────┐
│ UI │
└─────────┘

### ۲.۳. احراز هویت (Authentication)

┌─────────┐
│ User │
└────┬────┘
│ 1. Enter password
▼
┌─────────┐
│login.php│
└────┬────┘
│ 2. POST password
▼
┌─────────┐
│ auth.php│
└────┬────┘
│ 3. check_rate_limit()
│ 4. verify_password()
│ ├─ ❌ Increment fail count
│ └─ ✅ Create session
▼
┌─────────┐
│ Browser │ ◀── Redirect /index.php
└─────────┘

---

## ۳. انواع داده در جریان

### ۳.۱. Task Types (انواع تسک)

| Type  | Example            | Data Flow                    |
| ----- | ------------------ | ---------------------------- |
| `qty` | walk (7000 steps)  | `{k:'walk', v:7000}`         |
| `win` | fajr_pray (prime)  | `{k:'fajr_pray', v:'prime'}` |
| `chk` | reset_r (subtasks) | `{k:'reset_r', v:[1,1,0,1]}` |
| `lv`  | legacy tasks       | `{k:'task', v:2}`            |

### ۳.۲. Response Types

```json
// Success Response
{
  "ok": true,
  "stats": {
    "total": 8,
    "done": 5,
    "pct": 72,
    "stars": 4,
    "stars_done": 3,
    "stars_ok": false,
    "label": "معمول",
    "missing": ["fajr_pray"],
    "best": ["walk", "dhuhr_pray"]
  },
  "streak": {
    "current": 5,
    "best": 12
  },
  "eval": {
    "lv": 2,
    "pct": 72,
    "text": "معمول",
    "color": "#f59e0b"
  },
  "rec": {
    "t": {"walk": 3},
    "v": {},
    "br": [],
    "n": "ادامه بده!",
    "x": []
  }
}

// Error Response
{
  "ok": false,
  "error": "invalid_csrf",
  "message": "توکن CSRF نامعتبر است"
}

۴. تغییرات وضعیت (State Transitions)
۴.۱. وضعیت روز (Day Level)
┌──────────┐
│  شروع    │
│  نشده    │
└────┬─────┘
     │ first task
     ▼
┌──────────┐
│  در      │
│  جریان   │
└────┬─────┘
     │ all stars done + pct >= 75
     ▼
┌──────────┐
│  عالی    │
└──────────┘

     │ all stars done + pct >= 45
     ▼
┌──────────┐
│  معمول   │
└──────────┘

     │ any star missing
     ▼
┌──────────┐
│  بقا     │
└──────────┘

۴.۲. زنجیره (Streak)

┌─────────┐
│ current │ = 0
└────┬────┘
     │ day with all stars
     ▼
┌─────────┐
│ current │ = 1
└────┬────┘
     │ another day with all stars
     ▼
┌─────────┐
│ current │ = 2 → update best if current > best
└────┬────┘
     │ day with missing star
     ▼
┌─────────┐
│ current │ = 0 (reset)
└─────────┘

```

۵. همگام‌سازی داده (Data Sync)
۵.۱. Client ↔ Server

┌─────────┐ ┌─────────┐
│ Browser │ │ Server │
│ (JS) │ │ (PHP) │
└────┬────┘ └────┬────┘
│ │
│ 1. load │
│ ──────────────────────▶ │
│ │ 2. db_read()
│ │
│ │ 3. day_stats()
│ │
│ 4. JSON │
│ ◀────────────────────── │
│ │
│ 5. localStorage.save() │
│ │
│ 6. val(k, v) │
│ ──────────────────────▶ │
│ │ 7. db_write()
│ │
│ 8. JSON │
│ ◀────────────────────── │
│ │
│ 9. localStorage.save() │

     ۵.۲. Fallback Mechanism

Server down?
│
├─ Yes → Use localStorage cache
│ Show "آفلاین" badge
│ Queue changes
│
└─ No → Normal flow

    ۶. نکات مهم جریان داده

۶.۱. قوانین تغییر داده
قانون
توضیح
فقط api.php می‌نویسد
هیچ فایل دیگری db_write() نمی‌کند
همیشه با قفل
flock() قبل از نوشتن
همیشه بکاپ
قبل از تغییرات بزرگ، backup()
همیشه validate
input را قبل از ذخیره چک کن
۶.۲. نکات عملکردی
نکته
توضیح
Cache در حافظه
db_read() در متغیر استاتیک
Lazy load
فقط داده امروز بارگذاری می‌شود
Batch write
تغییرات کوچک را جمع کن، یکبار بنویس
پایان data-flow.md - حالا برو tech-stack.md را بخوان.
