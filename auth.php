<?php
/* ==========================================================
   auth.php — سیستم احراز هویت | نسخه 1.0
   بدون دیتابیس · مناسب هاست اشتراکی · PHP 7.4+
   ========================================================== */
declare(strict_types=1);

const AUTH_DIR       = __DIR__ . '/data';
const AUTH_USER_FILE = AUTH_DIR . '/auth_user.json';
const AUTH_LOCK_FILE = AUTH_DIR . '/auth_lock.json';
const AUTH_MAX_TRY   = 5;      // حداکثر تلاش ناموفق
const AUTH_LOCK_SEC  = 900;    // 15 دقیقه قفل
const AUTH_LIFETIME  = 2592000; // 30 روز «مرا به خاطر بسپار»

/* ---------- راه‌اندازی نشست امن ---------- */
function auth_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;

    if (!is_dir(AUTH_DIR)) @mkdir(AUTH_DIR, 0755, true);

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => AUTH_LIFETIME,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('DAFTAR_SID');
    session_start();
}

/* ---------- خواندن/نوشتن امن JSON ---------- */
function auth_read(string $file, array $default = []): array
{
    if (!is_file($file)) return $default;
    $raw = @file_get_contents($file);
    if ($raw === false || $raw === '') return $default;
    $d = json_decode($raw, true);
    return is_array($d) ? $d : $default;
}

function auth_write(string $file, array $data): bool
{
    $tmp = $file . '.tmp';
    $ok  = @file_put_contents($tmp, json_encode($data,
           JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    if ($ok === false) return false;
    return @rename($tmp, $file);
}

/* ---------- ساخت کاربر (فقط بار اول) ---------- */
function auth_user_exists(): bool
{
    $u = auth_read(AUTH_USER_FILE);
    return !empty($u['hash']);
}

function auth_create_user(string $pass): array
{
    if (auth_user_exists())        return ['ok' => false, 'msg' => 'کاربر قبلاً ساخته شده است.'];
    if (mb_strlen($pass) < 8)      return ['ok' => false, 'msg' => 'رمز باید حداقل ۸ کاراکتر باشد.'];

    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    $data = [
        'hash'       => password_hash($pass, $algo),
        'created_at' => time(),
        'ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
    ];
    if (!auth_write(AUTH_USER_FILE, $data))
        return ['ok' => false, 'msg' => 'پوشهٔ data قابل نوشتن نیست.'];

    return ['ok' => true, 'msg' => 'حساب ساخته شد.'];
}

/* ---------- محدودسازی تلاش ---------- */
function auth_locked(): int
{
    $l = auth_read(AUTH_LOCK_FILE, ['n' => 0, 'until' => 0]);
    $left = (int)($l['until'] ?? 0) - time();
    return $left > 0 ? $left : 0;
}

function auth_fail(): void
{
    $l = auth_read(AUTH_LOCK_FILE, ['n' => 0, 'until' => 0]);
    $l['n'] = (int)($l['n'] ?? 0) + 1;
    if ($l['n'] >= AUTH_MAX_TRY) {
        $l['until'] = time() + AUTH_LOCK_SEC;
        $l['n']     = 0;
    }
    auth_write(AUTH_LOCK_FILE, $l);
}

function auth_reset_fail(): void
{
    auth_write(AUTH_LOCK_FILE, ['n' => 0, 'until' => 0]);
}

/* ---------- ورود ---------- */
function auth_login(string $pass, bool $remember = false): array
{
    auth_boot();

    if (($w = auth_locked()) > 0)
        return ['ok' => false, 'msg' => 'قفل موقت. ' . ceil($w / 60) . ' دقیقه دیگر تلاش کن.'];

    $u = auth_read(AUTH_USER_FILE);
    if (empty($u['hash']))
        return ['ok' => false, 'msg' => 'هنوز حسابی ساخته نشده است.'];

    if (!password_verify($pass, $u['hash'])) {
        auth_fail();
        return ['ok' => false, 'msg' => 'رمز عبور نادرست است.'];
    }

    // بازسازی هش در صورت قدیمی بودن
    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    if (password_needs_rehash($u['hash'], $algo)) {
        $u['hash'] = password_hash($pass, $algo);
        auth_write(AUTH_USER_FILE, $u);
    }

    auth_reset_fail();
    session_regenerate_id(true);           // ضد Session Fixation
    $_SESSION['auth']       = true;
    $_SESSION['login_at']   = time();
    $_SESSION['fp']         = auth_fingerprint();
    $_SESSION['csrf']       = bin2hex(random_bytes(32));

    if ($remember) {
        setcookie(session_name(), session_id(), [
            'expires'  => time() + AUTH_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    return ['ok' => true, 'msg' => 'خوش آمدی.'];
}

function auth_fingerprint(): string
{
    return hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|DAFTAR_SALT_2026');
}

/* ---------- بررسی وضعیت ---------- */
function auth_check(): bool
{
    auth_boot();
    if (empty($_SESSION['auth'])) return false;
    if (($_SESSION['fp'] ?? '') !== auth_fingerprint()) { auth_logout(); return false; }
    if (time() - (int)($_SESSION['login_at'] ?? 0) > AUTH_LIFETIME) { auth_logout(); return false; }
    return true;
}

function auth_logout(): void
{
    auth_boot();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    auth_boot();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_verify(?string $t): bool
{
    auth_boot();
    return !empty($_SESSION['csrf']) && is_string($t)
        && hash_equals($_SESSION['csrf'], $t);
}

/* ---------- نگهبان (در بالای هر صفحه) ---------- */
function auth_guard(string $login = 'login.php'): void
{
    if (!auth_check()) {
        header('Location: ' . $login);
        exit;
    }
}

/* ---------- نگهبان API (خروجی JSON) ---------- */
function auth_guard_api(): void
{
    if (!auth_check()) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'unauthorized'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}