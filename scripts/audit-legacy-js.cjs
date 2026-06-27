/**
 * Audit public/js files vs @legacy imports from resources/js.
 * Run: node scripts/audit-legacy-js.cjs
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve('.');
const SKIP_DIRS = new Set(['node_modules', 'vendor', 'build', '.git']);
const SCAN_DIRS = ['resources', 'public/js', 'routes', 'app', 'config'];
const DIRECT_BROWSER_LOADS = new Set(['jquery-3.7.1.min.js']);

function walk(dir, ext, out = []) {
    const abs = path.join(ROOT, dir);
    if (!fs.existsSync(abs)) return out;
    for (const e of fs.readdirSync(abs, { withFileTypes: true })) {
        if (e.isDirectory()) {
            if (SKIP_DIRS.has(e.name)) continue;
            walk(path.join(dir, e.name), ext, out);
        } else if (e.name.endsWith(ext)) {
            out.push(path.join(dir, e.name).split(path.sep).join('/'));
        }
    }
    return out;
}

function readText(relPath) {
    return fs.readFileSync(path.join(ROOT, relPath), 'utf8');
}

function collectLegacyImports() {
    const imports = new Map();
    for (const f of walk('resources/js', '.js')) {
        const text = readText(f);
        for (const m of text.matchAll(/@legacy\/([^'"]+)/g)) {
            const target = m[1];
            if (!imports.has(target)) {
                imports.set(target, []);
            }
            imports.get(target).push(f);
        }
    }
    return imports;
}

function buildReferenceIndex() {
    const files = [];
    for (const dir of SCAN_DIRS) {
        walk(dir, '.php', files);
        walk(dir, '.js', files);
        walk(dir, '.blade.php', files);
        walk(dir, '.json', files);
    }

    const index = new Map();
    for (const f of files) {
        if (f.startsWith('public/build/')) continue;
        const text = readText(f);
        index.set(f, text);
    }
    return index;
}

function isReferencedElsewhere(rel, legacyImports, referenceIndex) {
    for (const [file, text] of referenceIndex.entries()) {
        if (file === `public/js/${rel}`) continue;
        if (text.includes(rel)) {
            return file;
        }
    }
    return null;
}

const legacyImports = collectLegacyImports();
const referenceIndex = buildReferenceIndex();
const jsFiles = walk('public/js', '.js');

const unused = [];
const imported = [];
const missingTargets = [];

for (const [target, sources] of legacyImports.entries()) {
    if (!fs.existsSync(path.join(ROOT, 'public/js', target))) {
        missingTargets.push({ target, sources });
    }
}

for (const f of jsFiles) {
    const rel = f.replace(/^public\/js\//, '');
    if (legacyImports.has(rel)) {
        imported.push(`${rel} (@legacy)`);
        continue;
    }
    if (DIRECT_BROWSER_LOADS.has(rel)) {
        imported.push(`${rel} (asset() in layout)`);
        continue;
    }
    const ref = isReferencedElsewhere(rel, legacyImports, referenceIndex);
    if (ref) {
        imported.push(`${rel} (referenced in ${ref})`);
    } else {
        unused.push(rel);
    }
}

console.log('@legacy import paths:', legacyImports.size);
console.log('Tracked public/js files:', imported.length);
console.log('Unused candidates:', unused.length);
console.log('Missing @legacy targets:', missingTargets.length);

if (unused.length) {
    console.log('\nUnused:');
    unused.sort().forEach((f) => console.log('  ' + f));
}

if (missingTargets.length) {
    console.log('\nMissing targets:');
    missingTargets.forEach(({ target, sources }) => {
        console.log(`  ${target} <- ${sources.join(', ')}`);
    });
}

const ok = unused.length === 0 && missingTargets.length === 0;
if (!ok) {
    process.exit(1);
}

console.log('\nOK: every public/js file is imported or intentionally loaded in layouts.');
