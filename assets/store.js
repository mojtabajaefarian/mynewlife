/* ===== FILE: assets/store.js | ابزارها + موتور ذخیرهٔ محلی ===== */
(function (w) {
'use strict';
var FA = '۰۱۲۳۴۵۶۷۸۹';
var JM = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
var WD = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
var LSK = 'daftar_v2';

function div(a, b) { return ~~(a / b); }

var U = {
    fa: function (s) { return String(s).replace(/[0-9]/g, function (d) { return FA[+d]; }); },
    pad: function (n) { return (n < 10 ? '0' : '') + n; },
    key: function (dt) { return dt.getFullYear() + '-' + U.pad(dt.getMonth() + 1) + '-' + U.pad(dt.getDate()); },
    parse: function (k) { var p = k.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); },
    add: function (k, n) { var d = U.parse(k); d.setDate(d.getDate() + n); return U.key(d); },
    diff: function (a, b) { return Math.round((U.parse(b) - U.parse(a)) / 864e5); },
    g2j: function (gy, gm, gd) {
        var gdm = [0,31,59,90,120,151,181,212,243,273,304,334], jy = gy <= 1600 ? 0 : 979;
        gy -= gy <= 1600 ? 621 : 1600;
        var gy2 = gm > 2 ? gy + 1 : gy;
        var days = 365*gy + div(gy2+3,4) - div(gy2+99,100) + div(gy2+399,400) - 80 + gd + gdm[gm-1];
        jy += 33*div(days,12053); days %= 12053;
        jy += 4*div(days,1461); days %= 1461;
        if (days > 365) { jy += div(days-1,365); days = (days-1) % 365; }
        var jm = days < 186 ? 1 + div(days,31) : 7 + div(days-186,30);
        var jd = 1 + (days < 186 ? days % 31 : (days-186) % 30);
        return [jy, jm, jd];
    },
    jshort: function (k) { var d = U.parse(k), j = U.g2j(d.getFullYear(), d.getMonth()+1, d.getDate());
        return U.fa(j[2]) + ' ' + JM[j[1]-1]; },
    jfull: function (k) { var d = U.parse(k), j = U.g2j(d.getFullYear(), d.getMonth()+1, d.getDate());
        return WD[d.getDay()] + ' ' + U.fa(j[2]) + ' ' + JM[j[1]-1] + ' ' + U.fa(j[0]); },
    el: function (t, c, x) { var e = document.createElement(t); if (c) e.className = c;
        if (x != null) e.textContent = x; return e; },
    toast: function (msg, kind) {
        var b = document.getElementById('toast');
        if (!b) { b = U.el('div'); b.id = 'toast'; document.body.appendChild(b); }
        b.textContent = msg; b.className = 'show ' + (kind || 'ok');
        clearTimeout(U._tt); U._tt = setTimeout(function () { b.className = ''; }, 2600);
    }
};

var L = {
    read: function () { try { return JSON.parse(localStorage.getItem(LSK)) || { start: '', days: {} }; }
        catch (e) { return { start: '', days: {} }; } },
    write: function (db) { try { localStorage.setItem(LSK, JSON.stringify(db)); return true; }
        catch (e) { return false; } },
    ensure: function (start) { var db = L.read(); if (!db.start) { db.start = start; L.write(db); } return db; },
    rec: function (d) { var r = (L.read().days || {})[d] || {};
        return { t: r.t || {}, br: r.br || {}, n: r.n || '', x: r.x || {} }; },
    save: function (d, fn) { var db = L.read(); db.days = db.days || {};
        var r = L.rec(d); fn(r); db.days[d] = r; L.write(db); return r; },
    syncFromState: function (S) {
        if (!S || !S.date) return;
        L.save(S.date, function (r) {
            r.t = S.rec.t || {};
            r.br = S.rec.br || {};
            r.n = S.rec.n || '';
            r.x = S.rec.x || {};
        });
    }
};

w.U = U; w.L = L;
})(window);