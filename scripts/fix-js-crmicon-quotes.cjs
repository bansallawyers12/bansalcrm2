const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);
if (!targets.length) {
    console.error('Usage: node scripts/fix-js-crmicon-quotes.cjs <file> ...');
    process.exit(1);
}

function fixContent(content) {
    let changed = 0;

    content = content.replace(
        /'([^'\\]*(?:\\.[^'\\]*)*?)\$\{crmIcon\(([^)]+)\)\}([^'\\]*(?:\\.[^'\\]*)*)'/g,
        (match, before, args, after) => {
            changed++;
            const parts = [];
            if (before) {
                parts.push("'" + before + "'");
            }
            parts.push('crmIcon(' + args + ')');
            if (after) {
                parts.push("'" + after + "'");
            }
            return parts.join(' + ');
        }
    );

    content = content.replace(
        /"([^"\\]*(?:\\.[^"\\]*)*?)\$\{crmIcon\(([^)]+)\)\}([^"\\]*(?:\\.[^"\\]*)*)"/g,
        (match, before, args, after) => {
            changed++;
            const parts = [];
            if (before) {
                parts.push('"' + before + '"');
            }
            parts.push('crmIcon(' + args + ')');
            if (after) {
                parts.push('"' + after + '"');
            }
            return parts.join(' + ');
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
        console.log(`${target}: ${changed} fixes`);
    }
}
