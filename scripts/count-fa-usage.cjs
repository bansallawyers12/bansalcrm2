const fs = require('fs');
const path = require('path');

function walk(dir, pattern, results = []) {
    if (!fs.existsSync(dir)) return results;
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            if (entry.name === 'node_modules' || entry.name === 'vendor' || entry.name === 'build') continue;
            walk(full, pattern, results);
        } else if (pattern.test(entry.name)) {
            results.push(full);
        }
    }
    return results;
}

function countMatches(files, re) {
    const byFile = {};
    let total = 0;
    for (const file of files) {
        const content = fs.readFileSync(file, 'utf8');
        const matches = content.match(re);
        if (matches && matches.length) {
            byFile[file] = matches.length;
            total += matches.length;
        }
    }
    return { total, byFile };
}

const blades = walk('resources/views', /\.blade\.php$/);
const jsFiles = walk('public/js', /\.js$/);
const phpFiles = walk('app', /\.php$/);

const bladeRaw = countMatches(blades, /<i class="fa[srb]?\s+fa-/g);
const bladeIcon = countMatches(blades, /@icon\(/g);
const jsFa = countMatches(jsFiles, /fa[srb]?\s+fa-/g);
const phpRaw = countMatches(phpFiles, /<i class="fa[srb]?\s+fa-/g);
const phpBroken = countMatches(phpFiles, /' \. \\App\\Helpers\\IconHelper::render\([^)]+\) \. '/g);

const top = (byFile, n = 25) =>
    Object.entries(byFile)
        .sort((a, b) => b[1] - a[1])
        .slice(0, n)
        .map(([f, c]) => ({ file: f.replace(/\\/g, '/'), count: c }));

console.log(JSON.stringify({
    summary: {
        bladeRawFa: bladeRaw.total,
        bladeAtIcon: bladeIcon.total,
        jsFaClasses: jsFa.total,
        phpRawFa: phpRaw.total,
    },
    topBladeRaw: top(bladeRaw.byFile),
    topJsFa: top(jsFa.byFile),
    topPhpRaw: top(phpRaw.byFile),
}, null, 2));
