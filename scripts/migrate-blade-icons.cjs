const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);
if (!targets.length) {
    console.error('Usage: node scripts/migrate-blade-icons.cjs <file> [file...]');
    process.exit(1);
}

const styleMap = { far: 'regular', fab: 'brands', fas: 'solid' };

const modifierClasses = new Set([
    'fa-spin', 'fa-pulse', 'fa-fw',
    'fa-xs', 'fa-sm', 'fa-lg', 'fa-1x', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x',
    'fa-6x', 'fa-7x', 'fa-8x', 'fa-9x', 'fa-10x',
]);

function parseIconClasses(classValue) {
    const parts = classValue.trim().split(/\s+/).filter(Boolean);
    let style = 'solid';
    let iconName = null;
    const extras = [];
    let spin = false;

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
            style = 'solid';
            continue;
        }
        if (part === 'fa-spin' || part === 'fa-pulse') {
            spin = true;
            continue;
        }
        if (part.startsWith('fa-') && !modifierClasses.has(part)) {
            iconName = part.slice(3);
            continue;
        }
        if (part.startsWith('fa-') && modifierClasses.has(part)) {
            if (part === 'fa-spin' || part === 'fa-pulse') {
                spin = true;
            } else if (part.match(/^fa-[0-9]/)) {
                extras.push(part.slice(3));
            }
            continue;
        }
        extras.push(part);
    }

    return { iconName, style, extras, spin };
}

function toBladeIcon({ iconName, style, extras, spin }) {
    if (!iconName) {
        return null;
    }

    if (iconName === 'spinner' && spin) {
        const options = ["'spin' => true"];
        if (extras.length) {
            options.push("'class' => '" + extras.join(' ') + "'");
        }
        return `@icon('spinner', 'solid', [${options.join(', ')}])`;
    }

    const options = [];
    if (spin) {
        options.push("'spin' => true");
    }
    if (extras.length) {
        options.push("'class' => '" + extras.join(' ') + "'");
    }

    if (style === 'solid' && !options.length) {
        return `@icon('${iconName}')`;
    }

    if (style === 'solid' && options.length) {
        return `@icon('${iconName}', 'solid', [${options.join(', ')}])`;
    }

    if (!options.length) {
        return `@icon('${iconName}', '${styleMap[style] || style}')`;
    }

    return `@icon('${iconName}', '${styleMap[style] || style}', [${options.join(', ')}])`;
}

function migrateContent(content) {
    let changed = 0;

    const replaceTag = (match, classValue) => {
        const parsed = parseIconClasses(classValue);
        const blade = toBladeIcon(parsed);
        if (!blade) {
            return match;
        }
        changed++;
        return blade;
    };

    content = content.replace(/<i\s+class="([^"]+)"\s*><\/i>/g, replaceTag);
    content = content.replace(/<i\s*\r?\n\s*class="([^"]+)"\s*><\/i>/g, replaceTag);
    content = content.replace(/<i\s+class="([^"]+)"\s+aria-hidden="[^"]*"\s*><\/i>/g, replaceTag);
    content = content.replace(/<i\s+aria-hidden="[^"]*"\s+class="([^"]+)"\s*><\/i>/g, replaceTag);

    // Inline JS spinner strings in Blade scripts
    content = content.replace(
        /'<i class="fas fa-spinner fa-spin"><\/i>([^']*)'/g,
        (match, label) => {
            changed++;
            return "crmIconSpinner('" + label + "')";
        }
    );
    content = content.replace(
        /"<i class=\"fas fa-spinner fa-spin\"><\/i>([^"]*)"/g,
        (match, label) => {
            changed++;
            return 'crmIconSpinner("' + label + '")';
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
    } else {
        console.log(`${target}: no changes`);
    }
}
