/* ===== FILE: assets/app.js | نسخه نهایی با لنگرها، امتیازدهی خودکار، Workout Builder ===== */
(function () {
'use strict';

var API = window.API_URL || 'api.php';
var S = null, MODE = 'server', DATE = '', TODAY = '', START = '', NT = null;

/* ---------- ابزارهای کمکی ---------- */
var U = window.U || {};
U.fa = U.fa || function (s) {
    return String(s).replace(/[0-9]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[+d]; });
};
U.el = U.el || function (t, c, x) {
    var e = document.createElement(t);
    if (c) e.className = c;
    if (x != null) e.textContent = x;
    return e;
};
U.toast = U.toast || function (msg, kind) {
    var b = document.getElementById('toast');
    if (!b) { b = U.el('div'); b.id = 'toast'; document.body.appendChild(b); }
    b.textContent = msg; b.className = 'show ' + (kind || 'ok');
    clearTimeout(U._tt); U._tt = setTimeout(function () { b.className = ''; }, 2600);
};
U.add = U.add || function (k, n) {
    var p = k.split('-'); var d = new Date(+p[0], +p[1]-1, +p[2]);
    d.setDate(d.getDate() + n);
    return d.getFullYear() + '-' + ('0'+(d.getMonth()+1)).slice(-2) + '-' + ('0'+d.getDate()).slice(-2);
};
U.g2j = U.g2j || function (gy, gm, gd) {
    var gdm=[0,31,59,90,120,151,181,212,243,273,304,334];
    var jy = gy<=1600 ? 0 : 979;
    gy -= gy<=1600 ? 621 : 1600;
    var gy2 = gm>2 ? gy+1 : gy;
    var days = 365*gy + Math.floor((gy2+3)/4) - Math.floor((gy2+99)/100) + Math.floor((gy2+399)/400) - 80 + gd + gdm[gm-1];
    jy += 33*Math.floor(days/12053); days %= 12053;
    jy += 4*Math.floor(days/1461); days %= 1461;
    if (days>365) { jy += Math.floor((days-1)/365); days = (days-1)%365; }
    var jm = days<186 ? 1+Math.floor(days/31) : 7+Math.floor((days-186)/30);
    var jd = 1 + (days<186 ? days%31 : (days-186)%30);
    return [jy,jm,jd];
};
var JM = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
var WD = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
U.jshort = U.jshort || function (k) {
    var p = k.split('-').map(Number);
    var d = new Date(p[0], p[1]-1, p[2]);
    var j = U.g2j(d.getFullYear(), d.getMonth()+1, d.getDate());
    return U.fa(j[2]) + ' ' + JM[j[1]-1];
};
U.jfull = U.jfull || function (k) {
    var p = k.split('-').map(Number);
    var d = new Date(p[0], p[1]-1, p[2]);
    var j = U.g2j(d.getFullYear(), d.getMonth()+1, d.getDate());
    return WD[d.getDay()] + ' ' + U.fa(j[2]) + ' ' + JM[j[1]-1] + ' ' + U.fa(j[0]);
};

/* ---------- ریشه DOM ---------- */
function root() {
    var a = document.getElementById('app');
    if (!a) { a = U.el('div'); a.id = 'app'; document.body.appendChild(a); }
    return a;
}

/* ---------- API calls ---------- */
function call(act, opt) {
    opt = opt || {};
    return fetch(API + '?a=' + act + (opt.qs || ''), {
        method: opt.body ? 'POST' : 'GET',
        headers: { 'Content-Type': 'application/json' },
        body: opt.body ? JSON.stringify(opt.body) : null
    }).then(function (r) { return r.json(); });
}

/* ---------- خطایاب ---------- */
function diag(msg, raw) {
    var h = '<div class="err"><b>خطا در ارتباط با api.php</b><br><br>' +
        msg.replace(/\n/g, '<br>') + '</div>';
    if (raw) h += '<div class="sec"><h2>پاسخ خام سرور</h2><div class="pad">' +
        '<pre style="white-space:pre-wrap;font-size:11px;color:#98a3bd;margin:0">' +
        raw.slice(0, 600).replace(/[<&]/g, function (c) { return c === '<' ? '&lt;' : '&amp;'; }) +
        '</pre></div></div>';
    root().innerHTML = h;
}

/* ---------- محاسبه سطح روز از مجموع امتیازها ---------- */
function computeLevel(rec, blocks) {
    var totalScore = 0, maxScore = 0, starsDone = 0, starsTotal = 0;
    var items = [];
    blocks.forEach(function (b) {
        (b.t || []).forEach(function (t) {
            var v = (rec.t && rec.t[t.k]) || 0;
            var opt = (t.options || []).find(function(o){ return o.v === v; });
            var score = opt ? (opt.score || 0) : 0;
            var maxOpt = (t.options || []).reduce(function(m,o){ return Math.max(m, o.score||0); }, 0);
            totalScore += score;
            maxScore += maxOpt;
            if (t.st) {
                starsTotal++;
                if (v > 0) starsDone++;
            }
            items.push({k: t.k, v: v, score: score, star: !!t.st, n: t.n});
        });
    });
    var pct = maxScore > 0 ? Math.round(totalScore / maxScore * 100) : 0;
    var starsOk = starsTotal > 0 && starsDone === starsTotal;
    var level;
    if (items.every(function(it){ return it.v === 0; })) level = 'شروع نشده';
    else if (starsOk && pct >= 75) level = 'عالی';
    else if ((starsOk && pct >= 45) || (!starsTotal && pct >= 55)) level = 'معمول';
    else level = 'بقا';
    return {
        level: level, pct: pct, starsOk: starsOk,
        starsDone: starsDone, starsTotal: starsTotal,
        totalScore: totalScore, maxScore: maxScore, items: items
    };
}

/* ---------- بارگذاری ---------- */
function load(d) {
    var url = API + '?a=state' + (d ? '&d=' + encodeURIComponent(d) : '');
    fetch(url, { cache: 'no-store' })
        .then(function (r) {
            return r.text().then(function (t) {
                return { code: r.status, type: r.headers.get('content-type') || '-', txt: t };
            });
        })
        .then(function (res) {
            if (/^\s*<\?php|^\s*<\?=/.test(res.txt)) {
                return diag('سرور کد PHP را اجرا نکرده. نوع محتوا: ' + res.type, res.txt);
            }
            if (res.code === 404) return diag('کد ۴۰۴ — فایل api.php در این مسیر نیست:<br>' + url);
            if (res.code >= 500) return diag('کد ' + res.code + ' — خطای داخلی سرور.', res.txt);
            var s;
            try { s = JSON.parse(res.txt); }
            catch (e) { return diag('پاسخ JSON نبود.<br>کد: ' + res.code, res.txt); }
            if (!s.ok) return diag('API پیام خطا داد: ' + (s.error || s.err || 'نامشخص'), res.txt);

            S = s; MODE = s.mode; DATE = s.date; TODAY = s.today;
            START = U.add(s.date, -(s.day - 1));
            if (MODE === 'local' && window.L) { window.L.ensure(START); }
            render();
        })
        .catch(function (e) {
            diag('درخواست شبکه ناموفق:<br>' + (e && e.message ? e.message : e) +
                '<br><br>آدرس:<br>' + url);
        });
}

/* ---------- ارسال تغییرات ---------- */
function push(act, body, done) {
    if (MODE === 'local') {
        if (done) done();
        if (window.L && window.L.syncFromState) window.L.syncFromState(S);
        return Promise.resolve();
    }
    body.date = DATE;
    return call(act, { body: body }).then(function (r) {
        if (!r.ok) {
            MODE = 'local';
            if (done) done();
            U.toast('سرور قابل نوشتن نیست — ذخیره در مرورگر', 'warn');
            return;
        }
        if (r.stats) { S.stats = r.stats; S.streak = r.streak; S.trend = r.trend; }
    }).catch(function () {
        MODE = 'local';
        if (done) done();
        U.toast('آفلاین — ذخیره در مرورگر', 'warn');
    });
}

/* ---------- عملیات ---------- */
function tick(k, v) {
    var cur = (S.rec.t && S.rec.t[k]) || 0;
    var nv = cur === v ? 0 : v;
    if (!S.rec.t) S.rec.t = {};
    if (nv === 0) delete S.rec.t[k]; else S.rec.t[k] = nv;
    push('tick', { k: k, v: nv }, function () {
        if (window.L) window.L.save(DATE, function (r) {
            if (nv === 0) delete r.t[k]; else r.t[k] = nv;
        });
    }).then(render);
    var task = findTask(k);
    if (task && nv > 0) {
        var opt = (task.options || []).find(function(o){ return o.v === nv; });
        if (opt) U.toast('ثبت: ' + opt.txt);
    }
}

function note(txt) {
    S.rec.n = txt;
    push('note', { text: txt }, function () {
        if (window.L) window.L.save(DATE, function (r) { r.n = txt; });
    }).then(function () { U.toast('یادداشت ذخیره شد'); });
}

function test(k, val) {
    if (!S.rec.x) S.rec.x = {};
    S.rec.x[k] = val;
    push('test', { k: k, val: val }, function () {
        if (window.L) window.L.save(DATE, function (r) { r.x[k] = val; });
    });
}

function findTask(k) {
    var blocks = S.blocks || [];
    for (var i = 0; i < blocks.length; i++) {
        var ts = blocks[i].t || [];
        for (var j = 0; j < ts.length; j++) {
            if (ts[j].k === k) return ts[j];
        }
    }
    return null;
}

/* ---------- گزارش متنی ---------- */
function localReport() {
    var lvl = computeLevel(S.rec, S.blocks);
    var o = '📔 گزارش روزانه — ' + U.jfull(DATE) + '\n';
    o += 'روز ' + U.fa(S.day) + ' | هفتهٔ ' + U.fa(S.week) + ' | ' + (S.phaseInfo ? S.phaseInfo.n : '') + '\n';
    o += 'وضعیت: ' + lvl.level + '  |  امتیاز: ' + U.fa(lvl.pct) + '٪  |  ';
    o += 'ستاره‌ها: ' + U.fa(lvl.starsDone) + '/' + U.fa(lvl.starsTotal) + '\n';
    o += Array(35).join('─') + '\n';
    (S.blocks || []).forEach(function (b) {
        o += '\n▌ ' + b.n + '\n';
        (b.t || []).forEach(function (t) {
            var v = (S.rec.t && S.rec.t[t.k]) || 0;
            var opt = (t.options || []).find(function(o){ return o.v === v; });
            o += '  ' + (v ? '✔' : '✘') + ' ' + t.n;
            if (opt) o += ' — ' + opt.txt + ' (' + opt.score + ')';
            o += '\n';
        });
    });
    if (S.rec.n) o += '\n▌ یادداشت\n  ' + S.rec.n.replace(/\n/g, '\n  ') + '\n';
    if (S.streak) o += '\nزنجیرهٔ فعلی: ' + U.fa(S.streak.current) + ' روز  |  رکورد: ' + U.fa(S.streak.best) + ' روز\n';
    return o;
}

function showReport() {
    var draw = function (txt) {
        var m = U.el('div', 'modal'), box = U.el('div', 'box');
        box.appendChild(U.el('h3', '', '📝 گزارش روز — ' + U.jfull(DATE)));
        var pre = U.el('pre', '', txt); box.appendChild(pre);
        var ft = U.el('div', 'ft');
        var cp = U.el('button', 'cp', 'کپی متن');
        cp.onclick = function () {
            var f = function () { cp.textContent = 'کپی شد ✔'; U.toast('در کلیپ‌بورد کپی شد'); };
            if (navigator.clipboard) navigator.clipboard.writeText(txt).then(f, f);
            else { var a = U.el('textarea'); a.value = txt; document.body.appendChild(a);
                a.select(); document.execCommand('copy'); a.remove(); f(); }
        };
        var cl = U.el('button', '', 'بستن'); cl.onclick = function () { m.remove(); };
        ft.appendChild(cp); ft.appendChild(cl); box.appendChild(ft); m.appendChild(box);
        m.onclick = function (e) { if (e.target === m) m.remove(); };
        document.body.appendChild(m);
    };
    if (MODE === 'local') return draw(localReport());
    call('report', { qs: '&d=' + DATE }).then(function (r) { draw(r.text || localReport()); })
        .catch(function () { draw(localReport()); });
}

function backup() {
    if (MODE === 'server') { window.location = API + '?a=export'; return; }
    var data = window.L ? window.L.read() : {};
    var blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
    var a = U.el('a'); a.href = URL.createObjectURL(blob);
    a.download = 'daftar-' + TODAY + '.json'; a.click();
}

function restore() {
    var f = U.el('input'); f.type = 'file'; f.accept = '.json';
    f.onchange = function () {
        var rd = new FileReader();
        rd.onload = function () {
            var p; try { p = JSON.parse(rd.result); } catch (e) { return U.toast('فایل خراب است', 'err'); }
            if (!p || !p.days) return U.toast('ساختار فایل معتبر نیست', 'err');
            if (MODE === 'local') { if (window.L) window.L.write(p); U.toast('بازیابی شد'); load(DATE); }
            else call('import', { body: { db: p } }).then(function (r) {
                U.toast(r.ok ? 'بازیابی شد' : 'ذخیره نشد', r.ok ? 'ok' : 'err'); load(DATE);
            });
        };
        rd.readAsText(f.files[0]);
    };
    f.click();
}

/* ---------- گروه‌بندی بلوک‌ها بر اساس لنگر ---------- */
function groupBlocksByAnchor(blocks, anchors) {
    var groups = {};
    (blocks || []).forEach(function (b) {
        var ak = b.anchor || '_default';
        if (!groups[ak]) groups[ak] = [];
        groups[ak].push(b);
    });
    // مرتب‌سازی بر اساس order لنگر
    var sorted = Object.keys(groups).sort(function (a, b) {
        var oa = anchors && anchors[a] ? (anchors[a].order || 99) : 99;
        var ob = anchors && anchors[b] ? (anchors[b].order || 99) : 99;
        return oa - ob;
    });
    return sorted.map(function (ak) {
        return { anchorKey: ak, anchor: (anchors && anchors[ak]) || { n: ak, c: '#64748b', time: '' }, blocks: groups[ak] };
    });
}

/* ---------- مودال قوانین ---------- */
function showRulesModal() {
    var m = U.el('div', 'modal'), box = U.el('div', 'box');
    box.appendChild(U.el('h3', '', '📜 قوانین طلایی و پروتکل‌های نجات'));
    var content = U.el('div', 'rules-content');

    var rules = S.rules || [];
    if (rules.length) {
        content.appendChild(U.el('h4', '', '⚖️ قوانین بقا و رشد'));
        rules.forEach(function(r) {
            var item = U.el('div', 'rule-item');
            item.appendChild(U.el('b', '', r.t));
            item.appendChild(U.el('p', '', r.d));
            content.appendChild(item);
        });
    }

    var protocols = S.protocols || {};
    var pKeys = Object.keys(protocols);
    if (pKeys.length) {
        content.appendChild(U.el('h4', '', '🚑 پروتکل‌های شرایط ویژه'));
        pKeys.forEach(function(k) {
            var p = protocols[k];
            var pBox = U.el('div', 'phase-box');
            pBox.appendChild(U.el('b', '', p.title));
            pBox.appendChild(U.el('small', '', 'شرایط فعال‌سازی: ' + p.trigger));
            var ul = U.el('ul');
            (p.steps || []).forEach(function(st) { ul.appendChild(U.el('li', '', st)); });
            pBox.appendChild(ul);
            if (p.mantra) pBox.appendChild(U.el('p', 'mantra', '💡 ' + p.mantra));
            content.appendChild(pBox);
        });
    }

    var phases = S.phases_info || [];
    if (phases.length) {
        content.appendChild(U.el('h4', '', '🗺️ نقشه راه ۹۰ روزه'));
        phases.forEach(function(p, idx) {
            var pBox = U.el('div', 'phase-box');
            pBox.style.borderRightColor = p.c || '#ccc';
            pBox.appendChild(U.el('b', '', 'فاز ' + U.fa(idx+1) + ': ' + p.n));
            pBox.appendChild(U.el('small', '', p.w || ''));
            pBox.appendChild(U.el('p', '', p.g || ''));
            content.appendChild(pBox);
        });
    }

    box.appendChild(content);
    var cl = U.el('button', 'btn-close', 'بستن');
    cl.onclick = function () { m.remove(); };
    box.appendChild(cl);
    m.appendChild(box);
    m.onclick = function (e) { if (e.target === m) m.remove(); };
    document.body.appendChild(m);
}

/* ---------- Workout Builder ---------- */
function showWorkoutModal() {
    var m = U.el('div', 'modal'), box = U.el('div', 'box');
    box.appendChild(U.el('h3', '', '🏋️ کتابخانه حرکات و برنامه‌های ورزشی'));
    var content = U.el('div', 'rules-content');

    var workouts = S.workouts || [];
    if (workouts.length) {
        content.appendChild(U.el('h4', '', '📋 برنامه‌های فعال'));
        workouts.forEach(function(w) {
            var wBox = U.el('div', 'phase-box');
            wBox.appendChild(U.el('b', '', w.n));
            wBox.appendChild(U.el('small', '', w.freq || ''));
            var ul = U.el('ul');
            (w.moves || []).forEach(function(mv) {
                var ex = (S.exercises || []).find(function(e){ return e.k === mv.ex; });
                var txt = (ex ? ex.n : mv.ex) + ' — ' + U.fa(mv.sets) + '×' + U.fa(mv.reps);
                if (mv.hold) txt += ' (' + U.fa(mv.hold) + ' ثانیه نگهداری)';
                ul.appendChild(U.el('li', '', txt));
            });
            wBox.appendChild(ul);
            content.appendChild(wBox);
        });
    }

    var exercises = S.exercises || [];
    if (exercises.length) {
        content.appendChild(U.el('h4', '', '💪 کتابخانه حرکات ('+exercises.length+')'));
        exercises.forEach(function(ex) {
            var eBox = U.el('div', 'rule-item');
            eBox.appendChild(U.el('b', '', ex.i + ' ' + ex.n));
            eBox.appendChild(U.el('small', '', ex.area || ''));
            if (ex.desc) eBox.appendChild(U.el('p', '', ex.desc));
            if (ex.video) {
                var a = U.el('a', 'vid-link', '📺 ویدیوی آموزشی');
                a.href = ex.video; a.target = '_blank';
                eBox.appendChild(a);
            }
            content.appendChild(eBox);
        });
    }

    box.appendChild(content);
    var cl = U.el('button', 'btn-close', 'بستن');
    cl.onclick = function () { m.remove(); };
    box.appendChild(cl);
    m.appendChild(box);
    m.onclick = function (e) { if (e.target === m) m.remove(); };
    document.body.appendChild(m);
}

/* ---------- رندر اصلی ---------- */
var FOLD = 'daftar_fold';
function folded() { try { return JSON.parse(localStorage.getItem(FOLD)) || {}; } catch (e) { return {}; } }
function fold(id, v) { var f = folded(); f[id] = v; localStorage.setItem(FOLD, JSON.stringify(f)); }

function render() {
    var a = root(); a.innerHTML = '';
    var lvl = computeLevel(S.rec, S.blocks);
    var fl = folded();

    /* ---------- سربرگ ---------- */
    var hd = U.el('div', 'hd'), top = U.el('div', 'hd-top');
    top.appendChild(U.el('h1', '', '📔 دفتر روزانه'));
    var btnSet = U.el('a', 'btn-settings', '⚙️');
    btnSet.href = 'admin.php'; btnSet.title = 'تنظیمات';
    top.appendChild(btnSet);
    top.appendChild(U.el('span', 'badge ' + (MODE === 'local' ? 'local' : 'server'),
        MODE === 'local' ? '● مرورگر' : '● سرور'));
    hd.appendChild(top);

    /* ---------- ناوبری تاریخ ---------- */
    var nav = U.el('div', 'nav');
    var bp = U.el('button', '', '›'); bp.title = 'روز قبل';
    bp.onclick = function () { load(U.add(DATE, -1)); };
    var dt = U.el('div', 'date');
    dt.appendChild(document.createTextNode(U.jfull(DATE)));
    dt.appendChild(U.el('span', 'now', DATE === TODAY ? ('اکنون ' + U.fa(S.now) + ' — تهران') : 'مرور گذشته'));
    var bn = U.el('button', '', '‹'); bn.title = 'روز بعد';
    bn.disabled = (DATE >= TODAY);
    bn.onclick = function () { load(U.add(DATE, 1)); };
    var bt = U.el('button', 'today', 'امروز');
    bt.disabled = (DATE === TODAY);
    bt.onclick = function () { load(TODAY); };
    nav.appendChild(bp); nav.appendChild(dt); nav.appendChild(bn); nav.appendChild(bt);
    hd.appendChild(nav);

    /* ---------- چیپ‌های وضعیت ---------- */
    var meta = U.el('div', 'meta');
    function chip(lbl, val) {
        var c = U.el('span', 'chip', lbl + ' ');
        c.appendChild(U.el('b', '', val)); meta.appendChild(c);
    }
    chip('روز', U.fa(S.day));
    chip('هفتهٔ', U.fa(S.week));
    chip('فاز', U.fa(S.phase));
    var pr = S.prayers || [];
    if (!Array.isArray(pr)) pr = Object.keys(pr).map(function (k) { return { n: k, t: pr[k] }; });
    pr.forEach(function (p) { chip(p.n || p[0], U.fa(p.t || p[1])); });
    hd.appendChild(meta);

    /* ---------- فاز جاری ---------- */
    var ph = U.el('div', 'phase'); ph.appendChild(U.el('i'));
    var pb = U.el('div');
    pb.appendChild(U.el('b', '', S.phaseInfo.n || ''));
    pb.appendChild(U.el('small', '', S.phaseInfo.d || S.phaseInfo.g || ''));
    ph.appendChild(pb);
    hd.appendChild(ph);

    /* ---------- کارت‌های KPI ---------- */
    var cards = U.el('div', 'cards');
    var ring = U.el('div', 'ring st-' + lvl.level);
    ring.style.setProperty('--p', lvl.pct);
    ring.appendChild(U.el('span', '', U.fa(lvl.pct) + '٪'));
    cards.appendChild(ring);
    function kpi(v, t) {
        var k = U.el('div', 'kpi');
        k.appendChild(U.el('b', '', v));
        k.appendChild(U.el('small', '', t));
        cards.appendChild(k);
    }
    kpi(lvl.level, 'سطح روز');
    if (S.streak) kpi(U.fa(S.streak.current), 'زنجیره (رکورد ' + U.fa(S.streak.best) + ')');
    kpi(U.fa(lvl.starsDone) + '/' + U.fa(lvl.starsTotal), 'نشکن‌ها');
    hd.appendChild(cards);

    /* ---------- جمله روز ---------- */
    var q = S.quote || {};
    hd.appendChild(U.el('div', 'quote', '“' + (q.t || '') + '”' + (q.a ? ' — ' + q.a : '')));
    a.appendChild(hd);

    /* ---------- دکمه‌های ابزار (قوانین + ورزش) ---------- */
    var tools = U.el('div', 'tools-row');
    var btnR = U.el('button', 'btn-rules', '📜 قوانین طلایی و پروتکل‌ها');
    btnR.onclick = showRulesModal;
    var btnW = U.el('button', 'btn-workouts', '🏋️ حرکات و برنامه‌های ورزشی');
    btnW.onclick = showWorkoutModal;
    tools.appendChild(btnR); tools.appendChild(btnW);
    a.appendChild(tools);

    /* ---------- بلوک‌های کاری گروه‌بندی‌شده بر اساس لنگر ---------- */
    var groups = groupBlocksByAnchor(S.blocks, S.anchors);
    groups.forEach(function (g) {
        var sec = U.el('div', 'anchor-sec');
        sec.style.borderRightColor = g.anchor.c || '#64748b';
        var hdr = U.el('div', 'anchor-hdr');
        hdr.style.background = 'linear-gradient(90deg, ' + (g.anchor.c || '#64748b') + '22, transparent)';
        var ico = U.el('span', 'anchor-ico', g.anchor.n.split(' ')[0] || '⏰');
        var title = U.el('span', 'anchor-title', g.anchor.n);
        var time = U.el('span', 'anchor-time', g.anchor.time || '');
        hdr.appendChild(ico); hdr.appendChild(title); hdr.appendChild(time);
        sec.appendChild(hdr);

        g.blocks.forEach(function (b, bi) {
            var id = 'b_' + b.k + '_' + bi, off = !!fl[id];
            var blk = U.el('div', 'blk' + (off ? ' off' : ''));
            var h2 = U.el('h2');
            h2.appendChild(U.el('span', 'ic', b.i || '•'));
            h2.appendChild(U.el('span', 'nm', b.n));
            var dn = 0;
            (b.t || []).forEach(function (t) { if ((S.rec.t && S.rec.t[t.k]) > 0) dn++; });
            h2.appendChild(U.el('span', 'cnt', U.fa(dn) + '/' + U.fa((b.t || []).length)));
            h2.onclick = function () { blk.classList.toggle('off'); fold(id, blk.classList.contains('off')); };
            blk.appendChild(h2);
            if (b.desc) blk.appendChild(U.el('div', 'block-desc', b.desc));
            if (b.h) blk.appendChild(U.el('div', 'hint', b.h));

            /* نکات بلوک */
            if (b.tips && b.tips.length) {
                var tipsWrap = U.el('div', 'block-tips');
                tipsWrap.appendChild(U.el('b', '', '💡 نکات کلیدی:'));
                var ul = U.el('ul');
                b.tips.forEach(function(t){ ul.appendChild(U.el('li', '', t)); });
                tipsWrap.appendChild(ul);
                blk.appendChild(tipsWrap);
            }

            var body = U.el('div', 'body');
            (b.t || []).forEach(function (t) {
                var v = (S.rec.t && S.rec.t[t.k]) || 0;
                var tk = U.el('div', 'task' + (v > 0 ? ' done' : ''));
                var ttl = U.el('div', 'ttl');
                ttl.appendChild(U.el('span', 'ic', v > 0 ? '✅' : (t.i || '⬜')));
                ttl.appendChild(U.el('span', 'nm', t.n));
                if (t.st) ttl.appendChild(U.el('span', 'star', '★ نشکن'));
                tk.appendChild(ttl);

                /* توضیحات تکمیلی */
                if (t.desc) tk.appendChild(U.el('div', 'task-info desc', t.desc));
                if (t.why) tk.appendChild(U.el('div', 'task-info why', '🧠 ' + t.why));
                if (t.mistake) tk.appendChild(U.el('div', 'task-info mistake', '⚠️ اشتباه رایج: ' + t.mistake));

                /* لینک ویدیو */
                if (t.video) {
                    var vidLink = U.el('a', 'vid-link', '📺 ویدیوی آموزشی');
                    vidLink.href = t.video; vidLink.target = '_blank';
                    tk.appendChild(vidLink);
                }

                /* دکمه‌های امتیازدهی (سه گزینه) */
                var seg = U.el('div', 'seg');
                (t.options || []).forEach(function (opt) {
                    var lbl = opt.txt;
                    var cls = v === opt.v ? 'on' + opt.v : '';
                    var scoreBadge = ' (' + U.fa(opt.score) + ')';
                    var btn = U.el('button', cls, lbl + scoreBadge);
                    btn.onclick = function () { tick(t.k, opt.v); };
                    seg.appendChild(btn);
                });
                tk.appendChild(seg);
                body.appendChild(tk);
            });
            blk.appendChild(body);
            sec.appendChild(blk);
        });
        a.appendChild(sec);
    });

    /* ---------- تست‌ها ---------- */
    if (S.tests && S.tests.length) {
        var ts = U.el('div', 'sec');
        ts.appendChild(U.el('h2', '', '📏 سنجه‌های دوره‌ای'));
        var tsPad = U.el('div', 'pad');
        S.tests.forEach(function (t) {
            var tw = U.el('div', 'test');
            tw.appendChild(U.el('b', '', (t.i || '') + ' ' + t.n));
            if (t.d) tw.appendChild(U.el('small', '', t.d));
            var optsWrap = U.el('div', 'test-opts');
            var curVal = (S.rec.x && S.rec.x[t.k]) || '';
            (t.opts || []).forEach(function(opt, idx) {
                var btnOpt = U.el('button', 'opt-btn' + (curVal === opt ? ' active' : ''), opt);
                btnOpt.onclick = function() {
                    test(t.k, opt);
                    U.toast('ثبت شد');
                    render();
                };
                optsWrap.appendChild(btnOpt);
            });
            tw.appendChild(optsWrap);
            if (curVal && t.opts && t.interp) {
                var idx = t.opts.indexOf(curVal);
                if (idx > -1) {
                    if (t.interp[idx]) tw.appendChild(U.el('div', 'interp', '🔍 ' + t.interp[idx]));
                    if (t.action && t.action[idx]) tw.appendChild(U.el('div', 'interp action', '🛠️ اقدام: ' + t.action[idx]));
                }
            }
            tsPad.appendChild(tw);
        });
        ts.appendChild(tsPad);
        a.appendChild(ts);
    }

    /* ---------- روند ۱۴ روز ---------- */
    if (S.trend && S.trend.length) {
        var tr = U.el('div', 'sec');
        tr.appendChild(U.el('h2', '', '📈 روند ۱۴ روز اخیر'));
        var ch = U.el('div', 'chart');
        S.trend.forEach(function (d) {
            var bar = U.el('div', 'bar b-' + (d.st === 'شروع نشده' ? 'شروع' : d.st) + (d.d === DATE ? ' now' : ''));
            var i = U.el('i'); i.style.height = Math.max(4, d.pct) + '%';
            i.title = d.lbl + ' — ' + U.fa(d.pct) + '٪';
            bar.appendChild(i); bar.appendChild(U.el('span', '', d.lbl));
            bar.onclick = function () { load(d.d); };
            ch.appendChild(bar);
        });
        tr.appendChild(ch); a.appendChild(tr);
    }

    /* ---------- یادداشت ---------- */
    var ns = U.el('div', 'sec');
    ns.appendChild(U.el('h2', '', '🗒 یادداشت روز'));
    var np = U.el('div', 'pad');
    var ta = U.el('textarea'); ta.id = 'note'; ta.value = S.rec.n || '';
    ta.placeholder = 'چه چیزی کمک کرد؟ چه چیزی مانع شد؟ فردا کدام کار را کوچک‌تر کنم؟';
    ta.oninput = function () { clearTimeout(NT); NT = setTimeout(function () { note(ta.value); }, 1200); };
    ta.onblur = function () { clearTimeout(NT); note(ta.value); };
    np.appendChild(ta); ns.appendChild(np); a.appendChild(ns);

    /* ---------- دکمه‌های پایانی ---------- */
    var ac = U.el('div', 'acts');
    var m1 = U.el('button', 'main', '📝 ساخت گزارش (برای کوچ)'); m1.onclick = showReport;
    var m2 = U.el('button', '', '⬇️ پشتیبان‌گیری'); m2.onclick = backup;
    var m3 = U.el('button', '', '⬆️ بازیابی'); m3.onclick = restore;
    ac.appendChild(m1); ac.appendChild(m2); ac.appendChild(m3);
    a.appendChild(ac);
}

/* ---------- شروع ---------- */
document.addEventListener('keydown', function (e) {
    if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'INPUT') return;
    if (e.key === 'ArrowRight') load(U.add(DATE, -1));
    if (e.key === 'ArrowLeft' && DATE < TODAY) load(U.add(DATE, 1));
});
if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', function () { load(); });
else load();
})();