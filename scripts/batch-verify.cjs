const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

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

function count(files, re) {
    let total = 0;
    for (const file of files) {
        const m = fs.readFileSync(file, 'utf8').match(re);
        if (m) total += m.length;
    }
    return total;
}

console.log('Running view:cache...');
try {
    execSync('php artisan view:cache', { stdio: 'inherit', cwd: path.resolve('.') });
    console.log('OK: Blade compiled');
} catch (e) {
    console.error('FAIL: Blade compile error');
    process.exit(1);
}

const blades = walk('resources/views', /\.blade\.php$/);
const jsFiles = walk('public/js', /\.js$/);
const phpFiles = walk('app', /\.php$/);

console.log('Remaining raw FA icons:');
console.log('  Blade:', count(blades, /<i class="fa[srb]?\s+fa-/g));
console.log('  JS:', count(jsFiles, /fa[srb]?\s+fa-/g));
console.log('  PHP:', count(phpFiles, /<i class="fa[srb]?\s+fa-/g));
