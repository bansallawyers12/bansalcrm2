/**
 * List public/js/*.js files not imported via @legacy from resources/js.
 * Run: node scripts/audit-legacy-js.cjs
 */
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

function walk(dir, ext, out = []) {
    if (!fs.existsSync(dir)) return out;
    for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) walk(p, ext, out);
        else if (p.endsWith(ext)) out.push(p.split(path.sep).join('/'));
    }
    return out;
}

const legacyImports = new Set();
for (const f of walk('resources/js', '.js')) {
    const t = fs.readFileSync(f, 'utf8');
    for (const m of t.matchAll(/@legacy\/([^'"]+)/g)) {
        legacyImports.add(m[1]);
    }
}

const jsFiles = walk('public/js', '.js');
const DIRECT_BROWSER_LOADS = new Set(['jquery-3.7.1.min.js']);
const unused = [];
const imported = [];

for (const f of jsFiles) {
    const rel = f.replace(/^public\/js\//, '');
    if (legacyImports.has(rel)) {
        imported.push(rel);
        continue;
    }
    if (DIRECT_BROWSER_LOADS.has(rel)) {
        imported.push(rel + ' (asset() in layout)');
        continue;
    }
    try {
        const hits = execSync(
            `rg -l --glob "!public/build/**" --glob "!node_modules/**" --fixed-strings "${rel}"`,
            { encoding: 'utf8', cwd: path.resolve('.') }
        ).trim();
        if (hits) {
            imported.push(rel + ' (referenced elsewhere)');
        } else {
            unused.push(rel);
        }
    } catch {
        unused.push(rel);
    }
}

console.log('@legacy imports:', legacyImports.size);
console.log('Imported/referenced:', imported.length);
console.log('Unused candidates:', unused.length);
unused.sort().forEach((f) => console.log('  ' + f));
