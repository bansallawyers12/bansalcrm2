const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);
if (!targets.length) {
    console.error('Usage: node scripts/migrate-php-icons.cjs <file> ...');
    process.exit(1);
}

const modifierClasses = new Set([
    'fa-spin', 'fa-pulse', 'fa-fw',
    'fa-xs', 'fa-sm', 'fa-lg', 'fa-1x', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x',
]);

function parseClassString(classValue) {
    const parts = classValue.trim().split(/\s+/).filter(Boolean);
    let style = 'solid';
    let iconName = null;
    const extras = [];

    for (const part of parts) {
        if (part === 'far' || part === 'fa-regular') {
            style = 'regular';
            continue;
        }
        if (part === 'fab' || part === 'fa-brands') {
            style = 'brands';
            continue;
        }
        if (part === 'fas' || part === 'fa-solid') {
            continue;
        }
        if (part.startsWith('fa-') && !modifierClasses.has(part)) {
            iconName = part.slice(3);
            continue;
        }
        if (!part.startsWith('fa-')) {
            extras.push(part);
        }
    }

    return { iconName, style, extras };
}

function toPhpRender({ iconName, style, extras }) {
    if (!iconName) {
        return null;
    }

    const args = ["'" + iconName + "'"];
    if (style !== 'solid') {
        args.push("'" + style + "'");
    }

    if (extras.length) {
        args.push("['class' => '" + extras.join(' ') + "']");
    } else if (style !== 'solid') {
        // render(name, style) — two args only
    } else {
        // render(name) — one arg
    }

    if (extras.length && style === 'solid') {
        return "' . \\App\\Helpers\\IconHelper::render('" + iconName + "', 'solid', ['class' => '" + extras.join(' ') + "']) . '";
    }

    if (extras.length) {
        return "' . \\App\\Helpers\\IconHelper::render('" + iconName + "', '" + style + "', ['class' => '" + extras.join(' ') + "']) . '";
    }

    if (style === 'solid') {
        return "' . \\App\\Helpers\\IconHelper::render('" + iconName + "') . '";
    }

    return "' . \\App\\Helpers\\IconHelper::render('" + iconName + "', '" + style + "') . '";
}

function migrateContent(content) {
    let changed = 0;

    content = content.replace(
        /<i class="((?:far|fas|fab)(?:\s+fa-[\w-]+)+)"(?:\s+aria-hidden="[^"]*")?\s*><\/i>/g,
        (match, classValue) => {
            const rendered = toPhpRender(parseClassString(classValue));
            if (!rendered) {
                return match;
            }
            changed++;
            return rendered;
        }
    );

    return { content, changed };
}

for (const target of targets) {
    const file = path.resolve(target);
    if (!fs.existsSync(file)) {
        console.warn('Skip (missing):', target);
        continue;
    }

    const original = fs.readFileSync(file, 'utf8');
    const { content, changed } = migrateContent(original);
    if (changed > 0) {
        fs.writeFileSync(file, content);
        console.log(`${target}: ${changed} replacements`);
    }
}
