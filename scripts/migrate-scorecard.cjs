/**
 * One-shot migrations for scorecard cleanup (BS4 attrs, blade confirm()).
 * Run: node scripts/migrate-scorecard.cjs
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve('.');
const SKIP = new Set(['node_modules', 'vendor', 'build', 'assets', '.git', 'storage']);

function walk(dir, exts, out = []) {
    const abs = path.join(ROOT, dir);
    if (!fs.existsSync(abs)) return out;
    for (const e of fs.readdirSync(abs, { withFileTypes: true })) {
        if (e.isDirectory()) {
            if (SKIP.has(e.name)) continue;
            walk(path.join(dir, e.name), exts, out);
        } else if (exts.some((x) => e.name.endsWith(x))) {
            out.push(path.join(dir, e.name).split(path.sep).join('/'));
        }
    }
    return out;
}

function read(rel) {
    return fs.readFileSync(path.join(ROOT, rel), 'utf8');
}

function write(rel, text) {
    fs.writeFileSync(path.join(ROOT, rel), text, 'utf8');
}

const BS4_MAP = [
    [/data-toggle="dropdown"/g, 'data-bs-toggle="dropdown"'],
    [/data-toggle="tooltip"/g, 'data-bs-toggle="tooltip"'],
    [/data-toggle="modal"/g, 'data-bs-toggle="modal"'],
    [/data-toggle="popover"/g, 'data-bs-toggle="popover"'],
    [/data-dismiss="modal"/g, 'data-bs-dismiss="modal"'],
    [/data-dismiss="alert"/g, 'data-bs-dismiss="alert"'],
    [/data-target="/g, 'data-bs-target="'],
    [/data-placement="/g, 'data-bs-placement="'],
    [/data-container="/g, 'data-bs-container="'],
];

function migrateBs4(rel) {
    let text = read(rel);
    let changed = false;
    for (const [re, rep] of BS4_MAP) {
        if (re.test(text)) {
            text = text.replace(re, rep);
            changed = true;
        }
    }
    if (changed) {
        write(rel, text);
        return true;
    }
    return false;
}

function migrateBladeConfirm(rel) {
    let text = read(rel);
    const original = text;

    // onsubmit="return confirm('...');" → data-crm-confirm="..."
    text = text.replace(
        /\s+onsubmit="return\s+confirm\((['"])([\s\S]*?)\1\);"/g,
        (m, q, msg) => ` data-crm-confirm=${q}${msg}${q}"`
    );

    // onclick="return confirm('...');" → data-crm-confirm="..."
    text = text.replace(
        /\s+onclick="return\s+confirm\((['"])([\s\S]*?)\1\);"/g,
        (m, q, msg) => ` data-crm-confirm=${q}${msg}${q}"`
    );

    if (text !== original) {
        write(rel, text);
        return true;
    }
    return false;
}

function migrateJsConfirm(rel) {
    let text = read(rel);
    const original = text;

    // if (confirm('msg')) { → crmConfirm('msg').then(function (ok) { if (!ok) return;
    text = text.replace(
        /if\s*\(\s*confirm\((['"])([\s\S]*?)\1\)\s*\)\s*\{/g,
        (m, q, msg) => `crmConfirm(${q}${msg}${q}).then(function (ok) { if (!ok) return;`
    );

    // if (!confirm('msg')) return; → crmConfirm(...).then(function (ok) { if (!ok) return;
    text = text.replace(
        /if\s*\(\s*!+\s*confirm\((['"])([\s\S]*?)\1\)\s*\)\s*return;/g,
        (m, q, msg) => `crmConfirm(${q}${msg}${q}).then(function (ok) { if (!ok) return;`
    );

    // var conf = confirm('msg'); ... if(conf){
    // Handled manually in custom.js — pattern too varied for regex.

    if (text !== original) {
        write(rel, text);
        return true;
    }
    return false;
}

const bs4Files = [
    ...walk('app/Http/Controllers', ['.php']),
    ...walk('resources/views', ['.blade.php']),
];
const bladeFiles = walk('resources/views', ['.blade.php']);
const jsFiles = walk('public/js', ['.js']).filter((f) => !f.includes('confirm-dialog.js'));

let bs4Count = 0;
for (const f of bs4Files) {
    if (migrateBs4(f)) bs4Count++;
}

let bladeConfirmCount = 0;
for (const f of bladeFiles) {
    if (migrateBladeConfirm(f)) bladeConfirmCount++;
}

let jsConfirmCount = 0;
for (const f of jsFiles) {
    if (migrateJsConfirm(f)) jsConfirmCount++;
}

console.log('BS4 files updated:', bs4Count);
console.log('Blade confirm migrations:', bladeConfirmCount);
console.log('JS confirm migrations (simple if patterns):', jsConfirmCount);
