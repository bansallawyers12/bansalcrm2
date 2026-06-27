const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);

function fixContent(content) {
    let changed = 0;

    content = content.replace(
        /\{!!\s*\\App\\Helpers\\IconHelper::render\(([\s\S]*?)\)\s*!!\}/g,
        (match, args) => {
            changed++;
            return '<?php echo \\App\\Helpers\\IconHelper::render(' + args.trim() + '); ?>';
        }
    );

    content = content.replace(
        /' \. \\App\\Helpers\\IconHelper::render\(([\s\S]*?)\) \. '/g,
        (match, args, offset, full) => {
            const lineStart = full.lastIndexOf('\n', offset) + 1;
            const lineEnd = full.indexOf('\n', offset);
            const line = full.slice(lineStart, lineEnd === -1 ? full.length : lineEnd);

            if (/^\s*\$[a-zA-Z_][\w]*\s*(\.=|=|\+\=)/.test(line) || /^\s*return\s+'/.test(line)) {
                return match;
            }

            if (/^\s*\$[a-zA-Z_][\w]*\s*=/.test(line) && line.includes("' . \\App\\Helpers\\IconHelper::render(")) {
                return match;
            }

            changed++;
            return '<?php echo \\App\\Helpers\\IconHelper::render(' + args.trim() + '); ?>';
        }
    );

    return { content, changed };
}

for (const target of targets) {
    const file = path.resolve(target);
    const original = fs.readFileSync(file, 'utf8');
    const { content, changed } = fixContent(original);
    if (changed > 0) {
        fs.writeFileSync(file, content);
        console.log(`${target}: ${changed} context fixes`);
    }
}
