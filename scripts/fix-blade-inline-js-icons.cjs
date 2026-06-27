const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);
if (!targets.length) {
    console.error('Usage: node scripts/fix-blade-inline-js-icons.cjs <blade-file> ...');
    process.exit(1);
}

function fixContent(content) {
    let changed = 0;

    const spinnerPatterns = [
        [/\.html\('@icon\('spinner',\s*'solid',\s*\[\s*'spin'\s*=>\s*true\s*\]\)\s*([^']*)'\)/g, ".html(crmIconSpinner('$1'))"],
        [/\.html\("@icon\('spinner',\s*'solid',\s*\[\s*'spin'\s*=>\s*true\s*\]\)\s*([^"]*)"\)/g, '.html(crmIconSpinner("$1"))'],
        [/btn\.innerHTML\s*=\s*'@icon\('spinner',\s*'solid',\s*\[\s*'spin'\s*=>\s*true\s*\]\)\s*([^']*)'/g, "btn.innerHTML = crmIconSpinner('$1')"],
        [/btn\.innerHTML\s*=\s*"@icon\('spinner',\s*'solid',\s*\[\s*'spin'\s*=>\s*true\s*\]\)\s*([^"]*)"/g, 'btn.innerHTML = crmIconSpinner("$1")'],
    ];

    for (const [re, repl] of spinnerPatterns) {
        const next = content.replace(re, (...args) => {
            changed++;
            return typeof repl === 'function' ? repl(...args) : repl.replace(/\$(\d+)/g, (_, n) => args[Number(n)] ?? '');
        });
        content = next;
    }

    content = content.replace(
        /<i\s+style="([^"]+)"\s+class="((?:far|fas|fab)(?:\s+fa-[\w-]+)+)"\s*><\/i>/g,
        (match, style, cls) => {
            changed++;
            const name = cls.replace(/^(?:far|fas|fab)\s+fa-/, '').replace(/^fa-/, '');
            const styleName = cls.includes('far') ? 'regular' : cls.includes('fab') ? 'brands' : 'solid';
            if (styleName === 'solid') {
                return "{!! \\App\\Helpers\\IconHelper::render('" + name.split(' ').pop().replace('fa-', '') + "', 'solid', ['attrs' => ['style' => '" + style.replace(/'/g, "\\'") + "']]) !!}";
            }
            const iconName = (cls.match(/fa-([\w-]+)/) || [])[1] || name;
            return "{!! \\App\\Helpers\\IconHelper::render('" + iconName + "', '" + styleName + "', ['attrs' => ['style' => '" + style.replace(/'/g, "\\'") + "']]) !!}";
        }
    );

    return { content, changed };
}

for (const target of targets) {
    const file = path.resolve(target);
    if (!fs.existsSync(file)) continue;
    const original = fs.readFileSync(file, 'utf8');
    const { content, changed } = fixContent(original);
    if (changed > 0) {
        fs.writeFileSync(file, content);
        console.log(`${target}: ${changed} edge-case fixes`);
    }
}
