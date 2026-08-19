# ADR-003: یادآوری با ICS

> **تاریخ:** 1405/05/28
> **وضعیت:** ✅ تصویب شده

---

## زمینه

کاربر نیاز به یادآوری تسک‌ها دارد.
محدودیت: هاست اشتراکی بدون Cron پیشرفته.

## گزینه‌ها

1. Web Push Notification
2. پیامک
3. ICS (Google Calendar)
4. ایمیل

## تصمیم

**ICS (Google Calendar)** انتخاب شد.

## دلایل

| گزینه    | مزایا             | معایب                          |
| -------- | ----------------- | ------------------------------ |
| Web Push | لحظه‌ای           | نیاز به Service Worker + HTTPS |
| پیامک    | همگانی            | هزینه                          |
| **ICS**  | **رایگان، جهانی** | **setup اولیه**                |
| ایمیل    | ساده              | spam می‌شود                    |

## پیاده‌سازی

```php
// تولید فایل ICS
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
SUMMARY:نماز ظهر
DTSTART:20260815T121100
DTEND:20260815T122600
RRULE:FREQ=DAILY
END:VEVENT
END:VCALENDAR
```

کاربر این فایل را به Google Calendar اضافه می‌کند.
پایان ADR-003
