const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const src = path.join(root, 'node_modules', '@fortawesome', 'fontawesome-free');
const dest = path.join(root, 'public', 'icons', 'font-awesome');

const allCss = path.join(src, 'css', 'all.min.css');
if (!fs.existsSync(allCss)) {
	console.error('Run npm install first (@fortawesome/fontawesome-free).');
	process.exit(1);
}

const legacyFonts = path.join(dest, 'fonts');
if (fs.existsSync(legacyFonts)) {
	fs.rmSync(legacyFonts, { recursive: true, force: true });
}

fs.mkdirSync(path.join(dest, 'css'), { recursive: true });
fs.mkdirSync(path.join(dest, 'webfonts'), { recursive: true });

for (const file of ['all.min.css', 'v4-shims.min.css']) {
	fs.copyFileSync(path.join(src, 'css', file), path.join(dest, 'css', file));
}

for (const file of fs.readdirSync(path.join(src, 'webfonts'))) {
	fs.copyFileSync(path.join(src, 'webfonts', file), path.join(dest, 'webfonts', file));
}

console.log('Font Awesome synced to public/icons/font-awesome/');
