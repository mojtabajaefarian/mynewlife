<?php
/* ===== FILE: index.php | صفحه اصلی با دکمه تنظیمات ===== */
declare(strict_types=1);


require_once __DIR__ . '/auth.php';
auth_guard();

require_once __DIR__ . '/config.php';
$ver = defined('APP_VER') ? APP_VER : '2.0';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0b1020">
<title>📔 دفتر روزانه</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css?v=<?= rawurlencode($ver) ?>">
</head>
<body>
<div id="app">
    <div class="boot">
        <h2>در حال بارگذاری دفتر...</h2>
        <p style="color: #98a3bd; font-size: 14px;">لطفاً صبر کنید</p>
    </div>
</div>
<a href="admin.php" class="settings-fab" title="تنظیمات">⚙️</a>
<noscript>
    <div style="padding: 40px; text-align: center; color: #fca5a5;">
        برای کار کردن برنامه، جاوااسکریپت را فعال کنید.
    </div>
</noscript>
<script>
window.API_URL = 'api.php';
window.APP_VER = <?= json_encode($ver) ?>;
</script>
<script src="assets/store.js?v=<?= rawurlencode($ver) ?>"></script>
<script src="assets/app.js?v=<?= rawurlencode($ver) ?>"></script>
</body>
</html>