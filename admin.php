<?php
/* ===== FILE: admin.php | صفحه مدیریت تنظیمات با قابلیت ویرایش کامل ===== */
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
auth_guard();

require_once __DIR__ . '/settings.php';

$message = '';
$error = '';

// پردازش فرم‌های ارسالی
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $settings = get_app_settings();
    
    if ($action === 'add_block') {
        $newBlock = [
            'k' => 'block_' . time(),
            'anchor' => $_POST['anchor'] ?? 'fajr',
            'n' => $_POST['name'] ?? 'بلوک جدید',
            'i' => $_POST['icon'] ?? '📦',
            'h' => $_POST['hint'] ?? '',
            'desc' => $_POST['desc'] ?? '',
            'tips' => array_filter(array_map('trim', explode("\n", $_POST['tips'] ?? ''))),
            't' => []
        ];
        $settings['blocks'][] = $newBlock;
        if (save_app_settings($settings)) $message = 'بلوک جدید اضافه شد';
        else $error = 'خطا در ذخیره';
    }
    
    if ($action === 'add_task') {
        $blockIdx = (int)$_POST['block_idx'];
        $newTask = [
            'k' => 'task_' . time(),
            'n' => $_POST['name'] ?? 'کار جدید',
            'i' => $_POST['icon'] ?? '⬜',
            'ph' => (int)($_POST['phase'] ?? 1),
            'st' => isset($_POST['star']) ? 1 : 0,
            'options' => [
                ['v' => 1, 'txt' => $_POST['opt1_txt'] ?? 'بقا', 'score' => (int)($_POST['opt1_score'] ?? 1)],
                ['v' => 2, 'txt' => $_POST['opt2_txt'] ?? 'معمول', 'score' => (int)($_POST['opt2_score'] ?? 2)],
                ['v' => 3, 'txt' => $_POST['opt3_txt'] ?? 'عالی', 'score' => (int)($_POST['opt3_score'] ?? 3)]
            ],
            'desc' => $_POST['desc'] ?? '',
            'video' => $_POST['video'] ?? '',
            'mistake' => $_POST['mistake'] ?? ''
        ];
        if (isset($settings['blocks'][$blockIdx])) {
            $settings['blocks'][$blockIdx]['t'][] = $newTask;
            if (save_app_settings($settings)) $message = 'کار جدید به بلوک اضافه شد';
            else $error = 'خطا در ذخیره';
        }
    }
    
    if ($action === 'add_quote') {
        $settings['quotes'][] = [
            't' => $_POST['text'] ?? '',
            'a' => $_POST['author'] ?? ''
        ];
        if (save_app_settings($settings)) $message = 'جمله جدید اضافه شد';
        else $error = 'خطا در ذخیره';
    }
    
    if ($action === 'add_rule') {
        $settings['rules'][] = [
            't' => $_POST['title'] ?? '',
            'd' => $_POST['desc'] ?? ''
        ];
        if (save_app_settings($settings)) $message = 'قانون جدید اضافه شد';
        else $error = 'خطا در ذخیره';
    }
    
    if ($action === 'add_exercise') {
        $settings['exercises'][] = [
            'k' => 'ex_' . time(),
            'n' => $_POST['name'] ?? '',
            'i' => $_POST['icon'] ?? '💪',
            'area' => $_POST['area'] ?? '',
            'desc' => $_POST['desc'] ?? '',
            'video' => $_POST['video'] ?? ''
        ];
        if (save_app_settings($settings)) $message = 'حرکت جدید اضافه شد';
        else $error = 'خطا در ذخیره';
    }
    
    if ($action === 'delete_item') {
        $type = $_POST['type'] ?? '';
        $idx = (int)$_POST['idx'];
        if (isset($settings[$type][$idx])) {
            array_splice($settings[$type], $idx, 1);
            if (save_app_settings($settings)) $message = 'حذف شد';
        }
    }
    
    if ($action === 'update_config') {
        $settings['config'] = [
            'start_date' => $_POST['start_date'] ?? $settings['config']['start_date'],
            'timezone' => $_POST['timezone'] ?? 'Asia/Tehran',
            'prayer_url' => $_POST['prayer_url'] ?? $settings['config']['prayer_url']
        ];
        if (save_app_settings($settings)) $message = 'تنظیمات ذخیره شد';
        else $error = 'خطا در ذخیره';
    }
    
    if ($action === 'edit_block') {
        $idx = (int)$_POST['block_idx'];
        if (isset($settings['blocks'][$idx])) {
            $settings['blocks'][$idx]['n'] = $_POST['name'];
            $settings['blocks'][$idx]['h'] = $_POST['hint'];
            $settings['blocks'][$idx]['desc'] = $_POST['desc'];
            $settings['blocks'][$idx]['tips'] = array_filter(array_map('trim', explode("\n", $_POST['tips'])));
            $settings['blocks'][$idx]['anchor'] = $_POST['anchor'];
            if (save_app_settings($settings)) $message = 'بلوک ویرایش شد';
        }
    }
    
    // بارگذاری مجدد بعد از تغییرات
    $settings = get_app_settings();
}

$settings = get_app_settings();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>⚙️ مدیریت تنظیمات برنامه</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Vazirmatn', sans-serif; background: #0b1020; color: #e8ecf6; padding: 20px; line-height: 1.8; }
.container { max-width: 1400px; margin: 0 auto; }
h1 { font-size: 24px; margin-bottom: 20px; color: #38bdf8; border-bottom: 2px solid #26314f; padding-bottom: 10px; }
h2 { font-size: 18px; margin: 30px 0 15px; color: #fbbf24; }
h3 { font-size: 15px; margin: 10px 0; color: #38bdf8; }
.card { background: #141b31; border: 1px solid #26314f; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 15px; }
.item { background: #1b2440; border: 1px solid #26314f; border-radius: 10px; padding: 15px; position: relative; }
.item h3 { color: #38bdf8; margin-bottom: 8px; }
.item p { font-size: 13px; color: #98a3bd; }
.tag { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; margin: 3px; }
.tag.anchor { background: #0f2033; color: #38bdf8; }
.tag.phase { background: #2a2110; color: #fbbf24; }
.btn { display: inline-block; padding: 8px 16px; border-radius: 8px; background: #0ea5e9; color: white; text-decoration: none; font-weight: 700; margin: 5px 5px 5px 0; border: none; cursor: pointer; font-family: inherit; font-size: 13px; }
.btn:hover { background: #0284c7; }
.btn.back { background: #64748b; }
.btn.danger { background: #dc2626; }
.btn.success { background: #16a34a; }
input, textarea, select { width: 100%; background: #0b1020; border: 1px solid #26314f; color: #e8ecf6; padding: 8px 12px; border-radius: 8px; font-family: inherit; font-size: 13px; margin: 5px 0; }
input:focus, textarea:focus, select:focus { outline: none; border-color: #0ea5e9; }
label { display: block; font-size: 12px; color: #98a3bd; margin: 8px 0 4px; font-weight: 700; }
.message { padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; }
.message.success { background: #0d3227; color: #86efac; border: 1px solid #16a34a; }
.message.error { background: #3a1414; color: #fca5a5; border: 1px solid #dc2626; }
.form-group { background: #1b2440; padding: 15px; border-radius: 10px; margin: 10px 0; }
.delete-btn { position: absolute; top: 10px; left: 10px; background: #dc2626; color: white; border: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; cursor: pointer; font-family: inherit; }
.delete-btn:hover { background: #b91c1c; }
.task-list { margin-top: 10px; background: #0b1020; border-radius: 8px; padding: 10px; }
.task-item { background: #141b31; padding: 10px; border-radius: 6px; margin: 6px 0; border-right: 3px solid #0ea5e9; }
.task-item small { color: #98a3bd; font-size: 11px; }
details { margin: 10px 0; }
summary { cursor: pointer; padding: 10px; background: #1b2440; border-radius: 8px; font-weight: 700; }
summary:hover { background: #232e4f; }
.tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; border-bottom: 1px solid #26314f; padding-bottom: 10px; }
.tab { padding: 8px 16px; background: #1b2440; border-radius: 8px; cursor: pointer; font-weight: 700; color: #98a3bd; }
.tab.active { background: #0ea5e9; color: white; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 20px; }
.stat { background: #1b2440; padding: 12px; border-radius: 10px; text-align: center; }
.stat b { display: block; font-size: 24px; color: #38bdf8; }
.stat span { font-size: 11px; color: #98a3bd; }
</style>
</head>
<body>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h1>⚙️ مدیریت تنظیمات برنامه</h1>
        <div>
            <a href="index.php" class="btn back">← بازگشت به برنامه</a>
            <a href="api.php?a=ping" class="btn" target="_blank">🔍 تست API</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message success">✔ <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error">✘ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat"><b><?= count($settings['anchors'] ?? []) ?></b><span>لنگر</span></div>
        <div class="stat"><b><?= count($settings['blocks'] ?? []) ?></b><span>بلوک</span></div>
        <div class="stat"><b><?= array_sum(array_map(fn($b) => count($b['t'] ?? []), $settings['blocks'] ?? [])) ?></b><span>کار</span></div>
        <div class="stat"><b><?= count($settings['exercises'] ?? []) ?></b><span>حرکت</span></div>
        <div class="stat"><b><?= count($settings['workouts'] ?? []) ?></b><span>برنامه ورزشی</span></div>
        <div class="stat"><b><?= count($settings['rules'] ?? []) ?></b><span>قانون</span></div>
        <div class="stat"><b><?= count($settings['quotes'] ?? []) ?></b><span>جمله انگیزشی</span></div>
        <div class="stat"><b><?= count($settings['tests'] ?? []) ?></b><span>تست</span></div>
    </div>

    <div class="tabs">
        <div class="tab active" data-tab="blocks">📦 بلوک‌ها و کارها</div>
        <div class="tab" data-tab="exercises">💪 حرکات اصلاحی</div>
        <div class="tab" data-tab="rules">📜 قوانین</div>
        <div class="tab" data-tab="quotes">💬 جملات انگیزشی</div>
        <div class="tab" data-tab="config">⚙️ تنظیمات کلی</div>
    </div>

    <!-- بخش بلوک‌ها -->
    <div class="tab-content active" id="blocks">
        <div class="card">
            <h2>➕ افزودن بلوک جدید</h2>
            <form method="post">
                <input type="hidden" name="action" value="add_block">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <div>
                        <label>نام بلوک</label>
                        <input type="text" name="name" required placeholder="مثلاً: پروتکل نماز صبح">
                    </div>
                    <div>
                        <label>آیکن</label>
                        <input type="text" name="icon" value="📦" placeholder="🌄">
                    </div>
                    <div>
                        <label>لنگر متصل</label>
                        <select name="anchor">
                            <?php foreach ($settings['anchors'] ?? [] as $k => $a): ?>
                                <option value="<?= $k ?>"><?= $a['n'] ?> (<?= $a['time'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>نکته کلیدی</label>
                        <input type="text" name="hint" placeholder="مثلاً: بیدار شو، بخوان، برگرد بخواب">
                    </div>
                </div>
                <label>توضیحات</label>
                <textarea name="desc" rows="2" placeholder="توضیحات کامل بلوک..."></textarea>
                <label>نکات (هر خط یک نکته)</label>
                <textarea name="tips" rows="3" placeholder="نکته اول&#10;نکته دوم"></textarea>
                <button type="submit" class="btn success">➕ افزودن بلوک</button>
            </form>
        </div>

        <h2>📋 بلوک‌های فعلی</h2>
        <?php foreach ($settings['blocks'] as $idx => $block): ?>
        <div class="card">
            <details>
                <summary style="display: flex; justify-content: space-between; align-items: center;">
                    <span><?= $block['i'] ?? '📦' ?> <?= htmlspecialchars($block['n']) ?> 
                        <span class="tag anchor"><?= $settings['anchors'][$block['anchor']]['n'] ?? $block['anchor'] ?></span>
                        <span class="tag phase"><?= count($block['t'] ?? []) ?> کار</span>
                    </span>
                </summary>
                <div style="padding: 15px;">
                    <form method="post">
                        <input type="hidden" name="action" value="edit_block">
                        <input type="hidden" name="block_idx" value="<?= $idx ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label>نام</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($block['n']) ?>">
                            </div>
                            <div>
                                <label>لنگر</label>
                                <select name="anchor">
                                    <?php foreach ($settings['anchors'] as $k => $a): ?>
                                        <option value="<?= $k ?>" <?= $k === $block['anchor'] ? 'selected' : '' ?>><?= $a['n'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <label>نکته</label>
                        <input type="text" name="hint" value="<?= htmlspecialchars($block['h'] ?? '') ?>">
                        <label>توضیحات</label>
                        <textarea name="desc" rows="2"><?= htmlspecialchars($block['desc'] ?? '') ?></textarea>
                        <label>نکات (هر خط یکی)</label>
                        <textarea name="tips" rows="3"><?= htmlspecialchars(implode("\n", $block['tips'] ?? [])) ?></textarea>
                        <button type="submit" class="btn">💾 ذخیره تغییرات</button>
                        <button type="submit" class="btn danger" onclick="this.form.querySelector('[name=action]').value='delete_item';this.form.insertAdjacentHTML('beforeend','<input type=hidden name=type value=blocks><input type=hidden name=idx value=<?= $idx ?>>');return confirm('حذف شود؟');">🗑 حذف بلوک</button>
                    </form>

                    <h3 style="margin-top: 20px;">کارهای این بلوک (<?= count($block['t'] ?? []) ?>)</h3>
                    <div class="task-list">
                        <?php foreach ($block['t'] ?? [] as $tidx => $task): ?>
                        <div class="task-item">
                            <strong><?= $task['i'] ?? '⬜' ?> <?= htmlspecialchars($task['n']) ?></strong>
                            <?php if (!empty($task['st'])): ?><span class="tag phase">⭐ ستاره</span><?php endif; ?>
                            <small>
                                فاز <?= $task['ph'] ?? 1 ?> · 
                                <?php foreach ($task['options'] ?? [] as $opt): ?>
                                    [<?= htmlspecialchars($opt['txt']) ?>: <?= $opt['score'] ?>]
                                <?php endforeach; ?>
                            </small>
                            <?php if (!empty($task['video'])): ?>
                                <div><a href="<?= htmlspecialchars($task['video']) ?>" target="_blank" style="color: #ef4444;">📺 ویدیو</a></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h3 style="margin-top: 20px;">➕ افزودن کار جدید به این بلوک</h3>
                    <form method="post">
                        <input type="hidden" name="action" value="add_task">
                        <input type="hidden" name="block_idx" value="<?= $idx ?>">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                            <div>
                                <label>نام کار</label>
                                <input type="text" name="name" required>
                            </div>
                            <div>
                                <label>آیکن</label>
                                <input type="text" name="icon" value="⬜">
                            </div>
                            <div>
                                <label>فاز فعال‌سازی</label>
                                <select name="phase">
                                    <option value="1">فاز ۱</option>
                                    <option value="2">فاز ۲</option>
                                    <option value="3">فاز ۳</option>
                                    <option value="4">فاز ۴</option>
                                </select>
                            </div>
                            <div>
                                <label>⭐ ستاره (غیرقابل حذف)</label>
                                <input type="checkbox" name="star" value="1" style="width: auto;">
                            </div>
                        </div>
                        
                        <h4 style="margin: 15px 0 5px; color: #fbbf24;">سه گزینه امتیازی:</h4>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                            <div style="background: #3a1414; padding: 10px; border-radius: 8px;">
                                <label style="color: #fca5a5;">🔴 بقا</label>
                                <input type="text" name="opt1_txt" value="بقا" placeholder="متن">
                                <input type="number" name="opt1_score" value="1" min="0" max="3">
                            </div>
                            <div style="background: #2a2110; padding: 10px; border-radius: 8px;">
                                <label style="color: #fcd34d;">🟡 معمول</label>
                                <input type="text" name="opt2_txt" value="معمول" placeholder="متن">
                                <input type="number" name="opt2_score" value="2" min="0" max="3">
                            </div>
                            <div style="background: #0d3227; padding: 10px; border-radius: 8px;">
                                <label style="color: #86efac;">🟢 عالی</label>
                                <input type="text" name="opt3_txt" value="عالی" placeholder="متن">
                                <input type="number" name="opt3_score" value="3" min="0" max="3">
                            </div>
                        </div>
                        
                        <label>توضیحات</label>
                        <textarea name="desc" rows="2" placeholder="توضیحات کار..."></textarea>
                        <label>اشتباه رایج</label>
                        <input type="text" name="mistake" placeholder="مثلاً: فکر کنید...">
                        <label>لینک ویدیو</label>
                        <input type="url" name="video" placeholder="https://youtube.com/...">
                        <button type="submit" class="btn success">➕ افزودن کار</button>
                    </form>
                </div>
            </details>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- بخش حرکات -->
    <div class="tab-content" id="exercises">
        <div class="card">
            <h2>➕ افزودن حرکت جدید</h2>
            <form method="post">
                <input type="hidden" name="action" value="add_exercise">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <div>
                        <label>نام حرکت</label>
                        <input type="text" name="name" required>
                    </div>
                    <div>
                        <label>آیکن</label>
                        <input type="text" name="icon" value="💪">
                    </div>
                    <div>
                        <label>ناحیه بدن</label>
                        <input type="text" name="area" placeholder="مثلاً: گردن">
                    </div>
                    <div>
                        <label>لینک ویدیو</label>
                        <input type="url" name="video">
                    </div>
                </div>
                <label>توضیحات</label>
                <textarea name="desc" rows="2"></textarea>
                <button type="submit" class="btn success">➕ افزودن حرکت</button>
            </form>
        </div>

        <h2>📋 حرکات فعلی</h2>
        <div class="grid">
            <?php foreach ($settings['exercises'] ?? [] as $idx => $ex): ?>
            <div class="item">
                <form method="post" style="display: inline;" onsubmit="return confirm('حذف شود؟');">
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="type" value="exercises">
                    <input type="hidden" name="idx" value="<?= $idx ?>">
                    <button type="submit" class="delete-btn">🗑</button>
                </form>
                <h3><?= $ex['i'] ?? '💪' ?> <?= htmlspecialchars($ex['n']) ?></h3>
                <span class="tag phase"><?= htmlspecialchars($ex['area'] ?? '') ?></span>
                <p><?= htmlspecialchars($ex['desc'] ?? '') ?></p>
                <?php if (!empty($ex['video'])): ?>
                    <a href="<?= htmlspecialchars($ex['video']) ?>" target="_blank" class="btn" style="font-size: 11px; padding: 4px 10px; margin-top: 8px;">📺 ویدیو</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- بخش قوانین -->
    <div class="tab-content" id="rules">
        <div class="card">
            <h2>➕ افزودن قانون جدید</h2>
            <form method="post">
                <input type="hidden" name="action" value="add_rule">
                <label>عنوان قانون</label>
                <input type="text" name="title" required>
                <label>توضیحات</label>
                <textarea name="desc" rows="2" required></textarea>
                <button type="submit" class="btn success">➕ افزودن قانون</button>
            </form>
        </div>

        <h2>📋 قوانین فعلی</h2>
        <div class="grid">
            <?php foreach ($settings['rules'] ?? [] as $idx => $rule): ?>
            <div class="item">
                <form method="post" style="display: inline;" onsubmit="return confirm('حذف شود؟');">
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="type" value="rules">
                    <input type="hidden" name="idx" value="<?= $idx ?>">
                    <button type="submit" class="delete-btn">🗑</button>
                </form>
                <h3><?= htmlspecialchars($rule['t']) ?></h3>
                <p><?= htmlspecialchars($rule['d']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- بخش جملات -->
    <div class="tab-content" id="quotes">
        <div class="card">
            <h2>➕ افزودن جمله جدید</h2>
            <form method="post">
                <input type="hidden" name="action" value="add_quote">
                <label>متن جمله</label>
                <textarea name="text" rows="2" required></textarea>
                <label>منبع / دسته</label>
                <input type="text" name="author" placeholder="مثلاً: قانون بقا">
                <button type="submit" class="btn success">➕ افزودن جمله</button>
            </form>
        </div>

        <h2>📋 جملات فعلی (<?= count($settings['quotes'] ?? []) ?>)</h2>
        <div class="grid">
            <?php foreach ($settings['quotes'] ?? [] as $idx => $quote): ?>
            <div class="item">
                <form method="post" style="display: inline;" onsubmit="return confirm('حذف شود؟');">
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="type" value="quotes">
                    <input type="hidden" name="idx" value="<?= $idx ?>">
                    <button type="submit" class="delete-btn">🗑</button>
                </form>
                <p style="font-style: italic;">«<?= htmlspecialchars($quote['t']) ?>»</p>
                <?php if (!empty($quote['a'])): ?>
                    <p style="font-size: 11px; color: #98a3bd; margin-top: 6px;">— <?= htmlspecialchars($quote['a']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- بخش تنظیمات کلی -->
    <div class="tab-content" id="config">
        <div class="card">
            <h2>⚙️ تنظیمات کلی برنامه</h2>
            <form method="post">
                <input type="hidden" name="action" value="update_config">
                <label>تاریخ شروع (میلادی)</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($settings['config']['start_date'] ?? '') ?>">
                <label>تایم‌زون</label>
                <input type="text" name="timezone" value="<?= htmlspecialchars($settings['config']['timezone'] ?? 'Asia/Tehran') ?>">
                <label>آدرس اوقات شرعی (بادصبا)</label>
                <input type="url" name="prayer_url" value="<?= htmlspecialchars($settings['config']['prayer_url'] ?? '') ?>">
                <button type="submit" class="btn success">💾 ذخیره تنظیمات</button>
            </form>
        </div>

        <div class="card">
            <h2>🔗 لینک‌های مفید</h2>
            <ul style="padding-right: 20px; font-size: 13px;">
                <li><a href="index.php" style="color: #38bdf8;">بازگشت به برنامه اصلی</a></li>
                <li><a href="api.php?a=state" target="_blank" style="color: #38bdf8;">مشاهده خروجی API (JSON)</a></li>
                <li><a href="api.php?a=ping" target="_blank" style="color: #38bdf8;">تست اتصال سرور</a></li>
                <li><a href="api.php?a=export" style="color: #38bdf8;">دانلود پشتیبان دیتابیس</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});
</script>
</body>
</html>