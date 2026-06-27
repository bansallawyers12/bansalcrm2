const fs = require('fs');
const path = require('path');

const targets = process.argv.slice(2);
if (!targets.length) {
    console.error('Usage: node scripts/migrate-js-icons.cjs <file> [file...]');
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
    let spin = false;

    for (const part of parts) {
        if (part === 'far') {
            style = 'regular';
            continue;
        }
        if (part === 'fab') {
            style = 'brands';
            continue;
        }
        if (part === 'fas') {
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
            }
            continue;
        }
        extras.push(part);
    }

    return { iconName, style, extras, spin };
}

function toCrmIconCall({ iconName, style, extras, spin }) {
    if (!iconName) {
        return null;
    }

    if (iconName === 'spinner' && spin) {
        return "crmIconSpinner('')";
    }

    const options = [];
    if (spin) {
        options.push('spin: true');
    }
    if (extras.length) {
        options.push("class: '" + extras.join(' ') + "'");
    }

    if (style === 'solid' && !options.length) {
        return "crmIcon('" + iconName + "')";
    }

    if (style === 'solid' && options.length) {
        return "crmIcon('" + iconName + "', { " + options.join(', ') + " })";
    }

    if (!options.length) {
        return "crmIcon('" + iconName + "', '" + style + "')";
    }

    return "crmIcon('" + iconName + "', '" + style + "', { " + options.join(', ') + " })";
}

function migrateContent(content) {
    let changed = 0;

    const replaceIconTag = (quote, classValue, after) => {
        const parsed = parseClassString(classValue);
        const call = toCrmIconCall(parsed);
        if (!call) {
            return quote + 'i class=' + quote + classValue + quote + after;
        }
        changed++;
        return '${' + call + '}' + (after === '></i>' ? '' : after.replace(/^><\/i>/, ''));
    };

    // Double-quoted HTML strings: "<i class="fas fa-x"></i> Label"
    content = content.replace(/"([^"]*)<i class=\\"([^"\\]+)\\"><\/i>([^"]*)"/g, (match, before, cls, after) => {
        const parsed = parseClassString(cls);
        const call = toCrmIconCall(parsed);
        if (!call) {
            return match;
        }
        changed++;
        return '"' + before + '${' + call + '}' + after + '"';
    });

    content = content.replace(/'([^']*)<i class=\\'([^'\\]+)\\'><\/i>([^']*)'/g, (match, before, cls, after) => {
        const parsed = parseClassString(cls);
        const call = toCrmIconCall(parsed);
        if (!call) {
            return match;
        }
        changed++;
        return "'" + before + '${' + call + '}' + after + "'";
    });

    // Simple quoted strings: '<i class="fas fa-x"></i> text'
    content = content.replace(
        /'(<i class="(?:far|fas|fab)\s+[^"]+"><\/i>)/g,
        (match, tag) => {
            const clsMatch = tag.match(/class="([^"]+)"/);
            if (!clsMatch) {
                return match;
            }
            const parsed = parseClassString(clsMatch[1]);
            const call = toCrmIconCall(parsed);
            if (!call) {
                return match;
            }
            changed++;
            return "'${" + call + "}";
        }
    );

    // crmIconSpinner with label: '<i class="fas fa-spinner fa-spin"></i> Loading...'
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

    // Template literals already using backticks with <i class="fas fa-*"></i>
    content = content.replace(/<i class="((?:far|fas|fab)\s+[^"]+)"><\/i>/g, (match, cls) => {
        const parsed = parseClassString(cls);
        const call = toCrmIconCall(parsed);
        if (!call) {
            return match;
        }
        changed++;
        return '${' + call + '}';
    });

    content = content.replace(/<i class='((?:far|fas|fab)\s+[^']+)'><\/i>/g, (match, cls) => {
        const parsed = parseClassString(cls);
        const call = toCrmIconCall(parsed);
        if (!call) {
            return match;
        }
        changed++;
        return '${' + call + '}';
    });

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
