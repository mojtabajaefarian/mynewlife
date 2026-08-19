<?php
/* ===== FILE: config.php | هسته: زمان، دیتابیس، و استخراج اوقات شرعی ===== */
declare(strict_types=1);
require_once __DIR__ . '/settings.php';

$SETTINGS = get_app_settings();
const APP_VER  = '2.0.0';
const TZ       = 'Asia/Tehran';
const DATA_DIR = __DIR__ . '/data';
const DB_FILE  = DATA_DIR . '/db.json';
const START_DATE = '2026-08-15';

date_default_timezone_set(TZ);
mb_internal_encoding('UTF-8');
if (function_exists('ini_set')) { @ini_set('display_errors', '0'); }

// تزریق تنظیمات به GLOBALS برای سازگاری با cfg() در api.php
$GLOBALS['blocks'] = $SETTINGS['blocks'] ?? [];
$GLOBALS['tests'] = $SETTINGS['tests'] ?? [];
$GLOBALS['quotes'] = $SETTINGS['quotes'] ?? [];
$GLOBALS['phases'] = $SETTINGS['phases'] ?? [];
$GLOBALS['rules'] = $SETTINGS['rules'] ?? [];
$GLOBALS['protocols'] = $SETTINGS['protocols'] ?? [];

/* ---------- کمکی‌های عمومی ---------- */
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function jout($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
function today_key(): string { return date('Y-m-d'); }
function day_number(string $start, string $today): int {
    $a = strtotime($start . ' 00:00:00'); $b = strtotime($today . ' 00:00:00');
    if ($a === false || $b === false) return 1;
    return max(1, (int)floor(($b - $a) / 86400) + 1);
}

/* ---------- تاریخ شمسی ---------- */
function g2j(int $gy, int $gm, int $gd): array {
    $gdm = [0,31,59,90,120,151,181,212,243,273,304,334];
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + (int)(($gy2 + 3) / 4) - (int)(($gy2 + 99) / 100)
          + (int)(($gy2 + 399) / 400) + $gd + $gdm[$gm - 1];
    $jy = -1595 + 33 * (int)($days / 12053); $days %= 12053;
    $jy += 4 * (int)($days / 1461); $days %= 1461;
    if ($days > 365) { $jy += (int)(($days - 1) / 365); $days = ($days - 1) % 365; }
    if ($days < 186) { $jm = 1 + (int)($days / 31); $jd = 1 + ($days % 31); }
    else { $days -= 186; $jm = 7 + (int)($days / 30); $jd = 1 + ($days % 30); }
    return [$jy, $jm, $jd];
}
const J_MONTHS = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
const J_DAYS   = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
function fa_num($v): string {
    return str_replace(['0','1','2','3','4','5','6','7','8','9'],
        ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], (string)$v);
}
function jlabel(string $ymd): string {
    $t = strtotime($ymd . ' 00:00:00'); if ($t === false) return '—';
    [$jy, $jm, $jd] = g2j((int)date('Y',$t), (int)date('n',$t), (int)date('j',$t));
    return J_DAYS[(int)date('w',$t)] . ' ' . fa_num($jd) . ' ' . J_MONTHS[$jm-1] . ' ' . fa_num($jy);
}

/* ---------- استخراج زنده اوقات شرعی از بادصبا ---------- */
function get_prayer_times(): array {
    $url = 'https://badesaba.ir/owghat/1229/%D9%81%D8%B1%D8%AF%DB%8C%D8%B3';
    $cache = __DIR__ . '/data/prayers_cache.json';
    if (file_exists($cache) && (time() - filemtime($cache) < 43200)) {
        $d = json_decode(file_get_contents($cache), true);
        if ($d) return $d;
    }
    $ctx = stream_context_create(['http'=>['header'=>"User-Agent: Mozilla/5.0\r\n"]]);
    $html = @file_get_contents($url, false, $ctx);
    $map = ['اذان صبح'=>'fajr','طلوع آفتاب'=>'sunrise','اذان ظهر'=>'dhuhr','اذان عصر'=>'asr','غروب آفتاب'=>'sunset','اذان مغرب'=>'maghrib','اذان عشاء'=>'isha','نیمه‌شب شرعی'=>'midnight'];
    $icons = ['fajr'=>'🌄','sunrise'=>'☀️','dhuhr'=>'🕛','asr'=>'🕒','sunset'=>'🌇','maghrib'=>'🌆','isha'=>'🌃','midnight'=>'🌙'];
    $out = [];
    if ($html && preg_match_all('/<p[^>]*class="time"[^>]*>\s*([0-9]{2}:[0-9]{2})\s*<\/p>\s*<p[^>]*class="title"[^>]*>\s*(.*?)\s*<\/p>/su', $html, $m, PREG_SET_ORDER)) {
        foreach ($m as $x) {
            $t = trim($x[2]);
            if (isset($map[$t])) {
                $k = $map[$t];
                $out[] = ['k'=>$k, 'n'=>$t, 't'=>trim($x[1]), 'i'=>$icons[$k]];
            }
        }
    }
    if (count($out) >= 5) {
        @file_put_contents($cache, json_encode($out, JSON_UNESCAPED_UNICODE));
        return $out;
    }
    return [
        ['k'=>'fajr','n'=>'اذان صبح','t'=>'03:50','i'=>'🌄'], ['k'=>'sunrise','n'=>'طلوع آفتاب','t'=>'05:23','i'=>'☀️'],
        ['k'=>'dhuhr','n'=>'اذان ظهر','t'=>'12:11','i'=>'🕛'], ['k'=>'asr','n'=>'اذان عصر','t'=>'15:55','i'=>'🕒'],
        ['k'=>'sunset','n'=>'غروب آفتاب','t'=>'18:58','i'=>'🌇'], ['k'=>'maghrib','n'=>'اذان مغرب','t'=>'19:17','i'=>'🌆'],
        ['k'=>'isha','n'=>'اذان عشاء','t'=>'20:09','i'=>'🌃'], ['k'=>'midnight','n'=>'نیمه‌شب شرعی','t'=>'23:24','i'=>'🌙']
    ];
}

/* ---------- دیتابیس JSON ---------- */
function db_blank(): array {
    return ['ver'=>APP_VER, 'start'=>START_DATE, 'days'=>[], 'notes'=>[], 'meta'=>['saved'=>null]];
}
function db_dir_ok(): bool {
    if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0755, true); }
    return is_dir(DATA_DIR);
}
function db_writable(): bool {
    if (!db_dir_ok()) return false;
    if (file_exists(DB_FILE)) return is_writable(DB_FILE);
    return is_writable(DATA_DIR);
}
function db_read(): array {
    if (!file_exists(DB_FILE)) return db_blank();
    $raw = @file_get_contents(DB_FILE);
    if ($raw === false || $raw === '') return db_blank();
    $d = json_decode($raw, true);
    if (!is_array($d)) return db_blank();
    $d += db_blank();
    if (!isset($d['days']) || !is_array($d['days'])) $d['days'] = [];
    if (empty($d['start'])) $d['start'] = START_DATE;
    return $d;
}
function db_write(array $d): bool {
    if (!db_dir_ok()) return false;
    $d['ver'] = APP_VER;
    $d['meta']['saved'] = date('c');
    $json = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) return false;
    $tmp = DB_FILE . '.' . getmypid() . '.tmp';
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) return false;
    if (!@rename($tmp, DB_FILE)) { @unlink($tmp); return false; }
    @chmod(DB_FILE, 0644);
    return true;
}
if (db_dir_ok() && !file_exists(DATA_DIR . '/.htaccess')) {
    @file_put_contents(DATA_DIR . '/.htaccess', "Require all denied\n");
}