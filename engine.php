<?php
/* ===== FILE: engine.php | موتور ورود جزئی و امتیاز رفتاری | v2.0 =====
   سازگار با مدل ۰–۳ موجود در api.php — بدون شکستن چیزی
   ==================================================================== */
declare(strict_types=1);

/* ---------------------------------------------------------------
   انواع تسک:
   'lv'  → سه‌سطحی دستی (پیش‌فرض؛ رفتار فعلی برنامه)
   'qty' → کمّی      | tg=هدف  sv=حداقل بقا  u=واحد
   'win' → پنجره‌ای  | نماز: prime|valid|qada|miss
   'chk' → چک‌لیستی  | it=[موارد]  sv=حداقل تیک
   --------------------------------------------------------------- */

if (!function_exists('eng_fa')) {
  function eng_fa($s): string {
    return str_replace(['0','1','2','3','4','5','6','7','8','9'],
                       ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], (string)$s);
  }
}

/** حالت‌های تسک پنجره‌ای (نماز) → سطح ۰..۳ + درصد دقیق */
function eng_win_modes(): array {
  return [
    'prime'  => ['n' => 'اول وقت',        'lv' => 3, 'p' => 100],
    'valid'  => ['n' => 'داخل وقت',       'lv' => 2, 'p' => 70],
    'qada'   => ['n' => 'قضا (همان روز)', 'lv' => 1, 'p' => 35],
    'miss'   => ['n' => 'نشد',            'lv' => 0, 'p' => 0],
  ];
}

/** نوع تسک را برگردان (با پیش‌فرض ایمن) */
function eng_type(array $t): string {
  $ty = (string)($t['ty'] ?? 'lv');
  return in_array($ty, ['lv','qty','win','chk'], true) ? $ty : 'lv';
}

/**
 * قلب موتور: مقدار خام → سطح ۰..۳ + درصد دقیق + متن فارسی
 * @param array $t   تعریف تسک از data.php
 * @param mixed $raw مقدار خام (عدد | رشتهٔ حالت | آرایهٔ تیک‌ها)
 */
function eng_eval(array $t, $raw): array {
  $ty = eng_type($t);

  /* ---------- پنجره‌ای (نماز) ---------- */
  if ($ty === 'win') {
    $M = eng_win_modes();
    $m = is_string($raw) && isset($M[$raw]) ? $raw : 'miss';
    return eng_pack($M[$m]['lv'], $M[$m]['p'], $M[$m]['n'], $t);
  }

  /* ---------- چک‌لیستی ---------- */
  if ($ty === 'chk') {
    $items = (array)($t['it'] ?? []);
    $all   = max(1, count($items));
    $done  = 0;
    if (is_array($raw)) foreach ($raw as $x) if (!empty($x)) $done++;
    $done = min($done, $all);
    $r    = $done / $all;
    $lv   = $r >= 1 ? 3 : ($r >= 0.5 ? 2 : ($done > 0 ? 1 : 0));
    return eng_pack($lv, (int)round($r * 100),
      eng_fa($done) . ' از ' . eng_fa($all) . ' مورد', $t);
  }

  /* ---------- کمّی ---------- */
  if ($ty === 'qty') {
    $tg   = max(0.001, (float)($t['tg'] ?? 1));
    $sv   = (float)($t['sv'] ?? 0);
    $done = max(0.0, (float)$raw);
    $r    = $done / $tg;

    if     ($r >= 1.0)                 $lv = 3;   // عالی
    elseif ($r >= 0.5)                 $lv = 2;   // معمول
    elseif ($sv > 0 && $done >= $sv)   $lv = 1;   // بقا
    elseif ($done > 0)                 $lv = 1;   // جرقه (زنجیره نمی‌شکند)
    else                               $lv = 0;

    $u  = (string)($t['u'] ?? '');
    $txt = eng_fa(eng_num($done)) . ' از ' . eng_fa(eng_num($tg)) . ($u !== '' ? ' ' . $u : '');
    return eng_pack($lv, min(100, (int)round($r * 100)), $txt, $t);
  }

  /* ---------- سه‌سطحی دستی (رفتار فعلی) ---------- */
  $lv = (int)$raw;
  if ($lv < 0) $lv = 0;
  if ($lv > 3) $lv = 3;
  $lbl = (array)($t['lv'] ?? []);
  $txt = $lv > 0 ? (string)($lbl[$lv - 1] ?? '') : 'نشد';
  return eng_pack($lv, (int)round($lv / 3 * 100), $txt, $t);
}

function eng_num(float $n): string {
  return rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.');
}

function eng_pack(int $lv, int $pct, string $txt, array $t): array {
  $meta = [
    0 => ['n' => 'نشد',   'c' => '#64748b', 'i' => '⚪'],
    1 => ['n' => 'بقا',   'c' => '#f97316', 'i' => '🟠'],
    2 => ['n' => 'معمول', 'c' => '#eab308', 'i' => '🟡'],
    3 => ['n' => 'عالی',  'c' => '#22c55e', 'i' => '🟢'],
  ][$lv];
  return [
    'lv'   => $lv,
    'pct'  => $pct,
    'text' => $txt !== '' ? $txt : $meta['n'],
    'name' => $meta['n'],
    'color'=> $meta['c'],
    'icon' => $meta['i'],
    'w'    => max(1, (int)($t['w'] ?? 1)),   // وزن تسک
  ];
}

/** اعتبارسنجی مقدار ورودی بر اساس نوع تسک — قبل از ذخیره */
function eng_sanitize(array $t, $raw) {
  switch (eng_type($t)) {
    case 'win':
      return isset(eng_win_modes()[(string)$raw]) ? (string)$raw : 'miss';
    case 'chk':
      $n = max(1, count((array)($t['it'] ?? [])));
      $o = [];
      for ($i = 0; $i < $n; $i++) $o[$i] = !empty(((array)$raw)[$i]) ? 1 : 0;
      return $o;
    case 'qty':
      $v = (float)$raw;
      if ($v < 0) $v = 0;
      $cap = (float)($t['tg'] ?? 1) * 10;   // سقف منطقی ضد خرابی داده
      return $v > $cap ? $cap : round($v, 2);
    default:
      $v = (int)$raw;
      return $v < 0 ? 0 : ($v > 3 ? 3 : $v);
  }
}

/** پیام روز — لحن کوچینگ، بدون سرزنش */
function eng_verdict(int $pct, bool $starsOk, int $done): array {
  if ($pct >= 85 && $starsOk) return ['t'=>'روز درخشان','m'=>'امروز در بهترین حالت خودت بودی. این حس را یادت بماند.','i'=>'🏆','c'=>'#22c55e'];
  if ($pct >= 65)             return ['t'=>'روز قوی','m'=>'بیشتر کارها انجام شد. مسیر درست است.','i'=>'💪','c'=>'#3b82f6'];
  if ($pct >= 40)             return ['t'=>'روز متعادل','m'=>'کامل نبود، اما نگهش داشتی. همین مهم است.','i'=>'⚖️','c'=>'#eab308'];
  if ($pct >= 15)             return ['t'=>'روز بقا','m'=>'امروز سخت بود و تو نشکستی. زنجیره سرِ جایش است.','i'=>'🛡️','c'=>'#f97316'];
  if ($done > 0)              return ['t'=>'یک جرقه','m'=>'خیلی کم بود، ولی صفر نبود. فردا از همین‌جا ادامه می‌دهیم.','i'=>'✨','c'=>'#a16207'];
  return                             ['t'=>'روز خالی','m'=>'امروز نشد. اشکالی ندارد — فردا فقط یک کار کوچک، همین کافی است.','i'=>'🌙','c'=>'#64748b'];
}
/* ===== END: engine.php ===== */