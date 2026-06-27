const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

function walk(dir, pattern, results = []) {
    if (!fs.existsSync(dir)) return results;
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            if (['node_modules', 'vendor', 'build'].includes(entry.name)) continue;
            walk(full, pattern, results);
        } else if (pattern.test(entry.name)) {
            results.push(full);
        }
    }
    return results;
}

const label = process.argv[2] || 'remaining';
const prefix = process.argv[3] || '';

const blades = walk('resources/views', /\.blade\.php$/).filter((f) => {
    if (prefix && !f.replace(/\\/g, '/').includes(prefix)) return false;
    return /<i class="fa[srb]?\s+fa-/.test(fs.readFileSync(f, 'utf8'));
});

if (!blades.length) {
    console.log(`No files with raw FA icons under ${prefix || 'all views'}`);
    process.exit(0);
}

console.log(`${label}: migrating ${blades.length} files...`);
execSync(`node scripts/migrate-blade-icons.cjs ${blades.map((f) => `"${f}"`).join(' ')}`, {
    stdio: 'inherit',
    cwd: path.resolve('.'),
    shell: true,
});

const fixTargets = blades.filter((f) =>
    /\.html\('@icon\('spinner'|<i\s+style="[^"]+"\s+class="(?:far|fas|fab)/.test(fs.readFileSync(f, 'utf8'))
);

if (fixTargets.length) {
    execSync(`node scripts/fix-blade-inline-js-icons.cjs ${fixTargets.map((f) => `"${f}"`).join(' ')}`, {
        stdio: 'inherit',
        cwd: path.resolve('.'),
        shell: true,
    });
}

execSync('node scripts/batch-verify.cjs', { stdio: 'inherit', cwd: path.resolve('.') });
