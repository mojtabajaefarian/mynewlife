<?php
require_once __DIR__ . '/auth.php';
auth_boot();

if (auth_check()) { header('Location: index.php'); exit; }

$err = $ok = '';
$isSetup = !auth_user_exists();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        $err = 'نشست منقضی شده. صفحه را تازه کن.';
    } elseif ($isSetup) {
        if (($_POST['pass'] ?? '') !== ($_POST['pass2'] ?? '')) {
            $err = 'دو رمز یکسان نیستند.';
        } else {
            $r = auth_create_user($_POST['pass'] ?? '');
            if ($r['ok']) { auth_login($_POST['pass']); header('Location: index.php'); exit; }
            $err = $r['msg'];
        }
    } else {
        $r = auth_login($_POST['pass'] ?? '', !empty($_POST['remember']));
        if ($r['ok']) { header('Location: index.php'); exit; }
        $err = $r['msg'];
    }
}
$tok = csrf_token();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= $isSetup ? 'ساخت حساب' : 'ورود' ?> · دفتر من</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Vazirmatn,system-ui,Tahoma,sans-serif;min-height:100vh;
 display:flex;align-items:center;justify-content:center;padding:20px;
 background:linear-gradient(145deg,#0f172a,#1e293b 55%,#0f172a);color:#e2e8f0}
.card{width:100%;max-width:400px;background:rgba(30,41,59,.85);
 backdrop-filter:blur(14px);border:1px solid rgba(148,163,184,.16);
 border-radius:22px;padding:34px 28px;box-shadow:0 22px 60px rgba(0,0,0,.45)}
.logo{width:62px;height:62px;margin:0 auto 16px;border-radius:18px;
 background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;
 align-items:center;justify-content:center;font-size:28px}
h1{font-size:20px;text-align:center;margin-bottom:6px}
.sub{font-size:13px;text-align:center;color:#94a3b8;margin-bottom:24px;line-height:1.9}
label{display:block;font-size:13px;margin:0 0 7px;color:#cbd5e1}
input[type=password]{width:100%;padding:13px 15px;margin-bottom:15px;
 border-radius:12px;border:1px solid rgba(148,163,184,.22);
 background:rgba(15,23,42,.65);color:#f1f5f9;font-family:inherit;font-size:15px;
 transition:.2s}
input[type=password]:focus{outline:0;border-color:#6366f1;
 box-shadow:0 0 0 3px rgba(99,102,241,.18)}
.row{display:flex;align-items:center;gap:8px;margin-bottom:18px;font-size:13px;color:#94a3b8}
button{width:100%;padding:14px;border:0;border-radius:12px;cursor:pointer;
 background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;
 font-family:inherit;font-size:15px;font-weight:600;transition:.2s}
button:hover{transform:translateY(-2px);box-shadow:0 10px 26px rgba(99,102,241,.4)}
.msg{padding:12px 14px;border-radius:11px;font-size:13px;margin-bottom:16px;line-height:1.8}
.err{background:rgba(239,68,68,.13);color:#fca5a5;border:1px solid rgba(239,68,68,.28)}
.hint{margin-top:20px;font-size:11.5px;color:#64748b;text-align:center;line-height:2}
</style>
</head>
<body>
<div class="card">
  <div class="logo">📔</div>
  <h1><?= $isSetup ? 'ساخت حساب' : 'خوش آمدی مجتبی' ?></h1>
  <p class="sub">
    <?= $isSetup
        ? 'یک رمز عبور برای دفترت انتخاب کن.<br>این رمز فقط روی همین هاست ذخیره می‌شود.'
        : 'دفتر برنامه‌ریزی شخصی' ?>
  </p>

  <?php if ($err): ?><div class="msg err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="post" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= $tok ?>">

    <label>رمز عبور</label>
    <input type="password" name="pass" required minlength="8" autofocus
           placeholder="حداقل ۸ کاراکتر">

    <?php if ($isSetup): ?>
      <label>تکرار رمز عبور</label>
      <input type="password" name="pass2" required minlength="8" placeholder="دوباره وارد کن">
    <?php else: ?>
      <div class="row">
        <input type="checkbox" name="remember" id="rm" checked>
        <label for="rm" style="margin:0">۳۰ روز مرا به خاطر بسپار</label>
      </div>
    <?php endif; ?>

    <button type="submit"><?= $isSetup ? 'ساخت حساب و ورود' : 'ورود به دفتر' ?></button>
  </form>

  <p class="hint">
    🔒 رمز با Argon2id هش می‌شود · پس از ۵ تلاش ناموفق، ۱۵ دقیقه قفل
  </p>
</div>
</body>
</html>