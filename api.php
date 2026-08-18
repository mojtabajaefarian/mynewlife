<?php
/* ===== FILE: api.php | موتور محاسبات، آمار، زنجیره، روند، گزارش ===== */
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
auth_guard();

@ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}
function fail(string $m, int $code = 200): void {
  http_response_code($code);
  out(['ok' => false, 'error' => $m]);
}
set_exception_handler(function ($e) { fail('exception: ' . $e->getMessage()); });
register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR], true))
    fail('fatal: ' . $e['message'] . ' @ ' . basename((string)$e['file']) . ':' . $e['line']);
});

/* ---------- بارگذاری تنظیمات ---------- */
if (!is_file(__DIR__ . '/config.php')) fail('config.php یافت نشد');
require_once __DIR__ . '/config.php';
if (is_file(__DIR__ . '/data.php')) require_once __DIR__ . '/data.php';
if (is_file(__DIR__ . '/engine.php')) require_once __DIR__ . '/engine.php';

date_default_timezone_set(defined('APP_TZ') ? APP_TZ : (defined('TZ') ? TZ : 'Asia/Tehran'));

/* ---------- تابع کمکی برای تبدیل اشیاء به آرایه ---------- */
function _to_array($data) {
    if (is_object($data)) {
        $data = (array)$data;
    }
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = _to_array($v);
        }
    }
    return $data;
}

/* ---------- پل سازگاری: خواندن تنظیمات با هر نام‌گذاری ---------- */
/* ---------- پل سازگاری: خواندن تنظیمات با هر نام‌گذاری ---------- */
function cfg(string $k, $def = null) {
  static $c = [];
  if (array_key_exists($k, $c)) return $c[$k];
  $v = null;
  foreach (['daftar_' . $k, 'cfg_' . $k, 'app_' . $k, $k . '_list', $k] as $f)
    if (function_exists($f)) { $v = $f(); break; }
  if ($v === null) {
    $up = strtoupper($k);
    foreach ([$up, 'DAFTAR_' . $up, 'APP_' . $up, 'CFG_' . $up] as $x)
      if (defined($x)) { $v = constant($x); break; }
  }
  if ($v === null)
    foreach ([$k, strtoupper($k), 'DAFTAR_' . $k] as $x)
      if (isset($GLOBALS[$x])) { $v = $GLOBALS[$x]; break; }
  
  if ($v === null) $v = $def;
  
  // تبدیل اشیاء (stdClass) به آرایه برای جلوگیری از خطا
  if (is_array($v) || is_object($v)) {
      $v = _to_array($v);
  }
  
  return $c[$k] = $v;
}

/* ---------- پل سازگاری: لایهٔ ذخیره‌سازی ---------- */
define('DB_DIR',  __DIR__ . '/data');
define('DB_FILE', DB_DIR . '/db.json');

function store_writable(): bool {
  static $w = null;
  if ($w !== null) return $w;
  foreach (['db_writable', 'data_writable', 'store_writable_impl'] as $f)
    if (function_exists($f)) return $w = (bool)$f();
  if (!is_dir(DB_DIR)) @mkdir(DB_DIR, 0755, true);
  if (!is_dir(DB_DIR)) return $w = false;
  $ht = DB_DIR . '/.htaccess';
  if (!is_file($ht)) @file_put_contents($ht, "Order allow,deny\nDeny from all\n");
  $probe = DB_DIR . '/.probe';
  $ok = (@file_put_contents($probe, '1') !== false);
  if ($ok) @unlink($probe);
  return $w = $ok;
}

function store_read(): array {
  foreach (['db_read', 'data_read', 'store_load'] as $f)
    if (function_exists($f)) { 
        $d = $f(); 
        return is_array($d) ? _to_array($d) : ['start' => '', 'days' => []]; 
    }
  if (!is_file(DB_FILE)) return ['start' => '', 'days' => []];
  $raw = @file_get_contents(DB_FILE);
  $d = $raw ? json_decode($raw, true) : null;
  if (!is_array($d)) $d = ['start' => '', 'days' => []];
  if (!isset($d['days']) || !is_array($d['days'])) $d['days'] = [];
  if (!isset($d['start'])) $d['start'] = '';
  return _to_array($d);
}


function store_write(array $db): bool {
  foreach (['db_write', 'data_write', 'store_save'] as $f)
    if (function_exists($f)) return (bool)$f($db);
  if (!store_writable()) return false;
  $tmp = DB_FILE . '.' . getmypid() . '.tmp';
  $js  = json_encode($db, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  if ($js === false) return false;
  if (@file_put_contents($tmp, $js, LOCK_EX) === false) return false;
  if (!@rename($tmp, DB_FILE)) { @unlink($tmp); return false; }
  @chmod(DB_FILE, 0644);
  return true;
}

/* ---------- تاریخ و اعداد ---------- */
const FA_D = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
const J_M  = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
const J_W  = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];

if (!function_exists('fa')) {
function fa($s): string { return str_replace(['0','1','2','3','4','5','6','7','8','9'], FA_D, (string)$s); }
} 

if (!function_exists('d_add')) {
function d_add(string $k, int $n): string { return date('Y-m-d', (int)strtotime($k . ' ' . $n . ' day')); }
} 

if (!function_exists('d_diff')) {
function d_diff(string $a, string $b): int { return (int)round(((int)strtotime($b) - (int)strtotime($a)) / 86400); }
} 

if (!function_exists('d_ok')) {
function d_ok(?string $s): bool { return is_string($s) && (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $s); }
} 

if (!function_exists('g2j')) {
function g2j(int $gy, int $gm, int $gd): array {
  $gdm = [0,31,59,90,120,151,181,212,243,273,304,334];
  if ($gy > 1600) { $jy = 979; $gy -= 1600; } else { $jy = 0; $gy -= 621; }
  $gy2 = ($gm > 2) ? $gy + 1 : $gy;
  $days = 365 * $gy + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100)
        + intdiv($gy2 + 399, 400) - 80 + $gd + $gdm[$gm - 1];
  $jy += 33 * intdiv($days, 12053); $days %= 12053;
  $jy += 4 * intdiv($days, 1461);   $days %= 1461;
  if ($days > 365) { $jy += intdiv($days - 1, 365); $days = ($days - 1) % 365; }
  $jm = ($days < 186) ? 1 + intdiv($days, 31) : 7 + intdiv($days - 186, 30);
  $jd = 1 + (($days < 186) ? $days % 31 : ($days - 186) % 30);
  return [$jy, $jm, $jd];
}
} 

function j_short(string $k): string {
  [$y, $m, $d] = g2j((int)date('Y', (int)strtotime($k)), (int)date('n', (int)strtotime($k)), (int)date('j', (int)strtotime($k)));
  return fa($d) . ' ' . J_M[$m - 1];
}
function j_full(string $k): string {
  $ts = (int)strtotime($k);
  [$y, $m, $d] = g2j((int)date('Y', $ts), (int)date('n', $ts), (int)date('j', $ts));
  return J_W[(int)date('w', $ts)] . ' ' . fa($d) . ' ' . J_M[$m - 1] . ' ' . fa($y);
}

/* ---------- منطق مرکزی ---------- */
function phase_of(int $day): int {
  $wk = (int)ceil(max(1, $day) / 7);
  return $wk <= 2 ? 1 : ($wk <= 4 ? 2 : ($wk <= 8 ? 3 : 4));
}
function blocks_for(int $phase): array {
  $out = [];
  foreach ((array)cfg('blocks', []) as $b) {
    $ts = [];
    foreach ((array)($b['t'] ?? []) as $t)
      if ((int)($t['ph'] ?? 1) <= $phase) $ts[] = $t;
    if ($ts) { $b['t'] = $ts; $out[] = $b; }
  }
  return $out;
}
function all_keys(): array {
  $k = [];
  foreach ((array)cfg('blocks', []) as $b)
    foreach ((array)($b['t'] ?? []) as $t) if (!empty($t['k'])) $k[$t['k']] = true;
  return $k;
}

function task_of(string $k): ?array {
  static $M = null;
  if ($M === null) {
    $M = [];
    foreach ((array)cfg('blocks', []) as $b)
      foreach ((array)($b['t'] ?? []) as $t)
        if (!empty($t['k'])) $M[(string)$t['k']] = $t;
  }
  return $M[$k] ?? null;
}

function rec_of(array $db, string $date): array {
  $r = $db['days'][$date] ?? [];
  return [
    't'  => is_array($r['t']  ?? null) ? $r['t']  : [],
    'v'  => is_array($r['v']  ?? null) ? $r['v']  : [],   // ← این خط جدید
    'br' => is_array($r['br'] ?? null) ? $r['br'] : [],
    'n'  => (string)($r['n'] ?? ''),
    'x'  => is_array($r['x'] ?? null) ? $r['x'] : [],
  ];
}


function stats_of(array $db, string $date, string $start): array {
  static $CACHE = [];
  $ck = $date . '|' . $start . '|' . count($db['days']);
  if (isset($CACHE[$ck])) return $CACHE[$ck];
  $day = d_diff($start, $date) + 1;

  
  $rec = rec_of($db, $date);
  $tot = $sum = $done = $st = $sok = 0;
  $miss = []; $best = [];
  foreach (blocks_for(phase_of($day)) as $b) foreach ($b['t'] as $t) {
    $v = (int)($rec['t'][$t['k']] ?? 0);
    $tot++; $sum += $v;
    if ($v > 0) $done++;
    if ($v === 3) $best[] = $t['n'];
    if (!empty($t['st'])) { $st++; if ($v > 0) $sok++; else $miss[] = $t['n']; }
  }
  $pct = $tot ? (int)round($sum / ($tot * 3) * 100) : 0;
  $all = ($st > 0 && $sok === $st);

if ($done === 0)            $lbl = 'شروع نشده';
  elseif ($all && $pct >= 75) $lbl = 'عالی';
  elseif ($all && $pct >= 45) $lbl = 'معمول';
  elseif ($all)               $lbl = 'بقا';
  elseif ($pct >= 60)         $lbl = 'قوی (ستاره ناتمام)';
  else                        $lbl = 'در جریان';
  
  return $CACHE[$ck] = ['total' => $tot, 'done' => $done, 'pct' => $pct, 'stars' => $st,
          'stars_done' => $sok, 'stars_ok' => $all, 'label' => $lbl,
          'missing' => $miss, 'best' => $best,
          'verdict' => function_exists('eng_verdict') ? eng_verdict($pct, $all, $done) : null];
}
function streak_of(array $db, string $start, string $today): array {
  $cur = 0;
  for ($i = 0; $i < 400; $i++) {
    $d = d_add($today, -$i);
    if ($d < $start) break;
    if (stats_of($db, $d, $start)['stars_ok']) { $cur++; continue; }
    if ($i === 0) continue;   /* روز جاری هنوز فرصت دارد */
    break;
  }
  $best = $run = 0; $n = d_diff($start, $today);
  for ($i = 0; $i <= $n; $i++) {
    $d = d_add($start, $i);
    if (stats_of($db, $d, $start)['stars_ok']) { $run++; if ($run > $best) $best = $run; }
    else $run = 0;
  }
  return ['current' => $cur, 'best' => max($best, $cur)];
}
function trend_of(array $db, string $start, string $today, int $n = 14): array {
  $out = [];
  for ($i = $n - 1; $i >= 0; $i--) {
    $d = d_add($today, -$i);
    if ($d < $start) continue;
    $s = stats_of($db, $d, $start);
    $out[] = ['d' => $d, 'lbl' => j_short($d), 'pct' => $s['pct'],
              'st' => $s['label'], 'sok' => $s['stars_ok']];
  }
  return $out;
}

/* ---------- گزارش متنی ---------- */
function report_of(array $db, string $date, string $start, string $today): string {
  $day = d_diff($start, $date) + 1;
  $ph  = phase_of($day);
  $pi  = phase_info($ph);
  $rec = rec_of($db, $date);
  $s   = stats_of($db, $date, $start);
  $sk  = streak_of($db, $start, $today);
  $lv  = ['—', 'بقا', 'معمول', 'عالی'];

  $o  = "گزارش روزانه — " . j_full($date) . "\n";
  $o .= "روز " . fa($day) . " | هفتهٔ " . fa((int)ceil($day / 7)) . " | " . $pi['n'] . "\n";
  $o .= "وضعیت: {$s['label']}  |  امتیاز: " . fa($s['pct']) . "٪  |  انجام‌شده: "
      . fa($s['done']) . " از " . fa($s['total']) . "  |  نشکن‌ها: "
      . fa($s['stars_done']) . "/" . fa($s['stars']) . "\n";
  $o .= str_repeat('─', 34) . "\n";

  foreach (blocks_for($ph) as $b) {
    $o .= "\n▌ " . $b['n'] . (isset($b['tm']) ? '  (' . fa($b['tm']) . ')' : '') . "\n";
    foreach ($b['t'] as $t) {
      $v  = (int)($rec['t'][$t['k']] ?? 0);
      $ex = '';
      if (!empty($t['br'])) {
        $m = $rec['br'][$t['k']] ?? $t['br'];
        $ex = ($m === 'p') ? ' [خمیردندان]' : ' [آب خالی]';
      }
      $o .= '  ' . ($v ? '✔' : '✘') . ' ' . $t['n'] . ($v ? ' — ' . $lv[$v] : '') . $ex . "\n";
    }
  }
  if ($s['missing']) $o .= "\n⚠ نشکن‌های جامانده: " . implode('، ', $s['missing']) . "\n";
  if ($s['best'])    $o .= "★ در سطح عالی: " . implode('، ', $s['best']) . "\n";

  $xs = [];
  foreach ((array)cfg('tests', []) as $t)
    if (!empty($rec['x'][$t['k']])) $xs[] = '  • ' . $t['n'] . ': ' . fa($rec['x'][$t['k']]);
  if ($xs) $o .= "\n▌ سنجه‌ها\n" . implode("\n", $xs) . "\n";

  if ($rec['n'] !== '') $o .= "\n▌ یادداشت\n  " . str_replace("\n", "\n  ", $rec['n']) . "\n";
  $o .= "\nزنجیرهٔ فعلی: " . fa($sk['current']) . " روز  |  رکورد: " . fa($sk['best']) . " روز\n";
  return $o;
}
function phase_info(int $p): array {
  $all = (array)cfg('phases', []);
  $x = $all[$p] ?? $all[$p - 1] ?? [];
  if (is_string($x)) $x = ['n' => $x, 'd' => ''];
  return ['n' => (string)($x['n'] ?? ('فاز ' . fa($p))), 'd' => (string)($x['d'] ?? ($x['g'] ?? ''))];
}

/* ---------- آماده‌سازی وضعیت ---------- */
$today = date('Y-m-d');
$db    = store_read();
if (empty($db['start'])) {
  $db['start'] = (defined('START_DATE') && d_ok(START_DATE)) ? START_DATE : $today;
  if (store_writable()) store_write($db);
}
$start = d_ok($db['start']) ? $db['start'] : $today;

$in = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $raw = file_get_contents('php://input');
  $tmp = $raw ? json_decode($raw, true) : null;
  $in  = is_array($tmp) ? $tmp : $_POST;
}
/* ---------- محافظت CSRF ---------- */
define('API_CSRF_STRICT', false);   // بعد از پچ app.js این را true کن
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $tok = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $in['csrf'] ?? '');
  $okc = function_exists('csrf_verify') ? csrf_verify($tok) : true;
  if (!$okc) {
    // ایمپورت همیشه سخت‌گیر است — خطرناک‌ترین اکشن
    if (API_CSRF_STRICT || ($_GET['a'] ?? '') === 'import') fail('csrf-invalid', 403);
    @error_log('[daftar] csrf-soft-warn: ' . ($_GET['a'] ?? '?'));
  }
}
$act  = (string)($_GET['a'] ?? 'state');
$date = (string)($in['date'] ?? $_GET['d'] ?? $today);
if (!d_ok($date)) $date = $today;
if ($date > $today) $date = $today;
if ($date < $start) $date = $start;
$day = d_diff($start, $date) + 1;

function bundle(array $db, string $date, string $start, string $today): array {
  return ['ok' => true,
          'stats'  => stats_of($db, $date, $start),
          'streak' => streak_of($db, $start, $today),
          'trend'  => trend_of($db, $start, $today, 14)];
}
function touch_rec(array &$db, string $date): void {
  if (!isset($db['days'][$date]))
    $db['days'][$date] = ['t' => [], 'v' => [], 'br' => [], 'n' => '', 'x' => []];
  foreach (['t', 'v', 'br', 'x'] as $f)          // ← 'v' اضافه شد
    if (!is_array($db['days'][$date][$f] ?? null)) $db['days'][$date][$f] = [];
}

/* ---------- مسیرها ---------- */
switch ($act) {

  case 'state':
    $ph = phase_of($day);
    $qs = array_values((array)cfg('quotes', []));
    $q  = $qs ? $qs[($day - 1) % count($qs)] : '';
    if (is_string($q)) $q = ['t' => $q, 'a' => ''];
    $tests = [];
    foreach ((array)cfg('tests', []) as $t)
      if ((int)($t['ph'] ?? 1) <= $ph) $tests[] = $t;
    $S = function_exists('get_app_settings') ? get_app_settings() : [];
    out([
      'ok'          => true,
      'mode'        => store_writable() ? 'server' : 'local',
      'ver'         => defined('APP_VER') ? APP_VER : '2.0',
      'date'        => $date,
      'today'       => $today,
      'now'         => date('H:i'),
      'day'         => $day,
      'week'        => (int)ceil($day / 7),
      'phase'       => $ph,
      'phaseInfo'   => phase_info($ph),
      'prayers'     => function_exists('get_prayer_times') ? get_prayer_times() : cfg('prayers', []),
      'quote'       => $q,
      'blocks'      => blocks_for($ph),
      'win_modes'   => function_exists('eng_win_modes') ? eng_win_modes() : [],
      'tests'       => $tests,
      'rules'       => cfg('rules', []),
      'protocols'   => cfg('protocols', []),
      'phases_info' => array_values((array)cfg('phases', [])),
      'anchors'     => $S['anchors'] ?? [],
      'exercises'   => $S['exercises'] ?? [],
      'workouts'    => $S['workouts'] ?? [],
      'rec'         => rec_of($db, $date),
      'stats'       => stats_of($db, $date, $start),
      'streak'      => streak_of($db, $start, $today),
      'trend'       => trend_of($db, $start, $today, 14),
    ]);
    break;

  case 'tick':
    if (!store_writable()) fail('not-writable');
    $k = (string)($in['k'] ?? ''); $v = (int)($in['v'] ?? 0);
    if ($k === '' || !isset(all_keys()[$k])) fail('bad-key');
    if ($v < 0 || $v > 3) fail('bad-value');
    touch_rec($db, $date);
    if ($v === 0) unset($db['days'][$date]['t'][$k]);
    else $db['days'][$date]['t'][$k] = $v;
    if (!store_write($db)) fail('write-failed');
    out(bundle($db, $date, $start, $today));

case 'val':   /* ورود جزئی: عدد / حالت نماز / چک‌لیست */
    if (!store_writable()) fail('not-writable');
    if (!function_exists('eng_eval')) fail('engine-missing');
    $k = (string)($in['k'] ?? '');
    $t = task_of($k);
    if ($t === null) fail('bad-key');

    $clean = eng_sanitize($t, $in['v'] ?? 0);
    $ev    = eng_eval($t, $clean);

    touch_rec($db, $date);
    $isEmpty = ($ev['lv'] === 0);
    if ($isEmpty) { unset($db['days'][$date]['v'][$k], $db['days'][$date]['t'][$k]); }
    else {
      $db['days'][$date]['v'][$k] = $clean;
      $db['days'][$date]['t'][$k] = $ev['lv'];   // سازگاری کامل با آمار موجود
    }
    if (!store_write($db)) fail('write-failed');

    $b = bundle($db, $date, $start, $today);
    $b['eval'] = $ev;
    $b['rec']  = rec_of($db, $date);
    out($b);
    
  case 'brush':
    if (!store_writable()) fail('not-writable');
    $k = (string)($in['k'] ?? ''); $m = (string)($in['m'] ?? '');
    if ($k === '' || !isset(all_keys()[$k])) fail('bad-key');
    if (!in_array($m, ['p', 'w'], true)) fail('bad-mode');
    touch_rec($db, $date);
    $db['days'][$date]['br'][$k] = $m;
    if (!store_write($db)) fail('write-failed');
    out(bundle($db, $date, $start, $today));

  case 'note':
    if (!store_writable()) fail('not-writable');
    touch_rec($db, $date);
    $db['days'][$date]['n'] = mb_substr((string)($in['text'] ?? ''), 0, 5000);
    if (!store_write($db)) fail('write-failed');
    out(bundle($db, $date, $start, $today));

  case 'test':
    if (!store_writable()) fail('not-writable');
    $k = (string)($in['k'] ?? '');
    if ($k === '' || !preg_match('/^[A-Za-z0-9_\-]{1,40}$/', $k)) fail('bad-key');
    touch_rec($db, $date);
    $val = mb_substr(trim((string)($in['val'] ?? '')), 0, 200);
    if ($val === '') unset($db['days'][$date]['x'][$k]);
    else $db['days'][$date]['x'][$k] = $val;
    if (!store_write($db)) fail('write-failed');
    out(bundle($db, $date, $start, $today));

  case 'report':
    out(['ok' => true, 'date' => $date, 'text' => report_of($db, $date, $start, $today)]);

  case 'export':
    header_remove('Content-Type');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="daftar-' . $today . '.json"');
    echo json_encode($db, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;

  case 'import':
    if (!store_writable()) fail('not-writable');
    $p = $in['db'] ?? null;
    if (!is_array($p) || !isset($p['days']) || !is_array($p['days'])) fail('bad-payload');
    $clean = ['start' => d_ok($p['start'] ?? '') ? $p['start'] : $start, 'days' => []];
    foreach ($p['days'] as $d => $r) {
      if (!d_ok((string)$d) || !is_array($r)) continue;
      $clean['days'][$d] = [
        't'  => is_array($r['t']  ?? null) ? $r['t']  : [],
        'br' => is_array($r['br'] ?? null) ? $r['br'] : [],
        'n'  => mb_substr((string)($r['n'] ?? ''), 0, 5000),
        'x'  => is_array($r['x'] ?? null) ? $r['x'] : [],
      ];
    }
  
    /* بکاپ خودکار قبل از جایگزینی */
    if (is_file(DB_FILE)) {
      $bk = DB_DIR . '/backup';
      if (!is_dir($bk)) @mkdir($bk, 0755, true);
      @copy(DB_FILE, $bk . '/db-' . date('Ymd-His') . '.json');
      /* نگهداری ۲۰ بکاپ آخر */
      $fs = glob($bk . '/db-*.json') ?: [];
      if (count($fs) > 20) { sort($fs); foreach (array_slice($fs, 0, count($fs) - 20) as $f) @unlink($f); }
    }
    if (!store_write($clean)) fail('write-failed');
  
    out(['ok' => true, 'days' => count($clean['days'])]);

  case 'ping':
    out(['ok' => true, 'mode' => store_writable() ? 'server' : 'local',
         'php' => PHP_VERSION, 'tz' => date_default_timezone_get(),
         'now' => date('Y-m-d H:i'), 'start' => $start,
         'blocks' => count((array)cfg('blocks', [])), 'keys' => count(all_keys())]);

  default:
    fail('unknown-action');
}
/* ===== END: api.php ===== */